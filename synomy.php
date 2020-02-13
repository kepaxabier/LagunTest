<?php
require "header.php" 
?>

<html>

<body>

<nav class="title-aux">
  <ol>  
      <li class="title-aux"><a href="">Synomym List<br><small>(proper noun:pink, pronoun:darkgreen, noun:lightgreen, adjective:darkblue, partitive:yellow, verb:red and adverb:orange)</small></a></li>
  </ol>
</nav>

<?php
session_start();
$emaPath = $_SESSION["path"];


if (file_exists($emaPath)) //&& filesize($emaPath) > 0)  
{
						#file_get_contents — Transmite un fichero completo a una cadena
	#chmod ($fn,0644);
	$progema = file_get_contents($emaPath);
	#echo "<hr>";
	#echo "<h3 style='text-align: center;'>Synomym list of rare words and Colouring words (proper noun:pink, pronoun:darkgreen, noun:lightgreen, adjective:darkblue, partitive:yellow, verb:red and adverd:orange) </h3>";          
	#$progema_web="<hr><h3 style='text-align: center;'>Synomym list of rare words and Colouring words (proper noun:pink, pronoun:darkgreen, noun:lightgreen, adjective:darkblue, partitive:yellow, verb:red and adverd:orange) </h3>";
	$synomy="<br>";
	$numberofsentences=0;
	$h=0;
	# array explode ( string $delimiter , string $string [, int $limit = PHP_INT_MAX ] )
	# Devuelve un array de string, siendo cada uno un substring del parámetro string formado por la división realizada por los delimitadores indicados en el parámetro delimiter. 
	$progema_array = explode("\n", $progema);

	for ($i=0; $i < (count($progema_array)); $i++) {
		#Por cada linea: delimitador de columna ","
		#$line_array=explode(",", $progema_array[$i]);  
		# mixed preg_replace ( mixed $pattern , mixed $replacement , mixed $subject [, int $limit = -1 [, int &$count ]] )
		#Busca en subject coincidencias de pattern y las reemplaza con replacement.  
		#$lerroa = preg_replace('/^[0-9]+\,/', '', $progema_array[$i]);
		#Si es la primera línea
		#if ($line_array[0] ==1) 
		    #$progema_web=$progema_web."<b>".$progema_array[i]."</b><br>";}
		#else 
		#parrafoka
		#kepa
		$line_array=explode("\t", $progema_array[$i]);


		if ($line_array[0] ==1) {
			$progema_web=$progema_web."<br>"; 
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
			$synomys=$synomys.$h." ".$synomy."<br>"; 

			
    	} 
    
    
		#fin de kepa o:
		#$progema_web=$progema_web.$progema_array[$i]."<br>";	
	} #for   
	echo "<div class='content' id='emadiv' style='text-align: justify;'>".$progema_web."<br><small>".$synomys."</small></div>";
                    
}

?>



</body>
</html>