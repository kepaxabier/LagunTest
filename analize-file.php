<?php
require "header.php" 
?>


<html>
    <script type="text/javascript">
        function processing(){
			document.getElementById('processing_img').style = "display:inline-block;align: center; color: #FBFBFB; position: relative; left: 50%;";
			document.getElementById('submit').style.display = 'none';
        }

        function checkextension() {
            var file = document.querySelector("#infile");
            if (document.getElementById("infile").files.length == 0) {
                alert("You have to upload one file!");
                return false;
            }  else {
                var index = 0;
                while (index < document.getElementById("infile").files.length){
                    if (/\.(txt|docx|odt|doc)$/i.test(file.files[index].name) === false ) {
                        alert("Invalid format. Only .txt, .odt, .doc or .docx files allowed.");
                        return false;
                    }
                    index++;
                }
        }
        processing();
        }
    </script>

<body>



	<form id="upload" enctype="multipart/form-data" method="post" onsubmit="checkextension()">
		
		<!-- Nivel + Idioma -->
		<div class="custom-select" id="list">
			Level:
			<select name="difficult" class="select-item">
				<option value="b">Bajo</option>
				<option value="m">Medio</option>
				<option value="a">Alto</option>
			</select>
			Language:
			<select name="language"class="select-item">
				<option value="english">English</option>
				<option value="basque">Euskera</option>
				<option value="spanish">Spanish</option>
			</select>
		</div>

		<!-- Seleccionar Ficheros -->	
		<div class="text-aux">
			<ol>
				<li><span class="byline">Select the file you want to analyze:</span></li>
				<li><input id="infile" name="infile[]" type="file" value='Seleccionar' multiple></li>
			</ol>
		</div>


		<!-- Submit & Clear Buttons -->	
		<nav class="boton-box">
				<ol>
				<li><button type="submit" id="submit" value="submit" name="submit">Analyze</button></li>
				</ol>
		</nav>

		<!-- Procesing Gif -->
		<div id="processing_img" style="display:none;"><img src="processing.gif" alt="" /><br>Processing...</div>
	
	
   	<?php
	session_start();
	$destDir = "/var/www/html/erraztest/";
	$binPath = "/var/www/html/erraztest/nlpcubeparserprocess.sh";
	$convertionPath = "/var/www/html/erraztest/convertToTXT.sh";
	$id = uniqid(true);
	
	// Execute this code if the submit button is pressed.
	if (isset($_POST['submit'])) {
		//Crear carpeta aleatoria
		mkdir($destDir.$id, 0770);

		#Guardar archivo al directorio nuevo
		if($_FILES['infile']['name'][1]){
			echo'<script type="text/javascript">
    			alert("Only one file allowed.");
    			</script>';
		}else{
			$inputfile = $_FILES['infile']['name'][0];
			$inputfilepath =$destDir.$id.'/'.$inputfile;
			$moved = move_uploaded_file($_FILES['infile']['tmp_name'][0], $inputfilepath);
			
			#Directorio completo donde se guardara el fichero
			$completeTextPath = $destDir.$id."/".$inputfile;
			
			//dos2unix - Convertidor de archivos de texto de formato DOS/Mac a Unix y viceversa
			exec("/usr/bin/dos2unix ".$completeTextPath);
	
			//Llamada a laguntest.py
			$comando=$binPath." ".$inputfile." ".$_POST['difficult']." ".$_POST['language']." ".$destDir.$id;
			exec($comando, $output, $return);

			//Variables de sesion
			$_SESSION["results"] = $completeTextPath.".out.csv";
			$_SESSION["syntax"] = $completeTextPath.".syntax.csv";
			$_SESSION["directory"] = $id;
			$_SESSION["baseDirectory"] = $destDir.$id;
			$_SESSION["language"] = $_POST['language'];
			$_SESSION["file"] = $inputfile;
		}
		
	}
	?>
	</form>

	</body>
</html>
