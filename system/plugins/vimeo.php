<?php
// Copyright (c) 2013-2015 Datenstrom, http://datenstrom.se
// This file may be used and distributed under the terms of the public license.

// Vimeo plugin
class YellowVimeo
{
	const Version = "0.5.1";
	var $yellow;			//access to API
	
	// Handle initialisation
	function onLoad($yellow)
	{
		$this->yellow = $yellow;
		$this->yellow->config->setDefault("vimeoStyle", "flexible");
	}
	
	// Handle page content parsing of custom block
	function onParseContentBlock($page, $name, $text, $shortcut)
	{
		$output = NULL;
		if($name=="vimeo" && $shortcut)
		{
			list($id, $style, $width, $height) = $this->yellow->toolbox->getTextArgs($text);
			if(empty($style)) $style = $this->yellow->config->get("vimeoStyle");
			$output = "<div class=\"".htmlspecialchars($style)."\">";
			$output .= "<iframe src=\"https://player.vimeo.com/video/".rawurlencode($id)."\" frameborder=\"0\" allowfullscreen";
			if($width && $height) $output .= " width=\"".htmlspecialchars($width)."\" height=\"".htmlspecialchars($height)."\"";
			$output .= "></iframe></div>";
		}
		return $output;
	}
}

$yellow->plugins->register("vimeo", "YellowVimeo", YellowVimeo::Version);
?>