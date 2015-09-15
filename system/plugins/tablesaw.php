<?php
// Copyright (c) 2015 Jef Lippiatt, http://nogginfuel.com
// This file may be used and distributed under the terms of the MIT license.
// Tablesaw Stackonly plugin (not all Tablesaw functionality)
class YellowTablesaw
{
	const Version = "0.1.1";
	var $yellow;			//access to API
	
	// Handle initialisation
	function onLoad($yellow)
	{
		$this->yellow = $yellow;
		$this->yellow->config->setDefault("tablesawPluginCdn", "https://raw.githubusercontent.com/filamentgroup/tablesaw/master/dist/stackonly/");
	}
	
	// Handle page extra HTML data
	function onExtra($name)
	{
		$output = NULL;
		if($name == "footer")
		{
			$pluginCdn = $this->yellow->config->get("tablesawPluginCdn");
			$output .= "<script type=\"text/javascript\" src=\"{$pluginCdn}tablesaw.stackonly.js\"></script>\n";
		}
		return $output;
	}
}
$yellow->plugins->register("tablesaw", "YellowTablesaw", YellowTablesaw::Version);
?>