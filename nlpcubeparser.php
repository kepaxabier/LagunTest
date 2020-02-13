<html>
<head>
	<meta charset="utf-8">
	<title>Reading comprehension in a foreign language</title>
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
	<div style="width: 860px;margin-left: auto; margin-right: auto;text-align: center;font-family:arial;font-size:11pt;">
	<div style="font-family:arial;font-size:11pt;">
	<h2 style="text-align: center;">Reading comprehension in a foreign language</h2>
	<div style="text-align: center;">

		<form id="form" method="post" onsubmit="processing()">

			<table>
				<tr>
				<td style='text-align:right'>Text:</td>
				<td>
					<textarea id="inputtext_area" rows="15" cols="100" name="inputtext" wrap="physical">
						<?php
						if (isset($_POST['inputtext'])) {
							echo $_POST['inputtext'];}
							else{echo "Milk is a white liquid food produced by the mammary glands of mammals. It is the primary source of nutrition for infant mammals. Early-lactation milk contains colostrum, which carries the mother's antibodies to its young. It contains many other nutrients including protein and lactose. \n";}
						?>
					</textarea>
				</td>
				</tr>
			</table>

			<br>
			
			<div id="processing_img" style="display:none;"><img src="processing.gif" alt="" /><br>Processing...</div>

			<input type="submit" id="submit" value="Submit" onclick="javascript:eraseEmaDiv();">
			<input type="button" id="clearbutton" value="Clear" onclick="javascript:eraseTextAndLink();">

		</form>

	</div>
	</div>

	<?php
		$workdir = "/var/www/html/";
		#$destDir = "/var/www/html/webfiles";
		$destDir = "/var/www/html";
		$binPath = "/var/www/html/nlpcubeparserprocess.sh";
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
					exec($binPath." ".$fn, $output, $return);
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
					#echo ($emaPath);
						#echo "</p>";
						//El fichero existe pero es de tamaño 0!!!!!
					if (file_exists($emaPath)) //&& filesize($emaPath) > 0)  
						{
						#file_get_contents — Transmite un fichero completo a una cadena
						chmod ($fn,0644);
						$progema = file_get_contents($emaPath);
						#echo "<hr>";
						#echo "<h3 style='text-align: center;'>Synomym list of rare words and Colouring words (proper noun:pink, pronoun:darkgreen, noun:lightgreen, adjective:darkblue, partitive:yellow, verb:red and adverd:orange) </h3>";          
						$progema_web="<hr><h3 style='text-align: center;'>Synomym list of rare words and Colouring words (proper noun:pink, pronoun:darkgreen, noun:lightgreen, adjective:darkblue, partitive:yellow, verb:red and adverd:orange) </h3>";
						$notaspietexto="<br>";
						$numberofsentences=0;
						$h=0;
						# array explode ( string $delimiter , string $string [, int $limit = PHP_INT_MAX ] )
						# Devuelve un array de string, siendo cada uno un substring del parámetro string formado por la división realizada por los delimitadores indicados en el parámetro delimiter. 
						$progema_array = explode("\n", $progema);
						for ($i=0; $i < (count($progema_array)); $i++) {
							#Por cada linea: delimitador de columna ","
							#$line_array=explode(",", $progema_array[$i]);  
						# mixed preg_replace ( mixed $pattern , mixed $replacement , mixed $subject [, int $limit = -1 [, int &$count ]] )
						#Busca en subject coincidencias de pattern y las reemplaza con replacement.  
							#$lerroa = preg_replace('/^[0-9]+\,/', '', $progema_array[$i]);
							#Si es la primera línea
							#if ($line_array[0] ==1) 
						#$progema_web=$progema_web."<b>".$progema_array[i]."</b><br>";}
						#else 
							#parrafoka
							#kepa
							$line_array=explode("\t", $progema_array[$i]);

							if ($line_array[0] ==1) {
								$progema_web=$progema_web."<br>"; 
							$numberofsentences=$numberofsentences+1;
							} else{
								$progema_web=$progema_web."&nbsp;&nbsp";  
							}

							$j=1;
							if ($line_array[3] == "PROPN"){
								$progema_web=$progema_web."<font color='pink'>".$line_array[1]."</font>";  
							}elseif ($line_array[3] == "PRON") {
								$progema_web=$progema_web."<font color='darkgreen'>".$line_array[1]."</font>";
							}elseif ($line_array[3] == "NOUN") {
								$progema_web=$progema_web."<font color='lightgreen'>".$line_array[1]."</font>";
							}elseif ($line_array[3] == "ADV") {
								$progema_web=$progema_web."<font color='orange'>".$line_array[1]."</font>";
							}elseif ($line_array[3] == "PART") {
								$progema_web=$progema_web."<font color='yellow'>".$line_array[1]."</font>";
							}elseif ($line_array[3] == "ADJ") {
								$progema_web=$progema_web."<font color='darkblue'>".$line_array[1]."</font>";
							}elseif ($line_array[3] == "AUX") {
								$progema_web=$progema_web."<font color='red'>".$line_array[1]."</font>";
							}elseif ($line_array[3] == "VERB") {
								$progema_web=$progema_web."<font color='red'>".$line_array[1]."</font>";
							}else{
									$progema_web=$progema_web.$line_array[$j];  
							}

							$pos = strpos($line_array[8], '}');
							// Note our use of ===. Simply, == would not work as expected
							// because the position of 'a' was the 0th (first) character.
							if ($pos === false) {
								$kk=1; //nop	
							}else{
								$h=$h+1;
								$progema_web=$progema_web."<sup>".$h."</sup>";
								$notaspietexto=$notaspietexto.$h." ".$line_array[8]."<br>"; 
							} 
							#fin de kepa o:
							#$progema_web=$progema_web.$progema_array[$i]."<br>";	
						} #for
							#echo "<div id='emadiv' style='text-align: justify;'>".$progema_web."<small>".$notaspietexto."</small></div>";
							
						#echo "<hr>";
						$wordcloud="<hr><h3 style='text-align: center;'>Word Cloud</h3>";
						#echo "<h3 style='text-align: center;'>Word Cloud</h3>";
						$file=$basefn.".png";
						#echo '<img src="'.$file.'">';
						$wordcloud=$wordcloud.'<img src="'.$file.'">';
						#echo "<div id='emadiv' style='text-align: justify;'>".$progema_web."<small>".$notaspietexto."</small>".$wordcloud."</div>";
						$parsing="<hr><h3 style='text-align: center;'>Syntax</h3>";
						$depreldescription="dependency relations: acl(adjectival clause), amod(adjectival modifier), advcl(adverbial clause modifier), conj(conjunct), advmod(adverbial modifier), appos(appositional modifier), aux(auxiliary verb), case(case marking), ccomp(clausal complement), clf(classifier), compound(compound), cc(coordinating conjunction), cop(copula), csubj(clausal subject), det(determiner), discourse(discourse element), dislocated(dislocated elements), expl(expletive), fixed(fixed multiword expression), flat(flat multiword expression), iobj(indirect object),list(list), nmod(nominal modifier), nsubj(nominal subject), nummod(numeric modifier), mark(marker), obj(object), obl(oblique nominal), orphan(orphan), parataxis(parataxis), punct(punctuation), root(root), vocative(vocative), xcomp(open clausal complement)";
						#echo "<hr>";
						#echo "<h3 style='text-align: center;'>Parsing</h3>";
						for ($j=1; $j < $numberofsentences+1; $j++) {
							$ficheros=$j.".png";
							#echo '<img src="'.$ficheros.'">'; #width="600" height="400">';
							#echo "<hr>";
							$parsing=$parsing.'<img src="'.$ficheros.'"><hr>';
						}
						echo "<div id='emadiv' style='text-align: justify;'>".$progema_web."<small>".$notaspietexto."</small>".$wordcloud.$parsing."<small>".$depreldescription."</small>"."</div>";
					} else {
						echo "<p>Erroreren bat egon da testua analizatzean.</p>";
					}
				
				}

			} else { 
				echo("<p>Erroreren bat egon da fitxategi tenporala sortzean.</p>");
			}
			
		}
	?>
<hr>
<div style="width: 860px;margin-left: auto; margin-right: auto;text-align:left;font-family:arial;font-size:11pt;">
</div>
</body>
</html>
