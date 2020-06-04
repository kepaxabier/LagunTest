<?php
require "header.php" 
?>
<html>

<body>
	<nav class="title-aux">
  		<ol><li class="title-aux"><a href="">Definitions<br></a></li></ol>
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
	$emaPath = $_SESSION["path"];

	if (file_exists($emaPath)) //&& filesize($emaPath) > 0)  
	{
		#Obtain text analized information
		$progema = file_get_contents($emaPath);
		#Save all definitions
		$definitions="<br>";
		#Number of sentences
		$numberOfSentences=0;
		#Words numbers
		$h=0;
		#Obtain information of each word divided in arrays
		$progema_array = explode("\n", $progema);
		#Array para el control de palabras repetidas
		$def;
		
		for ($i=0; $i < (count($progema_array)); $i++) {
			$line_array=explode("\t", $progema_array[$i]);

			#TEST
			/**echo "BEGIN";
			echo "<br>0: ";
			echo  $line_array[0];
			echo "<br>1: ";
			echo $line_array[1];
			echo "<br>2: ";
			echo $line_array[2];
			echo "<br>3: ";
			echo $line_array[3];
			echo "<br>4: ";
			echo $line_array[4];
			echo "<br>5: ";
			echo $line_array[5];
			echo "<br>6: ";
			echo $line_array[6];
			echo "<br>7: ";
			echo $line_array[7];
			echo "<br>8: ";
			echo $line_array[8];
			echo "<br>9: ";
			echo $line_array[9];
			echo "<br>10: ";
			echo $line_array[10];
			echo "<br>11: ";
			echo $line_array[11];
			echo "<br>def: ";
			echo $def[$i-1];
			echo "<br>";**/
			

			#line_array[0] -> Position of the word in the sentence
			if ($line_array[0] == 1) { #if first word of sentence
				$progema_web=$progema_web; 
				$numberOfSentences=$numberOfSentences+1;
			} else{
				$progema_web=$progema_web."&nbsp;&nbsp";  
			}


			$j=1;
			#line_array[3] -> Type of word | The color is different depending on the type of word
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
			
			#line_array[8] -> Definition
			#line_array[11] -> Examples
			if ($line_array[8] != '_' && $line_array[8] != "" && !in_array($line_array[1], $def)){
				$h=$h+1;
				$progema_web=$progema_web."<sup>".$h."</sup>";
				if($line_array[11]!="[]" && $line_array[11]!="_"){
					#echo substr($line_array[11], 1)."<br>";
					$examples = substr($line_array[11], 1);
					$char= array("[", "'", "]", "\"");
					$examples_format = str_replace($char, "", $examples);
					$examples_format = str_replace(",", ". ", $examples_format);
					$definitions=$definitions.$h.") <a style = 'text-transform:uppercase; font-weight: bold;'>".$line_array[1]."</a>: ".ucfirst($line_array[8]).". <a style='font-style: italic;'>Examples: ".ucfirst($examples_format).".</a><br>"; 
				}else{
					$definitions=$definitions.$h.") <a style = 'text-transform:uppercase; font-weight: bold;'>".$line_array[1]."</a>: ".ucfirst($line_array[8]).".<br>"; 
				}
			}
			$def[$i] = $line_array[1];
		}    
		
		echo "<div class='box-text'>".$progema_web."</div>"; 
		echo "<div class='box-definitions'>".$definitions."</div>";      	
		echo "<br>";
		echo "<br>";      		
		echo "<br>";      		

	
}
?>
</body>
</html>
