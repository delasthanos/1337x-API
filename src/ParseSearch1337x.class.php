<?php 
class ParseSearch1337x extends Search1337xHelperFunctions{

	private $folder="";
	private $title = []; //Holds current title (movie, tvshow) information. Seted inside findMovieInDB(),findTvshowInDB()
	private $imdbFolder; //seted inside findMovieInDB(),findTvshowInDB()
	private $HTMLFiles=[]; //seted inside findMovieInDB(),findTvshowInDB()
	private $torrents=[]; // Populated/seted in collectTorrentInformation()

	public function __construct($folder){

		$this->folder=$folder;
		$this->imdbFolder= $this->folder;
		switch (CATEGORY):
			case ('Movies'):
				$this->findMovieInDB( $this->folder ); // Sets $this->title with one row result from database
				break;
			case ('TV'):
				$this->findTvshowInDB( $this->folder ); // Sets $this->title with one row result from database
				break;
		endswitch;		
	}

	public function collectSearchResultsHTML(){

		$this->collectHTMLFromFolder(); // Collect HTML search pages for each title | Sets $this->HTMLFiles
	}

	// Works for one search results pages. Make it work for all of them
	public function collectTorrentInformation(){

		foreach ( $this->HTMLFiles as $currentFile ):
			//$destination=HTML_SEARCH_FILES_PATH."/".$this->imdbFolder."/".$this->HTMLFiles[0]; // HTML results page
			$destination=HTML_SEARCH_FILES_PATH."/".$this->imdbFolder."/".$currentFile; // HTML results page
			$getLocal = file_get_contents( $destination ); 
		
			$parseSearchResults=$this->parseSearchResultsPage($getLocal);
			if (is_array($parseSearchResults)){
		
				foreach ( $parseSearchResults as $p ){
					//if ($p['seeds'] > MIN_SEEDS )
					array_push( $this->torrents, $p );
				}
			}
		endforeach; // Foreach HTML file


		// Remove Doubles | Some torrents on 1337x appear on page 1 and page 2 from search results. 
		// I have no idea why they did this. So torrents array must be uniqued before proceeding
		$this->torrents = array_map("unserialize", array_unique(array_map("serialize", $this->torrents)));

		return $this->torrents;
	}
	
	public function findTotalTorrents(){
		print (n."\t[*]Found: ".count($this->torrents)." torrents." );
		return count($this->torrents);
	}

	public function findActiveTorrents(){

		$activeTorrents=0;
		
		foreach ( $this->torrents as $t ):
		
			if ( $t['seeds']>MIN_SEEDS ){++$activeTorrents;}
			
		endforeach;
		print (n."\t[*]Active( and REMOVE DOUBLES): ".$activeTorrents." ( seeds>".MIN_SEEDS." )");
		return $activeTorrents;

	}
	
	public function getActiveTorrents(){ //array

		$activeTorrents=[];
		
		foreach ( $this->torrents as $t ):
		
			if ( $t['seeds']>MIN_SEEDS ){ array_push($activeTorrents, $t); }
			
		endforeach;

		return $activeTorrents;	
	}

	private function collectHTMLFromFolder(){
	
		// Set folder name after findMovieInDB() to ensure imdb match from database
		$HTMLFiles=[];
		$folder=$this->title['imdb'];
		$files=array_diff( scandir(HTML_SEARCH_FILES_PATH."/".$folder), array('.','..'));
		$count = count($files);
		if ( $count > 0 ){
			printColor (n."\t".$count.' results pages',"white+bold");
			foreach ( $files as $f ){
				//print (n."\t----".$f);
				array_push($HTMLFiles, $f);
			}
			$this->HTMLFiles=$HTMLFiles; // All html filenames for this folder
		}
	}

	private function findMovieInDB($folder){
	
		// Match imfbFolder name with imdb code of database to ensure data integrity

		$dbh = dbhandler::getInstance(); 
		$dbConLocal = $dbh->dbCon; 
		if ( !$dbConLocal ) { exit(" \n\n db connection error.\n\n "); }

		$buildQuery="SELECT * FROM imdb.movies_list WHERE 1 AND imdb='$folder' LIMIT 10"; // Limit for safety. Normally you should get only one result.
		
		if ( !$stmt = $dbh->dbh->prepare($buildQuery) ) { 
			var_dump ( $dbh->dbh->errorInfo() ); 
		} 
		else { 		
			if (!$stmt->execute() ){

				printColor (n."[!]error","red+bold");
				var_dump($stmt->errorInfo());
				sleep(2);
				exit();
			} 
			else {

				printColor (n."_[#]ok_","green");
				$result = $stmt->fetchAll();
				if ( count($result)===1 ){
					print ($folder." = ".$result[0]['moviename']." ".$result[0]['yearmovie'].n);
					$this->title=$result[0];
				}else {
					printColor ( n.n."No results or more than one. This shouldn't happen.".n,"red+bold");
					exit();
				}
			}		
		}	
	}

	private function findTvshowInDB($folder){
	
		// Match imfbFolder name with imdb code of database to ensure data integrity

		$dbh = dbhandler::getInstance(); 
		$dbConLocal = $dbh->dbCon; 
		if ( !$dbConLocal ) { exit(" \n\n db connection error.\n\n "); }

		$buildQuery="SELECT * FROM imdb.tvshows_list WHERE 1 AND imdb='$folder' LIMIT 10"; // Limit for safety. Normally you should get only one result.
		
		if ( !$stmt = $dbh->dbh->prepare($buildQuery) ) { 
			var_dump ( $dbh->dbh->errorInfo() ); 
		} 
		else {
			if (!$stmt->execute() ){

				printColor (n."[!]error","red+bold");
				var_dump($stmt->errorInfo());
				sleep(2);
				exit();
			} 
			else {

				printColor ("_[#]ok_","green");
				$result = $stmt->fetchAll();
				if ( count($result)===1 ){
					print ($folder." = ".$result[0]['tvshowname']." ".$result[0]['imdb'].n);
					$this->title=$result[0];
				}else {
					printColor ( n.n."No results or more than one. This shouldn't happen.".n,"red+bold");
					exit();
				}
			}		
		}	
	}
}
?>
