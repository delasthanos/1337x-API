<?php 
class SaveTorrents1337x{

	private $dbh;
	private $JSON;
	private $title;
	private $imdb;
	private $summary_id;
	
	public function __construct(){
	
		$dbh = dbhandler::getInstance(); 
		$dbConLocal = $dbh->dbCon; 
		if ( !$dbConLocal ) { exit(" \n\n db connection error.\n\n "); }
		$this->dbh=$dbh;
	}

	public function downloadTorrents($title){

		/*
		*	Notes: 
		*	Collect torrents for $title using ParseSearch1337x class.
		*   Download torrent files and save them to local path for processing using DownloadHTMLTorrentPage
		*/
		printColor (n."\t[!]Class::SaveTorrents1337x()::downloadTorrents(imdbtitle)".n.n,"green+bold");
		$folder=$title['imdb'];
		$findTorrents=new ParseSearch1337x($title['imdb']);

		//Collect torrents from db
		$torrents = $findTorrents->collectTorrentsFromDB(); // Final array with torrent info for each folder
		$this->summary_id = $findTorrents->getSummaryId();
		if ( is_array($torrents) ){print (n."\t[*]Found: ".count($torrents)." torrents." );}
		else exit(n.n."Inside SaveTorrents1337x torrents is not array. Exit".n.n);
		
		// Download Torrent Pages HTML
		$countDownloaded=0;
		
		if (count($torrents)>0):
			foreach ($torrents as $torrent):
				
				$downloadHTMLTorrent= new DownloadHTMLTorrentPage( $folder, $torrent ); //$folder==imdb, $t==torrent Array
				$downloadHTMLTorrent->downloadTorrentHTMLPage();
				unset($downloadHTMLTorrent);
				
				if (++$countDownloaded>TORRENTS_DOWNLOAD_LIMIT){ printColor(n."Break after 5 torrent pages. Still testing.".n,"red+bold"); break; }

			endforeach; // Foreach torrent
		endif;
		
	}
	
	//  Parse torrent HTML files, Save JSON file, Set class variable to prepare for the import ( $JSON, $title )
	public function createJSON($title){ //requires $summary_id from downloadTorrents()
	
		$folderName=$title['imdb'];
		$torrents=[];
		$folder=HTML_TORRENTS_FILES_PATH;
		if (!file_exists($folder)){ exit("No html files found.Please run search first."); }
		$folders=array_diff( scandir($folder), array('.','..'));

		//foreach ($folders as $folderName ):

		if (file_exists(HTML_TORRENTS_FILES_PATH."/".$folderName."/")):

		$collectTorrents=[];
		$files=array_diff( scandir(HTML_TORRENTS_FILES_PATH."/".$folderName."/"), array('.','..'));
		
		if (count($files)>0):
			foreach ($files as $f ):

				$parseTorrent = new ParseTorrentPage(HTML_TORRENTS_FILES_PATH."/".$folderName."/".$f);
				$torrent = $parseTorrent->parseTorrentPage();
				array_push($collectTorrents, $torrent);

			endforeach;
	
			// Prepare to save JSON and private variables in this class | Required to finally import torrent into db.
			// Json Master keys
			$torrents['imdb'] = $folderName;
			$torrents['search_summary_id'] = $this->summary_id;
			$torrents['torrents'] = $collectTorrents;

			$this->title=$title;
			$this->imdb=$title['imdb'];
			$json=json_encode($torrents);
			$this->JSON=$json;
			if (!file_exists(JSON_FILE_PATH)) mkdir(JSON_FILE_PATH, 0777, true);
			file_put_contents(JSON_FILE_PATH.'/'.$folderName,  $json);
		endif;
		endif;//filexists
		//endforeach; // foreach folders
	}
	
	public function saveTorrents(){

		printColor (n."\t[!]Class::SaveTorrents1337x()::saveTorrents()".n.n,"green+bold");

		//var_dump($this->title);
		//var_dump($this->JSON);
		
		//Warning check for double entries here ex: Love
		//$select='select * from search_summary join search_results on search_summary.imdb=search_results.imdb WHERE search_summary.imdb="tt0337978";';
		//$select="select * from 1337x.search_summary join 1337x.search_results on search_summary.id=search_results.summary_id WHERE search_summary.imdb='tt0337978'";
		$select="select * from 1337x.search_summary join 1337x.search_results on search_summary.id=search_results.summary_id WHERE search_summary.imdb=:imdb";
		
		$summary_id=-1;
		$getSummaryId="SELECT id FROM 1337x.search_summary WHERE imdb=:imdb"; 
		if ( !$stmt = $this->dbh->dbh->prepare($select) ) { var_dump ( $dbh->dbh->errorInfo() ); } 
		else{
			$stmt->bindParam(':imdb', $this->imdb );
			if (!$stmt->execute() ){ var_dump( $stmt->errorInfo() ); exit(n.n."error inside saveTorrents()".n); }
			else { $search_results = $stmt->fetchAll(PDO::FETCH_ASSOC); }
		}
		
		$torrents=json_decode($this->JSON,true);
		$imdb=$this->imdb;
		print (n.n."Torrents from JSON are ".count($torrents['torrents']).n.n );
		
		//var_dump($torrents);
		foreach ($torrents['torrents'] as $t):
		
			//print_r($t);
/*
			$parameters=array(
				':1337x_id' => $t['1337x_id'],
				':category' => 1,
				':titlename' => $t['title'],
				':hash' => $t['hash'],
				':format_type' =>  $t['details']['Type'],
				':language' => $t['details']['Language'],
				':size' => $t['details']['Total-size'],
				':seeds' => $t['details']['Seeders'],
				':leeches' => $t['details']['Leechers'],
				':uploader' => $t['details']['Uploaded-By'],
				':uploaded' => $t['details']['Date-uploaded'],
				':checked' => $t['details']['Last-checked'],
				':downloads' => $t['details']['Downloads'],
				':links' => $t['links'],
				':images' => $t['images'],
				':imdbmatch' => 0
			);
*/
			$links=serialize($t['links']);
			$images=serialize($t['images']);
			$parameters=array(
				':1337x_id' => $t['1337x_id'],
				':category' => 1,
				':titlename' => $t['title'],
				':hash' => $t['hash'],
				':format_type' =>  $t['details']['Type'],
				':language' => $t['details']['Language'],
				':size' => $t['details']['Total-size'],
				':seeds' => $t['details']['Seeders'],
				':leeches' => $t['details']['Leechers'],
				':uploader' => $t['details']['Uploaded-By'],
				':uploaded' => $t['details']['Date-uploaded'],
				':checked' => $t['details']['Last-checked'],
				':downloads' => $t['details']['Downloads'],
				':links' => $links,
				':images' => $images,
				':imdbmatch' => 0
			);
			
			//print_r($parameters);
			$insert="INSERT INTO 1337x.1337xtorrents 
			( 1337x_id,category,titlename,hash,format_type,language,size,seeds,leeches,uploader,uploaded,checked,downloads,links,images,imdbmatch  ) 
			VALUES 
			(:1337x_id,:category,:titlename,:hash,:format_type,:language,:size,:seeds,:leeches,:uploader,:uploaded,:checked,:downloads,:links,:images,:imdbmatch )";

			

			if ( !$stmt = $this->dbh->dbh->prepare($insert) ) { var_dump ( $dbh->dbh->errorInfo() ); } 
			else { 
	
				if (!$stmt->execute($parameters) ){
					if ( in_array( 1062, $stmt->errorInfo() ) ){ 
						//return 1062;
						printColor (n."1062: Exists".n, "yellow");
					}
					else { return $stmt->errorInfo(); }
				} 
				else {
					print("ok"); 
					//return 1; 
				}
			}	


		
		endforeach;

		//if ( count($search_results)>0 ):
		//endif;

	}
}
?>
