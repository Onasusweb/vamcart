<?php
/* -----------------------------------------------------------------------------------------
   VamCart - http://vamcart.com
   -----------------------------------------------------------------------------------------
   Copyright (c) 2011 VamSoft Ltd.
   License - http://vamcart.com/license.html
   ---------------------------------------------------------------------------------------*/
class OrderBaseComponent extends Object
{

	function startup(&$controller) {
	}

	function load_models()
	{
		App::import('Component', 'ContentBase');
		$this->ContentBase =& new ContentBaseComponent();

		App::import('Model', 'Content');
		$this->Content =& new Content();

		App::import('Model', 'ContentDescription');
		$this->ContentDescription =& new ContentDescription();

		App::import('Model', 'Order');
		$this->Order =& new Order();

		App::import('Model', 'ContentProduct');
		$this->ContentProduct =& new ContentProduct();

		App::import('Model', 'ContentDownloadable');
		$this->ContentDownloadable =& new ContentDownloadable();
	}

	function get_order ($order_id = null)
	{
		if (($order_id == null) && (isset($_SESSION['Customer']['order_id']))) {
			$order_id = $_SESSION['Customer']['order_id'];
		}

		$this->load_models();

		if ($order_id != null) {
			$this->Order->unbindModel(array('belongsTo' => array('OrderStatus')));
			$this->Order->unbindModel(array('hasMany' => array('OrderComment')));
			$order = $this->Order->find(array('Order.id' => $order_id), null, null, 2);
		} else {
			$order = array();
		}

		return $order;
	}

	function get_order_shipping (&$order)
	{

		if (!isset($order['ShippingMethod']['code'])) {
			return 0;
		}

		$shipping = Inflector::classify($order['ShippingMethod']['code']);
		$shipping_controller =  Inflector::classify($order['ShippingMethod']['code']) . 'Controller';
		 App::import('Controller', 'shipping.'.$shipping);
		$this->MethodBase =& new $shipping_controller();

		$shipping_total = $this->MethodBase->calculate();

		return $shipping_total;
	}

	function get_order_tax (&$order)
	{
		$running_total = 0;

		foreach($order['OrderProduct'] AS $product) {
			$running_total += ($product['quantity'] * $product['tax']);
		}

		return $running_total;

	}

	function get_order_total (&$order)
	{
		$running_total = 0;

		foreach($order['OrderProduct'] AS $product) {
			$running_total += ($product['quantity'] * $product['price']);
		}

		return $running_total;
	}

	function update_order_totals ()
	{
		$this->load_models();

		$order = $this->Order->read(null,$_SESSION['Customer']['order_id']);

		$order['Order']['shipping'] = $this->get_order_shipping($order);
		$order['Order']['tax'] = $this->get_order_tax($order);
		$order['Order']['total'] = $this->get_order_total($order) + $order['Order']['tax'] + $order['Order']['shipping'] ;

		App::import('Component', 'EventBase');
		$this->EventBase =& new EventBaseComponent();

		$this->EventBase->ProcessEvent('UpdateOrderTotalsBeforeSave');
		$this->Order->save($order);
		$this->EventBase->ProcessEvent('UpdateOrderTotalsAfterSave');
	}

	function remove_product ($product_id, $qty = 1)
	{
		$this->load_models();

		$order_product = $this->Order->OrderProduct->find(array('content_id' => $product_id,'order_id' => $_SESSION['Customer']['order_id']));

		App::import('Component', 'EventBase');
		$this->EventBase =& new EventBaseComponent();

		$this->EventBase->ProcessEvent('RemoveFromCartBeforeSave');

		if($order_product['OrderProduct']['quantity'] <= $qty) {
			$this->Order->OrderProduct->delete($order_product['OrderProduct']['id']);
		} else {
			$order_product['OrderProduct']['quantity'] -= $qty;
			$this->Order->OrderProduct->save($order_product);
		}

		$this->EventBase->ProcessEvent('RemoveFromCartAfterSave');

		$this->update_order_totals();
	}

	function add_product($content_id, $qty = 1, $update = false) {
		$this->load_models();
		$content = $this->ContentBase->get_content_information($content_id);
		$content_type = $content['ContentType']['name'];
		$content_description = $this->ContentBase->get_content_description($content_id);
		$content['ContentDescription'] = $content_description['ContentDescription'];

		$product = $this->Order->OrderProduct->Content->find(array('Content.id' => $content_id));

		// Get the product from the OrderProduct model...
		$order_product = $this->Order->OrderProduct->find(array('order_id' => $_SESSION['Customer']['order_id'], 'content_id' => $content_id));

		// needed for calculating correct discount price
		switch ($content_type) {
			case 'product':
				$prices = $this->ContentProduct->find('first', array(
					'conditions' => array('content_id' => $content_id)
				));
				break;
			case 'downloadable':
				$prices = $this->ContentDownloadable->find('first', array(
					'conditions' => array('content_id' => $content_id)
				));
				break;
		}

		if (empty($order_product)) {

			if ('product' == $content_type) {
				if ($qty < $prices['ContentProduct']['moq']) {
					$qty = $prices['ContentProduct']['moq'];
				}
			}

			switch ($content_type) {
				case 'product':
					$order_product = array('order_id' => $_SESSION['Customer']['order_id'],
						'content_id' => $content_id,
						'name' => $content['ContentDescription']['name'],
						'model' => $product['ContentProduct']['model'],
						'quantity' => $qty,
						'price' => $product['ContentProduct']['price'],
						'weight' => $product['ContentProduct']['weight']
					);
					break;
				case 'downloadable':
					$order_product = array('order_id' => $_SESSION['Customer']['order_id'],
						'content_id' => $content_id,
						'name' => $content['ContentDescription']['name'],
						'model' => $product['ContentDownloadable']['model'],
						'quantity' => $qty,
						'price' => $product['ContentDownloadable']['price'],
						'weight' => 0,
						'filename' => $product['ContentDownloadable']['filename'],
						'filestorename' => $product['ContentDownloadable']['filestorename'],
						'download_count' => 0,
						'max_downloads' => (int)$product['ContentDownloadable']['max_downloads'],
						'max_days_for_download' => (int)$product['ContentDownloadable']['max_days_for_download'],
						'order_status_id' => (int)$product['ContentDownloadable']['order_status_id'],
						'download_key' => $this->_random_string()
					);
					break;
			}

			if (isset($prices['ContentProductPrice'])) {
				foreach ($prices['ContentProductPrice'] as $price) {
					if ($qty >= $price['quantity']) {
						$order_product['price'] = $price['price'];
					}
				}
			}
		} else {
			if (true === $update) {
				$order_product['OrderProduct']['quantity'] = abs($qty);
			} else {
				$order_product['OrderProduct']['quantity'] += abs($qty);
			}

			if ('product' == $content_type) {
				if ($order_product['OrderProduct']['quantity'] < $prices['ContentProduct']['moq']) {
					$order_product['OrderProduct']['quantity'] = $prices['ContentProduct']['moq'];
				}
			}

			if (isset($prices['ContentProductPrice'])) {
				foreach($prices['ContentProductPrice'] as $price) {
					if ($order_product['OrderProduct']['quantity'] >= $price['quantity']) {
						$order_product['OrderProduct']['price'] = $price['price'];
					}
				}
			}
		}

		App::import('Component', 'EventBase');
		$this->EventBase =& new EventBaseComponent();

		$this->EventBase->ProcessEvent('AddToCartBeforeSave');
		$this->Order->OrderProduct->save($order_product);
		$this->EventBase->ProcessEvent('AddToCartAfterSave');

		$this->update_order_totals();
	}
	
	function _random_string()
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randstring = '';
		for ($i = 0; $i < 16; $i++) {
			$randstring .= mb_substr($characters, rand(0, strlen($characters) - 1), 1);
		}
		return $randstring;
	}
}
?>