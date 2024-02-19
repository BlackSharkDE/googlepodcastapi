/*
 * Zum herausfinden der API-Url und URL-Kopieren-Funktion
 */

var apiUrl = window.location.href; //Aktuelle URL
apiUrl = apiUrl.substring(0,apiUrl.lastIndexOf('/')+1); //Substring bis einschließlich letztem Slash
apiUrl += 'api.php'; //Um Dateinamen ergänzen

//API-Url in das b-Tag packen
var pOutput = document.getElementById('gpaApiUrl'); //b-Tag finden
pOutput.innerText = apiUrl; //Text setzen

//Demo-Url
var demoUrl = apiUrl + "?feedid=aHR0cHM6Ly93d3cudGhlZ3VhcmRpYW4uY29tL3NjaWVuY2Uvc2VyaWVzL3NjaWVuY2UvcG9kY2FzdC54bWw&episode=26";

/**
 * Kopiert den Inhalt des <b>-Tags (API-URL) in die Zwischenablage. (void - Funktion)
 */
function copyUrlToClipboard() {
    let dummy = document.createElement('input'); //Dummy-Input
    let url   = document.getElementById('gpaApiUrl').innerText; //Die URL

    //Die URL in den Dummy-Input schreiben
    dummy.value = url;

    //Text in Dummy selektieren
    dummy.select();
    dummy.setSelectionRange(0, 99999);//FÜr mobile
    
    //Text in Zwischenablage speichern
    navigator.clipboard.writeText(dummy.value);

    alert("URL in die Zwischenablage kopiert!");
}