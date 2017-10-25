<?php 
class ViewStatsHTML{

	private $dbh;
	private $MODE="HOME"; // $this->MODE="RESULTS"; // $this->MODE="AJAX";
	private $totalSearches=0;
	private $next=1;
	private $perPage=10;
	private $imdb;

	public function __construct(){
		$this->dbh=dbhandler::getInstance();
	}

	public function viewStatsSummary($next,$perPage){

		$this->showSummary($next,$perPage);
	}

	public function viewResults($imdb){	
	
		$this->imdb=$imdb;
		$this->showResults($imdb);
		
	}
	
	//Accepts AJAX MODE | live update
	public function showTotalStats($mode){ //sets $this->totalSearches for global useage

		if ($mode==="AJAX") $this->MODE="AJAX";

		$selectquery ="select count(*) from 1337x.search_summary";
		$stmt = $this->dbh->dbh->prepare($selectquery);
		$stmt->execute();
		$searches = $stmt->fetchColumn();
		$this->totalSearches=$searches;

		$selectquery ="select count(*) from 1337x.search_results";
		$stmt = $this->dbh->dbh->prepare($selectquery);
		$stmt->execute();
		$results = $stmt->fetchColumn();

		if ($this->MODE!=="AJAX") print('<div id="update-stats-cont">'); // update-stats-cont already loaded in HTML page. 
		print('<div id="update-stats">');
		print ('<div class="show-stats"><span "live-update">'.$searches.'</span> searches perfomed for imdb titles.</div>');
		print ('<div class="show-stats"><span "live-update">'.$results.'</span> torrent results with seeds > '.MIN_SEEDS.'</div>');
		print('</div>');
		if ($this->MODE!=="AJAX") print('</div>');		
	}

	private function showSummary($next,$perPage){  //called from public viewStatsSummary()

		if ($this->next!==$next) $this->next=$next;
		if ($this->perPage!==$perPage) $this->perPage=$perPage;
		$next=(int)$this->next;
		$perPage=(int)$this->perPage;
		

		$selectquery ="select * from 1337x.search_summary JOIN imdb.movies_list 
		ON search_summary.imdb=imdb.movies_list.imdb 
		/*AND imdb.movies_list.yearmovie=2014 */ 
		ORDER BY activeTorrents DESC LIMIT :nextResults, :perPage 
		";
		if ( !$stmt = $this->dbh->dbh->prepare($selectquery) ) { 
			var_dump ( $dbh->dbh->errorInfo() );
		} 

		$stmt->bindParam(':nextResults', $next , PDO::PARAM_INT );
		$stmt->bindParam(':perPage', $perPage , PDO::PARAM_INT );
		//$stmt->bindValue(':nextResults', $next , PDO::PARAM_INT );

		if ( $stmt->execute() ) { 

			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if (count($rows)>0):				
				$this->printSummaryTable($rows);
			else:
				print ("No results. Check your query.");
			endif;
		}
		
		var_dump($stmt->errorInfo());
	}

	private function showResults($imdb){
	
		$this->MODE="RESULTS";

		$selectquery ="select * from 1337x.search_summary JOIN imdb.movies_list ON search_summary.imdb=imdb.movies_list.imdb WHERE 1 AND imdb.movies_list.imdb=:imdb";
		if ( !$stmt = $this->dbh->dbh->prepare($selectquery) ) { var_dump ( $dbh->dbh->errorInfo() ); } 
		
		$stmt->bindParam(':imdb', $imdb );

		if ( $stmt->execute() ) {
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$this->printSummaryTable($rows);
		}
		
		//$imdb=str_replace("tt","",$imdb); // remove tt from imdb code
		$selectquery="select * from 1337x.search_results WHERE imdb=:imdb ORDER BY CAST(`seeds` as UNSIGNED) DESC";
		if ( !$stmt = $this->dbh->dbh->prepare($selectquery) ) { var_dump ( $dbh->dbh->errorInfo() ); } 

		$stmt->bindParam(':imdb', $imdb );

		if ( $stmt->execute() ) {
			$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
			if (count($results)>0) $this->printResults($results, "torrents");
			else print ('<h3>No results</h3>');
		}
	}

	// Results page
	private function printResults( $rows , $divClass ){
	
		$getKeys=array_keys($rows[0]);
		
		print ('<div id="folder-'.$this->imdb.'" class="download-torrent-pages">Download torrent HTML pages</div>');
		print ('<pre id="terminal"><div id="download-torrent-results"></div></pre>');
		
		print ('<div class="'.$divClass.'">');
		print ('<h3>Total: '.count($rows).'</h3>');
		print ('<table>');
		print ('<thead><tr>');
		foreach ( $getKeys as $key ){print('<td>');	print($key);print('</td>');}
		print ('</tr></thead>');

		print ('<tbody>');
		$diff=false;		
		foreach ( $rows as $row ){

			if (!$diff){print ('<tr class="result-entry diff" id="'.$row['link'].'">');$diff=true;}
			else if ($diff){print ('<tr class="result-entry" id="'.$row['link'].'">');$diff=false;}
			foreach ( $row as $k=>$v){print('<td>');print($v);print('</td>');}
			/*
				$cells = '<td>'.$row['imdb'].'</td>';
				$cells .= '<td>'.$row['totalPages'].'/'.$row['activePages'].'</td>';
				$cells .= '<td>'.$row['totalTorrents'].'/'.$row['activeTorrents'].'</td>';
				$cells .= '<td>'.$row['last_checked'].'</td>';
				$cells .= '<td>'.$row['category'].'</td>';
				$cells .= '<td>'.$row['id'].'</td>';
				$cells .= '<td>'.$row['moviename'].'</td>';
				$cells .= '<td>'.$row['yearmovie'].'</td>';
				$cells .= '<td>'.$row['rating'].'</td>';
			print $cells;
			*/
			print ('</tr>');			
		}
		print ('</tbody>');
		print ('</table>');
		print ('</div>');
	
	}
	
	// HOME Page
	private function printSummaryTable( $rows ){

		$getKeys=array_keys($rows[0]);
		
		print ('<div class="results">');
		print ('<div class="results-header">');
			print ('<h3>Sowing: '.count($rows).' of '.$this->totalSearches.' title searches.</h3>');
			
			print ('<a href="view-stats.php?next='.($this->next-50).'&perPage=50">-<span class="small-text">50</span></a>');
			print ('<a href="view-stats.php?next='.($this->next-10).'&perPage=10">-prev <span class="small-text">10</span></a>');
			
			print ('<a href="view-stats.php?next='.($this->next+10).'&perPage=10">+next <span class="small-text">10</span></a>');
			print ('<a href="view-stats.php?next='.($this->next+50).'&perPage=50">+ <span class="small-text">50</span></a>');

		print ('</div>');
		print ('<table>');
		print ('<thead><tr>');		
		//foreach ( $getKeys as $key ){print('<td>');	print($key);print('</td>');}
		//imdb	totalPages	activePages	totalTorrents	activeTorrents	last_checked	category	id	moviename	yearmovie	rating	enabled
			$cellsHead='<td>imdb</td>';
			$cellsHead.='<td>moviename</td>';
			$cellsHead.='<td>activePages</td>';
			$cellsHead.='<td>activeTorrents</td>';
			$cellsHead.='<td>checked</td>';
			$cellsHead.='<td>cat</td>';
			$cellsHead.='<td>id</td>';
			$cellsHead.='<td>yearmovie</td>';
			$cellsHead.='<td>rating</td>';
			if ($this->MODE ==='SUMMARY')$cellsHead.='<td>@</td>';
		print ($cellsHead);
		print ('</tr></thead>');

		print ('<tbody>');
		$diff=false;		
		foreach ( $rows as $row ){

			//id="'.$row['imdb'].'"
			if (!$diff){print ('<tr class="sum-entry diff" >');$diff=true;}
			else if ($diff){print ('<tr class="sum-entry" >');$diff=false;}
			//foreach ( $row as $k=>$v){print('<td>');print($v);print('</td>');}
				$last_checked=timeDifference($row['last_checked']);

				$imdbLinkA='<a target="_blank" href="http://www.imdb.com/title/'.$row['imdb'].'/">';
				$cells = '<td class="imdb">'.$imdbLinkA.$row['imdb'].'</a></td>';
				$cells .= '<td class="moviename">'.$row['moviename'].'</td>';
				$cells .= '<td><span class="green-light">'.$row['activePages'].'</span>'.'/ '.$row['totalPages'].'</td>';
				$cells .= '<td><span class="green-light">'.$row['activeTorrents'].'</span>'.'/'.$row['totalTorrents'].'</td>';
				$cells .= '<td>'.$last_checked.'</td>';
				$cells .= '<td>'.$row['category'].'</td>';
				$cells .= '<td>'.$row['id'].'</td>';
				$cells .= '<td>'.$row['yearmovie'].'</td>';
				$cells .= '<td>'.$row['rating'].'</td>';
				if ($this->MODE ==='HOME')$cells .= '<td class="view-results white small-text underline" id="'.$row['imdb'].'">view results</td>';
			print $cells;
			print ('</tr>');			
		}
		print ('</tbody>');
		print ('</table>');
		print ('</div>');
	}
}
?>
