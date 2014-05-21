<?php

include_once("botclasses.php");

$mediawiki = new wikipedia;

$mediawiki->quiet = "true";

$pid = $_POST["pid"];

$title = $_POST["title"];

$name = $_POST["name"];

$year = $_POST["year"];

$transcription = $_POST["transcription"];

if ($name != "") {

	$transcription .= "  Transcribed by " . $name;

	if ($year != "" ) {

		$transcription .= ", Class of " . $year .;
	}

	$transcription .= ".";
}

$mediatitle = $pid . " " . $title;

$mediawiki->edit($mediatitle, $transcription);

echo "Thank you for helping PHPP to transcribe items from Poweshiek County's history. Your transcription has been saved.";

?>
