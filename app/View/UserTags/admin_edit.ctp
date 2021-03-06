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

	echo $this->Admin->ShowPageHeaderStart($current_crumb, 'cus-application-edit');

	$user_tag_id = $this->data['UserTag']['id'];
	echo $this->Form->create('UserTag', array('id' => 'contentform', 'action' => '/user_tags/admin_edit/'.$user_tag_id, 'url' => '/user_tags/admin_edit/'.$user_tag_id));
	
			echo '<ul id="myTab" class="nav nav-tabs">';
			echo $this->Admin->CreateTab('main',__('Main'), 'cus-application');
			echo $this->Admin->CreateTab('options',__('Options'), 'cus-cog');			
			echo '</ul>';

	echo $this->Admin->StartTabs();
	
	echo $this->Admin->StartTabContent('main');
		echo $this->Form->input('UserTag.id', 
						array(
				   		'type' => 'hidden'
	               ));
		echo $this->Form->input('UserTag.name', 
						array(
   				   	'label' => __('Name')
	               ));
		echo $this->Form->input('UserTag.content', 
						array(
   				   	'label' => __('Content')
	               ));
	echo $this->Admin->EndTabContent();

	echo $this->Admin->StartTabContent('options');
		echo $this->Form->input('UserTag.alias', 
						array(
   				   	'label' => __('Alias')
	                ));
	echo $this->Admin->EndTabContent();
	
	echo $this->Admin->EndTabs();
	
	echo $this->Admin->formButton(__('Submit'), 'cus-tick', array('class' => 'btn', 'type' => 'submit', 'name' => 'submitbutton', 'id' => 'submit')) . $this->Admin->formButton(__('Apply'), 'cus-disk', array('class' => 'btn', 'type' => 'submit', 'name' => 'apply')) . $this->Admin->formButton(__('Cancel'), 'cus-cancel', array('class' => 'btn', 'type' => 'submit', 'name' => 'cancelbutton'));
	echo '<div class="clear"></div>';
	echo $this->Form->end();
	echo $this->Admin->ShowPageHeaderEnd(); 
?>