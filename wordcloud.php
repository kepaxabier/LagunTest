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
#echo $basefn;


if (file_exists($emaPath)) //&& filesize($emaPath) > 0)  
{
	#$wordcloud="<hr><h3 style='text-align: center;'>Word Cloud</h3>";
	#echo "<h3 style='text-align: center;'>Word Cloud</h3>";
	$file="tmp/".$basefn.".png";
	#echo '<img src="'.$file.'">';
	#$wordcloud=$wordcloud.'<img src=/var/www/html/erraztest/tmp/'.$basefn.'.png>';

	$wordcloud=$wordcloud.'<img src="'.$file.'">';
	#echo "<div id='emadiv' style='text-align: justify;'>".$progema_web."<small>".$notaspietexto."</small>".$wordcloud."</div>";
	
	echo "<div class='image' id='emadiv' style='text-align: center;'>".$wordcloud."</div>";
}

?>

</body>
</html>