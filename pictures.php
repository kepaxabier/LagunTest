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
	$draw="<br>";
	$progema_array = explode("\n", $progema);

	for ($i=0; $i < (count($progema_array)); $i++) {
		$line_array=explode("\t", $progema_array[$i]);
		#echo $line_array[1]." ".$line_array[10]."<br>";
		
        	if ($line_array[3]=='NOUN'  &&  $line_array[10]!= "_"){
			$ext = end(explode('.', $line_array[10]));
			echo "<div class='gallery'>";                                                                                                                     echo "<a target='_blank'>";                                                                                                                       echo "<img src='".$line_array[10]."' width='800' height='600'";                                                                                   echo "</a>";                                                                                                                                      echo "<div class='desc'>".$line_array[1]."</div>";                                                                                                echo "</div>"; 
			
			#if ($ext != "svg"){
			#	
			#	$draw=$draw."<div class='content' id='emadiv'>".$line_array[1]."<br></div>";
			#	$imageData = base64_encode(file_get_contents($line_array[10]));
			#	$draw=$draw.'<img class="image" src="data:image/x-icon;base64,'.$imageData.'"><hr>';
			
			#}else{
			#	$draw=$draw."<div class='content' id='emadiv'>".$line_array[1]."<br></div>";
			#	$imageData = file_get_contents($line_array[10]);
			#	$draw=$draw.'<img class="image" src="data:image/x-icon;base64,'.$imageData.'"><hr>';
			#}
		
		}
	}
      	
	#echo "<div class='image' id='emadiv'>".$draw."<br></div>";
                    
}
?>
</body>
</html>
