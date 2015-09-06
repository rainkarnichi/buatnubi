<?php
// Copyright (c) 2013-2015 Datenstrom, http://datenstrom.se
// This file may be used and distributed under the terms of the public license.

// User permission plugin
class YellowUserpermission
{
	const Version = "0.5.2";
	var $yellow;			//access to API
	
	// Handle initialisation
	function onLoad($yellow)
	{
		$this->yellow = $yellow;
	}
	
	// Handle permission to change page
	function onUserPermission($location, $fileName, $users)
	{
		return substru($location, 0, strlenu($users->getHome())) == $users->getHome();
	}
}

$yellow->plugins->register("userpermission", "YellowUserpermission", YellowUserpermission::Version);
?>