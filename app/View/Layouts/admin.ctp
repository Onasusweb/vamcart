<?php
/* -----------------------------------------------------------------------------------------
   VamShop - http://vamshop.com
   -----------------------------------------------------------------------------------------
   Copyright (c) 2014 VamSoft Ltd.
   License - http://vamshop.com/license.html
   ---------------------------------------------------------------------------------------*/
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
										'font-awesome.min.css',
										'bootstrap/bootstrap.css',
										'bootstrap/cus-icons.css',
										'bootstrap/bootstrap-responsive.css',
										'dynatree/ui.dynatree.css',
											), null, array('inline' => true)); ?>

<?php echo $this->Html->script(array(
											'jquery/jquery.min.js',
											'bootstrap/bootstrap.min.js'
												),
											array('inline' => true)); ?>
<?php echo $this->Html->scriptBlock('
//<![CDATA[
$(document).ready(function () {$(\'[rel=tooltip],input[data-title]\').tooltip();
});
//]]>
', array('allowCache'=>false,'safe'=>false,'inline'=>true)); ?>			
								
<?php echo $scripts_for_layout; ?>
</head>

<body>

<div class="container-fluid">

<!-- Header -->
<div class="row-fluid">
	<div class="span4">
		<?php echo $this->Html->link($this->Html->image('admin/logo.png', array('alt' => __('VamShop',true))), '/admin/admin_top/', array('escape'=>false));?>  
	</div>
	
	<div class="span8">

		<ul class="inline text-right">
			<li><h3><a href="http://apps.<?php echo __('vamshop.com'); ?>/" target="_blank" title="<?php echo __('Apps'); ?>"><i class="fa fa-th"></i></a></h3></li>
			<li><h3><a href="http://support.<?php echo __('vamshop.com'); ?>/" target="_blank" title="<?php echo __('Support'); ?>"><i class="fa fa-question"></i></a></h3></li>
			<li><h3><?php echo $this->Html->link('<i class="fa fa-shopping-cart"></i>', '/', array('escape'=> false, 'target' => 'blank', 'title' => __('Launch Site'))); ?></h3></li>
			<li><h3><?php echo $this->Html->link('<i class="fa fa-sign-out"></i>', '/users/admin_logout/', array('escape'=> false, 'target' => 'blank', 'title' => __('Logout'))); ?></h3></li>
		</ul>
  
	</div>
	
</div>
<!-- /Header -->

	<div class="row-fluid">
		<div class="span12">
<?php
if(isset($navigation)) { 
?>
	<div class="navbar">
	  <div class="navbar-inner">
	    <div class="container">
			
			<?php echo $this->admin->DrawMenu($navigation); ?>

			<?php 
			echo $this->form->create('Search', array('class' => 'navbar-search pull-right', 'action' => '/search/admin_global_search/', 'url' => '/search/admin_global_search/'));
			echo $this->form->input('Search.term',array('class' => 'input-medium', 'label' => false,'value' => __('Global Record Search',true),"onblur" => "if(this.value=='') this.value=this.defaultValue;","onfocus" => "if(this.value==this.defaultValue) this.value='';"));
			//echo $this->form->submit( __('Submit', true));
			echo $this->form->end();
			?>   

	    </div>
	  </div><!-- /navbar-inner -->
	</div><!-- /navbar -->
<?php
} 
?>
<?php
if(isset($current_crumb)) { 
?>
<div class="breadCrumbs">
<?php
echo $this->admin->GenerateBreadcrumbs($navigation, $current_crumb);
?>
</div>
<?php
} 
?>

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