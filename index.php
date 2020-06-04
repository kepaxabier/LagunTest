<?php
require "header.php" 
?>


<html>
<head>
	<script type="text/javascript">
		function processing(){
			document.getElementById('processing_img').style = "display:inline-block;align: center; color: #FBFBFB; position: relative; left: 50%;";
			document.getElementById('buttonSubmit').style.display = 'none';
			document.getElementById('clearButton').style.display = 'none';
			document.getElementById('inputtext_area').style.display = 'none';
			document.getElementById('list').style.display = 'none';
		}

		function eraseText() {
			document.getElementById("inputtext_area").value = "";
		}
	</script>
</head>

<body>
  
		<form id="form" method="post" onsubmit="processing()">
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
					<option value="spanish">Castellano</option>
				</select>
			</div>

			<!-- Text Area Input -->
			<div class="input-text">
				<textarea id="inputtext_area" rows="5" cols="100" name="inputtext">
					<?php 
					if (isset($_POST['inputtext'])) {
						echo $_POST['inputtext'];
					}else{
						echo "Milk is a white liquid food produced by the mammary glands of mammals. It is the primary source of nutrition for infant mammals. Early-lactation milk contains colostrum, which carries the mother's antibodies to its young. It contains many other nutrients including protein and lactose.";
					}
					?>
				</textarea>    
			</div>

			<!-- Procesing Gif -->
			<div id="processing_img" style="display:none;"><img src="processing.gif" alt="" /><br>Processing...</div>
		
			<!-- Submit & Clear Buttons -->			
			<nav class="boton-box">
				<ol>
				<li><button type="submit" id="buttonSubmit" value="Submit">Submit</button></li>
				<li><button type="button" id="clearButton" value="Clear" onclick="javascript:eraseText();">Clear</button></li>
				</ol>
			
			</nav>
        </form>

    <?php
		session_start();
		$destDir = "/var/www/html/erraztest/";
		$binPath = "/var/www/html/erraztest/nlpcubeparserprocess.sh";
		$id = uniqid(true);
		// Execute this code if the submit button is pressed.
		if (isset($_POST['inputtext'])) {
			//Crear carpeta aleatoria
			mkdir($destDir.$id, 0770);

			//Directorio del fichero a crear
			$completeTextPath = $destDir.$id."/text.txt";

			if ($completeTextPath) {
				//Crear .txt
				$f = fopen ($completeTextPath, "w");
				//Permisos
				chmod($completeTextPath, 0664);

				if ($f){
					//Escibimos el contenido de la caja en el .txt
					fwrite($f,$_POST['inputtext']);
					fwrite($f,"\n");
					fclose($f);

					//dos2unix - Convertidor de archivos de texto de formato DOS/Mac a Unix y viceversa
					exec("/usr/bin/dos2unix ".$completeTextPath);

					//Llamada a laguntest.py
					$comando=$binPath." text.txt ".$_POST['difficult']." ".$_POST['language']." ".$destDir.$id;
					exec($comando, $output, $return);

					//Path donde se guardarán los resultados del análisis
					$emaPath = $completeTextPath.".out.csv";

					//Variables de sesion
					$_SESSION["path"] = $emaPath;
					$_SESSION["basefn"] = $destDir.$id;
					$_SESSION["carpeta"]= $id;
                }

			} else {
				echo("<p>Erroreren bat egon da fitxategi tenporala sortzean.</p>");
			}
		}
    ?>

</body>
</html>
