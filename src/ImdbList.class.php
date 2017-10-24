<?php
class ImdbList extends dbhandler{

	// TESTS: test movies with name like Love (2015) or Room (2015) Legend (2015)
	
	// TESTS: Daredevil append Marvel on search

	private $rootList=[];
	
	function __construct() {
	}
   
	public function getMoviesList(){
		//print ("Getting movies list");
		//$selectquery ="SELECT * FROM movies_list WHERE 1 AND enabled=1 AND moviename LIKE '%Lord of the%' LIMIT 100";
		//$selectquery ="SELECT * FROM movies_list WHERE 1 AND enabled=1 AND imdb='tt0167260' LIMIT 100";
		//$selectquery ="SELECT * FROM movies_list WHERE 1 AND enabled=1 AND year=2016 LIMIT 1";
		//$selectquery ="SELECT * FROM movies_list WHERE 1 AND enabled=1 AND `year`=2015 AND moviename LIKE 'The%' LIMIT 100, 500;";
		//select count(*) from movies_list where yearmovie=2015 OR yearmovie=2014;
		//$selectquery ="SELECT * FROM movies_list WHERE 1 AND enabled=1 AND moviename='A Gringo Walks Into a Bar'";
		//$selectquery ="SELECT * FROM imdb.movies_list WHERE 1 AND enabled=1 AND `yearmovie`=2014 OR yearmovie=2015";
		$selectquery ="SELECT * FROM imdb.movies_list WHERE 1 AND enabled=1 AND moviename LIKE '%Lord of the Rings%'";
		//$selectquery ="SELECT * FROM imdb.movies_list WHERE 1 AND enabled=1 AND moviename LIKE '%A Good Day to Die Hard%'";
		$selectquery ="SELECT * FROM imdb.movies_list WHERE 1 AND enabled=1 AND yearmovie='2015' limit 100";
		$selectquery ="SELECT * FROM imdb.movies_list WHERE 1 AND enabled=1 AND yearmovie='2015' AND moviename='The DUFF';";
		$selectquery ="SELECT * FROM imdb.movies_list WHERE 1 AND enabled=1 AND yearmovie='2015' AND moviename='Room';";
		$selectquery = "SELECT * FROM imdb.movies_list WHERE 1 AND enabled=1 AND yearmovie='2015' AND moviename='Love'";
		//$selectquery = "SELECT * FROM imdb.movies_list WHERE 1 AND enabled=1 AND moviename LIKE '%Die Hard with a Vengeance%'";
		//$selectquery = "SELECT * FROM imdb.movies_list WHERE 1 AND enabled=1 AND moviename LIKE '%Die Hard%'";
		//$selectquery ="SELECT * FROM imdb.movies_list WHERE 1 AND enabled=1 AND moviename LIKE '%Lord of the Rings%'";
		//$selectquery ="SELECT * FROM imdb.movies_list WHERE 1 AND enabled=1 AND moviename='The Lord of the Rings: The Return of the King'";
		
		printColor(n.$selectquery.n, "green");
		$dbh = $this->getInstance(); 
		if ( !$stmt = $dbh->dbh->prepare($selectquery) ) { 
			var_dump ( $dbh->dbh->errorInfo() );
		} 
		if ( $stmt->execute() ) { 
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			foreach ($rows as $r ) { array_push( $this->rootList, $r); }
			print ("Movies fecthed from database: ".count($this->rootList).n);
			return $this->rootList;
		}
	}
	
	public function getTvshowsList(){

		$selectquery ="SELECT * FROM imdb.tvshows_list WHERE 1 AND enabled=1 LIMIT 1,1";
		//$selectquery ="SELECT * FROM imdb.tvshows_list WHERE 1 AND enabled=1 AND tvshowname='American Crime Story' LIMIT 1";
		printColor(n.$selectquery.n, "green");

		$dbh = $this->getInstance(); 
		if ( !$stmt = $dbh->dbh->prepare($selectquery) ) { 
			var_dump ( $dbh->dbh->errorInfo() );
		} 
		if ( $stmt->execute() ) { 
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			foreach ($rows as $r ) { array_push( $this->rootList, $r); }
			print ("TvShows fecthed from database: ".count($this->rootList).n);
			return $this->rootList;
		}	
	}
	
}
?>
