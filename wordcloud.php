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
    $results = $_SESSION["results"];
    $fileName = $_SESSION["file"];
    $directory= $_SESSION["directory"];

    if (file_exists($results)){
        $file=$directory."/".$fileName.".png";
        $wordcloud='<img src="'.$file.'">';
        echo "<div class='image' id='emadiv' style='text-align: center;'>".$wordcloud."</div>";
    }
    ?>

    </body>
</html>
