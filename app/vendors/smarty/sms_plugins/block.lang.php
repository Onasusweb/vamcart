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

function smarty_block_lang($params, $content, &$smarty)
{
	
    if (is_null($content)) 
	{
        return;
    }
	
		// Start caching
		$cache_name = 'sms_lang_' .  $_SESSION['Customer']['language_id'] . '_' . $content;
		$output = Cache::read($cache_name);
		if($output === false)
		{
			ob_start();	
	
	
	loadModel('DefinedLanguage');
	$DefinedLanguage =& new DefinedLanguage();

	$language_content = $DefinedLanguage->find(array('language_id' => $_SESSION['Customer']['language_id'], 'key' => $content));
	if(empty($language_content['DefinedLanguage']['value']))
		//$output = "Error! Empty language value for: " . $content;
		$lang_output = $content;		
	else
		$lang_output = $language_content['DefinedLanguage']['value'];
		
		echo $lang_output;
		
			// End cache
			$output = @ob_get_contents();
			ob_end_clean();
			Cache::write($cache_name, $output);
		}
		echo $output;
	
}

function smarty_help_function_lang() {
	?>
	<h3>What does this do?</h3>
	<p>Outputs the correct language value specified by the key between the brackets.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template like: <code>{lang}message_language_key{/lang}</code></p>
	<p>Make sure you define the language key in the admin area.</p>
	<?php
}

function smarty_about_function_lang() {
	?>
	<p>Author: Kevin Grandon&lt;kevingrandon@hotmail.com&gt;</p>
	<p>Version: 0.1</p>
	<p>
	Change History:<br/>
	None
	</p>
	<?php
}
?>
