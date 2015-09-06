<?php
// Copyright (c) 2013-2015 Datenstrom, http://datenstrom.se
// This file may be used and distributed under the terms of the public license.

// Wiki plugin
class YellowWiki
{
	const Version = "0.5.7";
	var $yellow;			//access to API
	
	// Handle initialisation
	function onLoad($yellow)
	{
		$this->yellow = $yellow;
		$this->yellow->config->setDefault("wikiLocation", "/wiki/");
		$this->yellow->config->setDefault("wikiPaginationLimit", "30");
	}
	
	// Handle page content parsing of custom block
	function onParseContentBlock($page, $name, $text, $shortcut)
	{
		$output = NULL;
		if($name=="wikirecent" && $shortcut)
		{
			list($location, $pagesMax) = $this->yellow->toolbox->getTextArgs($text);
			if(empty($location)) $location = $this->yellow->config->get("wikiLocation");
			if(empty($pagesMax)) $pagesMax = 10;			
			$wiki = $this->yellow->pages->find($location);
			$pages = $wiki ? $wiki->getChildren(!$wiki->isVisible())->append($wiki) : $this->yellow->pages->clean();
			$pages->sort("modified", false)->limit($pagesMax);
			$page->setLastModified($pages->getModified());
			if(count($pages))
			{
				$output = "<div class=\"".htmlspecialchars($name)."\">\n";
				$output .= "<ul>\n";
				foreach($pages as $page)
				{
					$output .= "<li><a href=\"".$page->getLocation()."\">".$page->getHtml("titleNavigation")."</a></li>\n";
				}
				$output .= "</ul>\n";
				$output .= "</div>\n";
			} else {
				$page->error(500, "Wikirecent '$location' does not exist!");
			}
		}
		if($name=="wikirelated" && $shortcut)
		{
			list($location, $pagesMax) = $this->yellow->toolbox->getTextArgs($text);
			if(empty($location)) $location = $this->yellow->config->get("wikiLocation");
			if(empty($pagesMax)) $pagesMax = 4;
			$wiki = $this->yellow->pages->find($location);
			$pages = $wiki ? $wiki->getChildren(!$wiki->isVisible())->append($wiki) : $this->yellow->pages->clean();
			$pages->similar($page->getPage("main"))->limit($pagesMax);
			$page->setLastModified($pages->getModified());
			if(count($pages))
			{
				$output = "<div class=\"".htmlspecialchars($name)."\">\n";
				$output .= "<ul>\n";
				foreach($pages as $page)
				{
					$output .= "<li><a href=\"".$page->getLocation()."\">".$page->getHtml("titleNavigation")."</a></li>\n";
				}
				$output .= "</ul>\n";
				$output .= "</div>\n";
			} else {
				$page->error(500, "Wikirelated '$location' does not exist!");
			}
		}
		if($name=="wikitags" && $shortcut)
		{
			list($location) = $this->yellow->toolbox->getTextArgs($text);
			if(empty($location)) $location = $this->yellow->config->get("wikiLocation");
			$wiki = $this->yellow->pages->find($location);
			$pages = $wiki ? $wiki->getChildren(!$wiki->isVisible())->append($wiki) : $this->yellow->pages->clean();
			$page->setLastModified($pages->getModified());
			$tags = array();
			foreach($pages as $page) if($page->isExisting("tag")) foreach(preg_split("/,\s*/", $page->get("tag")) as $tag) ++$tags[$tag];
			if(count($tags))
			{
				uksort($tags, strnatcasecmp);
				$output = "<div class=\"".htmlspecialchars($name)."\">\n";
				$output .= "<ul>\n";
				foreach($tags as $key=>$value)
				{
					$output .= "<li><a href=\"".$wiki->getLocation().$this->yellow->toolbox->normaliseArgs("tag:$key")."\">";
					$output .= htmlspecialchars($key)."</a></li>\n";
				}
				$output .= "</ul>\n";
				$output .= "</div>\n";
			} else {
				$page->error(500, "Wikitags '$location' does not exist!");
			}
		}
		return $output;
	}
	
	// Handle page parsing
	function onParsePage()
	{
		if($this->yellow->page->get("template") == "wikipages")
		{
			if($this->yellow->toolbox->isLocationArgs($this->yellow->toolbox->getLocation()))
			{
				$pages = $this->yellow->page->getChildren(!$this->yellow->page->isVisible())->append($this->yellow->page);
				$pagesFilter = array();
				if($_REQUEST["special"] == "changes")
				{
					$chronologicalOrder = true;
					array_push($pagesFilter, $this->yellow->text->get("wikiSpecialChanges"));
				}
				if($_REQUEST["tag"])
				{
					$pages->filter("tag", $_REQUEST["tag"]);
					array_push($pagesFilter, $pages->getFilter());
				}
				if($_REQUEST["title"])
				{
					$pages->filter("title", $_REQUEST["title"], false);
					array_push($pagesFilter, $pages->getFilter());
				}
				if($_REQUEST["modified"])
				{
					$pages->filter("modified", $_REQUEST["modified"], false);
					array_push($pagesFilter, $this->yellow->text->normaliseDate($pages->getFilter()));
				}
				if(!empty($pagesFilter))
				{
					$pages->sort($chronologicalOrder ? "modified" : "title", $chronologicalOrder);
					$pages->pagination($this->yellow->config->get("wikiPaginationLimit"));
					if(!$pages->getPaginationNumber()) $this->yellow->page->error(404);
					$title = implode(' ', $pagesFilter);
					$this->yellow->page->set("titleHeader", $title." - ".$this->yellow->page->get("sitename"));
					$this->yellow->page->set("titleWiki", $this->yellow->text->get("wikiFilter")." ".$title);
					$this->yellow->page->set("wikipagesChronologicalOrder", $chronologicalOrder);
				}
				$this->yellow->page->setPages($pages);
				$this->yellow->page->setLastModified($pages->getModified());
				$this->yellow->page->setHeader("Cache-Control", "max-age=60");
			}
		}
	}
	
	// Handle page extra HTML data
	function onExtra($name)
	{
		return $this->onParseContentBlock($this->yellow->page, $name, "", true);
	}
}

$yellow->plugins->register("wiki", "YellowWiki", YellowWiki::Version);
?>