<?php
/* -----------------------------------------------------------------------------------------
   VamShop - http://vamshop.com
   -----------------------------------------------------------------------------------------
   Copyright (c) 2014 VamSoft Ltd.
   License - http://vamshop.com/license.html
   ---------------------------------------------------------------------------------------*/

echo $this->Admin->ShowPageHeaderStart($current_crumb, 'cus-lock');

echo '<p>'.__('Key:').' <strong>'. $license_data['licenseKey'] .'</strong> '. $this->Admin->ActionButton('edit','/license/admin_edit/' . $license_data['id'],__('Edit')) .'</p>';
if($license_data['check'] == 'true') {
	echo '<p>'.__('Domain:').' <strong>'. $license_data['params'][0] .'</strong></p>';
	echo '<p>'.__('Updates available until:').' <strong>'. $license_data['params'][1] .'</strong></p>';
} else {	echo '<p>'.__('License is invalid. Check your key.').'</p>';
}

echo $this->Admin->EmptyResults($license_data);

if(!isset($license_data['licenseKey'])) {
	echo $this->Admin->CreateNewLink();
}

echo $this->Admin->ShowPageHeaderEnd();

?>