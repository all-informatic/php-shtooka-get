<?php
error_reporting (E_ALL ^ E_NOTICE);

include 'functions.php';

// Site to check
$site = 'www.microsoft.com';

// Connect to this url using CURL
$url = 'http://www.google.ro/search?hl=en&q=site%3A'.$site.'&btnG=Search';

$data = LoadCURLPage($url);

// Extract information between STRING 1 & STRING 2

$string_one = '</b> of about <b>';
$string_two = '</b>';

$info = extract_unit($data, $string_one, $string_two);

echo 'Google has indexed '.$info.' pages for '.$site.'.';
?>