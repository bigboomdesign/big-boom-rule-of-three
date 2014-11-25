<?php
/**
 * Plugin Name: Rule Of Three
 * Description: Uses shortcode to insert a responsive, custom-defined rule of 3 into a page or post
 * Version: 0.0.1
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
		add_action("admin_enqueue_scripts", "ro3_admin_scripts");
		function ro3_admin_scripts(){
			# make sure we only load our scripts on the ro3_settings page
			$screen = get_current_screen();
			if($screen->id == "settings_page_ro3_settings"){
				# js
				wp_enqueue_media();	
				wp_enqueue_script("media-single-js", ro3_url('js/media-single.js'), array('media-views', 'jquery'));
				# css
				wp_enqueue_style('ro3-admin-css', ro3_url('css/admin_comp.css'));
			}
		}	
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
			add_options_page('Rule of Three Settings', 'Rule of Three', 'manage_options', 'ro3_settings', 'ro3_do_settings_page');
		}
		function ro3_do_settings_page(){ RO3_Options::settings_page(); }
	} #end: admin routines
	
	# Front end routines
	else{
		# scripts and styles
		add_action('wp_enqueue_scripts', 'ro3_enqueue_scripts');
		function ro3_enqueue_scripts(){
			wp_enqueue_style('ro3-css', ro3_url('/css/comp.css'));
			wp_enqueue_script('ro3-js', ro3_url('/js/rule-of-three.js'), array('jquery'));
		}
		# Main container shortcode
		function ro3_container($atts, $content = ""){ return RO3::do_container(); }
		add_shortcode("rule-of-three", "ro3_container");
	} # end: front end routines
} while(0); 
#end main routine

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