<?php 
class ParseSearch1337x extends Search1337xHelperFunctions{

	private $imdb="";
	private $imdbFolder; //seted inside findMovieInDB(),findTvshowInDB()
	private $title = []; //Holds current title (movie, tvshow) information. Seted inside findMovieInDB(),findTvshowInDB()
	private $HTMLFiles=[]; //seted inside findMovieInDB(),findTvshowInDB()
	private $torrents=[]; // Populated/seted in collectTorrentsFromHTML()
	private $summary_id;

	public function __construct($folder){

		// Match folder given from database. Bindparam beacuse it's called with AJAX too.
		//$this->imdb=$folder; // seted below in findMovieInDB(), findTvshowInDB()
		//$this->imdbFolder= $this->imdb;

		switch (CATEGORY):
			case ('Movies'):
				$this->findMovieInDB( $folder ); // Sets $this->title with one row result from database
				break;
			case ('TV'):
				$this->findTvshowInDB( $folder ); // Sets $this->title with one row result from database
				break;
		endswitch;		
	}

	public function collectTorrentsFromDB(){
	
		print (n."Collected torrents from db : ");
		
		$dbh = dbhandler::getInstance(); 
		$dbConLocal = $dbh->dbCon; 
		if ( !$dbConLocal ) { exit(" \n\n db connection error.\n\n "); }

		$select="SELECT id FROM 1337x.search_summary WHERE 1 AND imdb=:imdb LIMIT 10"; // Limit for safety. Normally you should get only one result.
		if ( !$stmt = $dbh->dbh->prepare($select) ) { var_dump ( $dbh->dbh->errorInfo() ); exit(); }
		else {
			$imdb=$this->imdb;
			$stmt->bindParam(':imdb', $imdb );
			if (!$stmt->execute() ){printColor (n."[!]error","red+bold");var_dump($stmt->errorInfo());exit();} 
			else { $result = $stmt->fetchAll(); $id=$result[0]['id']; $this->summary_id=$result[0]['id']; }
		}


		$select="select * from 1337x.search_results where summary_id=:id";
		if ( !$stmt = $dbh->dbh->prepare($select) ) { var_dump ( $dbh->dbh->errorInfo() ); exit(); }
		else {
			$imdb=$this->imdb;
			$stmt->bindParam(':id', $id );
			if (!$stmt->execute() ){printColor (n."[!]error","red+bold");var_dump($stmt->errorInfo());exit();} 
			else { 
				$torrents = $stmt->fetchAll();
				return $torrents;
			}
		}
		
		return 0;
	}
	
	public function getSummaryId(){
		return $this->summary_id;
	}

	// Read results pages from folders
	public function collectSearchResultsHTML(){

		//$this->collectHTMLFromFolder(); // Collect HTML search pages for each title | Sets $this->HTMLFiles DELETED
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
			var_dump($this->HTMLFiles);
		}
	}

	// Works for one search results pages. Make it work for all of them
	public function collectTorrentsFromHTML(){

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
	
	// TEST to restirct torrents by year in title. Deprecated because it is not working properly
	public function filterTorrentsByMovieYear(){
	
		$torrents=[];

		printColor (n.n."filterTorrentsByMovieYear() *UNDER TESTING* ".n, "white+bold" );
		
		foreach ( $this->torrents as $torrent ){

			//Sample names from 1337x
			// Notes: Die Hard 3 is the same as Die Hard with a Vengeance, but some movies do not include the whole title in torrent name. Hard to catch.
			//Batman vs. Two-Face (2017) [720p] [YTS] [YIFY]
			//[18+] Korean Back affair (2015) WEB-DL HD RIP
			//The.Last.Word.2017.BluRay.1080p.x264.AAC.5.1.-.Hon3y
			//Die.Hard.1988.720p.BluRay.x264-NeZu
			//Die Hard 3 1995 720p BrRip x264 bitloks YIFY
			//Die Hard 1988 1080p BrRip x264 bitloks YIFY
			//Die Hard 3 1995 1080p BrRip x264 bitloks YIFY

			$name=$torrent['name'];
			$movie=$this->title['moviename'];

			$name = strtolower(preg_replace('/[^a-zA-Z0-9]+/', ' ', $name));
			$movie = strtolower(preg_replace('/[^a-zA-Z0-9]+/', ' ', $movie));

			$splitmovie = explode(" ",$movie);

			$matches = array();
			preg_match('(\d{4})', $name, $matches); //find 4 digits // year
			//printColor(n.$movie.n,"white+bold");
			if ( count($matches)===1 ){ //only one year
				//print(n."One match:".n);
				$split = explode($this->title['yearmovie'], $name); //split torrent name by year of the movie
				$leftPart=trim($split[0]);

				print($leftPart);
				print(n.$leftPart ."~=". $movie );
				
				$splitLeft=explode(" ",$leftPart);
				$splitLeft=array_unique($splitLeft); //remove double words// ex: die hard 3 die hard with a vengeance
				$split=array_unique($split); //remove double words from movie title too // ex: the lord of the rings the return of the king  = lord of the rings return of the king
				
				$cl=count($splitLeft);
				$cmv=count($splitmovie);
				
				print ( " ".$cl." ".$cmv." " );

				
				if ( ($cl>=$cmv) && (($cl-$cmv)<=1) ): // torrent name length equal or greater than movie name and word count between one at most
					printColor(" possible match ","white+bold");

					if ($splitLeft[0]==$splitmovie[0]){ // Check first word
						// Match all words from movie name to torrent name left part
						$allWords=false;
						foreach ( $splitmovie as $sp ){
							//print (n."checking: ".$sp.n);
							if ( strpos($leftPart, $sp) !== false ){
								$allWords=true;
							}else {
								$allWords=false;
							}
						}
						if ($allWords){
							printColor ("ok","green");
							array_push($torrents, $torrent );
						} 
						if (!$allWords) printColor ("not","red");
					}
					else{
						printColor("_NOT_","red+bold");
					}

				endif;
				print n.n;
			} //only one year
			else{
				printColor ( n."More than one year in torrnent name. Exit. ","white+bold");
				exit();
			}

		}//foreach torrent
		
		printColor (n."filterTorrentsByMovieYear() *UNDER TESTING* ".n.n, "white+bold" );
		$this->torrents=$torrents;
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


	private function findMovieInDB($folder){ //sets imdb $this->imdb and $this->title
	
		// Match imfbFolder name with imdb code of database to ensure data integrity

		$dbh = dbhandler::getInstance(); 
		$dbConLocal = $dbh->dbCon; 
		if ( !$dbConLocal ) { exit(" \n\n db connection error.\n\n "); }

		$buildQuery="SELECT * FROM imdb.movies_list WHERE 1 AND imdb=:folder LIMIT 10"; // Limit for safety. Normally you should get only one result.
		
		if ( !$stmt = $dbh->dbh->prepare($buildQuery) ) { 
			var_dump ( $dbh->dbh->errorInfo() ); 
		} 
		else {
		
			$stmt->bindParam(':folder', $folder );

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
					$this->title=$result[0]; //set global private variables
					$this->imdb=$result[0]['imdb']; //set global private variables
					$this->imdbFolder=$result[0]['imdb']; //set global private variables
				}else {
					printColor ( n.n."No results or more than one for Movie. This shouldn't happen.".n,"red+bold");
					var_dump($result);
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

		$buildQuery="SELECT * FROM imdb.tvshows_list WHERE 1 AND imdb=:folder LIMIT 10"; // Limit for safety. Normally you should get only one result.
		
		if ( !$stmt = $dbh->dbh->prepare($buildQuery) ) { 
			var_dump ( $dbh->dbh->errorInfo() ); 
		} 
		else {
		
			$stmt->bindParam(':folder', $folder );

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
					$this->title=$result[0];//set global private variables
					$this->imdb=$result[0]['imdb'];//set global private variables
					$this->imdbFolder=$result[0]['imdb'];//set global private variables
				}else {
					printColor ( n.n."No results or more than one. This shouldn't happen.".n,"red+bold");
					exit();
				}
			}		
		}	
	}
}
?>
