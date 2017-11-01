<?php
class ParseTorrentPage{
/*
 * Parse HTML page for a torrent. Final stage of parsing.
 * Collect information: 

	-Torrent title
	- Header Table Information:
		[Category] => Movies
		[Type] => HD
		[Language] => English
		[Total-size] => 3.1 GB
		[Uploaded-By] =>  594mgnav
		[Downloads] => 8595
		[Last-checked] => 2 days ago
		[Date-uploaded] => 2 years ago
		[Seeders] => 30
		[Leechers] => 9
	- Download links and maybe magnet link.
	-INFOHASH
	-Imdb information
	-Screenshots
*/	
	
	private $file;
	private $imdb;
	private $torrent=[];

	public function __construct( $file, $imdb ){
		
		$this->file=$file;
		//$split=explode('/',$file);
		//array_pop($split);
		//$this->imdb=array_pop($split);
		$this->imdb=$imdb;
	}
	
	public function parseTorrentPage(){

		// Populate those:
		$title="";
		$detailsHeader=[];
		$torrentLinks=[];
		$torrentHash="";
		$images=[];
		
		$getFile=file_get_contents($this->file);
		
		$dom = new DOMDocument();
		@$dom->loadHTML($getFile);
		$xPath = new DOMXPath($dom);
		
		$splitFileName=explode("/",$this->file);
		$get1337xidFromFolder=array_pop($splitFileName);

		$this->torrent['1337x_id'] = $get1337xidFromFolder;		
		$this->torrent['title'] = $this->getTitle( $xPath );
		if ( $this->matchImdb($getFile) ){
			$this->torrent['imdbMatch'] = $this->imdb;
		} else {
			$this->torrent['imdbMatch'] = "not found";
		}
		//print (n.$title);
		
		$elements = $xPath->query('//div[contains(@class,"torrent-detail-page")]');
		foreach ( $elements as $element):

			$this->torrent['details']=$this->torrentDetailsHeader( $xPath, $element );
			//$detailsHeader = $this->torrentDetailsHeader( $xPath, $element );
			//printColor(n."[*]Details Header:".n,"white+bold");print_r ($detailsHeader);
			
			$this->torrent['links']=$this->torrentDownloadLinks( $xPath, $element );
			//$torrentLinks = $this->torrentDownloadLinks( $xPath, $element );
			//printColor(n."[*]Download Links:".n,"white+bold");print_r($torrentLinks);

			$this->torrent['hash']=$this->torrentHash( $xPath, $element );
			//$torrentHash= $this->torrentHash( $xPath, $element );
			//printColor(n."[*]Hash:".n,"white+bold");//print ($torrentHash);
			
			$this->torrent['images']=$this->torrentImages( $xPath, $element );
			//$images = $this->torrentImages( $xPath, $element );
			//printColor(n."[*]Images:".n,"white+bold");//print_r ($images);

		endforeach;
		
		return $this->torrent;
	}
	
	private function matchImdb($htmlFile){
		if ( strpos( $htmlFile, $this->imdb ) !== false ){
			printColor ( "IMDB", "green+bold" );
			return true;
		} else {
			printColor ( "IMDB", "red+bold" );
			return false;
		}
	}

	private function getTitle( $x ){
		$titles=$x->query( '//title' );
		foreach( $titles as $title ){
			//var_dump($title->nodeValue);
			return $title->nodeValue;
		}
	}
	
	private function torrentDetailsHeader( $x, $e ){
	
		$details=[];
		//Parse top details with class .torrent-category-detail
		$detailsHeader =$x->query( '//div[contains(@class,"torrent-category-detail")]/ul[@class="list"]/li', $e);
		foreach( $detailsHeader as $li ):

			//var_dump($li->nodeValue);

			$getKey=$x->query( 'strong', $li);
			$getValue=$x->query( 'span', $li);
			
			foreach ($getKey as $g ) $key = str_replace(" ","-", $g->nodeValue);
			foreach ($getValue as $v ) $value = $v->nodeValue;
			
			$details[$key]=$value;

		endforeach;

		return $details;
	}
	
	private function torrentDownloadLinks( $x, $e ){
	
		$torrentLinks=[];
		
		// Magnet Link 
		$detailsHeader=$x->query( '//div[contains(@class,"torrent-category-detail")]/ul[contains( @class, "download-links-dontblock")]/li/a/@href', $e);
		foreach( $detailsHeader as $li ):

			//var_dump($li->nodeValue);
			$link = $li->nodeValue;
			$split = explode(":",$link);
			if ( $split[0]=='magnet' ){
				//print ($li->nodeValue);
				$torrentLinks['magnet']=$li->nodeValue;
				$magnet1=$li->nodeValue;
			}
		endforeach;

		$downloadLinks=[];
		$detailsHeader=$x->query( '//div[contains(@class,"torrent-category-detail")]/ul[contains( @class, "download-links-dontblock")]/li[@class="dropdown"]/ul[@class="dropdown-menu"]/li/a/@href', $e);
		foreach( $detailsHeader as $li ):
		
			$split = explode(":", $li->nodeValue);
			if ( $split[0]=='magnet' ){
				$magnet2=$li->nodeValue;
			}
			array_push($downloadLinks, $li->nodeValue);

		endforeach;

		//if ( $magnet1 === $magnet2 ){print ( n.n.n." MAGNETS ARE THE SAME".n.n.n );}

		$torrentLinks['downloadLinks']=$downloadLinks;

		return $torrentLinks;
	}

	private function torrentHash( $x, $e ){
	
		// Class div .infohash-box
		$findHash=$x->query( '//div[@class="infohash-box"]/p/span', $e);
		foreach ($findHash as $node){
			//var_dump( $node->nodeValue );
			return $node->nodeValue;
		}
	}
	
	private function torrentImages( $x, $e ){

		//img .descrimg
		$images=[];
		$findImages=$x->query( '//img[contains(@class,"descrimg")]/@data-original', $e);
		foreach ($findImages as $node){
			//var_dump( $node->nodeValue );
			//return $node->nodeValue;
			array_push($images, $node->nodeValue);
		}

		return $images;
	}
}
?>
