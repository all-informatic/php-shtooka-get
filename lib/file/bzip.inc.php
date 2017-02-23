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

// Safety first:
error_reporting(-1);
// On error, set $php_errormsg:
ini_set("track_errors", "1");

    /**
    *@name readBZIP2File
    *@brief Reads the content of a BZIP2 compressed file with full error detection.

    *@internal
    * Reading a BZIP2 file can be tricky, and I never seen a complete example of
    * code that account for any possible failure that may happen accessing a file
    * in general, and decoding compressed data in this specific case.
    * The example that follows is my attempt to address this gap.
    * Some things that worth noting are:
    * - Encoding/decoding errors must be detected with bzerrno().
    * - bzopen() may fail returning FALSE if the file cannot be created or read,
    *   but succeeds also if the file is not properly encoded.
    * - bzread() may fail returning FALSE if it fails reading from the source, but
    *   it returns the empty string on end of file and on encoding error.
    * - bzread() may still return corrupted data with no error whatsoever until the
    *   BZIP2 algo encounters the first hash code, so data retrieved cannot be
    *   trusted until the very end of the file has been reached.
    * @param string $fn Filename.
    * @return string
    */
    function readBZIP2File($fn)
    {
	    $content="";
	    
	//echo "Reading $fn:\n";
	$bz = @bzopen($fn, "r");
	if( $bz === FALSE ){
	    echo "ERROR: bzopen() failed: $php_errormsg\n";
	    return;
	}
	$errno = bzerrno($bz);
	if( $errno != 0 ){
	    // May detect "DATA_ERROR_MAGIC" (not a BZIP2 file), or "DATA_ERROR"
	    // (BZIP2 decoding error) and maybe others BZIP2 errors too.
	    echo "ERROR: bzopen(): BZIP2 decoding failed: ", bzerrstr($bz), "\n";
	    @bzclose($bz);
	    return;
	}
	while(! feof($bz) ) {
	    $s = bzread($bz, 100);
	    if( $s === FALSE ){
		echo "ERROR: bzread() failed: $php_errormsg\n";
		@bzclose($bz);
		return;
	    }
	    $errno = bzerrno($bz);
	    if( $errno != 0 ){
		// May detect "DATA_ERROR" (BZIP2 decoding error) and maybe others
		// BZIP2 errors too.
		echo "ERROR: bzread(): BZIP2 decoding failed on '$fn': ", bzerrstr($bz), "\n";
		@bzclose($bz);
		return;
	    }
	    //echo "read: ", var_export($s, true), "\n";
	    $content.=$s;
	}
	if( ! bzclose($bz) ){
	    echo "ERROR: bzclose() failed: $php_errormsg\n";
	}
	return $content;
    }

?>
