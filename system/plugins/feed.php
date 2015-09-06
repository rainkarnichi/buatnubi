<?php
// Copyright (c) 2013-2015 Datenstrom, http://datenstrom.se
// This file may be used and distributed under the terms of the public license.

// Feed plugin
class YellowFeed
{
	const Version = "0.5.5";
	var $yellow;			//access to API
	
	// Handle initialisation
	function onLoad($yellow)
	{
		$this->yellow = $yellow;
		$this->yellow->config->setDefault("feedPaginationLimit", "30");
		$this->yellow->config->setDefault("feedLocation", "/feed/");
		$this->yellow->config->setDefault("feedFileXml", "feed.xml");
	}

	// Handle page parsing
	function onParsePage()
	{
		if($this->yellow->page->get("template") == "feed")
		{
			$pagination = $this->yellow->config->get("contentPagination");
			if($_REQUEST[$pagination] == $this->yellow->config->get("feedFileXml"))
			{
				$pages = $this->yellow->pages->index(false, false);
				$pages->sort("modified", false)->limit($this->yellow->config->get("feedPaginationLimit"));
				$this->yellow->page->setLastModified($pages->getModified());
				$this->yellow->page->setHeader("Content-Type", "application/rss+xml; charset=utf-8");
				$output = "<?xml version=\"1.0\" encoding=\"utf-8\"\077>\r\n";
				$output .= "<rss version=\"2.0\">\r\n";
				$output .= "<channel>\r\n";
				$output .= "<title>".$this->yellow->page->getHtml("titleHeader")."</title>\r\n";
				$output .= "<description>".$this->yellow->page->getHtml("description")."</description>\r\n";
				$output .= "<link>".$this->yellow->page->getUrl()."</link>\r\n";
				$output .= "<language>".$this->yellow->page->getHtml("language")."</language>\r\n";
				foreach($pages as $page)
				{
					$description = $this->yellow->toolbox->createTextDescription($page->getContent(), 1024, false, "<!--more-->");
					$output .= "<item>\r\n";
					$output .= "<title>".$page->getHtml("title")."</title>\r\n";
					$output .= "<link>".$page->getUrl()."</link>\r\n";
					$output .= "<guid isPermaLink=\"false\">".$page->getUrl()."?".$page->getModified()."</guid>\r\n";
					$output .= "<description><![CDATA[".$description."]]></description>\r\n";
					$output .= "</item>\r\n";
				}
				$output .= "</channel>\r\n";
				$output .= "</rss>\r\n";
				$this->yellow->page->setOutput($output);
			} else {
				$pages = $this->yellow->pages->index(false, false);
				$pages->sort("modified");
				$pages->pagination($this->yellow->config->get("feedPaginationLimit"));
				if(!$pages->getPaginationNumber()) $this->yellow->page->error(404);
				$this->yellow->page->setPages($pages);
				$this->yellow->page->setLastModified($pages->getModified());
			}
		}
	}
	
	// Handle page extra HTML data
	function onExtra($name)
	{
		$output = NULL;
		if($name == "header")
		{
			$locationFeed = $this->yellow->config->get("serverBase").$this->yellow->config->get("feedLocation");
			$locationFeed .= $this->yellow->config->get("contentPagination").":".$this->yellow->config->get("feedFileXml");
			$output = "<link rel=\"alternate\" type=\"application/rss+xml\" href=\"$locationFeed\" />\n";
		}
		return $output;
	}
}

$yellow->plugins->register("feed", "YellowFeed", YellowFeed::Version);
?>