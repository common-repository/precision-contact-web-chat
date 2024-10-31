<?php
/**
* Plugin Name: Precision Contact Web Chat
* Plugin URI:  https://messagemybiz.com/
* Description: Communicate with your customers the way they want to with text messaging and web chats! Text enable your existing business telephone number(s). Web chat enable your website with our drop in module. Receive customer texts and web chat messages on your mobile phone or computer browser.
* Version: 1.0.0
* Author: Red Phoenix, LLC
* Author URI: https://www.red-phoenix.com/
* License:     GPLv2 or later
* License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
* This program is free software; you can redistribute it and/or modify it under the terms of the GNU
* General Public License version 2, as published by the Free Software Foundation. You may NOT assume
* that you can use any other version of the GPL.
*
* This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
* even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
**/

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Invalid request.' );
}

add_action('wp_enqueue_scripts','precision_contact_enqueue_scripts_and_styles');

function precision_contact_enqueue_scripts_and_styles() {
	wp_enqueue_style('precision_contact_bootstrap', plugin_dir_url( __FILE__ ) . 'css/bootstrap.min.css' );
    wp_enqueue_style('precision_contact_pcwebchat', 'https://pcwebchat.messagemybiz.com/pc/pcwebchat.min.css');   
    wp_enqueue_style( 'precision_contact_google_fonts', 'https://fonts.googleapis.com/css2?family=Raleway:wght@700&family=Roboto:wght@400;500;700&family=Alata&display=swap', false ); 
    
    wp_enqueue_script( 'precision_contact_bootstrap_js',  plugin_dir_url( __FILE__ ) . 'js/bootstrap.min.js', array('jquery'),null,true );
    wp_enqueue_script( 'precision_contact_pcutils_js', 'https://pcwebchat.messagemybiz.com/pc/pcutils.js', array('jquery'),null,true );
    wp_enqueue_script( 'precision_contact_pcwebchat_js', 'https://pcwebchat.messagemybiz.com/pc/pcwebchat.min.js', array('jquery'),null,true );    
}

/* What to do when the plugin is activated? */
register_activation_hook(__FILE__,'precision_contact_web_chat_install');
function precision_contact_web_chat_install() {
/* Create a new database field */
add_option("precision_contact_web_chat_key", '', 'yes');
}

/* What to do when the plugin is deactivated? */
register_uninstall_hook( __FILE__, 'precision_contact_web_chat_remove' );
function precision_contact_web_chat_remove() {
/* Delete the database field */
delete_option('precision_contact_web_chat_key');
delete_plugins(['precision_contact_web_chat']);
}

add_action('admin_menu', 'precision_contact_web_chat_admin_menu');
function precision_contact_web_chat_admin_menu() {
add_options_page('Precision Contact Web Chat', 'Precision Contact Web Chat', 'manage_options',
'web-chat-plugin', 'precision_contact_web_chat_plugin_admin_options_page');
}

add_action('wp_footer', 'precision_contact_insert_chat_div');
function precision_contact_insert_chat_div() {
	$key = get_option('precision_contact_web_chat_key');
  if(is_front_page() && strlen($key)==36){
	  echo "<div id='pcChatHook'></div>";
	  echo "<script>var WEB_CHAT_KEY = '" . $key . "';</script>";
  }
}
?>
<?php
function precision_contact_web_chat_plugin_admin_options_page() {
?>
<div class="wrap">
<?php screen_icon(); ?>
<style>
		.error{
			color:red !important;
		}
		
		.notVisible{
			display:none;
		}
	</style>
<script>
	jQuery( document ).ready(function($) {
    	$("#settingsForm").submit(function( event ) {
			$("#precision_contact_web_chat_key").removeClass("error");
			var id = document.forms[0]["precision_contact_web_chat_key"].value;
			var pattern = /^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i;
			if (pattern.test(id) === true) {
			} else {
				$("#precision_contact_web_chat_key").addClass("error");
				$("#warning").removeClass("notVisible");
				event.preventDefault();
			}
	});
});
</script>
	
<h2>Precision Contact Web Chat</h2>
<form id="settingsForm" method="post" action="options.php">
<p>
<?php wp_nonce_field('update-options'); ?>
WEB_CHAT_KEY: <input style="width:300px;" name="precision_contact_web_chat_key" type="text" id="precision_contact_web_chat_key"
value="<?php echo get_option('precision_contact_web_chat_key'); ?>" />
<div id="warning" class="error notVisible">
	This is not a valid web chat key.
	</div>	
<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="precision_contact_web_chat_key" /><!-- value is a comma-separated list -->
</p>
<p>
<input type="submit" value="Save Changes" />
</p>
</form>

</div>
<?php
}
?>