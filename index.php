<?php
require "header.php" 
?>


<html>
<head>
	<script type="text/javascript">
		function processing(){
			document.getElementById('processing_img').style.display = 'block';
			document.getElementById('submit').style.display = 'none';
			document.getElementById('clearbutton').style.display = 'none';
		}
		function eraseTextAndLink() {
			document.getElementById("inputtext_area").value = "";
			document.getElementById('bratlink').style.display = 'none';
			document.getElementById('pdflink').style.display = 'none';
		}
		function eraseEmaDiv() {
		document.getElementById('emadiv').style.display = 'none';
		}
	</script>
</head>

<body>

    <div class="input-text-main">
        <form id="form" method="post" onsubmit="processing()">
            <textarea id="inputtext_area" rows="15" cols="100" name="inputtext" wrap="physical">
                <?php
                    if (isset($_POST['inputtext'])) {
                        echo $_POST['inputtext'];
                    }else{
                        echo "Milk is a white liquid food produced by the mammary glands of mammals. It is the primary source of nutrition for infant mammals. Early-lactation milk contains colostrum, which carries the mother's antibodies to its young. It contains many other nutrients including protein and lactose. \n";
					}
                ?>
            </textarea>

            <br>

            <div id="processing_img" style="display:none;"><img src="processing.gif" alt="" /><br>Processing...</div>
            <button type="submit" id="submit" value="Submit" onclick="javascript:eraseEmaDiv();">Submit</button>
            <button type="button" id="clearbutton" value="Clear" onclick="javascript:eraseTextAndLink();">Clear</button>
			<select name="difficult">
				<option value="b">Bajo</option>
				<option value="m">Medio</option>
				<option value="a">Alto</option>
			</select>
			<select name="language">
				<option value="basque">Euskera</option>
				<option value="english">English</option>
			</select>
        </form>
    </div>

    <?php
		session_start();
		$destDir = "/var/www/html/erraztest/";
		$binPath = "/var/www/html/erraztest/nlpcubeparserprocess.sh";
		$fileId = date('Y-m-d_His_');
		$id = uniqid(time(), true);
		// Execute this code if the submit button is pressed.
		if (isset($_POST['inputtext'])) {
			//Crear carpeta aleatoria
			mkdir($destDir.$id, 0770);
			//Crear fichero con el texto introducido dentro de la carpeta aleatorio
			$textPath = $destDir.$id."/text.txt";

			// Si lo ha creado vacio
			if ($textPath) {
				//abrimos
				$f = fopen ($textPath, "w");
				chmod($textPath, 0664);

				if ($f){
					//escibimos el contenido de la caja en el fichero
					fwrite($f,$_POST['inputtext']);
					fwrite($f,"\n");
					//cerramos el fichero
					fclose($f);
					//obtenemos el nombre del fichero sin path
					$basefn = basename($textPath);
					//dos2unix - Convertidor de archivos de texto de formato DOS/Mac a Unix y viceversa
					exec("/usr/bin/dos2unix ".$textPath);
					#Ejecutar laguntest.py
					exec($binPath." ".$textPath." ".$_POST['difficult']." ".$_POST['language']." ".$destDir.$id, $output, $return);

					$emaPath = $fn.".out.csv";
					$_SESSION["path"] = $emaPath;
					$_SESSION["basefn"] = $basefn;
                }

			} else {
				echo("<p>Erroreren bat egon da fitxategi tenporala sortzean.</p>");
			}

		}
    ?>

</body>
</html>