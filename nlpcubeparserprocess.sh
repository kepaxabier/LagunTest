#!/bin/bash
source python3envmetrix/bin/activate
python3 ./laguntest.py $1 $2 $3 $4
#python3 ./nlpcubeparser.py textos/english.doc.txt
#Para salir
deactivate
cd /var/www/html/erraztest/DepSVG-master
perl -I src demo/conll_to_svg.perl --dir $4 < $1.syntax.csv
cd $4
for i in *.svg
do 
/usr/bin/rsvg-convert $i -o `echo $i | sed -e 's/svg$/png/'` 
done
#cuando fin de sesion
#rm -rf /var/www/html/erraztest/$4



