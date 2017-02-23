<?php

	/**
	*
	*@brief Process package
	*
	*@file package.inc.php
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
	@name process_package
	@brief Process a package definition (package.xml) 
	@note  saves its index.xml.bz2 as well into cache 
	@param String $url the url of the package
	@param String $path path the path of the package matching its id..
	@param String $target, the cache root directory.
	**/
	function process_package($url,$path,$target) {
	//--------------------------------------------
	
		global $counts;
		global $show_package_info;
		
		$uri=$url.$path."package.xml";
		
		
		//path= "langue-group-author/format/"
		$name=rtrim($path,"/");
		
		$chunks=explode("/",$name);
		$format_path=$chunks[1];
		$id_path=$chunks[0];
		$package_id=$id_path;
		
		$repos_dir="$target".basename($url);
		$package_dir="{$repos_dir}/{$package_id}";
		
		if(!file_exists($package_dir)) {
				mkdir($package_dir);
		}
		
		$package_dir.="/{$format_path}";
		
		if(!file_exists($package_dir)) {
				mkdir($package_dir);
		}
		
		$chunks=explode("-",$id_path);
		$lang=$chunks[0];
		$group=$chunks[1];
		$author=$chunks[2];
		
		$name=str_replace("/","_",$name);
		

		$content=get_cached_xml("package.xml.bz2",$uri,"$package_dir/");
		/*echo "<pre>";
		echo $content;
		echo "</pre>";*/
		$html = str_get_html($content);

		
		
		$id_package=$html->find("package/id",0);

		if($show_package_info) {
			echo "\n<br>path=package:".$id_path."=".$id_package;
			echo "\n<br>package/version:".$html->find("package/version", 0)->plaintext;
			echo "\n<br>package/author:".$html->find("package/author", 0)->plaintext;
			echo "\n<br>package/license:".$html->find("package/license", 0)->plaintext;
			echo "\n<br>package/audio/format:".$format_path."=".$html->find("package/audio/format",0)->plaintext;
		}
				
		$node=$html->find("package/content/count", 0);
		$count=intval($node->plaintext);
		
		//var_dump($count);
		
		if(!array_key_exists($lang,$counts))
			$counts["$lang"]=0;
			
		$counts["$lang"]=$counts["$lang"]+$count;	
		
		if($show_package_info) {
			echo "\n<br>author:".$author;
			echo "\n<br>count:".$count;

			echo "\n<br>collection/name:".$html->find("collection/name", 0)->plaintext;
			echo "\n<br>collection/section:".$html->find("collection/section",0)->plaintext;
			echo "\n<br>collection/url:".$html->find("collection/url", 0)->plaintext;
			echo "\n<br>group=organization/name:".$group."=".$html->find("organization/name", 0)->plaintext;
			echo "\n<br>organization/url:".$html->find("organization/url", 0)->plaintext;
			echo "\n<br>description:".$html->find("description",0)->plaintext;
			echo "\n<br>readme:".$html->find("readme",0)->plaintext;
		}
		
		$index_dir="{$package_id}/{$format_path}";
		process_package_index($url,"$index_dir/",$target);
	}
	
?>
