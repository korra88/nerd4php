# nerd4php
PHP library to inferface NERD at http://nerd.eurecom.fr

## Usage

Include ```load.php``` and instantiate ```\NERD\client``` with a valid API key:

```
require_once "/path/to/nerd4php/load.php";

$nerd = new \NERD\client(YOUR_API_KEY);

```

### Create a new document and show info

```
$idDocument_1 = $nerd->createDocumentFromString("Some random text.");
// Created document @ http://nerd.eurecom.fr/document/$idDocument_1

$idDocument_2 = $nerd->createDocumentFromUri("http://www.example.com/uri/to/document");

// Show info for document #2

$document_2 = $nerd->getDocument($idDocument_2);

echo "\nidDocument: {$document_2->idDocument}";
echo "\ntext: {$document_2->text}";
echo "\nlanguage: {$document_2->language}";
echo "\ntype: {$document_2->type}";

```
