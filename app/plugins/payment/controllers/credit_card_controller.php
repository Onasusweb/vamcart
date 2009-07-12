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

class CreditCardController extends PaymentAppController {
	var $uses = null;

	function display_fields ()
	{
		$this->render();
	}

	function process_payment ()
	{
		loadComponent('CustomerBase');		
		$this->CustomerBase =& new CustomerBaseComponent();
		
		$this->CustomerBase->save_customer_details($this->data['CreditCard']);
		
		$this->redirect('/orders/place_order/');
	}

	function before_process () 
	{
	
		$content = '<form action="' . BASE . '/payment/credit_card/process_payment/" method="post">';

			$content .= $this->credit_card_fields();
		
		$content .= '
		
		</form>';
		return $content;	
	}
}

?>