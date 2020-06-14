<?php
require "header.php" 
?>

<html>

<body>
	<nav class="title-aux">
		<ol><li class="title-aux"><a href="">Synonym List<br></a></li></ol>
	</nav> 
	<nav class="text-aux">
		<div id="pink" class="box"></div><span class="explanation">Proper Noun</span>
		<div id="dark-green" class="box"></div><span class="explanation">Pronoun</span>
		<div id="light-green" class="box"></div><span class="explanation">Noun</span>
		<div id="dark-blue" class="box"></div><span class="explanation">Adjective</span>
		<div id="yellow" class="box"></div><span class="explanation">Partitive</span>
		<div id="red" class="box"></div><span class="explanation">Verb</span>
		<div id="orange" class="box"></div><span class="explanation">Adverb</span>
	</nav>
<?php
	session_start();
	$results = $_SESSION["results"];

	if (file_exists($results)){

	$progema = file_get_contents($results);
	$synomy="<br>";
	$numberofsentences=0;
	$h=0;
	$progema_array = explode("\n", $progema);

	for ($i=0; $i < (count($progema_array)); $i++) {
	
		$line_array=explode("\t", $progema_array[$i]);


		if ($line_array[0] ==1) {
			$progema_web=$progema_web; 
			$numberofsentences=$numberofsentences+1;
		} else{
			$progema_web=$progema_web."&nbsp;&nbsp";  
		}

		$j=1;
		if ($line_array[3] == "PROPN"){
			$progema_web=$progema_web."<font color='pink'>".$line_array[1]."</font>";  
		}elseif ($line_array[3] == "PRON") {
			$progema_web=$progema_web."<font color='darkgreen'>".$line_array[1]."</font>";
		}elseif ($line_array[3] == "NOUN") {
			$progema_web=$progema_web."<font color='lightgreen'>".$line_array[1]."</font>";
		}elseif ($line_array[3] == "ADV") {
			$progema_web=$progema_web."<font color='orange'>".$line_array[1]."</font>";
        }elseif ($line_array[3] == "PART") {
			$progema_web=$progema_web."<font color='yellow'>".$line_array[1]."</font>";
		}elseif ($line_array[3] == "ADJ") {
			$progema_web=$progema_web."<font color='darkblue'>".$line_array[1]."</font>";
		}elseif ($line_array[3] == "AUX") {
			$progema_web=$progema_web."<font color='red'>".$line_array[1]."</font>";
		}elseif ($line_array[3] == "VERB") {
			$progema_web=$progema_web."<font color='red'>".$line_array[1]."</font>";
		}else{
			$progema_web=$progema_web.$line_array[$j];  
		}

		$pos = strpos($line_array[9], '}');
		// Note our use of ===. Simply, == would not work as expected
		// because the position of 'a' was the 0th (first) character.
		if ($pos === false) {
			$kk=1; //nop	
		}else{
			$h=$h+1;
			$progema_web=$progema_web."<sup>".$h."</sup>";
			$palabra = explode(":", $line_array[9]);
			$char= array("_", "{", "}", "'");
			$synomy = str_replace($char, " ", $palabra[1]);
			$synomys=$synomys.$h.") <a style = 'text-transform:uppercase; font-weight: bold;'>".$line_array[1]."</a>: ".$synomy.".<br>"; 
		}
	}
	echo "<div class='box-text'>".$progema_web."</div>";
	echo "<div class='box-definitions'>".$synomys."</div>";
	}

?>



</body>
</html>
