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
		$setting = RO3::get_field_array($setting);
		extract(RO3_Options::$options);
		# call one of several functions based on what type of field we have
		switch($setting['type']){
			case "textarea":
				self::textarea_field($setting);
			break;
			case "checkbox":
				self::checkbox_field($setting);
			break;
			case 'select':
				self::select_field($setting);
			break;
			case "single-image":
				self::image_field($setting);
			break;
			case "radio":
				self::radio_field($setting);
			break;
			default: self::text_field($setting);
		}
		# field description
		if(array_key_exists('description', $setting)){
		?>
			<p class='description'><?php echo $setting['description']; ?></p>
		<?php
		}
		# preview for different RO3 styles
		if($setting['name'] == 'style'){
		?>
			<div id="ro3-preview">		
			<?php
				foreach($setting['choices'] as $a){
					$choice = $a['value'];
				?>
				<div 
					id="preview-<?php echo $choice; ?>"
					style="display: <?php echo ($style==$choice)?'block':'none'; ?>"
				>
					<?php if('nested' == $choice){
					?>
						<p><em>Note: this choice works best with a short description</em></p>
					<?php
					}
					?>
					<img src="<?php echo ro3_url('/images/'. $choice .'.jpg'); ?>" />
				</div>			
				<?php
				}
				?>
			</div>
		<?php
		}
		# Subcontent for 'Existing Content' radio buttons
		if(strpos($setting['name'], 'post_type') === 0){
			# get the section number 
			$section = $setting['section'];
			
			# 'Clear' link
		?>
			<a href="javascript:void(0)" class='clear-post-type-select' data-section="<?php echo $section; ?>">Clear</a>
		<?php	
			# Post select area for specific post type
		?>
			<div 
				id="post-select-<?php echo $section; ?>"
				class='post-select' 
				style="display: <?php echo (!empty(RO3_Options::$options['post_type'.$section])) ? 'block' : 'none';?>;"
			><?php RO3::select_post_for_type(RO3_Options::$options['post_type'.$section], $section); ?>
			</div>
		<?php
		}
		# Child fields (for conditional logic)
		if(array_key_exists('choices', $setting)){
			$choices = RO3::get_choice_array($setting);
			# keep track of which fields we've displayed (in case two choices have the same child)
			$aKids = array();

			# Loop through choices and display and children
			foreach($choices as $choice){
				if(array_key_exists('children', $choice)){
					foreach($choice['children'] as $child_setting){
						# add this child to the array of completed child settings
						if(!in_array($child_setting['name'], $aKids)){
							$aKids[] = $child_setting['name'];
							# note the child field div is hidden unless the parent option is selected
						?><div 
							id="child_field_<?php echo $child_setting['name']; ?>"
							style="display: <?php echo (RO3_Options::$options[$setting['name']] == $choice['value']) ? 'block' : 'none'?>"
						>
							<h4><?php echo $child_setting['label']; ?></h4>
							<?php self::do_settings_field($child_setting); ?>
						</div>
						<?php
						}
					}
				} # end: choice has children
			} # end: foreach: choices
		} # end: setting has choices		
	}
	# Text field
	static function text_field($setting){
		extract($setting);
		?><input 
			id="<?php echo $name; ?>" 
			name="ro3_options[<?php echo $name; ?>]" 
			class="regular-text <?php if(isset($class)) echo $class; ?>" 
			type='text' 
			value="<?php echo self::$options[$name]; ?>" 
		/>
		<?php	
	}
	# Textarea field
	static function textarea_field($setting){
		extract($setting);
		?><textarea id="<?php echo $name; ?>" name="ro3_options[<?php echo $name; ?>]" 
			cols='40' rows='7' class='<?php echo $class ? $class : ''; ?>'><?php echo self::$options[$name]; ?></textarea>
		<?php
	}
	# Checkbox field
	static function checkbox_field($setting){
		extract($setting);
		foreach($choices as $choice){
		?><label class='checkbox' for="<?php echo $choice['id']; ?>">
			<input 
				type='checkbox'
				id="<?php echo $choice['id']; ?>"
				name="ro3_options[<?php echo $choice['id']; ?>]"
				value="<?php echo $choice['value']; ?>"
				class="<?php if(array_key_exists('class', $setting)) echo $setting['class']; ?>"
				<?php checked(true, array_key_exists($choice['id'], self::$options)); ?>						
			/>&nbsp;<?php echo $choice['label']; ?> &nbsp; &nbsp;
		</label>
		<?php
		}
	}
	# <select> dropdown field
	static function select_field($setting){
		extract($setting);
	?><select 
		id="<?php echo $name; ?>"
		name="ro3_options[<?php echo $name; ?>]"
		<?php if(isset($class)) echo  "class='{$class}'"; ?>
		<?php
			if(array_key_exists('data', $setting)){
				foreach($setting['data'] as $k => $v){
					echo " data-{$k}='{$v}'";
				}
			}
		?>
	>
		<?php 
			# if we are given a string for $choices (i.e. single choice)
			if(is_string($choices)) {
				?><option 
					value="<?php echo RO3::clean_str_for_field($choices); ?>"
					<?php selected(RO3_Options::$options[$name], RO3::clean_str_for_field($choice) ); ?>
				><?php echo $choices; ?>
				</option>
			<?php
			}
			# if $choices is an array
			elseif(is_array($choices)){
				foreach($choices as $choice){
					# if $choice is a string
					if(is_string($choice)){
						$label = $choice;
						$value = RO3::clean_str_for_field($choice);
					}
					# if $choice is an array
					elseif(is_array($choice)){
						$label = $choice['label'];
						$value = isset($choice['value']) ? $choice['value'] : RO3::clean_str_for_field($choice['label']);
					}
				?>
					<option 
						value="<?php echo $value; ?>"
						<?php if(isset(RO3_Options::$options[$name])) selected(RO3_Options::$options[$name], $value ); ?>					
					><?php echo $label; ?></option>
				<?php
				} # end foreach: $choices
			} # endif: $choices is an array
		?>
		
	</select><?php
	}
	# Radio Button field
	static function radio_field($setting){
		extract($setting);
		$choices = RO3::get_choice_array($setting);
		foreach($choices as $choice){
				$label = $choice['label']; 
				$value = $choice['value'];
			?><label class='radio' for="<?php echo $choice['id']; ?>">
				<input type="radio" id="<?php echo $choice['id']; ?>" 
				name="ro3_options[<?php echo $name; ?>]" 
				value="<?php echo $value; ?>"
				class="<?php if(array_key_exists('class', $setting)) echo $setting['class']; ?>"
				<?php
				# add data attributes
				if(array_key_exists('data', $choice)){
					foreach($choice['data'] as $k => $v){
						echo " data-{$k}='{$v}'";
					}
				}
				# add checked property if we need to
				if(isset(self::$options[$name])) checked($value, self::$options[$name]); ?>
				autocomplete='off'
			/>&nbsp;<?php echo $label; ?></label>&nbsp;&nbsp;
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
			class='regular-text text-upload <?php echo $class ? $class : ''; ?>'
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
		## get choices for custom post types
		$pt_args = array(
			'_builtin' => false,
			'public' => true
		);
		$pts = get_post_types($pt_args, 'objects');
		# loop through settings and register
		foreach(RO3_Options::$settings as $setting){
			# add choices for custom post types
			foreach($pts as $pt){
				# make sure we have published posts for this post type
				if(!get_posts(
					array(
						'post_type' => $pt->name, 'post_status' => 'publish'
					)
				)) continue;
				if(strpos($setting['name'],'post_type') === 0){
					$setting['choices'][] = array(
						'label' => $pt->labels->name, 
						'value' => $pt->name,
						'data' => array('section' => $setting['section'])
					);
				}
			}
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
} # end class: RO3_Options

# Initialize static variables

## generate all settings for backend

### settings that will come in 3's (one for each block)
$n = 3;
$options = array(
	array('name' => 'post_type', 'type' => 'radio', 'label' => 'Use Existing Content',
		'class' => 'ro3-post-type-select',
		'choices' => array(
			array('label' => 'Post', 'value' => 'post'),
			array('label' => 'Page', 'value' => 'page'),
		),
	),
	array('name' => 'image', 'type' => 'single-image', 'label' => 'Image'),
	array('name' => 'fa_icon', 'type' => 'text', 'label' => 'Font Awesome Icon',
		'description' => 
			'Enter the name of the icon (Ex: <code style="font-style: normal; font-weight: bold;">coffee</code>, <code style="font-style: normal; font-weight: bold;">bed</code>, etc.)<br />
			See the list of <a target="_blank" href="http://fortawesome.github.io/Font-Awesome/icons/">available options</a>.',
		'class' => 'fa_icon'
	),
	array('name' => 'title', 'type' => 'text', 'label' => 'Title'),
	array('name' => 'description', 'type' => 'textarea', 'label' => 'Description'),
	array('name' => 'link', 'type' => 'text', 'label' => 'Link')
);
RO3_Options::$settings = array();
for($i = 1; $i <= $n; $i++){
	foreach($options as $option){
		// set the section (ro3_1, ro3_2, ro3_3) and name (title1, description1, etc )
		$option['section'] = $i;

		# the class shouldn't have an index
		$option['class'] = $option['name'] . (array_key_exists('class', $option) ? ' ' . $option['class'] : '');

		# the option name should have an index
		$option['name'] = $option['name'] . $i;
		if(array_key_exists('choices', $option)){
			foreach($option['choices'] as $k => $v){
				$option['choices'][$k]['data'] = array('section' => $i);
			}
		}
		RO3_Options::$settings[] = $option;
	}
}
### other settings
RO3_Options::$settings[] = array(
	'name'=>'style', 'type'=>'radio', 'label' => 'Style', 'section' => 'main',
	'choices' => array(
		array('value'=>'none', 'label'=>'None'),
		array('value' => 'drop-shadow', 'label' => 'Drop Shadow'),
		array('value' => 'nested', 'label' => 'Nested'),
		array('value' => 'circle', 'label' => 'Circle'),
		array('value' => 'bar', 'label' => 'Bar',),
		array('value' => 'fa-icon', 'label' => 'Font Awesome'),		
	)
);
RO3_Options::$settings[] = array(
	'name' => 'main_color', 'type' => 'text', 'class' => 'color-picker', 'label' => 'Main Color', 'section' => 'main'
);
RO3_Options::$settings[] = array(
	'name' => 'read_more', 'type' => 'checkbox', 'label' => 'Show "Read More"', 'section' => 'main',
	'choices' => 'Yes'
);
RO3_Options::$settings[] = array(
	'name' => 'fa_icon_size', 'type' => 'text', 'label' => 'Font Awesome Icon Size', 'section' => 'main',
	'description' => 
		'Please enter a valid CSS value like <code>24px</code> or <code>2em</code>.<br />
		Note that <code>em\'s</code> may generate a different preview size here than on the front end.',
);
## get saved options
RO3_Options::$options = get_option('ro3_options');

## if no options are set, make sure at least all keys are present in CPTD_Options::$options
if(!RO3_Options::$options){

	# make sure all the keys exist in our options
	foreach(RO3_Options::$settings as $setting){
		RO3_Options::$options[$setting['name']] = '';
	}
}

# set the defaults that we want to implement
if(!RO3_Options::$options['style']) RO3_Options::$options['style'] = 'none';
if(!RO3_Options::$options['main_color']) RO3_Options::$options['main_color'] = '#333';
if(!RO3_Options::$options['fa_icon_size']) RO3_Options::$options['fa_icon_size'] = '2em';
/*
if(!isset(RO3_Options::$options['style'])) RO3_Options::$options['style'] = 'none';
if(!isset(RO3_Options::$options['main_color'])) RO3_Options::$options['main_color'] = '#333';
if(!isset(RO3_Options::$options['fa_icon_size'])) RO3_Options::$options['fa_icon_size'] = '2em';
*/