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

	echo $form->create('Stylesheet', array('action' => '/stylesheets/admin_edit/'.$data['Stylesheet']['id'], 'url' => '/stylesheets/admin_edit/'.$data['Stylesheet']['id']));

	echo $admin->StartTabs();
			echo '<ul>';
			echo $admin->CreateTab('main',__('Main',true));
			echo $admin->CreateTab('options',__('Options',true));			
			echo '</ul>';
	
	echo $admin->StartTabContent('main');
	echo $form->inputs(array(
					'fieldset' => __('Stylesheet Details', true),
				   'Stylesheet/id' => array(
				   		'type' => 'hidden',
						'value' => $data['Stylesheet']['id']
	               ),
	               'Stylesheet/name' => array(
   				   		'label' => __('Name', true),				   
   						'value' => $data['Stylesheet']['name']
	               ),
				   'Stylesheet/stylesheet' => array(
   				   		'label' => __('Stylesheets', true),				   
   						'value' => $data['Stylesheet']['stylesheet']
	               )																										
			));
	echo $admin->EndTabContent();

	echo $admin->StartTabContent('options');			
		echo $form->inputs(array(
					'fieldset' => __('Stylesheet Details', true),
					'Stylesheet/alias' => array(
			   		'label' => __('Alias', true),				   
					'value' => $data['Stylesheet']['alias']
                	),
				   'Stylesheet/active' => array(
				   		'label' => __('Active', true),
				   		'type' => 'checkbox',
						'class' => 'checkbox_group',						
   						'checked' => $active_checked
	               )																										
			));
			
	echo $admin->EndTabContent();			

	echo $admin->EndTabs();
			
	echo $form->submit(__('Submit', true), array('name' => 'submit')) . $form->submit(__('Apply', true), array('name' => 'apply')) . $form->submit(__('Cancel', true), array('name' => 'cancel'));
	echo '<div class="clear"></div>';
	echo $form->end();
	?>