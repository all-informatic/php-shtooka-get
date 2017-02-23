<?php

//Multi agent improved version and proxyfied


$redirect_url="";




function LoadCURLPage($url, $agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.4)
 Gecko/20030624 Netscape/7.1 (ax)', $cookie = '', $referer = '', $post_fields = '', $return_transfer = 1, $follow_location = 1, $ssl = '', $curlopt_header = 0)
{
    global $redirect_url;
    
$ch = curl_init(); 

curl_setopt($ch, CURLOPT_URL, $url);

if($ssl)
{
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
}

curl_setopt ($ch, CURLOPT_HEADER, $curlopt_header);

if($agent)
{
curl_setopt($ch, CURLOPT_USERAGENT, $agent);
}

if($post_fields)
{
curl_setopt($ch, CURLOPT_POST, 1); 
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields); 
}

curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);






if($referer)
{
curl_setopt($ch, CURLOPT_REFERER, $referer);
}

if($cookie)
{
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
}

$result = curl_exec ($ch);


//Get the follow link 
//https://stackoverflow.com/questions/6129000/curl-follow-location-but-only-get-header-of-the-new-location
$curl_info = curl_getinfo($ch);
/*$headers = substr($http_data, 0, $curl_info["header_size"]); //split out header
//Step 3. Parse the headers to get the new URL
preg_match("!\r\n(?:Location|URI): *(.*?) *\r\n!", $headers, $matches);*/
$redirect_url = $curl_info["url"];// $matches[1];


curl_close ($ch);

return $result;
}

function extract_unit($string, $start, $end)
{
$pos = stripos($string, $start);

$str = substr($string, $pos);

$str_two = substr($str, strlen($start));

$second_pos = stripos($str_two, $end);

$str_three = substr($str_two, 0, $second_pos);

$unit = trim($str_three); // remove whitespaces

return $unit;
}


?>