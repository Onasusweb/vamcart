<?php
/** SMS - Selling Made Simple
 * Copyright 2007 by Kevin Grandon (kevingrandon@hotmail.com)
 * This project's homepage is: http://sellingmadesimple.org
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * BUT withOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
**/

	$id = $this->data['GlobalContentBlock']['id'];
	echo $form->create('GlobalContentBlock', array('action' => '/global_content_blocks/admin_edit/'.$id, 'url' => '/global_content_blocks/admin_edit/'.$id));
	
	echo $admin->StartTabs();
			echo $admin->CreateTab('main',__('Main',true));
			echo $admin->CreateTab('options',__('Options',true));			
	echo $admin->EndTabs();

	
	echo $admin->StartTabContent('main');
		echo $form->inputs(array(
					'fieldset' => __('global_content_block_details', true),
				   'GlobalContentBlock/id' => array(
				   		'type' => 'hidden'
	               ),
	               'GlobalContentBlock/name' => array(
   				   		'label' => __('name', true)
	               ),
				   'GlobalContentBlock/content' => array(
   				   		'label' => __('contents', true)
	               )																										
			));
	echo $admin->EndTabContent();

	echo $admin->StartTabContent('options');
		echo $form->inputs(array(
					'fieldset' => __('global_content_block_details', true),
	                'GlobalContentBlock/alias' => array(
   				   		'label' => __('alias', true)
	                ),
				    'GlobalContentBlock/active' => array(
						'type' => 'checkbox',
   				   		'label' => __('active', true),
						'value' => '1',
						'class' => 'checkbox_group'
	                )																										
			));
	echo $admin->EndTabContent();
	
	
	echo $form->submit('Submit', array('name' => 'submit')) . $form->submit('Apply', array('name' => 'apply')) . $form->submit('Cancel', array('name' => 'cancel'));
	echo '<div class="clearb"></div>';
	echo $form->end();
	?>
</div>