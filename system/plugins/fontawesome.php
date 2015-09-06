<?php
// Copyright (c) 2013-2015 Datenstrom, http://datenstrom.se
// This file may be used and distributed under the terms of the public license.

// Fontawesome plugin
class YellowFontawesome
{
	const Version = "0.5.3";
	var $yellow;			//access to API
	
	// Handle initialisation
	function onLoad($yellow)
	{
		$this->yellow = $yellow;
	}
	
	// Handle page extra HTML data
	function onExtra($name)
	{
		$output = NULL;
		if($name == "header")
		{
			$locationStylesheet = $this->yellow->config->get("serverBase").$this->yellow->config->get("pluginLocation")."fontawesome.css";
			$fileNameStylesheet = $this->yellow->config->get("pluginDir")."fontawesome.css";
			if(is_file($fileNameStylesheet)) $output = "<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"$locationStylesheet\" />\n";
		}
		return $output;
	}
}

$yellow->plugins->register("fontawesome", "YellowFontawesome", YellowFontawesome::Version);
?>