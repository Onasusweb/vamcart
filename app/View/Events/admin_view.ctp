<?php
/* -----------------------------------------------------------------------------------------
   VamShop - http://vamshop.com
   -----------------------------------------------------------------------------------------
   Copyright (c) 2014 VamSoft Ltd.
   License - http://vamshop.com/license.html
   ---------------------------------------------------------------------------------------*/

echo $this->Admin->ShowPageHeaderStart($current_crumb, 'cus-page-gear');

echo '<table class="contentTable">';

echo $this->Html->tableHeaders(array(	__('Originator'),__('Action')));

foreach ($event_handlers AS $handle)
{
	echo $this->Admin->TableCells(
		  array(
				$handle['EventHandler']['originator'],
				array($handle['EventHandler']['action'], array('align'=>'center'))
		   ));
		   	
}

echo '</table>';
echo $this->Admin->EmptyResults($event_handlers);

echo $this->Admin->ShowPageHeaderEnd();

?>