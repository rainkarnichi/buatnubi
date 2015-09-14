<?php
// Copyright (c) 2013-2015 Datenstrom, http://datenstrom.se
// This file may be used and distributed under the terms of the public license.

// Preview plugin
class YellowPreview
{
	const Version = "0.5.3";
	var $yellow;			//access to API

	// Handle initialisation
	function onLoad($yellow)
	{
		$this->yellow = $yellow;
		$this->yellow->config->setDefault("previewStyle", "preview");
	}
	
	// Handle page content parsing of custom block
	function onParseContentBlock($page, $name, $text, $shortcut)
	{
		$output = NULL;
		if($name=="preview" && $shortcut)
		{
			list($location, $style, $size) = $this->yellow->toolbox->getTextArgs($text);
			if(empty($location)) $location = $page->location;
			if(empty($style)) $style = $this->yellow->config->get("previewStyle");
			if(empty($size)) $size = "100%";
			$content = $this->yellow->pages->find($location);
			$pages = $content ? $content->getChildren() : $this->yellow->pages->clean();
			if($content && $this->yellow->plugins->isExisting("image"))
			{
				$page->setLastModified($pages->getModified());
				$output = "<ul class=\"".htmlspecialchars($style)."\">\n";
				foreach($pages as $page)
				{
					$fileName = $this->yellow->config->get("imageDir").basename($page->location).".jpg";
					list($src, $width, $height) = $this->yellow->plugins->get("image")->getImageInfo($fileName, $size, $size);
					$title = $page->get("titlePreview"); if(empty($title)) $title = $page->get("title");
					$output .= "<li><a href=\"".$page->getLocation()."\">";
					$output .= "<img src=\"".htmlspecialchars($src)."\" width=\"".htmlspecialchars($width)."\" height=\"".
						htmlspecialchars($height)."\" alt=\"".htmlspecialchars($title)."\" title=\"".
						htmlspecialchars($title)."\" /></a><br />";
					$output .= "<a href=\"".$page->getLocation()."\">".htmlspecialchars($title)."</a>";
					$output .= "</li>\n";
				}
				$output .= "</ul>";
			} else {
				$page->error(500, "Preview '$location' does not exist!");
			}
		}
		return $output;
	}

	// Handle page extra HTML data
	function onExtra($name)
	{
		return $this->onParseContentBlock($this->yellow->page, $name, "", true);
	}
}

$yellow->plugins->register("preview", "YellowPreview", YellowPreview::Version);
?>