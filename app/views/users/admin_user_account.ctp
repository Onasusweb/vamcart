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

	echo $form->create('User', array('action' => '/users/admin_user_account/', 'url' => '/users/admin_user_account/'));
	echo $form->inputs(array(
					'fieldset' => __('Account Details', true),
				   'User/id' => array(
				   		'type' => 'hidden'
	               ),
	               'User/username' => array(
				   		'label' => __('Username', true)
	               ),
				   'User/email' => array(
   				   		'label' => __('Email', true)
	               ),
				   'User/password' => array(
				   		'type' => 'password',
   				   		'label' => __('New Password', true)
	               ),
				   'User/confirm_password' => array(
				   		'type' => 'password',				   
   				   		'label' => __('Confirm Password', true)
	               )				   				   
			));
	echo $form->submit( __('Submit', true), array('name' => 'submit')) . $form->submit( __('Cancel', true), array('name' => 'cancel'));
	echo '<div class="clear"></div>';
	echo $form->end();
?>