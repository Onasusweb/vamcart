<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */


?>
<!DOCTYPE html>
<html>
<head>
<?php echo $this->Html->charset(); ?>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title><?php echo $title_for_layout; ?></title>
<?php echo $this->Html->css(array(
										'admin',
										'normalize.css',
										'bootstrap/bootstrap.css',
										'bootstrap/cus-icons.css',
										'bootstrap/bootstrap-responsive.css',
											), null, array('inline' => true)); ?>

<?php echo $this->Html->script(array(
											'jquery/jquery.min.js',
											'bootstrap/bootstrap.min.js'
												),
											array('inline' => true)); ?>
<?php echo $scripts_for_layout; ?>
</head>

<body>

<div class="container-fluid">

<!-- Header -->
<div class="row-fluid">
	<div class="span4">
		<?php echo $this->Html->image('admin/logo.png', array('alt' => __('VamShop',true)))?>
	</div>
</div>
<!-- /Header -->

	<div class="row-fluid">
		<div class="span12">

<!-- Content -->
<div id="wrapper">
<div id="content">

<?php if($this->Session->check('Message.flash')) echo $this->Session->flash(); ?>

<?php echo $this->fetch('content'); ?>

</div>
</div>
<!-- /Content -->

<!-- Footer -->
<div id="footer">
<p>
<a href="http://<?php echo __('vamshop.com'); ?>/"><?php echo __('Powered by'); ?></a> <a href="http://<?php echo __('vamshop.com'); ?>/"><?php echo __('VamShop'); ?></a>
</p>
</div>
<!-- /Footer -->

		</div>
	</div>
	
</div>

</body>
</html>