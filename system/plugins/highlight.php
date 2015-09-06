<?php
// Copyright (c) 2013-2015 Datenstrom, http://datenstrom.se
// This file may be used and distributed under the terms of the public license.

// Highlight plugin
class YellowHighlight
{
	const Version = "0.5.5";
	var $yellow;			//access to API
	
	// Handle initialisation
	function onLoad($yellow)
	{
		$this->yellow = $yellow;
		$this->yellow->config->setDefault("highlightClass", "highlight");
		$this->yellow->config->setDefault("highlightStylesheetDefault", "0");
		$this->yellow->config->setDefault("highlightLineNumber", "0");
	}
	
	// Handle page content parsing of custom block
	function onParseContentBlock($page, $name, $text, $shortcut)
	{
		$output = NULL;
		if(!empty($name) && !$shortcut)
		{
			list($language, $lineNumber, $class, $id) = $this->getHighlightInfo($name);
			if(!empty($language))
			{
				$geshi = new GeSHi(trim($text), $language);
				$geshi->set_language_path($this->yellow->config->get("pluginDir")."/highlight/");
				$geshi->set_header_type(GESHI_HEADER_PRE_TABLE);
				$geshi->set_overall_class($class);
				$geshi->set_overall_id($id);
				$geshi->enable_line_numbers($lineNumber ? GESHI_NORMAL_LINE_NUMBERS : GESHI_NO_LINE_NUMBERS);
				$geshi->start_line_numbers_at($lineNumber);
				$geshi->enable_classes(true);
				$geshi->enable_keyword_links(false);
				$output = $geshi->parse_code();
				$output = preg_replace("#<pre(.*?)>(.+?)</pre>#s", "<pre$1><code>$2</code></pre>", $output);
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
			if(!$this->yellow->config->get("highlightStylesheetDefault"))
			{
				$locationStylesheet = $this->yellow->config->get("serverBase").$this->yellow->config->get("pluginLocation")."highlight.css";
				$fileNameStylesheet = $this->yellow->config->get("pluginDir")."highlight.css";
				if(is_file($fileNameStylesheet)) $output = "<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"$locationStylesheet\" />\n";
			} else {
				$geshi = new GeSHi();
				$geshi->set_language_path($this->yellow->config->get("pluginDir")."/highlight/");
				foreach($geshi->get_supported_languages() as $language)
				{
					if($language == "geshi") continue;
					$geshi->set_language($language);
					$output .= $geshi->get_stylesheet(false);
				}
				$output = "<style type=\"text/css\">\n$output</style>";
			}
		}
		return $output;
	}
	
	// Return highlight info, split up name
	function getHighlightInfo($name)
	{
		$class = $this->yellow->config->get("highlightClass");
		foreach(explode(' ', $name) as $token)
		{
			if(empty($language) && preg_match("/^[\w\:]+$/", $token))
			{
			   list($language, $lineNumber) = explode(':', $token);
			   if(is_null($lineNumber)) $lineNumber = $this->yellow->config->get("highlightLineNumber");
			   continue;
			}
			if($token[0] == '.') $class = $class." ".substru($token, 1);
			if($token[0] == '#') $id = substru($token, 1);
		}
		return array($language, $lineNumber, $class, $id);
	}
}
	
require_once("highlight/geshi.php");

$yellow->plugins->register("highlight", "YellowHighlight", YellowHighlight::Version);
?>