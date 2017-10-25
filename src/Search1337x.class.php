<?php 
class Search1337x{

	// Factory to use SearchResults1337x for search results
	// 1. Search 1337x by title (Movie, Tv) :-> SearchResults1337x.class
	// 2. Parse search results, collect torrent links and save them to database :-> ParseSearch1337x.class, SearchResults.class

	public function search($title){
	
		// Search according to title
		$search = new SearchResults1337x();
		$status=$search->checkTitleInSearchSummary($title['imdb']); // 6 days 
		// Already searched. 
		if ($status['exists']===true):

			print(n."[*]Title ".$title['imdb']." already searched.");
			print ($status['text']);

		// Search.
		else:
		
			$search->searchForTitles($title);
		
			printColor (n."[*]Collecting data from results to save into database ...","white+bold"); //sleep(1);
			print (n."Search summary: Active Pages/Total Pages=(".$search->getActivePages()."/".$search->getTotalPages().")");

			// Parse Search Results
			$findTorrents=new ParseSearch1337x($title['imdb']); //Pass imdb folder name containing search results pages
			$findTorrents->collectSearchResultsHTML();
			$torrents = $findTorrents->collectTorrentsFromHTML(); // Final array with torrent info for each folder

			// UNDER TESTING | Match all words between torrent name and movie name. Testing failed.
			// Lots of conditions to take into consideration. Maybe be strict results for titles with one word, and be more resilient with longer movie titles
			//if (CATEGORY=="Movies"): $torrents = $findTorrents->filterTorrentsByMovieYear(); endif;

			// Save Search Results
			// $imdb,$totalPages,$activePages,$totalTorrents,$activeTorrents
			
			$searchResults=new SaveSearchResults( $title['imdb'], $search->getTotalPages(), $search->getActivePages(), $findTorrents->findTotalTorrents(), $findTorrents->findActiveTorrents(), $findTorrents->getActiveTorrents() );
			if ( $searchResults->saveSearchSummary() ):

				printColor ( n."[*]Saved search summary to database.","green" );
				printColor ( n."[*]Saving torrents search results...","yellow" );
				$searchResults->saveSearchResultsTorrents();
				printColor ( n."[*]Done".n.n,"white+bold" );

			endif;
			sleep(WAIT_SECONDS);
		endif;

	}
}
?>
