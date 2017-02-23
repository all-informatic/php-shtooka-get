<?php

	/**
	*
	*@brief File toolbox
	*
	*@file file.inc.php
	*@package php-shtooka-get
	*@dir lib/file
	*@note
	*	
	*	V1.0 20170130: first Version 
	*@author: Olivier LUTZWILLER / all-informatic.com
	*@see https://fsi-languages.yojik.eu/oldshtooka/Swac%20Tools%20-%20Shtooka%20Project.html	
	*@internal 
	*@compatibility php4/5  
	*@version V1.0 
	*@License LGPL
	*@Date 30.01.2017
	***/

      define('READ_LEN', 4096);
      //
      
     
      /**
	@brief Compares two files
	@note  Avoid heavy md5 calculations specally on big files
	@internal From http://php.net/manual/fr/function.md5-file.php
	@param String $fn1 the url of the package
	@param String $fn2 path path the path of the package matching its id..
	@param Boolean true if the same, else false
	**/
      function files_identical($fn1, $fn2) {
      //------------------------------------
      
	  if(filetype($fn1) !== filetype($fn2))
	      return FALSE;

	  if(filesize($fn1) !== filesize($fn2))
	      return FALSE;

	  if(!$fp1 = fopen($fn1, 'rb'))
	      return FALSE;

	  if(!$fp2 = fopen($fn2, 'rb')) {
	      fclose($fp1);
	      return FALSE;
	  }

	  $same = TRUE;
	  while (!feof($fp1) and !feof($fp2))
	      if(fread($fp1, READ_LEN) !== fread($fp2, READ_LEN)) {
		  $same = FALSE;
		  break;
	      }

	  if(feof($fp1) !== feof($fp2))
	      $same = FALSE;

	  fclose($fp1);
	  fclose($fp2);

	  return $same;
      }


?>
