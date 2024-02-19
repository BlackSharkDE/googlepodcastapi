<?php
/*
 * Gilt für die Google-Podcasts-Seite Stand 11.12.2023
 * Parameter:
 * --> feedid  => In der Podcast-URL zwischen "/feed/" und "?sa"
 * --> episode => Track- / Episodennummer auf der Feed-Seite ;  1 = Episode 1, 2 = Episode 2 ...
 * Rückgabe: JSON
 */

//----------------------------------------------------------------------------------------------------------------------------------------

class Podcast {

	//Alles auf "public" da keine gefährlichen Daten und diese außerhalb der Klasse gebraucht werden

	public string $feedId;            //Per API übergebene Feed-ID
	public string $computedFeedUrl;   //URL zum Feed (Google-Podcasts)
	public bool $urlReachable;        //Ob die "computedFeedUrl" erreichbar ist
	public DOMDocument $feedDocument; //Enthält die gedownloadete Feed-Seite als DOMDocument
	
	public array $podcastData;        //Rausgefiltertes Array mit den wichtigsten Daten zum Podcast
	
	public string $name;              //Name des Podcasts
	public string $publisher;         //Publisher des Podcasts
	public string $description;       //Beschreibung des Podcasts
	public string $coverUrl;          //URL zum Cover des Podcasts
	
	public array $episodes;           //Enthält alle Podcastepisoden
	public int $episodeCount;         //Anzahl der Episoden
	public int $maxEpisodeIndex;      //Maximaler Index für Episoden

	/**
     * -- Konstruktor -- 
     * @param string Feed-ID
     */
	public function __construct(string $fid) {
		$this->feedId = $fid;
		$this->computedFeedUrl = "https://podcasts.google.com/feed/" . $this->feedId;
		$this->urlReachable = $this->isUrlReachable();
	}

	/**
	 * Testet, ob die "computedFeedUrl" erreichbar ist.
	 * @return bool True, wenn ja / False, wenn nein
	 */
	private function isUrlReachable() {

		//Für URL-Status
		$headers = get_headers($this->computedFeedUrl);
		
		//Checke, ob die URL existiert / Google Podcasts antwortet
		if($headers && strpos($headers[0], '200')) {
			return true; 
		}

		return false;
	}

	/**
	 * Erstellt ein DOMDocument und weist es dem "feedDocument"-Attribut zu. (void - Funktion)
	 */
	public function createFeedDocument() {

		//Download der Podcast-Seite (Rohes HTML als String)
		$feedHtml = file_get_contents($this->computedFeedUrl,"r");
		
		//-- Unicode-Probleme beheben --
		//--> Beispielsweise "\\u003d" (Unicode), welches durch das auf der Podcast-Seite befindliche GSON verursacht wird

		//Mittels Regex den Unicode-Code in HTML-Entities umwandeln
		$feedHtml = preg_replace("/\\\\u([0-9a-fA-F]{4})/", "&#x\\1;", $feedHtml);

		//Die HTML-Entities in echte Zeichen umwandeln
		$feedHtml = html_entity_decode($feedHtml, ENT_QUOTES, 'UTF-8');

		//var_dump($feedHtml); //DEBUG
	
		//-- Konvertiere in DOMDocument --
		libxml_use_internal_errors(true); //Wegen HTML5
		$this->feedDocument = new DOMDocument(); //Neues DOMDocument
		$this->feedDocument->loadHTML('<?xml encoding="utf-8" ?>' . $feedHtml); //HTML-String in Dokument einlesen -> mit UTF-8 (muss man hier irgendwie extra mit XML angeben)
		libxml_clear_errors(); //Wegen HTML5
		//var_dump($this->feedDocument->textContent); //DEBUG
	}

	/**
	 * Filtert aus "feedDocument" die Daten für den Podcast heraus. (void - Funktion)
	 */
	public function setPodcastData() {
		
		//Suche nach bestimmtem <script>-Tag mit XPath
		$xpath = new DomXPath($this->feedDocument); //Neuer XPath mit HTML-Dokument
		$nodes = $xpath->query("//script"); //Alle <script>-Tags
		/*
		//DEBUG -- Ausgabe der Skript-Tags
		for($i = 0; $i < count($nodes); $i++) {
			echo("<p>-- Index = " . $i . " --</p>");
			var_dump($nodes[$i]);
			echo("<br><hr>");
		}
		*/
		$element = $nodes[16]; //Das <script>-Element mit allen Episoden

		//Rohes HTML im <script>-Tag (String) entnehmen
		$js = $element->textContent;

		//Ummantelung durch 'AF_initDataCallback(' am Anfang und ')' am Ende loswerden
		$js = substr($js,strpos($js,'AF_initDataCallback(') + 20); //+20 wegen dem String selbst
		$js = substr($js,0,strrpos($js,'})') + 1); //Sodass nur bis '}' geht und damit ')' entfällt

		//Standardmäßig sind in diesem Fall die Keys im JSON ohne "" angegeben
		//--> Benutze dafür anderen JSON-Parser
		//var_dump($js); //DEBUG - Vorher
		require './php_loose_json_decode/variant_1_modified.php';
		$js = loose_json_decode($js);
		
		//DEBUG - Falls bei der Verarbeitung des JSON Fehler aufgetreten sind
		//var_dump(json_last_error());
		//var_dump(json_last_error_msg());

		//var_dump($js); //DEBUG - Nachher
		
		//Daten des Podcast sind im "data"-Teil.
		if(is_array($js) && array_key_exists("data",$js) && count($js["data"]) > 1) {
			//"data"-Teil sollte ok sein
			$this->podcastData = $js["data"];
		} else {
			//Leeres Array für weitere Fehlerbehandlung
			$this->podcastData = array();
		}
		//var_dump($this->podcastData); //DEBUG
	}

	/**
	 * Setzt Beschreibungsattribute "name", "publisher", "description" und "coverUrl". (void - Funktion)
	 */
	public function setPodcastMeta() {
		//var_dump($this->podcastData[3]); //DEBUG - Hier sollten die nachfolgenden Attribute enthalten sein
		$this->name        = strval($this->podcastData[3][0]);
		$this->publisher   = strval($this->podcastData[3][1]);
		$this->description = strval($this->podcastData[3][2]);
		$this->coverUrl    = strval($this->podcastData[3][16][0]);
	}

	/**
	 * Setzt das Attribut "episodes", welches die Podcast-Episoden beinhaltet. (void - Funktion)
	 */
	public function setPodcastEpisodes() {
		//Die Podcast-Episoden (Arrays) sollten hier enthalten sein
		//--> Array umkehren, sodass das älteste Element Index 0 und das neueste Element der letzte Index ist
		$this->episodes = array_reverse($this->podcastData[1][0]);
		//var_dump($this->episodes[0]); //DEBUG - Erste Episode des Podcasts
		
		//Anzahl der Episoden
		$this->episodeCount = count($this->episodes);

		//Maximaler Index
		$this->maxEpisodeIndex = $this->episodeCount;
	}
}

class Episode {

	public string $title;         //Titel der Episode (Nicht Dateinamen-angepasst)
	public string $description;   //Beschreibung der Episode
	public int $releaseTimestamp; //Unix-Timestamp des Veröffentlichungsdatums
	public int $duration;         //Dauer der Episode in Sekunden
	public string $downloadUrl;   //Download-URL der Episode

	/**
     * -- Konstruktor -- 
     * @param array Ein Eintrag aus dem "Podcast->episodes"-Array
     */
	public function __construct(array $podcastArray) {
		$this->title            = $podcastArray[8];
		$this->description      = $podcastArray[9];
		$this->releaseTimestamp = $podcastArray[11]; //DEBUG für Datumsausgabe: date("Y-m-d H:i:s",$this->releaseTimestamp);
		$this->duration         = $podcastArray[12];
		$this->downloadUrl      = $podcastArray[13];
	}
}

//----------------------------------------------------------------------------------------------------------------------------------------

//Rückgabe-Array (assoziativ)
$returnArray = array();

//Parameter standardmäßig False, werden durch nachfolgende IFs überprüft
$feedId = false;
$episode = false;

if(isset($_GET["feedid"]) && isset($_GET["episode"])) {
	
	//Zwischenspeicher der URL-Parameter
	$temporary_fid = $_GET["feedid"];
	$temporary_epn = $_GET["episode"];

	//Teste "feedid"
	if($temporary_fid !== "" && !is_null($temporary_fid) && ctype_alnum($temporary_fid)) {
		//echo "Feed ok\n"; //DEBUG
		$feedId = strval($_GET["feedid"]); //Immer als String
	}

	//Teste "episode"
	if($temporary_epn !== "" && !is_null($temporary_epn) && is_numeric($temporary_epn)) {
		//echo "Episode ok\n"; //DEBUG
		$episode = intval($_GET["episode"]); //Immer als Integer
	}
	
	//DEBUG - Werte für GET-Parameter
	//$feedId = "aHR0cHM6Ly93d3cudGhlZ3VhcmRpYW4uY29tL3NjaWVuY2Uvc2VyaWVzL3NjaWVuY2UvcG9kY2FzdC54bWw";
	//$feedId = "aHR0cHM6Ly93d3cucmFkaW9laW5zLmRlL2FyY2hpdi9wb2RjYXN0L2RpZV9ibGF1ZV9zdHVuZGUueG1sL2ZlZWQ9cG9kY2FzdC54bWw";
	//$feedId = "aHR0cHM6Ly9nYW1lc3Rhci5saWJzeW4uY29tL3Jzcw";
	//$episode = 26;
	//$episode = 1;
	
	//Informationen für Validierung
	$returnArray += ["givenfeedid" => $feedId];   //URL zum Feed
	$returnArray += ["givenepisode" => $episode]; //Episodenindex
	
	//Wenn beide Parameter an sich ok sind (sind noch nicht gültig, aber valide)
	if($feedId !== false && $episode !== false) {

		//Podcast-Objekt erstellen
		$pO = new Podcast($feedId);
		
		//Nur wenn URL erreichbar ist
		if($pO->urlReachable) {
			
			$pO->createFeedDocument();

			if($pO->feedDocument !== false && strlen($pO->feedDocument->textContent) > 0) {
				
				$pO->setPodcastData();

				if(count($pO->podcastData) > 0) {
					
					$pO->setPodcastMeta();
					$pO->setPodcastEpisodes();
					
					if(!is_null($pO->episodes) && is_array($pO->episodes) && count($pO->episodes) > 0) {

						if($episode >= 1 && $episode <= $pO->maxEpisodeIndex) {
							
							//Episoden-Objekt erstellen
							$eO = new Episode($pO->episodes[$episode - 1]);

							//-- Erstellt positive API-Antwort --

							//Podcast-Daten
							$returnArray += ["computedFeedUrl" => $pO->computedFeedUrl];
							$returnArray += ["podcastName" => $pO->name];
							$returnArray += ["podcastPublisher" => $pO->publisher];
							$returnArray += ["podcastDescription" => $pO->description];
							$returnArray += ["podcastCoverUrl" => $pO->coverUrl];
							$returnArray += ["maxEpisodeIndex" => $pO->maxEpisodeIndex];
							
							//Episoden-Daten
							$returnArray += ["episodeTitle" => $eO->title];
							$returnArray += ["episodeDescription" => $eO->description];
							$returnArray += ["episodeReleaseTimestamp" => $eO->releaseTimestamp];
							$returnArray += ["episodeDuration" => $eO->duration];
							$returnArray += ["episodeDownloadUrl" => $eO->downloadUrl];

							$returnArray += ["success" => true];

						} else {
							//Unpassender Episodenindex
							$returnArray += ["success" => false];
							$returnArray += ["reason" => "Episode index out of bounds (1 - " . $pO->maxEpisodeIndex . ")"];
						}

					} else {
						//Keine Episoden gefunden
						$returnArray += ["success" => false];
						$returnArray += ["reason" => "No episodes found (might be internal error)"];
					}

				} else {
					//Daten konnten nicht richtig isoliert werden
					$returnArray += ["success" => false];
					$returnArray += ["reason" => "Could not find proper podcast data. Maybe wrong feedid-parameter or internal parsing error"];
				}

			} else {
				//Das "feedDocument" wurde nicht richtig erstellt
				$returnArray += ["success" => false];
				$returnArray += ["reason" => "Could not properly create feedDocument (internal error)"];
			}
			
		} else {
			//URL nicht ok
			$returnArray += ["success" => false];
			$returnArray += ["reason" => "URL not working or reachable"];
		}

	} else {
		//Übergabeparameter falsch
		$returnArray += ["success" => false];
		$returnArray += ["reason" => "Parameter values wrong"];
	}
	
} else {
	//Übergabeparameter nicht angegeben
	$returnArray += ["success" => false];
	$returnArray += ["reason" => "No or wrong parameters given"];
}

//---- Ausgabe der API ----

//JSON als Rückgabe angeben
header("Content-type: application/json");
echo json_encode($returnArray);

//DEBUG - Falls bei der Verarbeitung des JSON Fehler aufgetreten sind
//var_dump(json_last_error());
//var_dump(json_last_error_msg());