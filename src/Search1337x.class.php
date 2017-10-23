<?php 
class Search1337x extends Search1337xHelperFunctions{

	private $page=1;
	private $totalPages=0;
	private $activePages=0;
	private $titlename; // Clean title name to search| Remove special chars | Allow only spaces
	private $titlenameOriginal; // Original title name | Straight from Database
	private $filename; // Clean filename for folder & HTML files. Remove special chars | Replace spaces with underscores
	//private $URL; //seted by constructURLFromTitle each time
	private $HTML; // saved results page complete path 
	private $noResults=false;
	private $banned=false;
	
	public function getTotalPages(){
		return $this->totalPages;
	}

	public function getActivePages(){
		return $this->activePages;
	}
	

	public function searchForTitles($title){

		if (!is_array($title)){
			print ("\n\n[!]ERROR: searchForTitles() function accepts only an array as an argument.\n");
			return false;
		}
		// Process: Get first results page and parse it to count total search results pages. 
		// Prepare data: titlename, foldername, create folder 
		// SET 


		switch (CATEGORY):
			case ('Movies'):
				$this->titlenameOriginal = $title['moviename'];
				$this->settitlename($title['moviename']);
				$this->setFilename($title['imdb']);
				break;
			case ('TV'):
				$this->titlenameOriginal = $title['tvshowname'];
				$this->settitlename($title['tvshowname']);
				$this->setFilename($title['imdb']);
				break;
		endswitch;

		// Prepare | Create folder for Title. Folder is named with imdb code
		$this->createTitleFolder($this->filename);
		
//exit(n."Breakpoint in class".n);

		// Get first page to check for results and number of total pages
		printColor ("\nFirst Page ".($this->page).": ","white+bold");
		if ($this->getResultsPage() ){

			$this->findTotalPages($this->HTML);
			++$this->activePages;
			
			if ($this->noResults) { print ("\n[*]No results."); }
			if ($this->banned) { print ("\n\n\n----------->[!]WARNING. IP has been banned. Shit! You must get a new server now!\n\n\n"); exit("\n\n Emergency exit\n\n");}		
			printColor ("\n[*]Total pages found: ".$this->totalPages." for : ".$this->titlenameOriginal, "white+bold");

			// Loop to get the rest search results pages.
			while ( (++$this->page <= $this->totalPages) && ($this->totalPages>0) ){

				print ("\nPage ".($this->page).": " );
				$this->getResultsPage();
				++$this->activePages;

				// Break: Stop saving HTML pages on MIN_SEEDS or MAX_RESULTS_PAGES
				if ( !$this->checkResultsForSeeds($this->HTML) ){ break; }
				if ( $this->page > MAX_RESULTS_PAGES ){ printColor (n."[!]Reached MAX_RESULTS_PAGES: ".MAX_RESULTS_PAGES.". Break.".n, "red+bold"); break; }
			}
		}

	}
	
	private function getResultsPage(){
	
		// Note: If file exists don't download it again to avoid making noise on their server.

		$url=$this->constructURLFromTitle();
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
	
	private function findTotalPages($destination){ // Search for lst button

		// Parse DOM with Xpath
		// Link format : "/category-search/game+of+thrones/TV/50/" <- This is what we get here (50)
		$searchPageLink=[];
		$getLocal = file_get_contents($destination);
		$dom = new DOMDocument();
		@$dom->loadHTML($getLocal);
		// run xpath for the dom
		$xPath = new DOMXPath($dom);
		$elements = $xPath->query("//a/@href");
		foreach($elements as $element) {

			$link=$element->value;
			$split = explode("/", $link );
			if ( count($split) > 4 && $split[1]=='category-search'){
				array_push($searchPageLink, $link);
			}
		}
		$count = count($searchPageLink);
		if ( $count > 0 ){
			$split = explode( "/", $searchPageLink[$count-1]);
			array_pop($split);
			$pages = array_pop($split);
			$this->totalPages=$pages;
		} else {
			$this->totalPages=0;
			// Check for string in HTML file: "No results were returned. Please refine your search."
			if (stripos($getLocal, 'No results') !== false) {
				$this->noResults=true;
			}

			if (stripos($getLocal, 'has banned your IP address') !== false) {
				$this->banned=true;
			}				
		}
	}

	private function constructURLFromTitle(){
		return SEARCH_URL_START.urlencode($this->titlename).SEARCH_URL_END.$this->page."/";
	}

	protected function constructHTMLFilename(){
		return HTML_SEARCH_FILES_PATH."/".$this->filename."/".$this->filename."_".$this->page; // to save HTML search results page
	}

	private function createTitleFolder($filename){

		$filename = HTML_SEARCH_FILES_PATH."/".$filename;
		if ( !file_exists($filename) ) {
			$mkdir = mkdir( $filename, 0777, true); 
			//if ( !$mkdir ) { print ("\nError creating root folder.\n".TVI_DATA_PATH.$current_series_name."\n\n" ); exit(); }
		}
	}

	private function deleteMovieFolder($filename){

		$filename = "html/".$filename;
		if ( !file_exists($filename) ) {
			$mkdir = mkdir( $filename, 0777, true); 
			//if ( !$mkdir ) { print ("\nError creating root folder.\n".TVI_DATA_PATH.$current_series_name."\n\n" ); exit(); }
		}
	}

	private function settitlename($title){ $this->titlename=removeSpecialChars($title); }
	private function setFilename($title){ $this->filename=spacesToUnderscores(removeSpecialChars($title)); }
}
?>
