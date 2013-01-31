<?php
/* -----------------------------------------------------------------------------------------
   VamCart - http://vamcart.com
   -----------------------------------------------------------------------------------------
   Copyright (c) 2011 VamSoft Ltd.
   License - http://vamcart.com/license.html
   ---------------------------------------------------------------------------------------*/

$html->script(array(
	'modified.js',
	'focus-first-input.js'
), array('inline' => false));

	echo $admin->ShowPageHeaderStart($current_crumb, 'edit.png');

	echo $form->create('Currency', array('id' => 'contentform', 'action' => '/currencies/admin_edit/', 'url' => '/currencies/admin_edit/'));
	echo $form->inputs(array(
					'legend' => null,
					'fieldset' => __('Currency Details', true),
				   'Currency.id' => array(
				   		'type' => 'hidden'
	               ),
	               'Currency.name' => array(
				   		'label' => __('Name', true)
	               ),
				   'Currency.code' => array(
   				   		'label' => __('Code', true)
	               ),	
				   'Currency.symbol_left' => array(
   				   		'label' => __('Symbol Left', true)
	               ),	
				   'Currency.symbol_right' => array(
   				   		'label' => __('Symbol Right', true)
	               ),	
				   'Currency.decimal_point' => array(
   				   		'label' => __('Decimal Point', true)
	               ),					   				   			
				   'Currency.thousands_point' => array(
   				   		'label' => __('Thousands Point', true)
	               ),	
				   'Currency.decimal_places' => array(
   				   		'label' => __('Decimal Places', true)
	               ),				 
				   'Currency.value' => array(
   				   		'label' => __('Value', true)
	               )					     				   	   																									
			));
	echo $admin->formButton(__('Submit', true), 'submit.png', array('type' => 'submit', 'name' => 'submit')) . $admin->formButton(__('Cancel', true), 'cancel.png', array('type' => 'submit', 'name' => 'cancelbutton'));
	echo '<div class="clear"></div>';
	echo $form->end();
	echo $admin->ShowPageHeaderEnd(); 
?>