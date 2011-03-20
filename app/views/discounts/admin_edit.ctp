<?php
/* -----------------------------------------------------------------------------------------
   VaM Cart
   http://vamcart.com
   http://vamcart.ru
   Copyright 2009-2010 VaM Cart
   -----------------------------------------------------------------------------------------
   Portions Copyright:
   Copyright 2011 by David Lednik (david.lednik@gmail.com)
   -----------------------------------------------------------------------------------------
   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

$html->script(array(
	'modified.js',
	'jquery/jquery.min.js',
	'focus-first-input.js'
), array('inline' => false));

	echo $admin->ShowPageHeaderStart($current_crumb, 'edit.png');

	echo $form->create('Discount', array('id' => 'contentform', 'action' => '/discounts/admin_edit/', 'url' => '/discounts/admin_edit/'));
	echo $form->inputs(array(
					'legend' => null,
					'fieldset' => __('Discount Details', true),
				   'ContentProductPrice.id' => array(
				   		'type' => 'hidden'
	               ),
                                    'ContentProductPrice.content_product_id' => array(
				   		'type' => 'hidden'
	               ),
                                   'ContentProductPrice.quantity' => array(
				   		'label' => __('Quantity', true)
	               ),
				   'ContentProductPrice.price' => array(
   				   		'label' => __('Price', true)
	               )				     				   	   																									
			));
	echo $form->submit( __('Submit', true), array('name' => 'submit')) . $form->submit( __('Cancel', true), array('name' => 'cancel'));
	echo '<div class="clear"></div>';
	echo $form->end();
	echo $admin->ShowPageHeaderEnd(); 
?>