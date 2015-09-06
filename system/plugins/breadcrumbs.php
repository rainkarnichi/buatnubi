<?php
// Copyright (c) 2013-2015 Datenstrom, http://datenstrom.se
// This file may be used and distributed under the terms of the public license.

// Breadcrumbs plugin
class YellowBreadcrumbs
{
	const Version = "0.5.2";
	var $yellow;			//access to API
	
	// Handle initialisation
	function onLoad($yellow)
	{
		$this->yellow = $yellow;
		$this->yellow->config->setDefault("breadcrumbsSeparator", ">");
		$this->yellow->config->setDefault("breadcrumbsStyle", "breadcrumbs");
	}
	
	// Handle page content parsing of custom block
	function onParseContentBlock($page, $name, $text, $shortcut)
	{
		$output = NULL;
		if($name=="breadcrumbs" && $shortcut)
		{
			list($separator, $style) = $this->yellow->toolbox->getTextArgs($text);
			if(empty($separator)) $separator = $this->yellow->config->get("breadcrumbsSeparator");
			if(empty($style)) $style = $this->yellow->config->get("breadcrumbsStyle");
			$pages = $this->yellow->pages->path($page->getLocation(), true);
			$page->setLastModified($pages->getModified());
			$output = "<div class=\"".htmlspecialchars($style)."\">";
			$currentPage = $page;
			foreach($pages as $page)
			{
				$output .= "<a href=\"".$page->getLocation()."\">".$page->getHtml("titleNavigation")."</a>";
				if($page->getLocation() != $currentPage->getLocation()) $output .= " ".htmlspecialchars($separator)." ";
			}
			$output .= "</div>\n";
		}
		return $output;
	}
	
	// Handle page extra HTML data
	function onExtra($name)
	{
		return $this->onParseContentBlock($this->yellow->page, $name, "", true);
	}
}

$yellow->plugins->register("breadcrumbs", "YellowBreadcrumbs", YellowBreadcrumbs::Version);
?>