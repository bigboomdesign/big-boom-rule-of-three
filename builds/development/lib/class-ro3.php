<?php
class RO3{
	static $classes = array("ro3-options");
	# this object hold the options for front end views
	static $options;

	/** 
	* Back end
	**/
	static function select_post_for_type($post_type, $section = ''){
		$args = array(
			'post_type' => $post_type,
			'posts_per_page' => -1,
			'orderby' => 'post_title',
			'order' => 'ASC'
		);
		$posts = get_posts($args);
		$choices = array(
			array('value' => '', 'label' => '- Select -')
		);
		foreach($posts as $post){
			$choice = array('value' => $post->ID, 'label' => $post->post_title);
			$choices[] = $choice;
		}
		RO3_Options::do_settings_field(
			array('name' => 'post_id'.$section, 'type' => 'select', 'label' => $post->post_title,
				'data' => array('section' => $section),
				'choices' => $choices,
				'description' => '<b>Note:</b> Below, you have the option to alter the title, description, image, etc. for the post you choose.  Changes to the post do not auto update here.'
			)
		);
		?></div>
		<?php	
	}

	/** 
	* Front End
	**/
	# display the container on front end
	static function do_container(){
		extract(RO3_Options::$options);
		# do nothing if we don't have at least 1 title set
		if(!$title1 && !$title2 && !$title3) return;
		
		# number of columns we'll have
		$n = 3;
		# string to return
		$s .= "<div id='ro3-container' class='" . $style . "-container'>";
		for($i = 1; $i <= $n; $i++){
			# make sure we have a title set before starting the block
			$titlename = 'title'.$i;
			if($title = $$titlename){
				$s .= "<div class='ro3-block {$i} " 
					. ( $style == 'drop-shadow' ? "shadow-container " : '')
					. ( $style == 'nested' ? 'nested-container' : '')
					. "'>";
					# image
					$imgname = 'image'.$i;
					if($image = $$imgname){
						# if link exists, wrap it around the image
						$linkname = 'link'.$i;
						if($link = $$linkname) $s .= "<a class='ro3-link " . (($style == "none") ? "" : $style ) . "' href='{$link}'>";
							$s .= "<img src='{$image}'/>";
						if($link) $s .= "</a>";
					}
					# header (with link if it's set)
					$s .= "<div class='ro3-description'>";
					$s .= "<h2>" . ( $link ? "<a href='{$link}'>" : "" ) .$title . ($link ? "</a>" : "") . "</h2>";
					# description
					$descname = 'description'.$i;
					if($description = $$descname)
						$s .= "<p>$description</p>";
					$s .= "</div>"; # .ro3-description
				$s .= "</div>"; # .ro3-block
			}
		}
		$s .= "</div>"; // #ro3-container
		return $s;	
	}
	
	/**
	* Helper Functions
	**/
	# require a file, checking first if it exists
	static function req_file($path){ if(file_exists($path)) require_once $path; }
	# return a permalink-friendly version of a string
	static function clean_str_for_url( $sIn ){
		if( $sIn == "" ) return "";
		$sOut = trim( strtolower( $sIn ) );
		$sOut = preg_replace( "/\s\s+/" , " " , $sOut );					
		$sOut = preg_replace( "/[^a-zA-Z0-9 -]/" , "",$sOut );	
		$sOut = preg_replace( "/--+/" , "-",$sOut );
		$sOut = preg_replace( "/ +- +/" , "-",$sOut );
		$sOut = preg_replace( "/\s\s+/" , " " , $sOut );	
		$sOut = preg_replace( "/\s/" , "-" , $sOut );
		$sOut = preg_replace( "/--+/" , "-" , $sOut );
		$nWord_length = strlen( $sOut );
		if( $sOut[ $nWord_length - 1 ] == "-" ) { $sOut = substr( $sOut , 0 , $nWord_length - 1 ); } 
		return $sOut;
	}
	static function clean_str_for_field($sIn){
		if( $sIn == "" ) return "";
		$sOut = trim( strtolower( $sIn ) );
		$sOut = preg_replace( "/\s\s+/" , " " , $sOut );					
		$sOut = preg_replace( "/[^a-zA-Z0-9 -_]/" , "",$sOut );	
		$sOut = preg_replace( "/--+/" , "-",$sOut );
		$sOut = preg_replace( "/__+/" , "_",$sOut );
		$sOut = preg_replace( "/ +- +/" , "-",$sOut );
		$sOut = preg_replace( "/ +_ +/" , "_",$sOut );
		$sOut = preg_replace( "/\s\s+/" , " " , $sOut );	
		$sOut = preg_replace( "/\s/" , "-" , $sOut );
		$sOut = preg_replace( "/--+/" , "-" , $sOut );
		$sOut = preg_replace( "/__+/" , "_" , $sOut );
		$nWord_length = strlen( $sOut );
		if( $sOut[ $nWord_length - 1 ] == "-" || $sOut[ $nWord_length - 1 ] == "_" ) { $sOut = substr( $sOut , 0 , $nWord_length - 1 ); } 
		return $sOut;		
	}
	# Generate a label, value, etc. for any given choice 
	## input can be a string or array and a full, formatted array will be returned
	## If $field is a string we assume the string is 
	## if $field is an array we assume that at least a label exists
	## optionally, the parent field's name can be passed for better labelling
	static function get_field_array( $field, $parent_name = ''){
		$id = $parent_name ? $parent_name.'_' : '';
		if(!is_array($field)){
			$id .= self::clean_str_for_field($field);
			$out = array();
			$out['type'] = 'text';
			$out['label'] = $field;
			$out['value'] = $id;
			$out['id'] .= $id;
			$out['name'] = $id;
		}
		elseif(is_array($field)){
			# do nothing if we don't have a label
			if(!array_key_exists('label', $field)) return $field;
			
			$id .= array_key_exists('name', $field) ? $field['name'] : self::clean_str_for_field($field['label']);
			$out = $field;
			if(!array_key_exists('id', $out)) $out['id'] = $id;
			if(!array_key_exists('name', $out)) $out['name'] = $id;
			# make sure all choices are arrays
			if(array_key_exists('choices', $field)){
				$out['choices'] = self::get_choice_array($field);
			}
		}
		return $out;
	}
	# Get array of choices for a setting field
	## This allows choices to be set as strings or arrays with detailed properties, 
	## so that either way our options display function will have the data it needs
	static function get_choice_array($setting){
		extract($setting);
		if(!isset($choices)) return;
		$out = array();
		if(!is_array($choices)){
			$out[] = array(
				'id' => $name.'_'.self::clean_str_for_field($choices),
				'label' => $choices, 
				'value' => self::clean_str_for_field($choices)
			);
		}
		else{
			foreach($choices as $choice){
				if(!is_array($choice)){
					$out[] = array(
						'label' => $choice,
						'id' => $name . '_' . self::clean_str_for_field($choice),
						'value' => self::clean_str_for_field($choice)
					);
				}
				else{
					# if choice is already an array, we need to check for missing data
					if(!array_key_exists('id', $choice)) $choice['id'] = $name.'_'.self::clean_str_for_field($choice['label']);
					if(!array_key_exists('value', $choice)) $choice['value'] = $name.'_'.self::clean_str_for_field($choice['label']);
					$out[] = $choice;
				}
			}
		}
		return $out;
	}	
} # end class RO3

# require files for plugin
foreach(RO3::$classes as $class){ RO3::req_file(ro3_dir("lib/class-{$class}.php")); }