#!/bin/bash
source python3envmetrix/bin/activate
python3 ./stanfordparser.py $1 $2 $3
#python3 ./nlpcubeparser.py textos/english.doc.txt
#Para salir
deactivate
cd /var/www/html/erraztest/DepSVG-master
perl -I src demo/conll_to_svg.perl --dir /var/www/html/erraztest/tmp < $1.syntax.csv
cd /var/www/html/erraztest/tmp
for i in *.svg
do 
/usr/bin/rsvg-convert $i -o `echo $i | sed -e 's/svg$/png/'` 
done
cp /var/www/html/erraztest/DepSVG-master/img/* .
#rm $1.out.csv
#rm $1.png
#rm $1


