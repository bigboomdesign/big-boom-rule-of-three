<?php
/**
 * Plugin Name: Rule Of Three
 * Description: Uses shortcode to insert a responsive, custom-defined rule of 3 into a page or post
 * Version: 0.3.0
 * Author: Big Boom Design
 * Author URI: http://bigboomdesign.com
 */

# Main routine
if(ro3_should_load()) do{
	## Load main class
	require_once ro3_dir("lib/class-ro3.php");

	# Admin Routines
	if(is_admin()){
		# Scripts and styles
		add_action("admin_enqueue_scripts", array('RO3', 'admin_enqueue'));
	
		# define sections and fields for options page
		add_action('admin_init', 'ro3_init');
		function ro3_init(){ RO3_Options::register_settings(); }
		
		## plugin settings description
		function ro3_main_section_text(){
		?>
			<p>Define the blocks here that will show up when you use this shortcode:</p>
			<p><kbd>[rule-of-three]</kbd></p>
		<?php
		}
		## do field display
		function ro3_settings_field_callback($setting){ RO3_Options::do_settings_field($setting); }
		## validate fields when saved
		function ro3_options_validate($input) { return $input; }
		## plugin options page
		add_action('admin_menu', 'ro3_settings_page');
		function ro3_settings_page() {
			add_menu_page('Rule of Three Settings', 'Rule of Three', 'manage_options', 'ro3_settings', 'ro3_do_settings_page');
		}
		function ro3_do_settings_page(){ RO3_Options::settings_page(); }
	} #end: admin routines
	
	# Front end routines
	else{
		# scripts and styles
		add_action('wp_enqueue_scripts', array('RO3','enqueue'));
		
		# Main container shortcode
		add_shortcode("rule-of-three", array('RO3', 'container_html'));
		
	} # end: front end routines
} while(0); 
#end main routine

# AJAX
add_action('wp_ajax_ro3_get_posts_for_type', 'ro3_get_posts_for_type');
function ro3_get_posts_for_type(){
	RO3::select_post_for_type($_POST['post_type'], $_POST['section']);
	die();
}
add_action('wp_ajax_get_block_data_for_post', 'get_block_data_for_post');
function get_block_data_for_post(){
	$post = get_post($_POST['post_id']);
	$out = array(
		'post_title' => $post->post_title,
		'thumb' => wp_get_attachment_url( get_post_thumbnail_id($post->ID) ),
		'url' => get_permalink($post->ID),
	);
	$excerpt = $post->post_excerpt ? $post->post_excerpt : substr($post->post_content,0,250);
	$out['post_excerpt'] = $excerpt;
	echo json_encode($out);
	die();
}

###
# Helper functions
###
# paths
function ro3_url($s){ return plugins_url($s, __FILE__); }
function ro3_dir($s){ return plugin_dir_path(__FILE__) . $s; }
# we don't want to load if we don't have to
function ro3_should_load(){
	return true;
}