<?php
require "header.php" 
?>

<html>

<body>

<nav class="title-aux">
  <ol>  
      <li class="title-aux"><a href="">Pictures</a></li>
  </ol>
</nav>


<?php
session_start();
$emaPath = $_SESSION["path"];

if (file_exists($emaPath)) //&& filesize($emaPath) > 0)  
{
	$progema = file_get_contents($emaPath);
	#echo "<hr>";
	#echo "<h3 style='text-align: center;'>Synomym list of rare words and Colouring words (proper noun:pink, pronoun:darkgreen, noun:lightgreen, adjective:darkblue, partitive:yellow, verb:red and adverd:orange) </h3>";          
	#$progema_web="<hr><h3 style='text-align: center;'>Synomym list of rare words and Colouring words (proper noun:pink, pronoun:darkgreen, noun:lightgreen, adjective:darkblue, partitive:yellow, verb:red and adverd:orange) </h3>";
	$draw="<br>";
	$progema_array = explode("\n", $progema);

	for ($i=0; $i < (count($progema_array)); $i++) {
		$line_array=explode("\t", $progema_array[$i]);
		#echo $line_array[10]."<br>";

        if ($line_array[3]=='NOUN'  &&  $line_array[10]!= ""){
			$ext = end(explode('.', $line_array[10]));
			#echo $ext."<br>";
			
			if ($ext != "svg"){
				#echo substr($line_array[10], 1);
				$draw=$draw."<div class='content' id='emadiv'>".$line_array[1]."<br></div>";
				$imageData = base64_encode(file_get_contents($line_array[10]));
				$draw=$draw.'<img class="image" src="data:image/x-icon;base64,'.$imageData.'"><hr>';
				#echo "url: ".$line_array[10]."<br>";
				#echo $line_array[1].": url: ".substr($line_array[10], 1)."<br>";
			}else{
				$draw=$draw."<div class='content' id='emadiv'>".$line_array[1]."<br></div>";
				$imageData = file_get_contents($line_array[10]);
				$draw=$draw.'<img class="image" src="data:image/x-icon;base64,'.$imageData.'"><hr>';

				#$draw=$draw.'<embed class="image" type="image/svg+xml src="'.$imageData.'"><hr>';
				#echo "url: ".$line_array[10]."<br>";
				echo $line_array[1].": url: ".substr($line_array[10], 1)."<br>";
			}
		
		}
	}  
	echo "<div class='image' id='emadiv'>".$draw."<br></div>";
                    
}
?>

</body>
</html>