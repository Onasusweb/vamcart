<?php
/* -----------------------------------------------------------------------------------------
   VamShop - http://vamshop.com
   -----------------------------------------------------------------------------------------
   Copyright (c) 2014 VamSoft Ltd.
   License - http://vamshop.com/license.html
   ---------------------------------------------------------------------------------------*/

$this->Html->script(array(
	'modified.js',
	'focus-first-input.js',
	'codemirror/lib/codemirror.js',
	'codemirror/mode/javascript/javascript.js',
	'codemirror/mode/css/css.js',
	'codemirror/mode/xml/xml.js',
	'codemirror/mode/htmlmixed/htmlmixed.js'
), array('inline' => false));

$this->Html->css(array(
	'codemirror/codemirror',
), null, array('inline' => false));

	echo $this->Admin->ShowPageHeaderStart($current_crumb, 'cus-application-edit');

	echo $this->Form->create('AttributeTemplate', array('id' => 'contentform', 'action' => '/admin_edit/save/'));		
	echo $this->Form->input('AttributeTemplate.id',array('type' => 'hidden'));  
        
        
        echo '<ul id="myTab" class="nav nav-tabs">';
        echo $this->Admin->CreateTab('templates',__('Templates'),'cus-application-edit');
        echo $this->Admin->CreateTab('settings',__('Settings'),'cus-wrench');
        echo '</ul>';
               
        echo $this->Admin->StartTabs('sub-tabs');
        echo $this->Admin->StartTabContent('templates');
        
        echo '<br />';
        
            echo $this->Form->input('AttributeTemplate.name',array('label' => __('Name')));

            echo $this->Form->input('AttributeTemplate.template_filter', 
                                            array('type' => 'textarea',
                                                    'id' => 'code_template_filter',
                                                 'label' => __('Template for filter')
                                    ));
            echo $this->Form->input('AttributeTemplate.template_editor', 
                                            array('type' => 'textarea',
                                                    'id' => 'code_template_editor',
                                                 'label' => __('Template for editor')
                                    ));
            echo $this->Form->input('AttributeTemplate.template_catalog', 
                                            array('type' => 'textarea',
                                                    'id' => 'code_template_catalog',
                                                 'label' => __('Template for catalog')
                                    ));
            echo $this->Form->input('AttributeTemplate.template_product', 
                                            array('type' => 'textarea',
                                                    'id' => 'code_template_product',
                                                 'label' => __('Template for product')
                                    ));
            echo $this->Form->input('AttributeTemplate.template_compare', 
                                            array('type' => 'textarea',
                                                    'id' => 'code_template_compare',
                                                 'label' => __('Template for compare')
                                    ));

        echo '<br />';

            echo $this->Admin->EndTabContent();
        
        echo $this->Admin->StartTabContent('settings');

        echo '<br />';

            echo $this->Form->input('DefaulValue.dig_value',array('label' => __('Default value') . ' » (dig_value)' ,'type' => 'checkbox'));
            echo $this->Form->input('DefaulValue.max_value',array('label' => __('Default value') . ' » (max_value)' ,'type' => 'checkbox'));
            echo $this->Form->input('DefaulValue.min_value',array('label' => __('Default value') . ' » (min_value)' ,'type' => 'checkbox'));
            echo $this->Form->input('DefaulValue.like_value',array('label' => __('Default value') . ' » (like_value)' ,'type' => 'checkbox'));            
            echo $this->Form->input('DefaulValue.list_value',array('label' => __('Default value') . ' » (list_value)' ,'type' => 'checkbox'));            
            echo $this->Form->input('DefaulValue.checked_list',array('label' => __('Default value') . ' » (checked_list)' ,'type' => 'checkbox'));            
            echo $this->Form->input('DefaulValue.any_value',array('label' => __('Default value') . ' » (any_value)' ,'type' => 'checkbox'));            

        echo '<br />';

        echo $this->Admin->EndTabContent();
        echo $this->Admin->EndTabs();
        
	echo $this->Admin->formButton(__('Submit'), 'cus-tick', array('class' => 'btn', 'type' => 'submit', 'name' => 'submit')) . $this->Admin->formButton(__('Apply'), 'cus-disk', array('class' => 'btn', 'type' => 'submit', 'name' => 'apply')) . $this->Admin->formButton(__('Cancel'), 'cus-cancel', array('class' => 'btn', 'type' => 'submit', 'name' => 'cancelbutton'));
	echo '<div class="clear"></div>';
	echo $this->Form->end();
	echo $this->Admin->ShowPageHeaderEnd(); 
	
	echo $this->Html->scriptBlock('
        CodeMirror.fromTextArea(document.getElementById("code_template_filter"), {
          mode: "text/html",
          lineNumbers: true,
          lineWrapping: true
        });
        CodeMirror.fromTextArea(document.getElementById("code_template_editor"), {
          mode: "text/html",
          lineNumbers: true,
          lineWrapping: true
        });
        CodeMirror.fromTextArea(document.getElementById("code_template_catalog"), {
          mode: "text/html",
          lineNumbers: true,
          lineWrapping: true
        });
        CodeMirror.fromTextArea(document.getElementById("code_template_product"), {
          mode: "text/html",
          lineNumbers: true,
          lineWrapping: true
        });
        CodeMirror.fromTextArea(document.getElementById("code_template_compare"), {
          mode: "text/html",
          lineNumbers: true,
          lineWrapping: true
        });
        ', array('allowCache'=>false,'safe'=>false,'inline'=>true));	
	
?>