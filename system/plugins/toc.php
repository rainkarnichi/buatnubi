<?php
// Copyright (c) 2013-2015 Datenstrom, http://datenstrom.se
// This file may be used and distributed under the terms of the public license.

// Table of contents plugin
class YellowToc
{
	const Version = "0.5.2";
	var $yellow;			//access to API
	
	// Handle initialisation
	function onLoad($yellow)
	{
		$this->yellow = $yellow;
	}
	
	// Handle page content parsing
	function onParseContentText($page, $text)
	{
		$callback = function($matches) use ($page)
		{
			$output = "<ul class=\"toc\">\n";
			$major = $minor = 0;
			preg_match_all("/<h(\d) id=\"([^\"]+)\">(.*?)<\/h\d>/i", $page->getPage("main")->parserData, $matches, PREG_SET_ORDER);
			foreach($matches as $match)
			{
				switch($match[1])
				{
					case 2:	++$major; $minor = 0;
							$output .= "<li><a href=\"#$match[2]\">$major. $match[3]</a></li>\n";
							break;
					case 3:	++$minor;
							$output .= "<li><a href=\"#$match[2]\">$major.$minor. $match[3]</a></li>\n";
							break;
				}
			}
			$output .= "</ul>\n";
			return $output;
		};
		return preg_replace_callback("/<p>\[toc\]<\/p>\n/i", $callback, $text);
	}
}

$yellow->plugins->register("toc", "YellowToc", YellowToc::Version);
?>