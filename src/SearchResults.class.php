<?php 
class SearchResults{
/*
CREATE TABLE `search_resutls` (
  `imdb` int(11) UNSIGNED NOT NULL,
  `totalPages` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  `activePages` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  `totalTorrents` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  `activeTorrents` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  `last_checked` datetime,

  PRIMARY KEY (`imdb`)
  
CREATE TABLE `search_results_torrents` (
  `imdb` int(11) UNSIGNED NOT NULL,
  `1337x_id` int(11) UNSIGNED NOT NULL,
  `link` MEDIUMTEXT NOT NULL,
  `seeds` MEDIUMTEXT NOT NULL,
  `leeches` MEDIUMTEXT NOT NULL,

  FOREIGN KEY (`imdb`) REFERENCES search_results(imdb),
  UNIQUE INDEX `imdb_1337x_id_match` (`imdb`,`1337x_id`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8;
*/
	private $dbh;
	private $imdb;
	private $totalPages;
	private $activePages;
	private $totalTorrents;
	private $activeTorrents;
	private $torrents=[];
	
	public function __construct( $imdb, $totalPages, $activePages, $totalTorrents, $activeTorrents, $torrents ){
	
		$this->imdb=str_replace("tt","",$imdb);
		$this->totalPages=$totalPages;
		$this->activePages=$activePages;
		$this->totalTorrents=$totalTorrents;
		$this->activeTorrents=$activeTorrents;
		$this->torrents=$torrents;
		
		$dbh = dbhandler::getInstance(); 
		$dbConLocal = $dbh->dbCon; 
		if ( !$dbConLocal ) { exit(" \n\n db connection error.\n\n "); }
		$this->dbh=$dbh;

	}
	
	public function saveSearchResultsTorrents(){

		/* torrent: 
		(
			[seeds] => 0
			[link] => /torrent/1349552/Marvel-s-Daredevil-S01E04-2160p-NF-WEBRip-4K-H-265-DD-ULTRAHDCLUB/
			[leeches] => 0
			[size] => 3.3 GB0
			[uploader] => laura860

		*/

		$torrents=$this->torrents;
		foreach ( $torrents as $torrent ):

			$split = explode("/",$torrent['link']);
			$torrentid=$split[2];
			$torrentlink=$split[3];
			$imdb=$this->imdb;
			
			$insert = $this->insertTorrentResult( $torrentid, $torrentlink, $torrent['seeds'], $torrent['leeches'] );
			if ($insert===1) printColor("#","green");
			else if ($insert===1062) printColor("#","yellow");
			else {
				printColor(n.n."[!]Uncaught error while inserting torrent search results".n.n,"red");
				var_dump($insert);
				exit();
			}

		endforeach;

	}
	
	private function insertTorrentResult( $torrentid, $torrentlink, $seeds, $leeches ){
	
		$insert="INSERT INTO 1337x.search_results_torrents (imdb,1337x_id,link,seeds,leeches) VALUES (:imdb,:1337x_id,:link,:seeds,:leeches)";
		if ( !$stmt = $this->dbh->dbh->prepare($insert) ) { var_dump ( $dbh->dbh->errorInfo() ); } 
		else { 
			$stmt->bindParam(':imdb', $this->imdb );
			$stmt->bindParam(':1337x_id', $torrentid );
			$stmt->bindParam(':link', $torrentlink );
			$stmt->bindParam(':seeds', $seeds );
			$stmt->bindParam(':leeches', $leeches );			
	
			if (!$stmt->execute() ){
				if ( in_array( 1062, $stmt->errorInfo() ) ){ return 1062; } 
				else { return $stmt->errorInfo(); }
			} 
			else { return 1; }
		}	
	}

	public function saveSearchResults(){ //Calls insert or update accordingly

		// return true if insert or update succesfull
		$save = $this->insertSearchResults();
		if ($save===1):

			printColor (n."_[#]saved","green+bold");
			return true;

		elseif($save===1062):

			printColor (n."_[#]updating_","yellow+bold");
			$update=$this->updateSearchResults();
			if ( $update===1 ): printColor ("ok","green+bold"); return true;
			elseif ( $update===0 ): printColor ("[!]Error updating search results. Probably wrong imdb code","red+bold"); return true;
			else: printColor ("[!]Uncaught error on updating search results","red+bold"); return false;
			endif;

		else:

			printColor ("_[!]Uncaught error: ","red+bold"); printColor ("while saving search results to db: ","white+bold");
			var_dump($save);
			exit();

		endif;

	}
	
	private function insertSearchResults(){
		
		$insert="INSERT INTO 1337x.search_results (imdb,totalPages,activePages,totalTorrents,activeTorrents,last_checked) VALUES (:imdb,:totalPages,:activePages,:totalTorrents,:activeTorrents,NOW())";
		if ( !$stmt = $this->dbh->dbh->prepare($insert) ) { var_dump ( $dbh->dbh->errorInfo() ); } 
		else { 
			$stmt->bindParam(':imdb', $this->imdb );
			$stmt->bindParam(':totalPages', $this->totalPages );
			$stmt->bindParam(':activePages', $this->activePages );
			$stmt->bindParam(':totalTorrents', $this->totalTorrents );
			$stmt->bindParam(':activeTorrents', $this->activeTorrents );
	
			if (!$stmt->execute() ){
				if ( in_array( 1062, $stmt->errorInfo() ) ){ return 1062; } 
				else { return $stmt->errorInfo(); }
			} 
			else { return 1; }
		}
	} //insert

	private function updateSearchResults(){
		
		$update="UPDATE 1337x.search_results SET totalPages=:totalPages,activePages=:activePages,totalTorrents=:totalTorrents,activeTorrents=:activeTorrents,last_checked=NOW() WHERE imdb=:imdb";

		if ( !$stmt = $this->dbh->dbh->prepare($update) ) { var_dump ( $this->dbh->dbh->errorInfo() ); } 
		else { 
			$stmt->bindParam(':imdb', $this->imdb );
			$stmt->bindParam(':totalPages', $this->totalPages );
			$stmt->bindParam(':activePages', $this->activePages );
			$stmt->bindParam(':totalTorrents', $this->totalTorrents );
			$stmt->bindParam(':activeTorrents', $this->activeTorrents );
	
			if (!$stmt->execute() ){return $stmt->errorInfo();} 
			else { 

				$checkCount=$stmt->rowCount();
				if ($checkCount===1){return 1;}
				if ($checkCount===0){return 0;}
			}
		}
	} //insert

}// SearchResults Class
?>
