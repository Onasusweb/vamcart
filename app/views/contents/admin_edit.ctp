<?php
/* -----------------------------------------------------------------------------------------
   VaM Shop
   http://vamshop.com
   http://vamshop.ru
   Copyright 2009 VaM Shop
   -----------------------------------------------------------------------------------------
   Portions Copyright:
   Copyright 2007 by Kevin Grandon (kevingrandon@hotmail.com)
   -----------------------------------------------------------------------------------------
   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/
?>
<?php
	echo $javascript->link('jquery/jquery.min', false);
	echo $javascript->link('jquery/plugins/jquery-ui.min', false);
	echo $javascript->link('tabs', false);
	echo $html->css('jquery/plugins/ui/css/smoothness/jquery-ui','','', false);
	echo $javascript->link('tabs', false);
	echo $javascript->link('tiny_mce/tiny_mce', false);
	echo $javascript->link('tiny_mce/plugins/tinybrowser/tb_tinymce.js.php', false);
?>
<?php echo $javascript->codeBlock('
tinyMCE.init({
	mode : "textareas",
	editor_deselector : "notinymce",
	theme : "advanced",
	language : "ru",
	paste_create_paragraphs : false,
	paste_create_linebreaks : false,
	paste_use_dialog : true,
	convert_urls : false,

	plugins : "safari,typograf,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,imagemanager,filemanager",

	file_browser_callback : "tinyBrowser",

	spellchecker_languages : "+Russian=ru,English=en",
	spellchecker_rpc_url : "'.BASE.'/js/tiny_mce/plugins/spellchecker/rpc_proxy.php",

	// Theme options
	theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
	theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
	theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
	theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,typograf,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true

});

function toggleHTMLEditor(id) {
	if (!tinyMCE.get(id))
		tinyMCE.execCommand("mceAddControl", false, id);
	else
		tinyMCE.execCommand("mceRemoveControl", false, id);
}
', array('allowCache'=>false,'safe'=>false,'inline'=>false)); ?>
<?php echo $javascript->codeBlock('
	$(document).ready(function(){

		$("select#ContentContentTypeId").change(function () {
			$("div#content_type_fields").load("'. BASE . '/contents/admin_edit_type/"+$("select#ContentContentTypeId").val());
		})

	});
', array('allowCache'=>false,'safe'=>false,'inline'=>false)); ?>
<?php
	
	// Set default div styling for template_requured container divs
	if(($data['ContentType']['template_type_id'] > 0)||(empty($data)))
		$tpl_req_style = "block";
	else
		$tpl_req_style = "none";

	echo $form->create('Content', array('id' => 'contentform', 'name' => 'contentform','enctype' => 'multipart/form-data', 'action' => '/contents/admin_edit/'.$data['Content']['id'], 'url' => '/contents/admin_edit/'.$data['Content']['id']));
	
	echo $admin->StartTabs();
			echo '<ul>';
			echo $admin->CreateTab('main',__('Main', true));
			echo $admin->CreateTab('view_images',__('View Images', true));	
			echo $admin->CreateTab('upload_images',__('Upload Images', true));			
			echo $admin->CreateTab('options',__('Options', true));			
			echo '</ul>';
	
	echo $admin->StartTabContent('main');
		echo $form->inputs(array(
				'fieldset' => __('Categories & Products',true),
				'Content.id' => array(
					'type' => 'hidden',
					'value' => $data['Content']['id']
	               ),
				'Content.order' => array(
					'type' => 'hidden',
					'value' => $data['Content']['order']
	               )
				   ));
	$parent_language_bug_fix = __('Parent', true);
	
	echo '<div class="input"><label>' . $parent_language_bug_fix . '</label>' . $form->select('Content.parent_id', $parents, $data['Content']['parent_id'], $attributes = array('label' => $parent_language_bug_fix), $showEmpty = false) . '</div>';

   		echo $form->inputs(array(
   					'Content.content_type_id' => array(
						'type' => 'select',
				   	'label' => __('Content Type', true),
						'options' => $content_types,
						'selected' => (!isset($data['Content']['content_type_id'])? 2 : $data['Content']['content_type_id'])
	               )));

		echo '<div id="content_type_fields">';
			echo $this->requestAction( '/contents/admin_edit_type/' . (!isset($data['Content']['content_type_id'])? 2 : $data['Content']['content_type_id']) . '/' . $data['Content']['id'], array('return'));	
		echo '</div>';
	
	echo '<div class="template_required" id="template_required_template_picker" style="display:' . $tpl_req_style . ';">';
	
	  	echo $form->inputs(array('Content.template_id' => array(
						'type' => 'select',
				   	'label' => __('Template', true),
						'options' => $templates,
						'selected' => $data['Content']['template_id']
	            	  )));
	echo '</div>';	
	
	// Loop through the languages and display a name and descrition for each
	foreach($languages AS $language)
	{
		$language_key = $language['Language']['id'];
		
		echo $form->inputs(array('ContentDescription]['.$language['Language']['id'].'][name.' . $language['Language']['id'] => array(
				   		'label' => $admin->ShowFlag($language['Language']) . '&nbsp;' . sprintf(__('Title (%s) ', true),__($language['Language']['name'], true)),
						'value' => $data['ContentDescription'][$language_key]['name']
	            	  )));																								
	
		echo '<div id="template_required_' . $language['Language']['id'] . '" class="template_required" style="display:' . $tpl_req_style . ';">';
			echo $form->inputs(array('ContentDescription]['.$language['Language']['id'].'][description.' . $language['Language']['id'] => array(
				   		'label' => $admin->ShowFlag($language['Language']) . '&nbsp;' . sprintf(__('Description (%s) ', true),__($language['Language']['name'], true)),
						'type' => 'textarea',
						'class' => 'pagesmalltextarea',						
						'value' => $data['ContentDescription'][$language_key]['description']
	            	  )));
		echo '</div>';						  

		echo $form->inputs(array('ContentDescription]['.$language['Language']['id'].'][meta_title.' . $language['Language']['id'] => array(
				   		'label' => $admin->ShowFlag($language['Language']) . '&nbsp;' . sprintf(__('Meta Title (%s) ', true),__($language['Language']['name'], true)),
						'value' => $data['ContentDescription'][$language_key]['meta_title']
	            	  )));																								

		echo $form->inputs(array('ContentDescription]['.$language['Language']['id'].'][meta_description.' . $language['Language']['id'] => array(
				   		'label' => $admin->ShowFlag($language['Language']) . '&nbsp;' . sprintf(__('Meta Description (%s) ', true),__($language['Language']['name'], true)),
						'value' => $data['ContentDescription'][$language_key]['meta_description']
	            	  )));																								

		echo $form->inputs(array('ContentDescription]['.$language['Language']['id'].'][meta_keywords.' . $language['Language']['id'] => array(
				   		'label' => $admin->ShowFlag($language['Language']) . '&nbsp;' . sprintf(__('Meta Keywords (%s) ', true),__($language['Language']['name'], true)),
						'value' => $data['ContentDescription'][$language_key]['meta_keywords']
	            	  )));																								

	}
		
		
	echo $admin->EndTabContent();

	echo $admin->StartTabContent('view_images');
		echo '<div id="content_images_holder">';		
		echo $this->requestAction('/images/admin_view_content_images/' . (!isset($data['Content']['id'])? 0 : $data['Content']['id']), array('return'));	
		echo '</div>';
		
	echo $admin->EndTabContent();


	echo $admin->StartTabContent('upload_images');
	
	if((isset($data['Content']['id'])) && ($data['Content']['id'] > 0))
	{
		echo '<p>' . __('Press \'Upload Images\' and choose images from your computer to upload. Select as many files as you would like. Images will upload right after you select them.', true) . '</p>';
		echo '<div class="help tip"><p>' . __('TIP: Hold the \'control\' button to select more than one image.', true) . '</p></div>';		
		?>
		<?php echo $javascript->link('swfupload/swfupload', false);  ?>
		<?php echo $javascript->link('swfupload/callbacks', false);  ?>
	
<?php echo $javascript->codeBlock('
			var swfu;

			window.onload = function() {

				swfu = new SWFUpload({
				upload_script : "' . BASE . '/contents/upload_images/' . $data['Content']['id'] . '",
				target : "SWFUploadTarget",
				flash_path : "' . BASE . '/js/swfupload/swfupload.swf?session_name()=' . session_id() . '&"+Math.random(),
				allowed_filesize : 3072,	// 30 MB
				allowed_filetypes : "*.*",
				allowed_filetypes_description : "All files...",
				browse_link_innerhtml : "' . __('Browse Images', true) . '",
				upload_link_innerhtml : "' . __('Upload Images', true) . '",
				browse_link_class : "swfuploadbtn browsebtn",
				upload_link_class : "swfuploadbtn uploadbtn",
				flash_loaded_callback : "swfu.flashLoaded",
				upload_file_queued_callback : "fileQueued",
				upload_file_start_callback : "uploadFileStart",
				upload_progress_callback : "uploadProgress",
				upload_file_complete_callback : "uploadFileComplete",
				upload_file_cancel_callback : "uploadFileCancelled",
				upload_queue_complete_callback : "uploadQueueComplete",
				upload_error_callback : "uploadError",
				upload_cancel_callback : "uploadCancel",
				auto_upload : true			
				});
			};
', array('allowCache'=>false,'safe'=>false,'inline'=>false)); ?>
		
		<div id="SWFUploadFileListingFiles"></div>
		<div class="clear"></div>
		<h4 id="queueinfo"></h4>		
		<div id="SWFUploadTarget"></div>		
		<?php
	
	}
	else
	{
		echo '<p>' . __('This is a new product. Please press apply before uploading images.', true)	 . '</p>';
	}
			
	echo $admin->EndTabContent();

	echo $admin->StartTabContent('options');
			echo $form->inputs(array(
				'fieldset' => __('Content Details', true),
                'Content.alias' => array(
			   		'label' => __('Alias', true),				   
					'value' => $data['Content']['alias']
                ),
				'Content.head_data' => array(
					'label' => __('Head Data', true),
					'type' => 'textarea',
					'class' => 'pagesmalltextarea',
					'value' => $data['Content']['head_data']
	             ),				
			    'Content.active' => array(
					'type' => 'checkbox',
			   		'label' => __('Active', true),
					'value' => '1',
					'class' => 'checkbox_group',
					'checked' => $active_checked
                ),
			    'Content.show_in_menu' => array(
					'type' => 'checkbox',
			   		'label' => __('Show in menu', true),
					'value' => '1',
					'class' => 'checkbox_group',
					'checked' => $menu_checked
                )
		));
	echo $admin->EndTabContent();

	echo $admin->EndTabs();
	
	echo $form->submit( __('Submit', true), array('name' => 'submitbutton', 'id' => 'submitbutton')) . $form->submit( __('Apply', true), array('name' => 'applybutton')) . $form->submit( __('Cancel', true), array('name' => 'cancelbutton'));
	echo '<div class="clear"></div>';
	echo $form->end();
	?>
</div>