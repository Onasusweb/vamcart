<?php
/* -----------------------------------------------------------------------------------------
   VamShop - http://vamshop.com
   -----------------------------------------------------------------------------------------
   Copyright (c) 2014 VamSoft Ltd.
   License - http://vamshop.com/license.html
   ---------------------------------------------------------------------------------------*/
   
class OrdersController extends AppController {
	public $name = 'Orders';
	public $helpers = array('Time');
	public $uses = array('EmailTemplate', 'AnswerTemplate', 'Order');
	public $components = array('EventBase', 'Email', 'Smarty','ConfigurationBase');
	public $paginate = array('limit' => 20, 'order' => array('Order.created' => 'desc'));

	public function confirmation ()
	{
		
		global $config;
		global $order;

		if (isset($_SESSION['Customer']['order_id'])) {

		foreach($_POST AS $key => $value)
			$order['Order'][$key] = $value;

		if ($_POST['module_coupon_code'] != '') $_SESSION['module_coupon_code'] = $_POST['module_coupon_code'];
		
		if ($_POST['email'] != '') {

		App::uses('Sanitize', 'Utility');
		$clean = new Sanitize();
		$clean->clean($_POST);
					
		App::import('Model', 'Customer');
		$Customer = new Customer();					
			
		$customer = $Customer->find('count',array('conditions' => array('Customer.email' => $_POST['email'])));
		
		if ($customer == 0) {
			
		$customer_data = array();
		$customer_password = $this->RandomString(6);
		
		if ($_POST['bill_name'] != '') $customer_data['Customer']['name'] = $_POST['bill_name'];
		if ($_POST['email'] != '') $customer_data['Customer']['email'] = $_POST['email'];
		$customer_data['Customer']['password'] = Security::hash($customer_password, 'sha1', true);
		if ($_POST['bill_name'] != '') $customer_data['AddressBook']['ship_name'] = $_POST['bill_name'];
		if ($_POST['bill_line_1'] != '') $customer_data['AddressBook']['ship_line_1'] = $_POST['bill_line_1'];
		if ($_POST['bill_line_2'] != '') $customer_data['AddressBook']['ship_line_2'] = $_POST['bill_line_2'];
		if ($_POST['bill_city'] != '') $customer_data['AddressBook']['ship_city'] = $_POST['bill_city'];
		if ($_POST['bill_country'] != '') $customer_data['AddressBook']['ship_country'] = $_POST['bill_country'];
		if ($_POST['bill_state'] != '') $customer_data['AddressBook']['ship_state'] = $_POST['bill_state'];
		if ($_POST['bill_zip'] != '') $customer_data['AddressBook']['ship_zip'] = $_POST['bill_zip'];
		if ($_POST['phone'] != '') $customer_data['AddressBook']['phone'] = $_POST['phone'];

		// Save customer data
		$Customer->saveAll($customer_data);

		// Send registration email to customer
		
		// Retrieve email template
		$this->EmailTemplate->unbindModel(array('hasMany' => array('EmailTemplateDescription')));
		$this->EmailTemplate->bindModel(array(
			'hasOne' => array(
				'EmailTemplateDescription' => array(
					'className'  => 'EmailTemplateDescription',
					'conditions' => 'language_id = ' . $this->Session->read('Customer.language_id')
				)
			)
		));

		// Get email template
		$email_template = $this->EmailTemplate->findByAlias('new-customer');

		// Email Subject
		$subject = $email_template['EmailTemplateDescription']['subject'];
		$subject = $config['SITE_NAME'] . ' - ' . $subject;

		$body = $email_template['EmailTemplateDescription']['content'];
		$body = str_replace('{$name}', $_POST['bill_name'], $body);
		$fio = explode(" ", $_POST['bill_name']);				
		$body = str_replace('{$firstname}', isset($fio[0]) ? $fio[0] : $_POST['bill_name'], $body);
		$body = str_replace('{$lastname}', isset($fio[1]) ? $fio[1] : $_POST['bill_name'], $body);
		$body = str_replace('{$email}', $_POST['email'], $body);
		$body = str_replace('{$password}', $customer_password, $body);

		$this->Email->init();
		$this->Email->From = $config['NEW_ORDER_FROM_EMAIL'];
		$this->Email->FromName = __($config['NEW_ORDER_FROM_NAME'],true);

		// Send to customer
		$this->Email->AddAddress($order['Order']['email']);
		// Send to admin
		//$this->Email->AddCC($config['SEND_EXTRA_EMAIL']);
		$this->Email->Subject = $subject;

		// Email Body
		$this->Email->Body = $body;

		// Sending mail
		$this->Email->send();
		
		
		}	
		}	

		if (isset($_SESSION['Customer']['customer_id'])) {
			$order['Order']['customer_id'] = $_SESSION['Customer']['customer_id'];
		} else {
			$order['Order']['customer_id'] = ($Customer->id > 0) ? $Customer->id : 0;
		}

		// Save order data
		$this->Order->save($order);
		
		$this->redirect('/page/confirmation' . $config['URL_EXTENSION']);				

		}
				
	}
		
	public function place_order ()
	{
		global $config;
		
		if (isset($_SESSION['Customer']['order_id'])) {
			$order = $this->Order->read(null,$_SESSION['Customer']['order_id']);
			$this->EventBase->ProcessEvent('PlaceOrderBeforeSave');

			foreach($_POST AS $key => $value) {
				$order['Order'][$key] = $value;
			}

			// Update products ordered 
			foreach($order['OrderProduct'] as $order_data) {

					$filename = (isset($order['OrderProduct']['filename'])) ? $order['OrderProduct']['filename'] : false;

					if (!$filename) {

					App::import('Model', 'ContentProduct');
					$ContentProduct = new ContentProduct();
					//$product_data = $ContentProduct->findByContentId($order_data['content_id']);
               $product_data = $ContentProduct->find('first',array('conditions' => array('ContentProduct.content_id' => $order_data['content_id'])
                                                                                           ,'fields' => array('ContentProduct.id','ContentProduct.ordered','ContentProduct.stock')
                                                                                           ));
               if ($product_data) {                                                                            
					if ($order_data['quantity'] > 0) $product_data['ContentProduct']['ordered'] = $product_data['ContentProduct']['ordered'] + $order_data['quantity'];
					if ($order_data['quantity'] > 0) $product_data['ContentProduct']['stock'] = $product_data['ContentProduct']['stock'] - $order_data['quantity'];
					$ContentProduct->save($product_data);
					}
				} else {
					App::import('Model', 'ContentDownloadable');
					$ContentDownloadable = new ContentDownloadable();
					//$product_data = $ContentDownloadable->findByContentId($order_data['content_id']);
               $product_data = $ContentDownloadable->find('first',array('conditions' => array('ContentDownloadable.content_id' => $order_data['content_id'])
                                                                                           ,'fields' => array('ContentDownloadable.id','ContentDownloadable.ordered','ContentDownloadable.stock')
                                                                                           ));                                        
               if ($product_data) {                                                                            
					if ($order_data['quantity'] > 0) $product_data['ContentDownloadable']['ordered'] = $product_data['ContentDownloadable']['ordered'] + $order_data['quantity'];
					if ($order_data['quantity'] > 0) $product_data['ContentDownloadable']['stock'] = $product_data['ContentDownloadable']['stock'] - $order_data['quantity'];
					$ContentDownloadable->save($product_data);
					}
				}

			}

			// Get the default order status
			if ($order['Order']['order_status_id'] == 0) {
				$default_status = $this->Order->OrderStatus->find('first', array('conditions' => array('default' => '1')));
				$order['Order']['order_status_id'] = $default_status['OrderStatus']['id'];
			}

			// Save the order
			$this->Order->save($order);

			// Load the after_process public function from the payment modules
			$this->requestAction('/payment/'.$order['PaymentMethod']['alias'].'/after_process/');

			// Empty the cart
			$_SESSION['Customer']['order_id'] = null;

			$this->EventBase->ProcessEvent('PlaceOrderAfterSave');

			// Sending email
			
			// Retrieve email template
			$this->EmailTemplate->unbindModel(array('hasMany' => array('EmailTemplateDescription')));
			$this->EmailTemplate->bindModel(
				array('hasOne' => array(
					'EmailTemplateDescription' => array(
						'className'  => 'EmailTemplateDescription',
						'conditions' => 'language_id = ' . $this->Session->read('Customer.language_id')
					)
				))
			);

			// Get email template
			$email_template = $this->EmailTemplate->findByAlias('new-order');
			// Get current order status
			$current_order_status = $this->Order->OrderStatus->OrderStatusDescription->find('first', array('conditions' => array('OrderStatusDescription.order_status_id =' => $order['Order']['order_status_id'], 'OrderStatusDescription.language_id =' => $this->Session->read('Customer.language_id'))));

			// Email Subject
			$subject = $email_template['EmailTemplateDescription']['subject'];
			$subject = str_replace('{$order_number}',$order['Order']['id'], $subject);
			$subject = $config['SITE_NAME'] . ' - ' . $subject;

			$body = $email_template['EmailTemplateDescription']['content'];
			$body = str_replace('{$name}', $order['Order']['bill_name'], $body);
			$fio = explode(" ", $order['Order']['bill_name']);				
			$body = str_replace('{$firstname}', isset($fio[0]) ? $fio[0] : $order['Order']['bill_name'], $body);
			$body = str_replace('{$lastname}', isset($fio[1]) ? $fio[1] : $order['Order']['bill_name'], $body);
			$body = str_replace('{$order_number}', $order['Order']['id'], $body);
			$body = str_replace('{$order_status}', $current_order_status['OrderStatusDescription']['name'], $body);

			$body = str_replace('{$bill_name}', $order['Order']['bill_name'], $body);
			$body = str_replace('{$bill_line_1}', $order['Order']['bill_line_1'], $body);
			$body = str_replace('{$bill_line_2}', $order['Order']['bill_line_2'], $body);
			$body = str_replace('{$bill_city}', $order['Order']['bill_city'], $body);
			$body = str_replace('{$bill_state}', $order['BillState']['name'], $body);
			$body = str_replace('{$bill_country}', $order['BillCountry']['name'], $body);
			$body = str_replace('{$bill_zip}', $order['Order']['bill_zip'], $body);

			$body = str_replace('{$ship_name}', $order['Order']['ship_name'], $body);
			$body = str_replace('{$ship_line_1}', $order['Order']['ship_line_1'], $body);
			$body = str_replace('{$ship_line_2}', $order['Order']['ship_line_2'], $body);
			$body = str_replace('{$ship_city}', $order['Order']['ship_city'], $body);
			$body = str_replace('{$ship_state}', $order['ShipState']['name'], $body);
			$body = str_replace('{$ship_country}', $order['ShipCountry']['name'], $body);
			$body = str_replace('{$ship_zip}', $order['Order']['ship_zip'], $body);

			$order = $this->Order->find('all', array('conditions' => array('Order.id' => $order['Order']['id'])));
			$order = $order[0];

			$body = str_replace('{$shipping_method}', $order['ShippingMethod']['name'], $body);
			$body = str_replace('{$payment_method}', $order['PaymentMethod']['name'], $body);
			$body = str_replace('{$date}', $order['Order']['created'], $body);
			$body = str_replace('{$phone}', $order['Order']['phone'], $body);
			$body = str_replace('{$email}', $order['Order']['email'], $body);
			$body = str_replace('{$order_total}', $order['Order']['total'], $body);

			$order_products = '';
			foreach($order['OrderProduct'] AS $product) {
				$order_products .= $product['quantity'] . ' x ' . $product['name'] . ' = ' . $product['quantity']*$product['price'] . "\n";
				if ('' != $product['filename']) {
					$order_products .= __('Download link: ', true) . FULL_BASE_URL . BASE . '/download/' . $order['Order']['id'] . '/' . $product['id'] . '/' . $product['download_key'] . "\n";
				}
			}

			$order_products .= "\n" . $order['ShippingMethod']['name'] . ': ' . $order['Order']['shipping'] . "\n";
			$order_products .= __('Order Total',true) . ': ' . $order['Order']['total'] . "\n";

			$body = str_replace('{$products}', $order_products, $body);

			if ($order['Order']['email'] != '') {
				// Set up mail
				$this->Email->init();
				$this->Email->From = $config['NEW_ORDER_FROM_EMAIL'];
				$this->Email->FromName = __($config['NEW_ORDER_FROM_NAME'],true);
				// Send to customer
				$this->Email->AddAddress($order['Order']['email']);
				$this->Email->Subject = $subject;

				// Email Body
				$this->Email->Body = $body;

				// Sending mail
				$this->Email->send();
			}
		
			// Send to admin
			if($config['SEND_EXTRA_EMAIL'] != '') {

				// Set up mail
				$this->Email->init();
				$this->Email->From = $config['NEW_ORDER_FROM_EMAIL'];
				$this->Email->FromName = __($config['NEW_ORDER_FROM_NAME'],true);
				$this->Email->AddAddress($config['SEND_EXTRA_EMAIL']);
				$this->Email->Subject = $subject;

				// Email Body
				$this->Email->Body = $body;
		
				// Sending mail
				$this->Email->send();

			}

		}

		// Get the configuration values to redirect
		$this->redirect('/page/success' . $config['URL_EXTENSION']);
	}

	public function admin_modify_selected() 	
	{
		$build_flash = "";
		foreach($this->params['data']['Order']['modify'] AS $value)
		{
			// Make sure the id is valid
			if($value > 0)
			{
				$this->Order->id = $value;
				$order = $this->Order->read();
		
				switch ($this->data['multiaction']) 
				{
					case "delete":

						// Restock 
						foreach($order['OrderProduct'] as $order_data) {
			
							$filename = (isset($order['OrderProduct']['filename'])) ? $order['OrderProduct']['filename'] : false;

							if (!$filename) {

								App::import('Model', 'ContentProduct');
								$ContentProduct = new ContentProduct();
								//$product_data = $ContentProduct->findByContentId($order_data['content_id']);
                                                                $product_data = $ContentProduct->find('first',array('conditions' => array('ContentProduct.content_id' => $order_data['content_id'])
                                                                                           ,'fields' => array('ContentProduct.id','ContentProduct.ordered','ContentProduct.stock')
                                                                                           ));                                                                
								$product_data['ContentProduct']['ordered'] = $product_data['ContentProduct']['ordered'] - $order_data['quantity'];
								$product_data['ContentProduct']['stock'] = $product_data['ContentProduct']['stock'] + $order_data['quantity'];
								$ContentProduct->save($product_data);
							} else {
								App::import('Model', 'ContentDownloadable');
								$ContentDownloadable = new ContentDownloadable();
								//$product_data = $ContentDownloadable->findByContentId($order_data['content_id']);
                                                                $product_data = $ContentDownloadable->find('first',array('conditions' => array('ContentDownloadable.content_id' => $order_data['content_id'])
                                                                                           ,'fields' => array('ContentDownloadable.id','ContentDownloadable.ordered','ContentDownloadable.stock')
                                                                                           ));                                                                 
								$product_data['ContentDownloadable']['ordered'] = $product_data['ContentDownloadable']['ordered'] - $order_data['quantity'];
								$product_data['ContentDownloadable']['stock'] = $product_data['ContentDownloadable']['stock'] + $order_data['quantity'];
								$ContentDownloadable->save($product_data);
							}
			
						}
			
						// Delete the order
						$this->Order->delete($value);

						$build_flash .= __('Record deleted.', true) . ' ' . __('Order Id', true) . ' ' . $order['Order']['id'] . '<br />';									
			
					break;								
					case "change_status":
						$status_id = $this->data['status'];
						$comment = $this->data['comment'];
						$notify = $this->data['notify'];
						$this->_change_status($order, $status_id, $comment, $notify);
						$build_flash .= __('Order status changed.', true);
						$target_page = '/orders/admin/';
						break;
				}
			}
		}
		$this->Session->setFlash($build_flash);
		$this->redirect('/orders/admin/');
	}	

	public function _change_status($order, $status_id, $comment, $notify)
	{
		$order['Order']['order_status_id'] = $status_id;

		if ($comment != '') {
		$order['OrderComment']['order_id'] = $order['Order']['id'];
		$order['OrderComment']['comment'] = $comment;
		$order['OrderComment']['sent_to_customer'] = $notify;
		$order['OrderComment']['created'] = date("Y-m-d H:i:s");
		$order['OrderComment']['modified'] = date("Y-m-d H:i:s");
		}
		
		$this->Order->save($order['Order']);

		if ($comment != '') {
		$this->Order->OrderComment->saveAll($order['OrderComment']);
		}
		
		if ($notify == 1) {
		
		global $config;
		$config = $this->ConfigurationBase->load_configuration();
		
		// Retrieve email template
		$this->EmailTemplate->unbindModel(array('hasMany' => array('EmailTemplateDescription')));
		$this->EmailTemplate->bindModel(
	        array('hasOne' => array(
				'EmailTemplateDescription' => array(
                    'className' => 'EmailTemplateDescription',
					'conditions'   => 'language_id = ' . $this->Session->read('Customer.language_id')
                )
            )
           	)
	    );
		
		// Get email template
		$email_template = $this->EmailTemplate->findByAlias('new-order-status');
		// Get current order status
		$current_order_status = $this->Order->OrderStatus->OrderStatusDescription->find('first', array('conditions' => array('OrderStatusDescription.order_status_id =' => $status_id, 'OrderStatusDescription.language_id =' => $this->Session->read('Customer.language_id'))));
		
		if ($order['Order']['email'] != '') {
		// Set up mail
		$this->Email->init();
		$this->Email->From = $config['NEW_ORDER_STATUS_FROM_EMAIL'];
		$this->Email->FromName = __($config['NEW_ORDER_STATUS_FROM_NAME'],true);
		$this->Email->AddAddress($order['Order']['email']);
		
		// Email Subject
		$subject = $email_template['EmailTemplateDescription']['subject'];
		$subject = str_replace('{$order_number}', $order['Order']['id'], $subject);
		$subject = $config['SITE_NAME'] . ' - ' . $subject;
		$this->Email->Subject = $subject;
		
		$body = $email_template['EmailTemplateDescription']['content'];
		$body = str_replace('{$name}', $order['Order']['bill_name'], $body);
		$fio = explode(" ", $order['Order']['bill_name']);				
		$body = str_replace('{$firstname}', isset($fio[0]) ? $fio[0] : $order['Order']['bill_name'], $body);
		$body = str_replace('{$lastname}', isset($fio[1]) ? $fio[1] : $order['Order']['bill_name'], $body);
		$body = str_replace('{$order_number}', $order['Order']['id'], $body);
		$body = str_replace('{$order_status}', $current_order_status['OrderStatusDescription']['name'], $body);
		$body = str_replace('{$comments}', $order['OrderComment']['comment'], $body);

		$body = str_replace('{$bill_name}', $order['Order']['bill_name'], $body);
		$body = str_replace('{$bill_line_1}', $order['Order']['bill_line_1'], $body);
		$body = str_replace('{$bill_line_2}', $order['Order']['bill_line_2'], $body);
		$body = str_replace('{$bill_city}', $order['Order']['bill_city'], $body);
		$body = str_replace('{$bill_state}', $order['BillState']['name'], $body);
		$body = str_replace('{$bill_country}', $order['BillCountry']['name'], $body);
		$body = str_replace('{$bill_zip}', $order['Order']['bill_zip'], $body);

		$body = str_replace('{$ship_name}', $order['Order']['ship_name'], $body);
		$body = str_replace('{$ship_line_1}', $order['Order']['ship_line_1'], $body);
		$body = str_replace('{$ship_line_2}', $order['Order']['ship_line_2'], $body);
		$body = str_replace('{$ship_city}', $order['Order']['ship_city'], $body);
		$body = str_replace('{$ship_state}', $order['ShipState']['name'], $body);
		$body = str_replace('{$ship_country}', $order['ShipCountry']['name'], $body);
		$body = str_replace('{$ship_zip}', $order['Order']['ship_zip'], $body);

		$body = str_replace('{$shipping_method}', $order['ShippingMethod']['name'], $body);
		$body = str_replace('{$payment_method}', $order['PaymentMethod']['name'], $body);
		$body = str_replace('{$date}', $order['Order']['created'], $body);
		$body = str_replace('{$phone}', $order['Order']['phone'], $body);
		$body = str_replace('{$email}', $order['Order']['email'], $body);
		$body = str_replace('{$order_total}', $order['Order']['total'], $body);
		
		// Email Body
		$this->Email->Body = $body;
		
		// Sending mail
		$this->Email->send();
		}
		
		}
	}

	public function admin_order_statuses()
	{
		// Bind and set the order status select list
		$this->Order->OrderStatus->unbindModel(array('hasMany' => array('OrderStatusDescription')));
		$this->Order->OrderStatus->bindModel(
	        array('hasOne' => array(
				'OrderStatusDescription' => array(
                    'className' => 'OrderStatusDescription',
					'conditions'   => 'language_id = ' . $this->Session->read('Customer.language_id')
                )
            )
           	)
	    );		
		
		$status_list = $this->Order->OrderStatus->find('all', array('order' => array('OrderStatus.order ASC')));
		$order_status_list = array();
		
		foreach($status_list AS $status)
		{
			$status_key = $status['OrderStatus']['id'];
			$order_status_list[$status_key] = $status['OrderStatusDescription']['name'];
		}
		
		$this->set('order_status_list',$order_status_list);

			// Retrieve answer template
			$this->AnswerTemplate->unbindModel(array('hasMany' => array('AnswerTemplateDescription')));
			$this->AnswerTemplate->bindModel(
				array('hasOne' => array(
					'AnswerTemplateDescription' => array(
						'className'  => 'AnswerTemplateDescription',
						'conditions' => 'language_id = ' . $this->Session->read('Customer.language_id')
					)
				))
			);

		$answer_status_list = $this->AnswerTemplate->find('all', array('order' => array('AnswerTemplate.order ASC')));
		$answer_template_list = array();

		foreach($answer_status_list AS $answer_status)
		{
			$answer_status_key = $answer_status['AnswerTemplateDescription']['content'];
			$answer_template_list[$answer_status_key] = $answer_status['AnswerTemplateDescription']['name'];
		}
		
		$this->set('answer_template_list',$answer_template_list);
		
	}
		
	public function admin_delete ($id)
	{
		$this->Session->setFlash(__('Record deleted.',true));

			$order = $this->Order->read(null,$id);

			// Restock 
			foreach($order['OrderProduct'] as $order_data) {

					$filename = (isset($order['OrderProduct']['filename'])) ? $order['OrderProduct']['filename'] : false;

					if (!$filename) {

					App::import('Model', 'ContentProduct');
					$ContentProduct = new ContentProduct();
					//$product_data = $ContentProduct->findByContentId($order_data['content_id']);
                                        $product_data = $ContentProduct->find('first',array('conditions' => array('ContentProduct.content_id' => $order_data['content_id'])
                                                                                           ,'fields' => array('ContentProduct.id','ContentProduct.ordered','ContentProduct.stock')
                                                                                           ));                                        
					$product_data['ContentProduct']['ordered'] = $product_data['ContentProduct']['ordered'] - $order_data['quantity'];
					$product_data['ContentProduct']['stock'] = $product_data['ContentProduct']['stock'] + $order_data['quantity'];
					$ContentProduct->save($product_data);
				} else {
					App::import('Model', 'ContentDownloadable');
					$ContentDownloadable = new ContentDownloadable();
					//$product_data = $ContentDownloadable->findByContentId($order_data['content_id']);
                                        $product_data = $ContentDownloadable->find('first',array('conditions' => array('ContentDownloadable.content_id' => $order_data['content_id'])
                                                                                           ,'fields' => array('ContentDownloadable.id','ContentDownloadable.ordered','ContentDownloadable.stock')
                                                                                           )); 
					$product_data['ContentDownloadable']['ordered'] = $product_data['ContentDownloadable']['ordered'] - $order_data['quantity'];
					$product_data['ContentDownloadable']['stock'] = $product_data['ContentDownloadable']['stock'] + $order_data['quantity'];
					$ContentDownloadable->save($product_data);
				}

			}

			// Delete the order
			$this->Order->delete($id,true);

		$this->redirect('/orders/admin/');
	}

	public function admin_new_comment ($user = null)
	{
		// First get the original order, and see if we're changing status
		$old_order = $this->Order->read(null,$this->data['Order']['id']);

		if($old_order['Order']['order_status_id'] != $this->data['Order']['order_status_id'])
		{
			// Do nothing for now, but we'll do something later I think....
		}
		
		$this->Order->save($this->data);
		$this->Order->OrderComment->save($this->data);
		
		if ($this->data['OrderComment']['sent_to_customer'] == 1) {
		
		global $config;
		$config = $this->ConfigurationBase->load_configuration();
		
		// Retrieve email template
		$this->EmailTemplate->unbindModel(array('hasMany' => array('EmailTemplateDescription')));
		$this->EmailTemplate->bindModel(
	        array('hasOne' => array(
				'EmailTemplateDescription' => array(
                    'className' => 'EmailTemplateDescription',
					'conditions'   => 'language_id = ' . $this->Session->read('Customer.language_id')
                )
            )
           	)
	    );
		
		// Get email template
		$email_template = $this->EmailTemplate->findByAlias('new-order-status');
		// Get current order status
		$current_order_status = $this->Order->OrderStatus->OrderStatusDescription->find('first', array('conditions' => array('OrderStatusDescription.order_status_id =' => $this->data['Order']['order_status_id'], 'OrderStatusDescription.language_id =' => $this->Session->read('Customer.language_id'))));
		
		if ($old_order['Order']['email'] != '') {
		// Set up mail
		$this->Email->init();
		$this->Email->From = $config['NEW_ORDER_STATUS_FROM_EMAIL'];
		$this->Email->FromName = __($config['NEW_ORDER_STATUS_FROM_NAME'],true);
		$this->Email->AddAddress($old_order['Order']['email']);
		
		// Email Subject
		$subject = $email_template['EmailTemplateDescription']['subject'];
		$subject = str_replace('{$order_number}', $this->data['Order']['id'], $subject);
		$subject = $config['SITE_NAME'] . ' - ' . $subject;
		$this->Email->Subject = $subject;
		
		$body = $email_template['EmailTemplateDescription']['content'];

		$body = str_replace('{$name}', $old_order['Order']['bill_name'], $body);
		$fio = explode(" ", $old_order['Order']['bill_name']);				
		$body = str_replace('{$firstname}', isset($fio[0]) ? $fio[0] : $old_order['Order']['bill_name'], $body);
		$body = str_replace('{$lastname}', isset($fio[1]) ? $fio[1] : $old_order['Order']['bill_name'], $body);

		$body = str_replace('{$order_number}', $this->data['Order']['id'], $body);
		$body = str_replace('{$order_status}', $current_order_status['OrderStatusDescription']['name'], $body);
		$body = str_replace('{$comments}', $this->data['OrderComment']['comment'], $body);

		$body = str_replace('{$bill_name}', $old_order['Order']['bill_name'], $body);
		$body = str_replace('{$bill_line_1}', $old_order['Order']['bill_line_1'], $body);
		$body = str_replace('{$bill_line_2}', $old_order['Order']['bill_line_2'], $body);
		$body = str_replace('{$bill_city}', $old_order['Order']['bill_city'], $body);
		$body = str_replace('{$bill_state}', $old_order['BillState']['name'], $body);
		$body = str_replace('{$bill_country}', $old_order['Order']['bill_country'], $body);
		$body = str_replace('{$bill_zip}', $old_order['Order']['bill_zip'], $body);

		$body = str_replace('{$ship_name}', $old_order['Order']['ship_name'], $body);
		$body = str_replace('{$ship_line_1}', $old_order['Order']['ship_line_1'], $body);
		$body = str_replace('{$ship_line_2}', $old_order['Order']['ship_line_2'], $body);
		$body = str_replace('{$ship_city}', $old_order['Order']['ship_city'], $body);
		$body = str_replace('{$ship_state}', $old_order['ShipState']['name'], $body);
		$body = str_replace('{$ship_country}', $old_order['Order']['ship_country'], $body);
		$body = str_replace('{$ship_zip}', $old_order['Order']['ship_zip'], $body);

		$body = str_replace('{$shipping_method}', $old_order['ShippingMethod']['name'], $body);
		$body = str_replace('{$payment_method}', $old_order['PaymentMethod']['name'], $body);
		$body = str_replace('{$date}', $old_order['Order']['created'], $body);
		$body = str_replace('{$phone}', $old_order['Order']['phone'], $body);
		$body = str_replace('{$email}', $old_order['Order']['email'], $body);
		
		// Email Body
		$this->Email->Body = $body;
		
		// Sending mail
		$this->Email->send();
		}
		
		}
		
		$this->redirect('/orders/admin_view/' . $this->data['Order']['id']);
	}	
	
	public function admin_view ($id)
	{
		$this->set('current_crumb', __('Order View', true));
		$this->set('title_for_layout', __('Order View', true));
		$order = $this->Order->find('all', array('conditions' => array('Order.id' => $id)));
		$this->set('data',$order[0]);
		
		// Bind and set the order status select list
		$this->Order->OrderStatus->unbindModel(array('hasMany' => array('OrderStatusDescription')));
		$this->Order->OrderStatus->bindModel(
	        array('hasOne' => array(
				'OrderStatusDescription' => array(
                    'className' => 'OrderStatusDescription',
					'conditions'   => 'language_id = ' . $this->Session->read('Customer.language_id')
                )
            )
           	)
	    );		
		
		$status_list = $this->Order->OrderStatus->find('all', array('order' => array('OrderStatus.order ASC')));
		$order_status_list = array();
		
		foreach($status_list AS $status)
		{
			$status_key = $status['OrderStatus']['id'];
			$order_status_list[$status_key] = $status['OrderStatusDescription']['name'];
		}
		
		$this->set('order_status_list',$order_status_list);

			// Retrieve answer template
			$this->AnswerTemplate->unbindModel(array('hasMany' => array('AnswerTemplateDescription')));
			$this->AnswerTemplate->bindModel(
				array('hasOne' => array(
					'AnswerTemplateDescription' => array(
						'className'  => 'AnswerTemplateDescription',
						'conditions' => 'language_id = ' . $this->Session->read('Customer.language_id')
					)
				))
			);
			
		$answer_status_list = $this->AnswerTemplate->find('all', array('order' => array('AnswerTemplate.order ASC')));
		$answer_template_list = array();
		
		foreach($answer_status_list AS $answer_status)
		{
			$answer_status_key = $answer_status['AnswerTemplateDescription']['content'];
			$answer_template_list[$answer_status_key] = $answer_status['AnswerTemplateDescription']['name'];
		}
		
		$this->set('answer_template_list',$answer_template_list);

	}
	
	public function admin ($ajax = false)
	{
		$this->set('current_crumb', __('Orders Listing', true));
		$this->set('title_for_layout', __('Orders Listing', true));

		$data = $this->paginate('Order',"Order.order_status_id > 0");

		// Bind and set the order status select list
		$this->Order->OrderStatus->unbindModel(array('hasMany' => array('OrderStatusDescription')));
		$this->Order->OrderStatus->bindModel(
	        array('hasOne' => array(
				'OrderStatusDescription' => array(
                    'className' => 'OrderStatusDescription',
					'conditions'   => 'language_id = ' . $this->Session->read('Customer.language_id')
                )
            )
           	)
	    );		
		
		$status_list = $this->Order->OrderStatus->find('all', array('order' => array('OrderStatus.order ASC')));
		$order_status_list = array();
		
		foreach($status_list AS $status)
		{
			$status_key = $status['OrderStatus']['id'];
			$order_status_list[$status_key] = $status['OrderStatusDescription']['name'];
		}
		
		$this->set('order_status_list',$order_status_list);


		$this->set('data',$data);

	}	

	public function admin_search() {
		if (!isset($_SESSION['Search'])) {
			$_SESSION['Search'] = array();
		}

		if (isset($this->data['Search']['term'])) {
			$_SESSION['Search']['term'] = $this->data['Search']['term'];
		}
		$this->set('current_crumb', __('Search Result', true));
		$this->set('title_for_layout', __('Search Result', true));

		if (isset($_SESSION['Search']['term']) and ($this->RequestHandler->isPost() or isset($this->params['named']['page']) )) {
			$term = $_SESSION['Search']['term'];
		} else {
			$term ='~';
			unset($_SESSION['Search']['term']);
		}

		$data = $this->paginate('Order', "Order.order_status_id > 0 and (Order.id='" . (int)$term . "' or Order.bill_name LIKE '%" . $term . "%' or Order.email LIKE '%" . $term . "%' or Order.phone LIKE '%" . $term . "%')");
		$this->set('data',$data);

		// Bind and set the order status select list
		$this->Order->OrderStatus->unbindModel(array('hasMany' => array('OrderStatusDescription')));
		$this->Order->OrderStatus->bindModel(
	        array('hasOne' => array(
				'OrderStatusDescription' => array(
                    'className' => 'OrderStatusDescription',
					'conditions'   => 'language_id = ' . $this->Session->read('Customer.language_id')
                )
            )
           	)
	    );		
		
		$status_list = $this->Order->OrderStatus->find('all', array('order' => array('OrderStatus.order ASC')));
		$order_status_list = array();
		
		foreach($status_list AS $status)
		{
			$status_key = $status['OrderStatus']['id'];
			$order_status_list[$status_key] = $status['OrderStatusDescription']['name'];
		}
		
		$this->set('order_status_list',$order_status_list);

	}
	
	private function RandomString($length) 
		{
		$chars = array( 'a', 'A', 'b', 'B', 'c', 'C', 'd', 'D', 'e', 'E', 'f', 'F', 'g', 'G', 'h', 'H', 'i', 'I', 'j', 'J',  'k', 'K', 'l', 'L', 'm', 'M', 'n','N', 'o', 'O', 'p', 'P', 'q', 'Q', 'r', 'R', 's', 'S', 't', 'T',  'u', 'U', 'v','V', 'w', 'W', 'x', 'X', 'y', 'Y', 'z', 'Z', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0');
		
		$max_chars = count($chars) - 1;
		srand( (double) microtime()*1000000);
		
		$rand_str = '';
		for($i=0;$i<$length;$i++)
		{
		$rand_str = ( $i == 0 ) ? $chars[rand(0, $max_chars)] : $rand_str . $chars[rand(0, $max_chars)];
		}
		
		return $rand_str;
		}
			
}
?>