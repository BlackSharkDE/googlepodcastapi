<!DOCTYPE html>
<html>
<head>
	<title>GPA - Google Podcast API</title>
	<meta charset="utf-8">
	<link rel="icon" href="./frontend_stuff/logo.png">
	
	<link rel="stylesheet" type="text/css" href="./frontend_stuff/css/style.css">
	<link rel="stylesheet" type="text/css" href="./frontend_stuff/css/script_areas.css">
</head>
<body>
	<div id="main">
		
		<div id="header">
			<a href="./index.php"><img alt="gpalogo" src="./frontend_stuff/logo.png"></a>
		</div>
		
		<div id="introduction">
			<p>Die GooglePodcastApi (GPA) ist eine API zum herausfinden von Informationen über Podcasts, die auf der Website  <a href="https://podcasts.google.com/" target="_blank">podcasts.google.com</a> von Google gelistet sind.</p>			
		</div>
		
		<div id="apiurl">
			<p>API-URL: <b id="gpaApiUrl"></b></p>
			<button class="betterButton" onClick="copyUrlToClipboard()">API-URL kopieren</button>
			<script type="text/javascript" src="./frontend_stuff/apiurl.js"></script>
		</div>
		
		<div id="apiuse">
			<h2>API benutzen</h2>
			<p>Die API funktioniert über einen simplen URL-Aufruf und das Ergebnis ist eine JSON-Ausgabe.</p>
			
			<h3>Beispielaufruf</h3>
			<div class="codebox">
				<p class="demourl"></p>
			</div>
			<a class="betterButton demourl" href="" target="_blank">API-Demoaufruf</a>
			<script type="text/javascript">
				//Demo-Url den beiden Elementen mit der "demourl"-Klasse geben
				var demoUrlItems = document.getElementsByClassName("demourl");
				demoUrlItems[0].innerText = demoUrl;
				demoUrlItems[1].href = demoUrl;
			</script>
			
			<div class="infobox">
				<p><b>Es müssen 2 Parameter angegeben werden:</b></p>
				<ul>
					<li><b>feedid</b> - Kann mit nachfolgendem Tool herausgefunden werden (eindeutige ID des Podcast-Feeds - Dieser enthält alle Episoden des jeweiligen Podcasts)</li>
					<li><b>episode</b> - Nummer der Episode, zu der Informationen angezeigt werden sollen (1 = erste Episode, 2 = zweite Episode etc.)</li>
				</ul>
			</div>
		</div>
		
		<div id="urlTool">
			<h2>Feed-ID eines Podcasts herausfinden</h2>
			<h5>Einfach den Link einer Podcast-Übersichtsseite eingeben</h5>
			<input type="text" id="urlToolBox" placeholder="Podcast-Übersichtsseite-URL">
			<br>
			<br>
			<input type="button" class="betterButton" value="Feed-ID herausfinden" onclick="showFeedId()">
			<p id="urlToolResult"></p>
			<script type="text/javascript" src="./frontend_stuff/urlTool.js"></script>
		</div>
		
	</div>
	
	<footer>
		<p>&copy; 2020. All Rights Reserved by Joe Equinozio</p>
	</footer>
</body>
</html>