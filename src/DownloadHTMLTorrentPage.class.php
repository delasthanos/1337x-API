<?php 
class DownloadHTMLTorrentPage{

	private $imdbFolderName;
	private $imdbFolderPath;
	private $torrent;
	private $torrentid;

	public function __construct($folder,$torrent){

		$this->imdbFolderName=$folder;
		$this->torrent=$torrent;

	}

	public function downloadTorrentHTMLPage(){
	
		$this->createHTMLTorrentFolder($this->imdbFolderName);
		$this->donwloadTorrentPage($this->torrent);
	}

	private function donwloadTorrentPage($torrent){
	
		print (n."donwloadTorrentPage(): ");
		$split=explode("/",$torrent['link']);
		
		// Check if $torrent is from database or directly from HTML
		if (count($split)===1){ // link read from database
	
			$this->torrentid=$torrent['1337x_id'];
			$url=$this->constructTorrentPageURL('/torrent/'.$torrent['1337x_id'].'/'.$torrent['link'].'/');	
		
		}else { // link read directly from HTML parse

			$this->torrentid=$split[2]; // 1337x torrent id from link to create HTML filename
			$url=$this->constructTorrentPageURL($torrent['link']);
		}


		$destination=$this->constructHTMLFilename();

		if (!file_exists($destination)){
		
			sleep(WAIT_SECONDS_RESULTS);
		
			if ( getHtmlFile( $url, $destination ) ){
				printColor (" [*HTML]Donwload finised: ","green+bold");
				printColor( $url, "white+bold" );
				$this->HTML=$destination;
				return true;
			} else {
				printColor ("\n[*HTML]Error retrieving HTML file.","red+bold");
				return false;
			}
		}else {
			printColor ("[*HTML]File exists. Ommiting: ".$url,"yellow");
			$this->HTML=$destination;
			return true;
		}
	
	}

	private function createHTMLTorrentFolder($folder){

		// Refactoring: save torrents out of imdb folder
		//$filename = HTML_TORRENTS_FILES_PATH."/".$folder;
		$filename = HTML_TORRENTS_FILES_PATH."/";
		if ( !file_exists($filename) ) {
			$mkdir = mkdir( $filename, 0777, true); 
			//if ( !$mkdir ) { print ("\nError creating root folder.\n".TVI_DATA_PATH.$current_series_name."\n\n" ); exit(); }
		}
		$this->imdbFolderPath=$filename;
	}
	
	private function constructTorrentPageURL($torrentURL){ return TORRENT_URL_PREFIX.$torrentURL; }
	// Refactoring: save torrents out of imdb folder
	//private function constructHTMLFilename(){ return HTML_TORRENTS_FILES_PATH."/".$this->imdbFolderName."/".$this->torrentid; }
	private function constructHTMLFilename(){ return HTML_TORRENTS_FILES_PATH."/".$this->torrentid; }	
}
?>
