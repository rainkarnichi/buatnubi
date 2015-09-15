<?php
// Copyright (c) 2013-2015 Datenstrom, http://datenstrom.se
// This file may be used and distributed under the terms of the public license.

// Fotorama plugin
class YellowFotorama
{
	const Version = "0.5.2";
	var $yellow;			//access to API
	
	// Handle initialisation
	function onLoad($yellow)
	{
		$this->yellow = $yellow;
		$this->yellow->config->setDefault("fotoramaCdn", "/cdn/fotorama/");
		// Original source "https://cdnjs.cloudflare.com/ajax/libs/fotorama/4.6.4/"
		$this->yellow->config->setDefault("fotoramaStyle", "fotorama");
		$this->yellow->config->setDefault("fotoramaNav", "dots");
		$this->yellow->config->setDefault("fotoramaAutoplay", "0");
		if(!$this->yellow->config->isExisting("jqueryCdn"))
		{
		   $this->yellow->config->setDefault("jqueryCdn", "/cdn/jquery/");
		   // Original source "https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.1/"
		}
	}
	
	// Handle page content parsing of custom block
	function onParseContentBlock($page, $name, $text, $shortcut)
	{
		$output = NULL;
		if($name=="fotorama" && $shortcut)
		{
			list($pattern, $style, $nav, $autoplay) = $this->yellow->toolbox->getTextArgs($text);
			if(empty($style)) $style = $this->yellow->config->get("fotoramaStyle");
			if(empty($nav)) $nav = $this->yellow->config->get("fotoramaNav");
			if(empty($autoplay)) $autoplay = $this->yellow->config->get("fotoramaAutoplay");
			if(empty($pattern))
			{
				$files = $page->getFiles(true);
			} else {
				$images = $this->yellow->config->get("imageDir");
				$files =  $this->yellow->files->index(true, true)->match("#$images$pattern#");
			}
			if(count($files))
			{
				$page->setLastModified($files->getModified());
				$output = "<div class=\"".htmlspecialchars($style)."\" data-nav=\"".htmlspecialchars($nav)."\" data-autoplay=\"".
					htmlspecialchars($autoplay)."\" data-loop=\"true\">\n";
				foreach($files as $file)
				{
					list($width, $height) = $this->yellow->toolbox->detectImageInfo($file->fileName);
					$output .= "<img src=\"".htmlspecialchars($file->getLocation())."\" width=\"".htmlspecialchars($width)."\" height=\"".
						htmlspecialchars($height)."\" alt=\"".basename($file->getLocation())."\" title=\"".
						basename($file->getLocation())."\" />\n";
					
				}
				$output .= "</div>";
			} else {
				$page->error(500, "Fotorama '$pattern' does not exist!");
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
			$fotoramaCdn = $this->yellow->config->get("fotoramaCdn");
			$jqueryCdn = $this->yellow->config->get("jqueryCdn");
			$output = "<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"{$fotoramaCdn}fotorama.css\" />\n";
			$output .= "<script type=\"text/javascript\" src=\"{$jqueryCdn}jquery.min.js\"></script>\n";
			$output .= "<script type=\"text/javascript\" src=\"{$fotoramaCdn}fotorama.min.js\"></script>\n";
		}
		return $output;
	}
}

$yellow->plugins->register("fotorama", "YellowFotorama", YellowFotorama::Version);
?>