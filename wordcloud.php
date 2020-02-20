<?php
require "header.php" 
?>

<html>

<body>

<nav class="title-aux">
  <ol>  
      <li class="title-aux"><a href="">Word Cloud</a></li>
  </ol>
</nav>


<?php
session_start();
$emaPath = $_SESSION["path"];
$basefn = $_SESSION["basefn"];
$carpeta= $_SESSION["carpeta"];
#echo $basefn;


if (file_exists($emaPath)) //&& filesize($emaPath) > 0)  
{
	#$wordcloud="<hr><h3 style='text-align: center;'>Word Cloud</h3>";
	#echo "<h3 style='text-align: center;'>Word Cloud</h3>";
	#$file=$carpeta."/1.png";
	$file=$carpeta."/text.txt.png";
	#echo '<p>'.$file.'</p>';
	#$wordcloud=$wordcloud.'<img src=/var/www/html/erraztest/tmp/'.$basefn.'.png>';

	$wordcloud='<img src="'.$file.'">';
	#$wordcloud='<img class="image" src="data:image/x-icon;base64,/var/www/html/erraztest/15822186235e4ebd7ff275b3.28921161/text.txt.png">';
	#echo "<div id='emadiv' style='text-align: justify;'>".$progema_web."<small>".$notaspietexto."</small>".$wordcloud."</div>";
	
	echo "<div class='image' id='emadiv' style='text-align: center;'>".$wordcloud."</div>";
}

?>

</body>
</html>
