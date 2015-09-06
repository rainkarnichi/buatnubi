<?php
// Copyright (c) 2013-2015 Datenstrom, http://datenstrom.se
// This file may be used and distributed under the terms of the public license.

// Disqus plugin
class YellowDisqus
{
	const Version = "0.5.2";
	var $yellow;			//access to API
	
	// Handle initialisation
	function onLoad($yellow)
	{
		$this->yellow = $yellow;
		$this->yellow->config->setDefault("disqusShortname", "Yellow");
	}
	
	// Handle page extra HTML data
	function onExtra($name)
	{
		$output = NULL;
		if($name=="disqus" || $name=="comments")
		{
			$shortname = $this->yellow->config->get("disqusShortname");
			$output = "<div id=\"disqus_thread\"></div>\n";
			$output .= "<script type=\"text/javascript\">\n";
			$output .= "var disqus_shortname = '".htmlspecialchars($shortname)."';\n";
			$output .= "var disqus_url = '".$this->yellow->page->get("pageRead")."';\n";
			$output .= "(function() {\n";
			$output .= "var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;\n";
			$output .= "dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';\n";
			$output .= "(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);\n";
			$output .= "})();\n";
			$output .= "</script>\n";
			$output .= "<noscript>Please enable JavaScript to view the <a href=\"http://disqus.com/?ref_noscript\">comments powered by Disqus.</a></noscript>\n";
		}
		return $output;
	}
}

$yellow->plugins->register("disqus", "YellowDisqus", YellowDisqus::Version);
?>