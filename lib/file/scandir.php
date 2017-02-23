<?php

	/**
	*
	*@brief Scan Directory tools
	*
	*@file scandir.php
	*@package php-shtooka-get
	*@dir lib/file
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


	$base_xml="lib/xml/";

	set_time_limit (0 );

	error_reporting (E_ALL ^ E_NOTICE);
		
	$counts=array();	
		
		
	if(!defined("DOM")) {

		
		include ($base_xml.'simple_web_spider/functions.php');

		include($base_xml.'simplehtmldom_1_5/simple_html_dom.php');
		
		define("DOM",1);

	}


	//$directory=basename($current);
	include("cache.inc.php");
		//echo $content; //

	function html_scandir($uri,$filter,$package) {
	//-------------------------------------------
	
		$content=get_cached_xml("{$package}-dir.xml",$uri,"data/");	
			
		$html = str_get_html($content);
			
		$anchors=$html->find("a");
		$aMax=count($anchors);
		$files=array();
		
		for($a=0;$a<$aMax;$a++) {
			$anchor=$anchors[$a]; 
			$path=$anchor->attr['href'];
			if(preg_match($filter,$path)) {
				//echo "\n<br>$path";
				$files[]=$path;
			}
		}
		return $files;
	}



	function html_imgscandir($uri,$package) {
	//--------------------------------------
		$filter="/\.(flac|mp3|ogg)$/";
		$files=html_scandir($uri,$filter,$package);
		return $files;
	}

	function package_htmlscandir($repos,$package,$format) {
	//--------------------------------------------------
		$uri=$repos."/".$package."/".$format."/";
		return html_imgscandir($uri,$package);
	}

	/*
	$repos="http://packs.shtooka.net/";
	$package="wuu-balm-congcong";
	$format="flac";
	$files=package_htmlscandir($repos,$package,$format);

	echo "<pre>";
	print_r($files);
	echo "</pre>";
	*/
	/*$repos="http://packs.shtooka.net/";
	echo basename($repos);*/



	/*
	  * From mrlemonade ~ http://php.net/manual/fr/function.readdir.php
	  */

	function getFilesFromDir($dir) {
	//------------------------------
	
	  $files = array();
	  if ($handle = opendir($dir)) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != "..") {
				if(is_dir($dir.'/'.$file)) {
					$dir2 = $dir.'/'.$file;
					$files[] = getFilesFromDir($dir2);
				}
				else {
				  $files[] = $dir.'/'.$file;
				}
			}
		}
		closedir($handle);
	  }

	  return array_flat($files);
	}

	function array_flat($array) {
	//---------------------------
	
		$tmp=array();
		  foreach($array as $a) {
			if(is_array($a)) {
			  $tmp = array_merge($tmp, array_flat($a));
			}
			else {
			  $tmp[] = $a;
			}
		  }

	  return $tmp;
	}

	//Recursives functions, faster and eats less memory!

	function list_files($dir,&$files) {
	//-----------------------------------
	  // $files=getFilesFromDir($dir);
	    if ($handle = opendir($dir)) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != "..") {
				if(is_dir($dir.'/'.$file)) {
					$dir2 = $dir.'/'.$file;
					//$files[] = 
					list_files($dir2,$files,$filter);
				}
				else {
				  $files[] = $dir.'/'.$file;
				}
			}
		}
		closedir($handle);
	  }
	  return count($files);
	}

	function list_subdirs($dir,&$dirs) {
	//-----------------------------------
	  // $files=getFilesFromDir($dir);
		$subdirs=scandir($dir);
		foreach( $subdirs as $subdir) {
			if ($subdir != "." && $subdir != "..") {
				$dir2 = rtrim($dir,"/").'/'.$subdir;							 	 		
				if(is_dir($dir2)) {
					$dirs[]= $dir2;
					list_subdirs($dir2,$dirs);
				}
			}
				
		}

	  return count($dirs);
	}

	




?>
