<?php


      /**
	*
	*@brief Main class to retrieve index and install audio locally
	*
	*@file main.php
	*@package php-shtooka-get
	*@dir lib/swac/scan
	*@note
	*	V1.0 20170131: first Version 
	*@author: Olivier LUTZWILLER / all-informatic.com
	*@see https://fsi-languages.yojik.eu/oldshtooka/Swac%20Tools%20-%20Shtooka%20Project.html	
	*@compatibility php4/5  
	*@version V1.0 
	*@License LGPL
	*@Date 31.01.2017
	***/

	$base_lib=dirname(__FILE__)."/../../../lib/";
	$base_file=$base_lib."file/";

	include($base_file."bzip.inc.php");
	include($base_file."cache.inc.php");
	include($base_file."scandir.php");
	include($base_file."file.inc.php");

	$base_xml=$base_lib."xml/";
	$counts=array();	
			
	if(!defined("DOM")) {
	
		include ($base_xml.'simple_web_spider/functions.php');
		include($base_xml.'simplehtmldom_1_5/simple_html_dom.php');		
		define("DOM",1);

	}
	
    //Process stuff
	include("repository.inc.php");
	include("package.inc.php");
	include("index.inc.php");
	


	class Tswac_scan {
	
		/**
		@name source_load
		@brief Make a local copy of all index and packages of a swac repository
		@note if $process_files is set to true, it will download audio files (this may last!), You can use read_tarballs function to avoid harmering server.
		@param String $url the url of the repository 
		@param String $target_dir  the base of the cache directory with '/', default is "data/" who need to be writeable by web user (www-data)
		**/
		public function source_load($url,$target_dir="data/") {
		//------------------------------------------------------
		
			if((!is_dir($target_dir))||(!is_writeable($target_dir))) die("source_load error:Target dir '$target_dir' is not writeable (do a chmod 777) or does not exists (requires /)");  
			$content=process_repository($url,$target_dir);
		
		} 

		/**
		@name read_tarballs
		@brief Unpack package tarballs from a download repository to current local cached repository
		@param String $url the url of the repository 
		@param String $target_dir  the base of the cache directory with '/', default is "data/" who need to be writeable by web user (www-data)
		@param String $repos_tar the download directory where the swac packages archives (.tar) reside. 
		**/
		public function read_tarballs($url,$target_dir="data/",$repos_tar='http://mayday/download.shtooka.net/') {
		//---------------------------------------------------------------------------------------------------------
		
			global $packages_lang_to_install;
		
			$uri=$url."repository.xml";
		
			$repos_dir="$target_dir".basename($url);
		
			if(!file_exists($repos_dir)) {
		
				$repos_dir.="/";
		
			}
	
			$content=get_cached_xml("repository.xml.bz2",$uri,$repos_dir);
			$available=0;
			$html = str_get_html($content);
			$anchors=$html->find('//repository/directory/');
			$aMax=count($anchors);
			for($a=0;$a<$aMax;$a++) {
				$anchor=$anchors[$a]; 
				$path=$anchor->attr['path'];
				$tarball=$anchor->find('tarball',0)->plaintext;
			
				$type="";
				$types="\.(mp3|flac|ogg|spx)\.tar";
				if(preg_match("/$types$/",$tarball,$matches)) {
					$type=$matches[1];
				} else {
					die("Invalid tarball name, must ends like this ".$types);
				}
			
				//relocate..anf fix names...
				$tarball=$repos_tar.preg_replace("/\.(mp3|flac|ogg|spx)\./","_$1.",basename($tarball));
				$color="red";
				if(url_exists($tarball)) {
					$color="green";
					$available++;
					;
				
					if(preg_match("/^(".implode("|",$packages_lang_to_install).")/",$path)) {
						echo "<hr><font color=\"$color\">".$tarball."</font>";
						try {
							$package_dir=dirname(rtrim($repos_dir."/".$path,"/"));
							echo "<br>Extract $tarball to ".$package_dir;
						
							if(!file_exists($package_dir."/index.tags.txt")) {
						
								
								  //download locally the archive .tar in a temporary file, add the good extension so PharData will know how to handle it
								  $file = tmpfile();
								  $path=stream_get_meta_data($file)['uri'];
								  rename($path, $path.='.tar'); 
								  
								  //file_put_contents($path,file_get_contents($tarball)); is too much memory consuming
								  $src = fopen($tarball, 'r');
								  $dest = fopen($path, 'w');
								  stream_copy_to_stream($src, $dest);
								  
								  //we have no choice but rename index and package if previous ones, else PharData will have problem with rights
								  $fn1=$package_dir."/".$type."/index.xml";
								  $fn2=$package_dir."/".$type."/index_old.xml";
								  if(file_exists($fn1)&&!file_exists($fn2))					
									  rename($fn1,$fn2);
								  
								  $fn1=$package_dir."/".$type."/index.xml.bz2";
								  $fn2=$package_dir."/".$type."/index_old.xml.bz2";
								  if(file_exists($fn1)&&!file_exists($fn2))					
									  rename($fn1,$fn2);
								  
								  $fn1=$package_dir."/".$type."/package.xml";
								  $fn2=$package_dir."/".$type."/package_old.xml";
								  if(file_exists($fn1)&&!file_exists($fn2))					
									  rename($fn1,$fn2);
								  
								  $fn1=$package_dir."/".$type."/package.xml.bz2";
								  $fn2=$package_dir."/package_old.xml.bz2";
								  if(file_exists($fn1)&&!file_exists($fn2))	
									  rename($fn1,$fn2);
								  
								  //so PharData can untar it
								  $phar = new PharData($path);
								  $phar->extractTo($package_dir,null, true);// extract all files , overwrite
								  
								  $fn1=$package_dir."/".$type."/index.xml";
								  $fn2=$package_dir."/".$type."/index_old.xml";
								  if(files_identical($fn1, $fn2)) 
									  unlink($fn2);
									  
								  $fn1=$package_dir."/".$type."/index.xml.bz2";
								  $fn2=$package_dir."/".$type."/index_old.xml.bz2";
								  if(files_identical($fn1, $fn2)) 
									  unlink($fn2);
								  
								  $fn1=$package_dir."/".$type."/package.xml";
								  $fn2=$package_dir."/".$type."/package_old.xml";
								  if(files_identical($fn1, $fn2)) 
									  unlink($fn2);
								  
								  $fn1=$package_dir."/".$type."/package.xml.bz2";
								  $fn2=$package_dir."/package_old.xml.bz2";
								  if(files_identical($fn1, $fn2)) 
									  unlink($fn2);
								  
								  unlink($path);
							} else {
								echo("<br>Tar seems already extracted as index.tags.txt found, skipping");
							}
						
						} catch (Exception $e) {
							// handle errors
							var_dump($e);
							die("<br>untar crashed on $tarball");
						}
					} else
						echo "<hr>Ignoring <font color=\"orange\">".$tarball."</font>";
				} else
					echo "<hr><font color=\"$color\">".$tarball."</font>";
				
				flush();	
			
			}
			echo("$available/$aMax");
		}
	}




?>
