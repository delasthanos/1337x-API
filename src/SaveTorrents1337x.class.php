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
				
			//if (++$countDownloaded>10){ printColor(n."Break after 10 torrent pages. Still testing.".n,"red"); break; }

		endforeach; // Foreach torrent
	}
}
?>
