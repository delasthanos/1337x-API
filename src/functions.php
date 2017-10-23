<?php 

function commandLineHelp(){

	global $allowedArgs;
	
	printColor ("1337x Torrents API","white+bold");
	print (n.n."[*] Please pass one of the following argument: ".n);
	$getKeys=array_keys($allowedArgs);
	foreach ( $getKeys as $key ) print ( "\n\t\t".$key." ");
	print(n.n);
	exit();
}

function getHtmlFile( $url, $destination ){
	global $n;
	$urlTorrent = $url;
	//$ch = curl_init( urlencode($urlTorrent) );
	$ch = curl_init( $urlTorrent );
	//curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
	//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt($ch, CURLOPT_ENCODING ,"");
	curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
	//curl_setopt($ch, CURLOPT_USERAGENT,'Googlebot/2.1 (+http://www.google.com/bot.html)');
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0); 
	curl_setopt($ch, CURLOPT_TIMEOUT, 20); //timeout in seconds
	//curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
	//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	//curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
	//curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
	//curl_setopt($ch, CURLOPT_COOKIE, 'cookiename=cookievalue');
	
//print(n.n);
//print (($url));
//exit(n.n);
$html = curl_exec($ch);
	//var_dump($html);
	//$html = file_get_contents( $url ); 
	//$html = mb_convert_encoding($html, 'HTML-ENTITIES', "UTF-8"); 
	if ( !file_put_contents( $destination, $html ) )  { return false; }
	else { return true; }
} 

function testCurl($url){
	$ch = curl_init( $url );
	//curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
	//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
	//curl_setopt($ch, CURLOPT_ENCODING ,"");
	curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0); 
	curl_setopt($ch, CURLOPT_TIMEOUT, 20); //timeout in seconds
	curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
	//curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
	//curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
	//curl_setopt($ch, CURLOPT_COOKIE, 'cookiename=cookievalue');
	$html = curl_exec($ch);
	var_dump($html);
	//$html = file_get_contents( $url ); 
	//$html = mb_convert_encoding($html, 'HTML-ENTITIES', "UTF-8"); 

}

function findTorrentPageLink( $link ){
	// Pass a link spli by / and find if first word is torrent
	$split = explode("/", $link );
	//print ("COUNT= ".count($split)." LINK= ".$link);
	if ( count($split) > 4 && $split[1]=='torrent'){
		//var_dump($split);
		//print ("Found torrent link: ".$link );
		return true;
	} else { return false; }
}

function findSearchPages( $link ){
	// Pass a link spli by / and find if first word is torrent
	$split = explode("/", $link );
	//print ("COUNT= ".count($split)." LINK= ".$link);
	if ( count($split) > 4 && $split[1]=='category-search'){
		//var_dump($split);
		//print ("Found torrent link: ".$link );
		return true;
	} else { return false; }
}

function printColor( $message, $c ){
	global $color;
	print ( $color->set($message,$c) );
}

function removeSpecialChars($string){
	return preg_replace('/[^\da-z ]/i', '', $string);
}

function removeDoubleDots($string){
	return str_replace(":","", $string);
}

function spacesToUnderscores($string){
	return str_replace(" ","_", $string);
}

function underscoresToSpaces($string){
	return str_replace("_"," ", $string);
}
?>
