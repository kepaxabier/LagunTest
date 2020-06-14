<?php
require "header.php" 
?>

<html>

<body>

<nav class="title-aux">
  <ol>  
      <li class="title-aux"><a href="">Syntax</a></li>
  </ol>
</nav>

<?php

session_start();
$results = $_SESSION["results"]; #results
$directory= $_SESSION["directory"];
$syntax= $_SESSION["syntax"];
$language= $_SESSION["language"];

function getDesc($arg){
	$desc="";
    switch ($arg) {
		case "m":
			$desc = array("Modifier","Aditzlaguna", "Complemento circunstancial");
			break;
		case "lo":
			$desc = array("Conjunction", "Lokailua", "Conjunción");
			break;
		case "au":
			$desc = array("Auxiliary verb", "Aditz laguntzailea", "Verbo auxiliar");
			break;
		case "ca":
			$desc = array("Case", "Kasua", "Caso");
			break;
		case "a":
			$desc = array("Core argument (other)", "Bestelako osagarria","Otro complemento");
			break;
		case "mw":
			$desc = array("Multiword", "Hitz konposatua", "Palabra compuesta");
			break;
		case "c":
			$desc = array("Copula verb ('to be')", "'Izan' edo 'Egon' aditza", "Verbo copulativo ('ser' o 'estar')");
			break;
		case "s":
			$desc = array("Subject", "Subjektua", "Sujeto");
			break;
		case "de":
			$desc = array("Determiner", "Artikulu determinatua", "Articulo determinado");
			break;
		case "io":
			$desc = array("Indirect object", "Zehar osagarria", "Complemento indirecto");
			break;
		case "o":
			$desc = array("Object", "Osagarri zuzena", "Complemento directo");
			break;
		case "p":
			$desc = array("Punctuation", "Puntuazioa", "Puntuación");
			break;
		case "v":
			$desc = array("Main verb", "Aditz nagusia", "Verbo principal");
			break;
		case "vo":
			$desc = array("Vocative", "Bokatiboa", "Vocativo");
			break;
		default:
			$desc = array($arg, $arg, $arg);
			break;
	}
	return $desc;
}


if (file_exists($syntax)){
	$progema = file_get_contents($syntax);
    $numberofsentences=0;
	$info;
	$h=0;
	$descripcion="";
	$cuerpo;

	$progema_array = explode("\n", $progema);
	$count = count($progema_array);
	for ($i=0; $i < (count($progema_array)); $i++) {
		$line_array=explode("\t", $progema_array[$i]);


		if ($line_array[0] == 1) {
			$info=[];
			$numberofsentences=$numberofsentences+1;
			$fichero=$directory."/".$numberofsentences.".png";
			$cuerpo = $cuerpo."<div class='gallery' style='width: 50%;'><a target='_blank'><img src='".$fichero."'</a></div><table>";
		}

		if(!in_array($line_array[7], $info) && $line_array[7]!='_' && $line_array[7]!=""){
			$desc = getDesc($line_array[7]);
			if($desc!=""){
				switch ($language) {
					case 'english':
						$descripcion = $desc[0];
						break;
					case 'basque':
						$descripcion = $desc[1];
						break;
					case 'spanish':
						$descripcion = $desc[2];
						break;
					default:
						$descripcion = $desc[0];
						break;
				}
			}
			$cuerpo = $cuerpo.'<tr><td>'.$line_array[7].'</td><td>'.$descripcion.'</td></tr>';
		}

		#Si termina la frase
		if($i+1 < (count($progema_array))){
			$next = explode("\t", $progema_array[$i+1]);
			if($next[0] == 1){
				$cuerpo = $cuerpo.'</table>';
				echo $cuerpo;
				$cuerpo = "";
			}
		}
				
		if($count == $i+1){
			$cuerpo = $cuerpo.'</table>';
			echo $cuerpo;
			$cuerpo = "";
		}

		$info[$i]=$line_array[7];
		$descripcion="";
	}
}

?>

</body>
</html>
