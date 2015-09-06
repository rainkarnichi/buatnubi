<?php
// Copyright (c) 2013-2015 Datenstrom, http://datenstrom.se
// This file may be used and distributed under the terms of the public license.

// Soundcloud plugin
class YellowSoundcloud
{
	const Version = "0.5.1";
	var $yellow;			//access to API
	
	// Handle initialisation
	function onLoad($yellow)
	{
		$this->yellow = $yellow;
		$this->yellow->config->setDefault("soundcloudStyle", "soundcloud");
	}
	
	// Handle page content parsing of custom block
	function onParseContentBlock($page, $name, $text, $shortcut)
	{
		$output = NULL;
		if($name=="soundcloud" && $shortcut)
		{
			list($id, $style, $width, $height) = $this->yellow->toolbox->getTextArgs($text);
			if(empty($style)) $style = $this->yellow->config->get("soundcloudStyle");
			if(empty($width)) $width = "100%";
			if(empty($height)) $height = "166";
			$output = "<div class=\"".htmlspecialchars($style)."\">";
			$output .= "<iframe src=\"https://w.soundcloud.com/player/?url=https%3A%2F%2Fapi.soundcloud.com%2Ftracks%2F".rawurlencode($id)."\" frameborder=\"0\"";
			$output .= " width=\"".htmlspecialchars($width)."\" height=\"".htmlspecialchars($height)."\"";
			$output .= "></iframe></div>";
		}
		return $output;
	}
}

$yellow->plugins->register("soundcloud", "YellowSoundcloud", YellowSoundcloud::Version);
?>