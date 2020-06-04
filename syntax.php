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
$carpeta= $_SESSION["carpeta"];

if (file_exists($emaPath)){
	$progema = file_get_contents($emaPath);
    $numberofsentences=0;
	$depreldescription="dependency relations: acl(adjectival clause), amod(adjectival modifier), advcl(adverbial clause modifier), conj(conjunct), advmod(adverbial modifier), appos(appositional modifier), aux(auxiliary verb), case(case marking), ccomp(clausal complement), clf(classifier), compound(compound), cc(coordinating conjunction), cop(copula), csubj(clausal subject), det(determiner), discourse(discourse element), dislocated(dislocated elements), expl(expletive), fixed(fixed multiword expression), flat(flat multiword expression), iobj(indirect object),list(list), nmod(nominal modifier), nsubj(nominal subject), nummod(numeric modifier), mark(marker), obj(object), obl(oblique nominal), orphan(orphan), parataxis(parataxis), punct(punctuation), root(root), vocative(vocative), xcomp(open clausal complement)";

	$h=0;
	# array explode ( string $delimiter , string $string [, int $limit = PHP_INT_MAX ] )
	# Devuelve un array de string, siendo cada uno un substring del parámetro string formado por la división realizada por los delimitadores indicados en el parámetro delimiter. 
	$progema_array = explode("\n", $progema);
	for ($i=0; $i < (count($progema_array)); $i++) {
		$line_array=explode("\t", $progema_array[$i]);
		if ($line_array[0] ==1) {
			$numberofsentences=$numberofsentences+1;
		}
    }
    
    for ($j=1; $j < $numberofsentences+1; $j++) {
	   	$ficheros=$carpeta."/".$j.".png";
		#$parsing=$parsing.'<img src="'.$ficheros.'"/><hr>';
		echo "<div class='gallery'>";
		echo "<a target='_blank'>";
		echo "<img src='".$ficheros."'";
		echo "</a>";
		echo "</div>"; 
	}
	echo "<small>".$depreldescription."</small>";
}


?>

</body>
</html>
