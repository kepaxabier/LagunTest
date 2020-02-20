#!/bin/bash
cd /var/www/html/erraztest
source python3envmetrix/bin/activate
echo -e "$1 $2 $3 $4" > kk.txt
var="$4/"
echo -e "$1 $2 $3 $var" >>kk.txt
python3 /var/www/html/erraztest/laguntest.py $1 $2 $3 $var
echo $? >> kk.txt
#python3 ./nlpcubeparser.py textos/english.doc.txt
#Para salir
deactivate
cd /var/www/html/erraztest/DepSVG-master
perl -I src demo/conll_to_svg.perl --dir $var < $var$1.syntax.csv
cd $var
for i in *.svg
do 
/usr/bin/rsvg-convert $i -o `echo $i | sed -e 's/svg$/png/'` 
done
cd $var

#cuando fin de sesion
#rm -rf /var/www/html/erraztest/$4



