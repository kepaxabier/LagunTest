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
                alert("You have to upload at least one file!");
                return false;
            }  else {
                var index = 0;
                while (index < document.getElementById("infile").files.length){
                    if (/\.(txt|docx|odt|doc)$/i.test(file.files[index].name) === false ) {
                        alert("Invalid format. Only .txt, .odt or .docx files allowed.");
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
			
		$inputfile = $_FILES['infile']['name'][0];
		$inputfilepath =$destDir.$id.'/'.$inputfile;
		#echo "Nombre fichero introducido: ".$inputfile;

		#Guardar archivo al directorio nuevo
		$moved = move_uploaded_file($_FILES['infile']['tmp_name'][0], $inputfilepath);
		#if( $moved ) {
		#	echo "<br> moved";
		#} else {
		#	echo "The files could not be loaded. <br>";
		#}
		
		#Cambiar nombre del fichero
		$completeTextPath = $destDir.$id."/text.txt";
		
		#Convertir en txt
		exec($convertionPath." ".$id." ".$inputfile);
		$content = file_get_contents($completeTextPath);
		#echo "Content: ".$content."<br>";

		#Analizar
		exec("/usr/bin/dos2unix ".$completeTextPath);
		$comando=$binPath." text.txt ".$_POST['difficult']." ".$_POST['language']." ".$destDir.$id;	
		exec($comando, $output, $return);
		$emaPath = $completeTextPath.".out.csv";
		$_SESSION["path"] = $emaPath;
		$_SESSION["basefn"] = $destDir.$id;
		$_SESSION["carpeta"]=$id;
	}
	?>
	</form>

	</body>
</html>
