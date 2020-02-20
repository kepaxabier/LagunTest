<?php
require "header.php" 
?>


<html>
    <script type="text/javascript">
        function processing(){
            document.getElementById("img_processing").style.display = "block";
        }

        function checkextension() {
            var file = document.querySelector("#infile");
            if (document.getElementById("infile").files.length == 0) {
                alert("You have to upload at least one file!");
                return false;
            }  else {
                var index = 0;
                while (index < document.getElementById("infile").files.length){
                    if (/\.(txt|docx|doc|odt)$/i.test(file.files[index].name) === false ) {
                        alert("Invalid format. Only .txt, .odt, .doc or .docx files allowed.");
                        return false;
                    }
                    index++;
                }
        }
        document.getElementById("mensajeResultados").innerHTML="Analyzing... This can take a few minutes.\n\nPlease, wait.";
        processing();
        }
    </script>

<body>

<div id="wrapper">
	<div id="banner">
		<div class="container">
			<div class="title">
				<span class="byline">Select the files you want to analyze</span> </div>
			</div>
			<ul>
				<form id="upload" enctype="multipart/form-data" method="post" onsubmit="return checkextension(); ">
					<li><input id="infile" name="infile[]" type="file" value='Seleccionar' multiple></li>
					<li><input type="submit" id="submit" name="submit" value="ANALYZE" class="button"></li>
					<select name="difficult">
						<option value="8">Bajo</option>
						<option value="5">Medio</option>
						<option value="3">Alto</option>
					</select>
					<select name="language">
						<option value="eu">Euskera</option>
						<option value="en">English</option>
					</select>
				</form>
			</ul>
		</div>
		<span id="mensajeResultados" class="byline"></span> </div>

   <?php
   	session_start();
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
	function is_dir_empty($dir) {
	  if (!is_readable($dir)) return NULL;
	  $handle = opendir($dir);
	  while (false !== ($entry = readdir($handle))) {
	    if ($entry != "." && $entry != "..") {
	      return FALSE;
	    }
	  }
	  return TRUE;
	}
   	 session_start();
         $destDir = "/var/www/html/erraztest/";
         $binPath = "/var/www/html/erraztest/nlpcubeparserprocess.sh";

	// Execute this code if the submit button is pressed.
	if (isset($_POST['submit'])) {
		$id = uniqid(true);
		//Crear carpeta aleatoria
                mkdir($destDir.$id, 0770);
		
		$inputfile = $_FILES['infile']['name'][0];
		$inputfilepath = $id.$inputfile;
		#echo "Nombre: ".$inputfile;

		#Guardar archivo al directorio nuevo
		$moved = move_uploaded_file($_FILES['infile']['tmp_name'][0], $id.$inputfile);
		if( $moved ) {
			echo "<br>";
		} else {
			echo "The files could not be loaded. <br>";
		}
		

		#Comprobar extension
		$ext = end(explode('.', $inputfile));

		#Obtener contenido del fichero
		if($ext === "odt"){
			$zip = new ZipArchive;
			if(true === $zip->open($inputfilepath)){
				$zip->extractTo($id);
				$zip->close();
			}
			$content = file_get_contents($id."/content.xml");
			#echo ".odt: ".$content."<br>";
		}else if($ext === "doc"){
			$content = shell_exec('antiword '.'/var/www/html/erraztest/'.$inputfilepath);
			#echo ".doc: ".$content."<br>";
		}else{ //txt y docx
			$content = file_get_contents($inputfilepath);
			#echo ".txt or .docx: ".$content."<br>";
		}
		##################################################
		
			
		//Crear fichero con el texto introducido dentro de la carpeta aleatorio
		$textPath = $destDir.$id."/text.txt";

		// Si lo ha creado vacio
	
		if ($textPath) {
			//abrimos
			$f = fopen ($textPath, "w");
			chmod($textPath, 0664);

			if ($f){
				//escibimos el contenido de la caja en el fichero
				fwrite($f,$content);
				fwrite($f,"\n");
				//cerramos el fichero
				fclose($f);
				//obtenemos el nombre del fichero sin path
				$basefn = basename($textPath);
				//dos2unix - Convertidor de archivos de texto de formato DOS/Mac a Unix y viceversa
				exec("/usr/bin/dos2unix ".$textPath);
				$comando=$binPath." text.txt ".$_POST['difficult']." ".$_POST['language']." ".$destDir.$id;	
				exec($comando, $output, $return);
                                #echo("<p>".$return."</p>");
				$emaPath = $textPath.".out.csv";
				$_SESSION["path"] = $emaPath;
				$_SESSION["basefn"] = $destDir.$id;
				$_SESSION["carpeta"]=$id;
                	}

		}
   
	}
    ?>
	<br>
	<span id="mensajeResultados" class="byline"></span> </div>
	<div id="img_processing" style="display:none;margin:auto;"><img src="processing.gif" alt="" /><br>Processing...</div>
	
    </div>
  </div>
</div>

</body>
</html>
