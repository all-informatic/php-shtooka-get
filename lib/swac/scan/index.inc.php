 <?php
 
      /**
	*
	*@brief Process package's index
	*
	*@file index.inc.php
	*@package php-shtooka-get
	*@dir lib/swac/scan
	*@note
	*	V1.0 20170130: first Version 
	*@author: Olivier LUTZWILLER / all-informatic.com
	*@see https://fsi-languages.yojik.eu/oldshtooka/Swac%20Tools%20-%20Shtooka%20Project.html	
	*@compatibility php4/5  
	*@version V1.0 
	*@License LGPL
	*@Date 30.01.2017
	***/
 
 
	/**
	@name process_package_index
	@brief Process a package index xml 
	@note  save its index.xml.bz2 as well into cache 
	@param $url the url of the package
	@param path path the path of the package matching its id..
	@param String target, the cache root directory.
	**/
	function process_package_index($url,$path,$target) {
	//--------------------------------------------------
	
		global $process_files;
		global $check_files;
	
		$uri=$url.$path."index.xml";
		
		$name=rtrim($path,"/");
		
		$chunks=explode("/",$name);
		$format_path=$chunks[1];
		$id_path=$chunks[0];
		
		$chunks=explode("-",$id_path);
		$lang=$chunks[0];
		$group=$chunks[1];
		$author=$chunks[2];
		
		$name=str_replace("/","_",$name);
	
	
		$repos_dir="$target".basename($url);
		$index_dir="{$repos_dir}/{$path}";

	    
		$content=get_cached_xml("index.xml.bz2",$uri,$index_dir);
		
		$index_valid=true;
		
		
		if($check_files) {
		
			$index_valid=false;
			/*echo "<pre>";
			echo $content;
			echo "</pre>";*/
			$html = str_get_html($content);
	
			$files=$html->find("index/file");
			$fMax=count($files);
			$index_valid=true;
			$errors_max=2;
			echo "\n<br>";
			for($f=0;$f<$fMax;$f++) {
				$file=$files[$f]; 
				$filename=$file->attr['path'];
				$uri=$url.$path.$filename;
		
				if(!url_exists($uri)) {
					echo "\n<br><font color=\"red\">File $uri is missing</font>";
					$index_valid=false;
					$errors_max=$errors_max-1;
					if($errors_max<0) $f=$fMax;
				}
		
				if($process_files) {
					$content=get_cached_xml($filename,$uri,"$index_dir",false);
		
					if($content == "") {
						echo "\n<br>Caching $filename into $index_dir from $uri : Failed";
						//echo "\n<br>get_cached('<a href=\"$uri\">$uri</a>') return an empty file";
						echo "ERROR: the Index is broken for $url. aborting";
						$index_valid=false;
						$f=$fMax;
					} else {
						//echo "\n<br>Caching $filename into $index_dir from $uri : Success";
					/*	$tag=$file->find("tag",0);
						$swac_alphaidx=$tag->attr['swac_alphaidx'];//"pǔ" 
						$swac_coll_section=$tag->attr['swac_coll_section'];//"HSK niveau IV" 
						$swac_pron_phon=$tag->attr['swac_pron_phon'];//"pǔ" 
						$swac_tech_date=$tag->attr['swac_tech_date'];//"2009-07-10" 
						$swac_text=$tag->attr['swac_text'];//"谱"
						echo "[".$swac_text.":".$swac_pron_phon."]";*/
			
					}
				}
			
			}
		}
		echo "\n<br>Package $path processed ". ($index_valid)? "successfully" : "with errors" ;
		flush();
	}

?>
