<?php
// Copyright (c) 2013-2015 Datenstrom, http://datenstrom.se
// This file may be used and distributed under the terms of the public license.

// Google map plugin
class YellowGooglemaps
{
	const Version = "0.5.1";
	var $yellow;			//access to API
	
	// Handle initialisation
	function onLoad($yellow)
	{
		$this->yellow = $yellow;
		$this->yellow->config->setDefault("googlemapsZoom", "15");
		$this->yellow->config->setDefault("googlemapsStyle", "flexible");
	}
	
	// Handle page content parsing of custom block
	function onParseContentBlock($page, $name, $text, $shortcut)
	{
		$output = NULL;
		if($name=="googlemaps" && $shortcut)
		{
			list($address, $zoom, $style, $width, $height) = $this->yellow->toolbox->getTextArgs($text);
			if(empty($zoom)) $zoom = $this->yellow->config->get("googlemapsZoom");
			if(empty($style)) $style = $this->yellow->config->get("googlemapsStyle");
			$language = $page->get("language");
			$output = "<div class=\"".htmlspecialchars($style)."\">";
			$output .= "<iframe src=\"https://maps.google.com/maps?q=".rawurlencode($address)."&amp;ie=UTF8&amp;t=m&amp;z=".rawurlencode($zoom)."&amp;hl=$language&amp;iwloc=near&amp;num=1&amp;output=embed\" frameborder=\"0\"";
			if($width && $height) $output .= " width=\"".htmlspecialchars($width)."\" height=\"".htmlspecialchars($height)."\"";
			$output .= "></iframe></div>";
		}
		return $output;
	}
}

$yellow->plugins->register("googlemaps", "YellowGooglemaps", YellowGooglemaps::Version);
?>