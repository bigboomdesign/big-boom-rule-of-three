<?php
class RO3{
	static $classes = array("ro3-options");
	# this object hold the options for front end views
	static $options;

	# display the container on front end
	static function do_container(){
		extract(RO3_Options::$options);
		# do nothing if we don't have at least 1 title set
		if(!$title1 && !$title2 && !$title3) return;
		
		# number of columns we'll have
		$n = 3;
		# string to return
		$s .= "<div id='ro3-container'>";
		for($i = 1; $i <= $n; $i++){
			# make sure we have a title set before starting the block
			$titlename = 'title'.$i;
			if($title = $$titlename){
				$s .= "<div class='ro3-block {$i}'>";
					# image
					$imgname = 'image'.$i;
					if($image = $$imgname){
						# if link exists, wrap it around the image
						$linkname = 'link'.$i;
						if($link = $$linkname) $s .= "<a class='ro3-link' href='{$link}'>";
							$s .= "<img src='{$image}' />";
						if($link) $s .= "</a>";
					}
					# header (with link if it's set)
					$s .= "<h2>" . ( $link ? "<a href='{$link}'>" : "" ) .$title . ($link ? "</a>" : "") . "</h2>";
					# description
					$descname = 'description'.$i;
					if($description = $$descname)
						$s .= "<p>$description</p>";
				$s .= "</div>"; # .ro3-block
			}
		}
		$s .= "</div>";
		return $s;	
	}
	# require a file, checking first if it exists
	static function req_file($path){ if(file_exists($path)) require_once $path; }
} # end class RO3

# require files for plugin
foreach(RO3::$classes as $class){ RO3::req_file(ro3_dir("lib/class-{$class}.php")); }