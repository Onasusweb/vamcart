<?php
/* -----------------------------------------------------------------------------------------
   VamShop - http://vamshop.com
   -----------------------------------------------------------------------------------------
   Copyright (c) 2014 VamSoft Ltd.
   License - http://vamshop.com/license.html
   ---------------------------------------------------------------------------------------*/

$this->Html->script(array(
	'modified.js',
	'focus-first-input.js'
), array('inline' => false));

	echo $this->Admin->ShowPageHeaderStart($current_crumb, 'cus-add');

	echo $this->Form->create('User', array('id' => 'contentform', 'action' => '/users/admin_new/', 'url' => '/users/admin_new/'));
	echo $this->Form->input('User.username', 
						array(
				   		'label' => __('Username')
	               ));
	echo $this->Form->input('User.email', 
						array(
   				   	'label' => __('Email')
	               ));
	echo $this->Form->input('User.password', 
						array(
				   		'type' => 'text',
   				   	'label' => __('Password')
	               ));
	echo $this->Admin->formButton(__('Submit'), 'cus-tick', array('class' => 'btn', 'type' => 'submit', 'name' => 'submit')) . $this->Admin->formButton(__('Cancel'), 'cus-cancel', array('class' => 'btn', 'type' => 'submit', 'name' => 'cancelbutton'));
	echo '<div class="clear"></div>';
	echo $this->Form->end();
	
	echo $this->Admin->ShowPageHeaderEnd();
	
?>