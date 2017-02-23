<?php

	/**
	*
	*@brief Process repository
	*
	*@file repository.inc.php
	*@package php-shtooka-get
	*@dir lib/swac/scan
	*@note
	*	
	*	V1.0 20170130: first Version 
	*@author: Olivier LUTZWILLER / all-informatic.com
	*@see https://fsi-languages.yojik.eu/oldshtooka/Swac%20Tools%20-%20Shtooka%20Project.html	
	*@internal For Index processing.
	*@compatibility php4/5  
	*@version V1.0 
	*@License LGPL
	*@Date 30.01.2017
	***/
	
	
	/**
	@name process_repository
	@brief Process a repository definition (repository.xml) 
	@note  save its repository.xml.bz2 as well into cache and output number of packages
	@param $url the url of the package
	@param $path path the path of the repository matching its id..
	**/
	function process_repository($url,$target) {
	//----------------------------------------
		global $counts;
		global $show_package_info;
		
		$uri=$url."repository.xml";
		
		$repos_dir="$target".basename($url);
		
		if(!file_exists($repos_dir)) {
				mkdir($repos_dir);
		}
		
		$repos_dir.="/";
		
		$content=get_cached_xml("repository.xml.bz2",$uri,$repos_dir);
		
		$html = str_get_html($content);
		$anchors=$html->find('//repository/directory/');
		$aMax=count($anchors);
		for($a=0;$a<$aMax;$a++) {
			$anchor=$anchors[$a]; 
			$path=$anchor->attr['path'];
			echo (($show_package_info) ? "<hr>" : "\n<br>");
			echo "<font color=\"blue\">Processing package <b>".$url.$path."</b></font>";
			process_package($url,$path,$target);
		}
		echo $aMax;
	}


?>
