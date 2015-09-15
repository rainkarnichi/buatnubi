<?php
// Copyright (c) 2013-2015 Datenstrom, http://datenstrom.se
// This file may be used and distributed under the terms of the public license.

// Gallery plugin
class YellowGallery
{
	const Version = "0.5.2";
	var $yellow;			//access to API

	// Handle initialisation
	function onLoad($yellow)
	{
		$this->yellow = $yellow;
		$this->yellow->config->setDefault("galleryPhotoswipeCdn", "https://cdnjs.cloudflare.com/ajax/libs/photoswipe/4.1.0/");
		$this->yellow->config->setDefault("galleryStyle", "photoswipe");
	}
	
	// Handle page content parsing of custom block
	function onParseContentBlock($page, $name, $text, $shortcut)
	{
		$output = NULL;
		if($name=="gallery" && $shortcut)
		{
			list($pattern, $style, $size) = $this->yellow->toolbox->getTextArgs($text);
			if(empty($style)) $style = $this->yellow->config->get("galleryStyle");
			if(empty($size)) $size = "100%";
			if(empty($pattern))
			{
				$files = $page->getFiles(true);
			} else {
				$images = $this->yellow->config->get("imageDir");
				$files =  $this->yellow->files->index(true, true)->match("#$images$pattern#");
			}
			if(count($files) && $this->yellow->plugins->isExisting("image"))
			{
				$page->setLastModified($files->getModified());
				$output = "<div class=\"".htmlspecialchars($style)."\" data-fullscreenel=\"false\" data-shareel=\"false\">\n";
				foreach($files as $file)
				{
					list($widthInput, $heightInput) = $this->yellow->toolbox->detectImageInfo($file->fileName);
					list($src, $width, $height) = $this->yellow->plugins->get("image")->getImageInfo($file->fileName, $size, $size);
					$output .= "<a href=\"".$file->getLocation()."\" data-size=\"{$widthInput}x{$heightInput}\">";
					$output .= "<img src=\"".htmlspecialchars($src)."\" width=\"".htmlspecialchars($width)."\" height=\"".
						htmlspecialchars($height)."\" alt=\"".basename($file->getLocation())."\" title=\"".
						basename($file->getLocation())."\" />";
					$output .= "</a>\n";
				}
				$output .= "</div>";
			} else {
				$page->error(500, "Gallery '$pattern' does not exist!");
			}
		}
		return $output;
	}

	// Handle page extra HTML data
	function onExtra($name)
	{
		$output = NULL;
		if($name == "header")
		{
			$photoswipeCdn = $this->yellow->config->get("galleryPhotoswipeCdn");
			$pluginLocation = $this->yellow->config->get("serverBase").$this->yellow->config->get("pluginLocation");
			$output = "<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"{$photoswipeCdn}photoswipe.css\" />\n";
			$output .= "<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"{$photoswipeCdn}default-skin/default-skin.css\" />\n";
			$output .= "<script type=\"text/javascript\" src=\"{$photoswipeCdn}photoswipe.min.js\"></script>\n";
			$output .= "<script type=\"text/javascript\" src=\"{$photoswipeCdn}photoswipe-ui-default.min.js\"></script>\n";
			$output .= "<script type=\"text/javascript\" src=\"{$pluginLocation}gallery.js\"></script>\n";
		}
		return $output;
	}
}

$yellow->plugins->register("gallery", "YellowGallery", YellowGallery::Version);
?>