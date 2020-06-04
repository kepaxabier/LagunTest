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

    if (file_exists($emaPath)){
        $file=$carpeta."/text.txt.png";
        $wordcloud='<img src="'.$file.'">';
        echo "<div class='image' id='emadiv' style='text-align: center;'>".$wordcloud."</div>";
    }
    ?>

    </body>
</html>
