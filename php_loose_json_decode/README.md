# php_loose_json_decode

Ein PHP Parser für JSON-Daten, dessen Keys nicht mit Anführungszeichen umrandet sind.

# Problemstellung

1.  Normales JSON (wird für Datenübertragung etc. benutzt):
```
{
    "name": "Kyle",
    "age": 22,
    "hobbies": ["Weight Lifting","Bowling"]
}
```

2.  JavaScript-Objekte (JavaScript interne Objektdarstellung):
```
{
    name: "Kyle",
    age: 22,
    hobbies: ["Weight Lifting","Bowling"]
}
```
Wie man sieht, sind die JSON-Keys mal mit Anführungszeichen (Beispiel 1) und mal ohne Anführungszeichen (Beispiel 2) angegeben.

Die Standard-PHP-Funktion `json_decode` kann nur Strings mit dem Format aus dem ersten Beispiel verarbeiten. Um auch die zweite Version zu verarbeiten, benötigt man
*loose* (lockere) JSON-Parser.

Leider gibt es keine wirklich offiziellen *loose-Parser* (außer eventuell in PHP-Frameworks). Daher greift dieses Repository offen zugängliche Quellcodes auf.