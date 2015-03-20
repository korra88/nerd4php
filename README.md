## nerd4php
PHP library to inferface NERD at http://nerd.eurecom.fr

### Usage

Include ```load.php``` and instantiate ```\NERD\client``` with a valid API key:

```
require_once "/path/to/nerd4php/load.php";

$nerd = new \NERD\client(YOUR_API_KEY);
```

### Create a new document and show info

```
$idDocument_1 = $nerd->createDocumentFromString("Some random text.");
// Created document @ http://nerd.eurecom.fr/document/$idDocument_1

$document_uri = "http://www.example.com/uri/to/document";
$idDocument_2 = $nerd->createDocumentFromUri($document_uri);

// Show info for document #2

$document_2 = $nerd->getDocument($idDocument_2);

echo "\n" . "idDocument: {$document_2->idDocument}";
echo "\n" . "text: {$document_2->text}";
echo "\n" . "language: {$document_2->language}";
echo "\n" . "type: {$document_2->type}";

```
