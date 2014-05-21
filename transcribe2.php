<?php

include_once("botclasses.php");

$pid = $_GET["pid"];

$pid = str_replace(":", " ", $pid);

$title = $_GET["title"];

$mediawiki = new wikipedia;

$mediawiki->quiet = "true";

$mediatitle = $pid . " " . $title;

$current = $mediawiki->getpage($mediatitle);

//var_dump($current);

echo "<html><p>Transcribe " . strtoupper($title) . "</p>
	<form action='submit.php' method='post'>
	<textarea rows='20' cols='60' name='transcription'>" . $current . "</textarea>
	<br />
	<p>We would like to give you credit for your transcription. Please enter your name and, if you are a current Grinnell student or an alum, your class year. If you would prefer to participate anonymously, just leave these fields blank.
	<br /><label>Name</label>&nbsp;<input type='text' size='35' name='name'></input>&nbsp;&nbsp;<label>Class year</label>&nbsp;<input type='text' size='4' name='year'></input>
	<br />
	<input type='hidden' name='pid' value='" . $pid . "'>
	<input type='hidden' name='title' value='" . $title . "'>
	<input type='Submit' value='Save your transcription'></form></html>";
?>
