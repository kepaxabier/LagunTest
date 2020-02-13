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
$emaPath = $_SESSION["path"];
$basefn = $_SESSION["basefn"];

if (file_exists($emaPath)) //&& filesize($emaPath) > 0)  
{
						#file_get_contents — Transmite un fichero completo a una cadena
	#chmod ($fn,0644);
	$progema = file_get_contents($emaPath);
	#echo "<hr>";
	#echo "<h3 style='text-align: center;'>Synomym list of rare words and Colouring words (proper noun:pink, pronoun:darkgreen, noun:lightgreen, adjective:darkblue, partitive:yellow, verb:red and adverd:orange) </h3>";          
	#$progema_web="<hr><h3 style='text-align: center;'>Synomym list of rare words and Colouring words (proper noun:pink, pronoun:darkgreen, noun:lightgreen, adjective:darkblue, partitive:yellow, verb:red and adverd:orange) </h3>";
	$numberofsentences=0;
	$depreldescription="dependency relations: acl(adjectival clause), amod(adjectival modifier), advcl(adverbial clause modifier), conj(conjunct), advmod(adverbial modifier), appos(appositional modifier), aux(auxiliary verb), case(case marking), ccomp(clausal complement), clf(classifier), compound(compound), cc(coordinating conjunction), cop(copula), csubj(clausal subject), det(determiner), discourse(discourse element), dislocated(dislocated elements), expl(expletive), fixed(fixed multiword expression), flat(flat multiword expression), iobj(indirect object),list(list), nmod(nominal modifier), nsubj(nominal subject), nummod(numeric modifier), mark(marker), obj(object), obl(oblique nominal), orphan(orphan), parataxis(parataxis), punct(punctuation), root(root), vocative(vocative), xcomp(open clausal complement)";

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
			$numberofsentences=$numberofsentences+1;
		}
		#fin de kepa o:
		#$progema_web=$progema_web.$progema_array[$i]."<br>";	
    } #for  
    
    for ($j=1; $j < $numberofsentences+1; $j++) {
		#$file="tmp/".$basefn.".png";
		$ficheros="tmp/".$j.".png";
		#echo '<img src="'.$ficheros.'">'; #width="600" height="400">';
		#echo "<hr>";
		$parsing=$parsing.'<img src="'.$ficheros.'"><hr>';
	}
	


	echo "<div class='image' id='emadiv' style='text-align: center;'>".$parsing."<br>".$depreldescription."</div>";
                    
}


?>

</body>
</html>