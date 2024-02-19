/**
 * Filtert die Feed-ID aus der URL heraus.
 * @param URL der Podcast-Übersichtsseite (String).
 * @return Feed-ID (String).
 */
function urlTool(urlToFilter) {
	
	//Feed-ID herausfinden
	urlToFilter = urlToFilter.substring(urlToFilter.indexOf("feed/")+5);
	
	//Wenn mit ?-Parameter etc.
	if(urlToFilter.indexOf("?") > 0) {
		urlToFilter = urlToFilter.substring(0,urlToFilter.indexOf("?"));
	}
	
	//Rückgabe
	return urlToFilter;
}

/**
 * Gibt die Feed-ID aus. (void - Funktion)
 */
function showFeedId() {
	
	//Boxinhalt einlesen
	var input = document.getElementById("urlToolBox"); //Box einlesen
	var value = input.value; //Value zuweisen
	input.value = ""; //Value aus Box entfernen
	
	value = urlTool(value);
	
	//Ergebnis ausgeben
	var p = document.getElementById("urlToolResult");
	p.innerText = value;
}