<?php
// Copyright (c) 2013-2015 Datenstrom, http://datenstrom.se
// This file may be used and distributed under the terms of the public license.

// Draft status plugin
class YellowDraft
{
	const Version = "0.5.1";
	var $yellow;			//access to API
	
	// Handle initialisation
	function onLoad($yellow)
	{
		$this->yellow = $yellow;
		$this->yellow->config->setDefault("draftStatusCode", "500");
	}
	
	// Handle page meta data parsing
	function onParseMeta($page)
	{
		if($page->get("status") == "draft") $page->visible = false;
	}
	
	// Handle page parsing
	function onParsePage()
	{
		if($this->yellow->page->get("status")=="draft" && $this->yellow->getRequestHandler()=="core")
		{
			$this->yellow->page->error($this->yellow->config->get("draftStatusCode"), "Page has 'draft' status!");
		}
	}
}

$yellow->plugins->register("draft", "YellowDraft", YellowDraft::Version);
?>