cd /media/datos/Dropbox/
cp /media/datos/Dropbox/ikerkuntza/UZ/programak/jupyter/jupyter_env/textclassification/webtool1/nlpcubeparser.py /media/datos/Dropbox/ikerkuntza/UZ/programak/jupyter/jupyter_env/textclassification/webtool1/nlpcubeparserprocess.sh /media/datos/Dropbox/ikerkuntza/UZ/programak/jupyter/jupyter_env/textclassification/webtool1/nlpcubeparser.php /media/datos/Dropbox/ikerkuntza/UZ/programak/jupyter/jupyter_env/textclassification/webtool1/resume.png /media/datos/Dropbox/TFG1Instalador
cp -r /media/datos/Dropbox/ikerkuntza/UZ/programak/jupyter/jupyter_env/textclassification/webtool1/DepSVG-master /media/datos/Dropbox/TFG1Instalador
scp TFG1Instalador/nlpcubeparser* julen@178.128.198.190:/home/julen
#scp -r TFG1Instalador/DepSVG-master julen@178.128.198.190:/home/julen
ssh julen@178.128.198.190
#sudo cp nlpcubeparser* /var/www/html
#sudo cp -r DepSVG-master /var/www/html
#sudo chown -R www-data:www-data /var/www
#sudo su
#su - www-data -s /bin/bash
#firefox http://178.128.198.190/nlpcubeparser.php

