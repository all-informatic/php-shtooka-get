<?php

$repos="http://packs.shtooka.net/";
$package="";
$format="flac";

$current=$repos."/".$package."/".$format."/";
//$directory=basename($current);


function get_cached_xml($filename,$uri="",$cache_dir="") {
	
		$fn=$cache_dir.$filename;	
	
		if(file_exists($fn)) {
			$content=file_get_contents($fn);
		}else {
			$content=file_get_contents($uri);
			file_put_contents($fn,$content);
			
			//Unpack
			if(preg_match("/\.bz2$/", $uri))
			{
				$content=displaysBZIP2File($fn);
				file_put_contents($fn,$content);			
			}
		}
		return $content;
	}
	
	$uri=$current;
	$content=get_cached_xml("{$package}-dir.xml",$uri,"data/");
	
?>
