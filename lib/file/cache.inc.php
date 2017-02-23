<?php


	/**
	*
	*@brief Cache system 
	*
	*@file cache.inc.php
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


if(!defined("CACHE")) {

	define("DEBUG_CACHE",0);

	/**
	*@name url_exists
	*@param String $url the url to test
	*@return Boolean
	**/
	function url_exists($url){
   		$headers=get_headers($url);
 	    return stripos($headers[0],"200 OK")?true:false;
	}

	
	/**
	*@name get_cached_xml
	*@param String $filename the url to test
	*@param String $uri the url of the file to cache
	*@param String $cache_dir the base directory of the cache
	*@param Boolean $crash_on_error , default halt on error
	*@return Boolean
	**/
	function get_cached_xml($filename,$uri="",$cache_dir="",$crash_on_error=true) {
	//-----------------------------------------------------------------------------
	
		$fn=$cache_dir.$filename;	
	
		if(file_exists($fn)) {
		
			if(DEBUG_CACHE) echo '<br><font color="green">Reading '.$filename.' from  cache (local copy of '.$uri.') </font>';
			
			//Unpack
			if(preg_match("/\.bz2$/", $fn))
			{
				//echo "UNPACK($fn)";
				$content=readBZIP2File($fn);
				
			} else {
			    $content=file_get_contents($fn);	
			}
			
		}else {
			if(DEBUG_CACHE) echo "<br>'$fn' was Not found in cache";
			
			//Here uri contains .xml content
			if(url_exists($uri)) {
				
				//echo '<br>reading content from '.$uri;
				$content=file_get_contents($uri);
				
				
				if(preg_match("/\.bz2$/", $fn))
				{
					//save .xml first
					if(DEBUG_CACHE) echo '<br><font color="green">Saving '.basename($uri).' to cache directory '.$cache_dir.' (local copy of '.$uri.')</font>';
					file_put_contents($cache_dir.basename($uri),$content);	
					
					//Compress content .xml	for further coherency check
					$bzcontent=bzcompress($content,9);
					
					if(!url_exists($uri.".bz2")) {
						if(DEBUG_CACHE) echo '<br><font color="red">'.$filename.' is missing on server, will generate it from uncompressed copy! </font>';
						$content=$bzcontent;
					} else {
						$content=file_get_contents($uri.".bz2");
						
						//Coherency check
						if($content != 	$bzcontent) {
							if(DEBUG_CACHE) echo "<br><font color=\"orange\">File $filename and its uncompressed version are not synchronized ($uri), fixing it!</font>";	
							$content=$bzcontent;
						}	
					}
						
				}
				
			}else {
				echo("<br><font color=\"darkorange\">File <a href=\"$uri\">$uri</a> not found.</font>");
				if($crash_on_error) die("crashing :(");
				return "";
			}
			
			//Save normally .xml.bz2
			file_put_contents($fn,$content);
			
			if(file_exists($fn)) {
				if(DEBUG_CACHE) echo '<br><font color="green">Saving '.$filename.' to cache directory '.$cache_dir.'</font>';
				//unpack
				if(preg_match("/\.bz2$/", $filename))
				{
					$content=readBZIP2File($fn);			
				}
			} else {
				die("Cannot write $fn");
			}
			
		}
		
		//if($content == "") die("get_cached('<a href=\"$uri\">$uri</a>') return an empty file");
		
		return $content;
	}
	define("CACHE",1);
}

?>
