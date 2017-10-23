<?php 
class SaveSearchResults{
/*
CREATE TABLE `search_resutls` (
  `imdb` int(11) UNSIGNED NOT NULL,
  `totalPages` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  `activePages` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  `totalTorrents` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  `activeTorrents` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
  `last_checked` datetime,

  PRIMARY KEY (`imdb`)
*/
	private $imdb;
	private $totalPages;
	private $activePages;
	private $totalTorrents;
	private $activeTorrents;
	
	public function __construct( $imdb, $totalPages, $activePages, $totalTorrents, $activeTorrents ){
	
		$this->imdb=$imdb;
		$this->totalPages=$totalPages;
		$this->activePages=$activePages;
		$this->totalTorrents=$totalTorrents;
		$this->activeTorrents=$activeTorrents;


		echo $this->imdb; echo n;
		echo $this->totalPages; echo n;
		echo $this->activePages; echo n;
		echo $this->totalTorrents; echo n;
		echo $this->activeTorrents; echo n;
	}
	
	public function save(){
	
		$dbh = dbhandler::getInstance(); 
		$dbConLocal = $dbh->dbCon; 
		if ( !$dbConLocal ) { exit(" \n\n db connection error.\n\n "); }
		
		$inset="INSERT INTO 1337x.search_results (imdb,totalPages,activePages,totalTorrents,activeTorrents,last_checked) VALUES (:imdb,:totalPages,:activePages,:totalTorrents,:activeTorrents,NOW())";
		if ( !$stmt = $dbh->dbh->prepare($inset) ) { 
			var_dump ( $dbh->dbh->errorInfo() ); 
		} else { 

				$stmt->bindParam(':imdb', $imdb );
				$stmt->bindParam(':totalPages', $this->totalPages );
				$stmt->bindParam(':activePages', $this->activePages );
				$stmt->bindParam(':totalTorrents', $this->totalTorrents );
				$stmt->bindParam(':activeTorrents', $this->activeTorrents );
		
				if (!$stmt->execute() ){
					if ( in_array( 1062, $stmt->errorInfo() ) ){
						//print ($n."[#] Already pesisted");
						printColor("#","yellow");
					} else { 
						printColor ($n."[!]error","red+bold");
						var_dump($stmt->errorInfo());
						sleep(2);
						exit();
					}
				} else {
						printColor ("_[#]ok","green+bold");
				}
		}
	} //save
	
	
}
?>
