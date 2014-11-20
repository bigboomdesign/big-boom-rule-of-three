<?php
class RO3_Options{
	# Static variables are set after class definition
	## available settings
	static $settings;
	## saved options
	static $options = array();
	
	var $sections;
	var $fields;
	
	# Display field input
	static function do_settings_field($setting){
		# call one of several functions based on what type of field we have
		switch($setting['type']){
			case "textarea":
				self::textarea_field($setting);
			break;
			case "single-image":
				self::image_field($setting);
			break;
			case "radio":
				self::radio_field($setting);
			break;
			default: self::text_field($setting);
		}
	}
	# Text field
	static function text_field($setting){
		extract($setting);
		?><input id="<?php echo $name; ?>" name="ro3_options[<?php echo $name; ?>]" class='regular-text' type='text' value="<?php echo self::$options[$name]; ?>" />
		<?php	
	}
	# Textarea field
	static function textarea_field($setting){
		extract($setting);
		?><textarea id="<?php echo $name; ?>" name="ro3_options[<?php echo $name; ?>]" cols='40' rows='7'><?php echo self::$options[$name]; ?></textarea>
		<?php
	}
	# Radio Button field
	static function radio_field($setting){
		extract($setting);
		foreach($choices as $choice){
			?><label class='radio' for="<?php echo $name.'-'.$choice['value']; ?>"><input type="radio" id="<?php echo $name.'-'.$choice['value']; ?>" name="ro3_options[<?php echo $name; ?>]" value="<?php echo $choice['value']; ?>" <?php checked($choice['value'], self::$options[$name]); ?>/><?php echo $choice['label']; ?></label>
			<?php
		}
	}
	# Image field
	static function image_field($setting){
		# this will set $name for the field
		extract($setting);
		# current value for the field
		$value = self::$options[$name];		
		?><input 
			type='text'
			id="<?php echo $name; ?>" 
			class='regular-text text-upload'
			name="ro3_options[<?php echo $name; ?>]"
			value="<?php if($value) echo esc_url( $value ); ?>"
		/>		
		<input 
			id="media-button-<?php echo $name; ?>" type='button'
			value='Choose/Upload image'
			class=	'button button-primary open-media-button single'
		/>
		<div id="<?php echo $name; ?>-thumb-preview" class="ro3-thumb-preview">
			<?php if($value){ ?><img src="<?php echo $value; ?>" /><?php } ?>
		</div>
		<?php
	}
	# Register settings
	static function register_settings(){
		register_setting( 'ro3_options', 'ro3_options', 'ro3_options_validate' );
		add_settings_section('ro3_main', '', 'ro3_main_section_text', 'ro3_settings');
		
		# set up section for each rule component
		for($i = 1; $i <= 3; $i++){
			add_settings_section('ro3_'.$i, 'Block ' . $i, '', 'ro3_settings');
		}
		# add fields
		foreach(RO3_Options::$settings as $setting){
			add_settings_field($setting['name'], $setting['label'], 'ro3_settings_field_callback', 'ro3_settings', 'ro3_'.$setting['section'], $setting);
		}	
	}
	# Do settings page
	static function settings_page(){
		?><div>
			<h2>Rule of Three Settings</h2>
			<form action="options.php" method="post">
			<?php settings_fields('ro3_options'); ?>
			<?php do_settings_sections('ro3_settings'); ?>
			<?php submit_button(); ?>
			</form>
		</div><?php
	}
}
# Initialize static variables
## generate all settings for backend

### settings that will come in 3's (one for each block)
$n = 3;
$options = array(
	array('name' => 'image', 'type' => 'single-image', 'label' => 'Image'),
	array('name' => 'title', 'type' => 'text', 'label' => 'Title'),
	array('name' => 'description', 'type' => 'textarea', 'label' => 'Description'),
	array('name' => 'link', 'type' => 'text', 'label' => 'Link')
);
ro3_options::$settings = array();
for($i = 1; $i <= $n; $i++){
	foreach($options as $option){
		// set the section (ro3_1, ro3_2, ro3_3) and name (title1, description1, etc )
		$option['section'] = $i;
		$option['name'] = $option['name'] . $i;
		ro3_options::$settings[] = $option;
	}
}
### other settings
ro3_options::$settings[] = array(
	'name'=>'style', 'type'=>'radio', 'label' => 'Style', 'section' => 'main',
	'choices' => array(
		array('value'=>'none', 'label'=>'None'),
		array('value' => 'drop-shadow', 'label' => 'Drop Shadow')
	)
);
## get saved options
ro3_options::$options = get_option('ro3_options');