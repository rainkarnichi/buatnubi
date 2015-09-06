<?php
// Copyright (c) 2013-2015 Datenstrom, http://datenstrom.se
// This file may be used and distributed under the terms of the public license.

// Youtube plugin
class YellowYoutube
{
	const Version = "0.5.1";
	var $yellow;			//access to API
	
	// Handle initialisation
	function onLoad($yellow)
	{
		$this->yellow = $yellow;
		$this->yellow->config->setDefault("youtubeStyle", "flexible");
	}
	
	// Handle page content parsing of custom block
	function onParseContentBlock($page, $name, $text, $shortcut)
	{
		$output = NULL;
		if($name=="youtube" && $shortcut)
		{
			list($id, $style, $width, $height) = $this->yellow->toolbox->getTextArgs($text);
			if(empty($style)) $style = $this->yellow->config->get("youtubeStyle");
			$output = "<div class=\"".htmlspecialchars($style)."\">";
			$output .= "<iframe src=\"https://www.youtube.com/embed/".rawurlencode($id)."\" frameborder=\"0\" allowfullscreen";
			if($width && $height) $output .= " width=\"".htmlspecialchars($width)."\" height=\"".htmlspecialchars($height)."\"";
			$output .= "></iframe></div>";
		}
		return $output;
	}
}

$yellow->plugins->register("youtube", "YellowYoutube", YellowYoutube::Version);
?>