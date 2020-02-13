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
				<option value="eu">Euskera</option>
				<option value="en">English</option>
			</select>
        </form>
    </div>

    <?php
		session_start();
		$workdir = "/var/www/html/erraztest/";
		$destDir = "/var/www/html/erraztest/tmp";
		$binPath = "/var/www/html/erraztest/nlpcubeparserprocess.sh";
		$fileId = date('Y-m-d_His_');
		// Execute this code if the submit button is pressed.
		if (isset($_POST['inputtext'])) {
			//Visualizar el contenido del inputtext
			//echo ('Input text:&nbsp;');
			//echo $_POST['inputtext'];
			//$fn es un nombre aletorio del pelo de /tmp/2018-12-14_162700_GVC0f3 en la carpeta /tmp
			//tempnam — Crea un fichero con un nombre de fichero único Descripción string tempnam ( string $dir , string $prefix )
			//$fn = tempnam (sys_get_temp_dir(), $fileId); 
			$fn = tempnam ($destDir, $fileId);
			//echo $fn;
			/*echo "<p>";
			echo ($fn);
			echo "</p>";*/
			// da permisos rw_r__r__
			chmod ($fn,0664);
			// Si lo ha creado vacio
			if ($fn) {
				//abrimos
				$f = fopen ($fn, "w");
				if ($f){
					//escibimos el contenido de la caja en el 
					fwrite($f,$_POST['inputtext']);
					fwrite($f,"\n");
					//cerramos el fichero
					fclose($f);
					//obtenemos el nombre del fichero sin path
					$basefn = basename($fn);
					//dos2unix - Convertidor de archivos de texto de formato DOS/Mac a Unix y viceversa
					exec("/usr/bin/dos2unix ".$fn);
					#The php script will look for a script "x.sh" in the current directory
					#the script will run as the user of the web-server - typically "www-data".
					#Bai ->  $sysout = shell_exec("cp /tmp/".$basefn." ".$fn.".out.csv" );
					
					#$sysout = exec($binPath." /tmp/".$basefn);
					#Este me crea vacio!!!!!
					#exec("/var/www/neurriak.com/html/webprocess.sh /var/www/neurriak.com/html/webfiles/proba2.txt", $output, $return);
					exec($binPath." ".$fn." ".$_POST['difficult']." ".$_POST['language'], $output, $return);
					/*echo "<p>";
					echo ($binPath." ".$fn.":".$output.":".$return);
					echo "</p>"; */
					// Return will return non-zero upon an error
					#if (!$return) {
						#		echo "Created Successfully";
					#	} 
					#    else {
					#		echo "not created";
					#	}
						#echo "<p>";
						#echo ($sysout);
						#echo "</p>";*/
					#No ->   $sysout = shell_exec($binPath." /tmp/".$basefn);
					$emaPath = $fn.".out.csv";
					$_SESSION["path"] = $emaPath;
					$_SESSION["basefn"] = $basefn;
                    #echo "<input type='hidden' name='ruta' value='" . $emaPath . "' />";
                }

			} else { 
				echo("<p>Erroreren bat egon da fitxategi tenporala sortzean.</p>");
			}
			
		}
    ?>

</body>
</html>
