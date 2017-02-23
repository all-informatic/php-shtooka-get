<?php


      /**
	*@brief Shtooka-get, example script to get a local copy of swac shtooka , dumps eventually its properties 
	*
	*@file shtooka-get.php
	*@package php-shtooka-get 
	*@note
	*	V2.0 20170131 -  Added credits, all includes deported into main.php
	*	V1.0 20170119 - first Version 
	*@author: Olivier LUTZWILLER / all-informatic.com
	*@see https://fsi-languages.yojik.eu/oldshtooka/Swac%20Tools%20-%20Shtooka%20Project.html	
	*@internal For Index processing.
	*@compatibility php4/5  
	*@version 2.0 
	*@License LGPL
	*@Date 30.01.2017
	***/
	
	include("credits.php");
	include("lib/swac/scan/main.php");

	error_reporting (E_ALL ^ E_NOTICE);
	set_time_limit (0 );
	ini_set('memory_limit',"128M");
	
	//========== CONFIGURATION =============================================
	
	$show_package_info=false;                 //Displays package information on scan if true
	$check_files=false;		          //Avoid by default to inspect index for file by coherency
	$process_files=false;                     //Will show  missing files in package if $check_file is set to true
	$url="http://packs.shtooka.net/";         //Swac server to clone
	$target_dir="data/";                      //Base of the cache directory, clone of $url result will be stored there
	$repos_tar='http://download.shtooka.net/';//Where to fetch the .tar of the packages containing audio files
	
	//Choose with packages lang you wish, the whole current archives make 7-8Go, be sure to have place!
	$packages_lang_to_install=array(
	   // 'ces',// 'Česky'
	    'deu',// 'Deutsch'
	   /* 'nld',// 'Nederlands'
	    'spa',// 'Español'*/
	    'eng',// 'English'
	    'fra',// 'Français'
	    'chi',// 'Chinois'
	   /* 'rus',// 'Русский'
	    'cmn',// '中文'
	    'swe',// 'Svenska'
	    'ukr',// 'Українська'
	    'pol',// 'Polski'
	    'bul',// 'Български'
	    'hun',// 'Magyar'
	    'ita',// 'Italiano'
	    'wol',// 'Wolof'
	    'heb',// 'עברית'
	    'arb',// 'العربية'
	    'por',// 'Português'
	    'bam',// 'Bamanankan'	
	    'srp',// 'Српски'
	    'ell',// 'Ελληνικά'
	    'tur',// 'Türkçe'
	    'bre',// 'Brezhoneg'
	    'cat',// 'Català'
	    'jpn',// '日本語'
	    'yue',// '粵語'
	    'ron',// 'Română'*/
	);
	//=====================================================================
	
	Cache_Disallow();
	Credits_start();
	echo "<h1>".get_credits()."</h1><hr>";

	$ts=new Tswac_scan();

	//Rerieve all indexes of the swac repository of the given url and make a local repository clone into target dir
	$ts->source_load($url,$target_dir);
	
	//Now try to uncompress audio package if provided into the local swac clone
	$url=$ts->read_tarballs($url,$target_dir,$repos_tar);

	echo "<pre>";
	print_r($counts);
	echo "</pre>";

	Credits_stop();
?>