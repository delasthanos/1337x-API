<?php 
abstract class Search1337xHelperFunctions{

	// Pass filename of results page to parse it and find information about torrents. Maybe use this inside Search133x class to stop retrieving files of seeds are 0
	protected function parseSearchResultsPage($getLocal){

		$currentTorrent=[];
		$torrents=[];

		$dom = new DOMDocument();
		@$dom->loadHTML($getLocal);
		$xPath = new DOMXPath($dom);
		$elements = $xPath->query('//tr');
		$trCounter=0;

		// Foreach //tr
		foreach($elements as $element): 
		
			$currentTorrent=[];
			//Not needed. Use torrent link instead
			//$name = $xPath->query('td[contains(@class,"name")]', $element);
			//foreach ( $name as $nm ){ print n."Name: ".$nm->nodeValue; }

			// First get seeds. If seeds are 0 then do not store torrent to array
			$seeds = $xPath->query('td[contains(@class,"seeds")]', $element);
			foreach ( $seeds as $s ){ 
				//print n."Seeds: ".(int)$s->nodeValue;
				$currentTorrent['seeds']=(int)$s->nodeValue;
			}
			//var_dump($currentTorrent);
			//if ( array_key_exists('seeds', $currentTorrent ) && ($currentTorrent['seeds'] > 1) ):
			if ( array_key_exists('seeds', $currentTorrent ) ):

				$torrentLink = $xPath->query('td[contains(@class,"name")]/a/@href', $element);
				foreach ( $torrentLink as $tlink ){ 

					$link=$tlink->value;
					$split = explode("/", $link );
					if ( count($split) > 4 && $split[1]=='torrent'){
						//array_push($searchPageLink, $link);
						//print (n."\t\t----".$link);
						//print n."Torrent link: ".$tlink->nodeValue;
						$currentTorrent['link']=$tlink->nodeValue;
					}

				}
			
				$leeches = $xPath->query('td[contains(@class,"leeches")]', $element);
				foreach ( $leeches as $l ){ //print n."Leeches: ".$l->nodeValue;
					$currentTorrent['leeches']=$l->nodeValue;
				}
			
				$size = $xPath->query('td[contains(@class,"size")]', $element);
				foreach ( $size as $sz ){ //print n."Size: ".$sz->nodeValue;
					$currentTorrent['size']=$sz->nodeValue;
				}
			
				$uploader = $xPath->query('td[ contains(@class,"vip") or contains(@class,"uploader") ]', $element);
				foreach ( $uploader as $u ){ //print n."Uploader: ".$u->nodeValue;
					$currentTorrent['uploader']=$u->nodeValue;
				}

				array_push( $torrents, $currentTorrent );

			endif; // If seeds > 1
		endforeach; // Foreach //tr

		return $torrents;
	}

	protected function checkResultsForSeeds($htmlFilePath){
	
		// Accepts only HTML file.
	
		//This is a helper function to check for seeds at results pages. If seeds are less than minimum seted seeds ( <1 ) then stop search and download of HTML files

		$torrents = $this->parseSearchResultsPage(file_get_contents($htmlFilePath));
		$countTorrents=count($torrents);
		$countZeroSeeds=0;
		foreach ( $torrents as $t ){
			if ( ($t['seeds'] === 0) || ($t['seeds'] === 1) ){
				$countZeroSeeds++;
			}
		}
		
		if ( $countZeroSeeds === $countTorrents ){
			printColor( n."[!]All ".$countTorrents."torrents in page have 0 or 1 seed: ". $countZeroSeeds."/".$countTorrents, "yellow");
			printColor( n."[*]Stop saving or reading results pages", "red");
			return false;
		}
		else {
			return true;
		}
	}
}
?>
