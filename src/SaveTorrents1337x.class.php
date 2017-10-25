<?php 
class SaveTorrents1337x{

	public function saveTorrents($title){
	
		printColor (n.n."\t[!]TEST save torrents files here".n.n,"yellow");

		// Print stats
		$folder=$title['imdb'];
		$findTorrents=new ParseSearch1337x($title['imdb']);
		$findTorrents->collectSearchResultsHTML();
		$torrents = $findTorrents->collectTorrentsFromHTML(); // Final array with torrent info for each folder
		if ( is_array($torrents) ){print (n."\t[*]Found: ".count($torrents)." torrents." );}

		$activeTorrents=0;
		foreach ($torrents as $t ):
		
			if ( $t['seeds']>MIN_SEEDS ){++$activeTorrents;}
			
		endforeach;
		print (n."\t[*]Active: ".$activeTorrents." ( seeds>".MIN_SEEDS." )");
		// Print stats
		
		// Download Torrent Pages HTML
		$countDownloaded=0;
		foreach ($torrents as $t ):

			if ( $t['seeds']>MIN_SEEDS ):
				$downloadHTMLTorrent= new DownloadHTMLTorrentPage( $folder, $t ); //$folder==imdb, $t==torrent Array
				$downloadHTMLTorrent->downloadTorrentHTMLPage();
				unset($downloadHTMLTorrent);
			endif;
			//if (++$countDownloaded>10){ printColor(n."Break after 10 torrent pages. Still testing.".n,"red"); break; }

		endforeach; // Foreach torrent
	}
}
?>
