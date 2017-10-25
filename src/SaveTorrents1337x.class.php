<?php 
class SaveTorrents1337x{

	public function saveTorrents($title){
	
	
		/*
		*	Notes: 
		*	Collect torrents for $title useing ParseSearch1337x class.
		*
		*/
	
		printColor (n.n."\t[!]TEST save torrents files here".n.n,"yellow");
		$folder=$title['imdb'];
		$findTorrents=new ParseSearch1337x($title['imdb']);
		$torrents = $findTorrents->collectTorrentsFromDB(); // Final array with torrent info for each folder
		if ( is_array($torrents) ){print (n."\t[*]Found: ".count($torrents)." torrents." );}
		else exit(n.n."Inside SaveTorrents1337x torrents is not array. Exit".n.n);
		
		// Download Torrent Pages HTML
		$countDownloaded=0;
		foreach ($torrents as $torrent):
				
			$downloadHTMLTorrent= new DownloadHTMLTorrentPage( $folder, $torrent ); //$folder==imdb, $t==torrent Array
			$downloadHTMLTorrent->downloadTorrentHTMLPage();
			unset($downloadHTMLTorrent);
				
			if (++$countDownloaded>10){ printColor(n."Break after 5 torrent pages. Still testing.".n,"red+bold"); break; }

		endforeach; // Foreach torrent
		
	}
	
	// show torrents from html folders
	public function showTorrents($title){
	
		$folderName=$title['imdb'];
		$torrents=[];
		$folder=HTML_TORRENTS_FILES_PATH;
		if (!file_exists($folder)){ exit("No html files found.Please run search first."); }
		$folders=array_diff( scandir($folder), array('.','..'));

		//foreach ($folders as $folderName ):

		$collectTorrents=[];
		$files=array_diff( scandir(HTML_TORRENTS_FILES_PATH."/".$folderName."/"), array('.','..'));
		foreach ($files as $f ):

			$parseTorrent = new ParseTorrentPage(HTML_TORRENTS_FILES_PATH."/".$folderName."/".$f);
			$torrent = $parseTorrent->parseTorrentPage();
			array_push($collectTorrents, $torrent);

		endforeach;
	
		$torrents[$folderName] = $collectTorrents;
		
		print_r($torrents);
		
		$json=json_encode($torrents);
		file_put_contents(JSON_FILE_PATH.'/'.$folderName,  $json);

		//endforeach; // foreach folders
	}
}
?>
