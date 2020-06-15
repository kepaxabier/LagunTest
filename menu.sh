#!/bin/bash

##Instalacion y Mantenimiento de una Aplicacion Web
#Importar funciones de otros ficheros
############################################################
#                 0)DESINSTALAR 
#############################################################
function desinstalar()
{
sudo service apache2 stop
sudo apt-get update
#Then uninstall Apache2 and its dependent packages. Use purge option instead of remove with apt-get command. The former option will try to remove dependent packages, as well as any configuration files created by them. In addition, use autoremove option as well, to remove any other dependencies that were installed with Apache2, but are no longer used by any other package.
sudo apt-get purge apache2 apache2-utils apache2-data
#paquetes sugeridos: apache2-doc apache2-suexec-pristine | apache2-suexec-custom
sudo apt-get purge php libapache2-mod-php*
#
sudo apt-get autoremove
sudo rm -rf /var/www/html/*
}

###########################################################
#                  1) INSTALL APACHE                     #
###########################################################
function apacheInstall()
{
	aux=$(aptitude show apache2 | grep "State: installed")
	aux2=$(aptitude show apache2 | grep "Estado: instalado")
	aux3=$aux$aux2
	if [ -z "$aux3" ]
	then 
 	  echo "instalando ..."
 	  sudo apt-get install apache2
	else
   	  echo "apache ya estaba instalado"
    
	fi 
}


###########################################################
#        2) WEB APACHE ZERBITZUA PROBATU/TESTEATU         #
###########################################################

function webApacheTest(){
#para saber si el servico esta activado
sudo systemctl status apache2
aux=$(sudo systemctl status apache2 | grep "active")
if [ -z "$aux" ]
	then 
 	  echo "activando ..."
 	  sudo /etc/init.d/apache2 start
          #sudo service apache2 start
          #sudo systemctl start apache2
	  echo "Apache2 esta en marcha"
	  sleep 1
	else
   	  echo "apache ya estaba activado"
    
	fi 
sleep 1
#Para saber apache por que puerto esta escuchando 
echo "Apache esta escuchando por el puerto:"
sudo apt install net-tools
listening=$(sudo netstat -anp | grep "apache2")
if [ ! -z "$listening" ]
then
	#This displays no error on sensible-browser execution
	echo "Apache está escuchando."
        sleep 1
	#"It Works"
	echo "Para saber si se visualiza la página por defecto index.html que esta en la carpeta /var/www/html"  
	firefox http://127.0.0.1/index.html
else
	echo "Apache no está escuchando por el puerto 80."
fi

}
############################################################
#                  3) Crea un entorno virtual host
###########################################################
function createvirtualhost(){
#http://localhost:8080/index.php
sudo rm /etc/apache2/sites-available/erraztest.conf
sudo mkdir /var/www/html/erraztest
sudo cp /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-available/erraztest.conf
sudo sed -i "s/80/8080/g" /etc/apache2/sites-available/erraztest.conf
sudo sed -i "s/\/var\/www\/html/\/var\/www\/html\/erraztest/g" /etc/apache2/sites-available/erraztest.conf
sudo sed -i "s/<\/VirtualHost>/\<Directory \/var\/www\/html\/erraztest\>\nOptions Indexes FollowSymLinks MultiViews\nAllowOverride All\nOrder allow,deny\nallow from all\n<\/Directory>\n<\/VirtualHost>\n/g" /etc/apache2/sites-available/erraztest.conf
aux2=$(grep 8080 /etc/apache2/ports.conf)
echo $aux2
if [ -z "$aux2" ]
then
 	echo -e "Añadiendo puerto Listen 8080\n"	
        sudo sed -i "s/Listen 80/Listen 80\nListen 8080/g"  /etc/apache2/ports.conf
	sleep 1
else
	echo -e "Ya esta añadido\n"	
fi
cd /etc/apache2/sites-available
sudo a2ensite erraztest.conf
sudo systemctl reload apache2
}
############################################################
#                  4)Testea el virtual host creado, es decir,  que cuando apache le llega una petición por el puerto 8080 muestra el index.html de la carpeta /var/www/html/erraztest.
############################################################
function webVirtualApacheTest(){
#para saber si el servico esta activado
sudo systemctl status apache2
aux=$(sudo systemctl status apache2 | grep "active")
if [ -z "$aux" ]
	then 
 	  echo "activando ..."
 	  sudo /etc/init.d/apache2 start
          #sudo service apache2 start
          #sudo systemctl start apache2
	  echo "Apache2 esta en marcha"
	  sleep 1
	else
   	  echo "apache ya estaba activado"
    
	fi 
sleep 1
#Para saber apache por que puerto esta escuchando 
echo "Apache esta escuchando por el puerto:"
sudo apt install net-tools
listening=$(sudo netstat -anp | grep "apache2")
if [ ! -z "$listening" ]
then
	#This displays no error on sensible-browser execution
		echo "Apache está escuchando por el puerto 8080."
                sleep 1
		#"It Works"
		echo "Copiamos la página por defecto index.html que esta en la carpeta /var/www/html"  
                        sudo cp /var/www/html/index.html /var/www/html/erraztest
		sleep 5
		firefox http://127.0.0.1:8080
else
		echo "Apache no está escuchando por el puerto 8080."
fi

}
###########################################################
#                  5) PHP INSTALATU                       #
###########################################################

function phpInstall(){
#Para saber si el modulo php esta instalado
aux=$(aptitude show php | grep "State: installed")
aux2=$(aptitude show php | grep "Estado: instalado")
aux3=$aux$aux2
if [ -z "$aux3" ]
then
	#ez dago instalatuta
	echo "Instalando el modulo php ...\n"	
	sudo apt install php libapache2-mod-php
	sudo systemctl restart apache2
	echo "El modulo php se ha instalado correctamente\n"	
	sleep 1
else
	#Instalatuta dago
	echo "PHP ya estaba instalado\n" 5 40	
	sleep 1
fi
}


###########################################################
#                  6) PHP TESTEATU                        #
###########################################################
function phpTest(){
#Sortu “testphp.php” fitxategia  /var/www katalogoan hurrengo kodearekin  ( <?php phpinfo(); ?>)
        sudo echo "<?php phpinfo(); ?>" > test.php
        sudo cp test.php /var/www/html/erraztest/
	sleep 2
#Firefox-en ireki http://localhost/testphp.php  
	echo "Firefox-en ireki http://localhost/testphp.php irekiko da\n"
	sleep 2
	firefox http://127.0.0.1:8080/test.php 
}	

############################################################
#               7) Creando Entorno Virtual Python3
#############################################################
function creandoEntornoVirtualPython3(){
aux=$(aptitude show virtualenv | grep "State: installed")
aux2=$(aptitude show virtualenv | grep "Estado: instalado")
aux3=$aux$aux2
if [ -z "$aux3" ]
then
 	#instalar el entorno virtual y python3
 	sudo apt-get install virtualenv python3
 	#or sudo apt-get install -y python3-venv 
 	#o tambien con pip:sudo pip install virtualenv
 else
	#Instalatuta dago
	echo "Entorno virtual instalado\n" 5 40	
	sleep 1
fi
if [ -d "/var/www/html/erraztest/python3envmetrix" ]
then
   #Instalatuta dago
  echo "Entorno virtual creado\n" 5 40	
  sleep 1
  
else
  #Para crear un entorno virtual con Python 3, simplemente ejecutamos el comando virtualenv de la siguiente manera:
   sudo python3 -m venv /var/www/html/erraztest/python3envmetrix
   #o
   #sudo virtualenv /var/www/html/erraztest/python3envmetrix --python=python3
fi
}
 
#############################################################

############################################################
#               8) Instala los paquetes necesarios, para la aplicación 
#############################################################
function instalandoPaquetesEntornoVirtualPythonyAplicacion(){
 #ir a la carpeta python3envmetrix
 cd /var/www/html/erraztest/python3envmetrix
 source bin/activate
 apt install python3-pip
 pip3 install numpy
 pip3 install nltk
 pip3 install -U wordcloud
 pip3 install argparse
 pip3 install -U nlpcube
 pip3 install csv
 pip3 install textract
 pip3 install -U wordfreq
 pip3 install uuid
 pip3 install wikipedia
 pip3 install wikidata
 pip3 install beautifulsoup4
 pip3 install requests
 pip3 install stanfordnlp
 pip3 install SPARQLWrapper
 #dos2unix
 sudo apt install dos2unix
#deactivate: es un comando del bash: desactivar garantiza que cualquier entorno virtual existente se desactive antes de crear uno nuevo. Los entornos virtuales están separados unos de otros; no pueden ser anidados
#deactivate calls export to restore the old environmental variables, then calls unset to remove unneeded variables from the environment. (You can verify this from the terminal by using the command env to view all your environmental variables.) Finally, deactivate calls unset -f deactivate to remove the deactivate function itself. (-f removes a function.) The function is now gone from the environment, which you can easily verify:
deactivate
#copiamos los programas y ficheros necesarios a /var/www/html
cd /media/datos/Dropbox/ikerkuntza/metrix-env/LagunTest-master
sudo cp *.php *.sh *.py *.png *.gif /var/www/html/erraztest
sudo cp -r en /var/www/html/erraztest
sudo cp -r es /var/www/html/erraztest
sudo cp -r eu /var/www/html/erraztest
sudo cp -r css /var/www/html/erraztest
sudo cp -r DepSVG-master /var/www/html/erraztest
}


###################################################################
#Desambiguador semántico
###################################################
instalandodesambiguadorsemantico()
{
#Instalar ukb:http://ixa2.si.ehu.es/ukb/
#The current version of UKB is 3.2. You can download it here
cd /var/www/html/erraztest
sudo wget http://ixa2.si.ehu.es/ukb/ukb_3.2.tgz
sudo tar -xvzf ukb_3.2.tgz
sudo mv ukb-3.2 ukb-master
cd ukb-master/
cd src
#less INSTALL
#ukb needs boost version 1.48 or higher to compile.
sudo apt install libboost-dev
sudo apt install libboost-all-dev
#dpkg -s libboost-dev | grep Version
#Download ukb de https://github.com/asoroa/ukb
#whereis boost->boost: /usr/include/boost
#Para compilar ukb una vez instalado boost
#Crear el fichero makefile
sudo ./configure --with-boost-include=/usr/include
sudo make
#src/README
#The 'ukb_wsd' application performs knowledge-based WSD using a Personalized PageRank calculation. It needs a compiled KB, a dictionary and an input context.
}

instalandoyconfigurandodesambiguadorsemanticoparaingles()
{
#1.Follow these instructions to create a graph and dictionary from WordNet 3.1 (including gloss relations). The obtained graph relations and dictionary can be directly downloaded from [here](http://ixa2.si.ehu.es/ukb/graphs/wnet30_eng.tar.bz2)
#1.1
cd /var/www/html/erraztest/ukb-master/src
sudo wget http://ixa2.si.ehu.es/ukb/graphs/wnet30_eng.tar.bz2
sudo bunzip2 wnet30_eng.tar.bz2
sudo tar -xvf wnet30_eng.tar
cd wnet30_eng
sudo cp wnet30_dict.txt wnet30g_rels.txt wnet30_rels.txt ..
cd ..
#1.2 Compiling the KB: 
sudo cat wnet30_rels.txt wnet30g_rels.txt | sudo ./compile_kb -o wn30+gloss.bin -
}
arrancardesambiguadorsemanticoingles()
{
# 3. Arrancar el servidor:
cd /var/www/html/erraztest/ukb-master/src      
sudo ./ukb_wsd --daemon --port 10000 -K wn30+gloss.bin -D wnet30_dict.txt --ppr_w2w
}
pararservidorsemantico()
{
/var/www/html/erraztest/ukb-master/src/ukb_wsdukb_wsd --shutdown --port 10001
}
testeardesambiguadorsemanticoingles()
{
echo -e "ctx_01\nman#n#w1#1 kill#v#w2#1 cat#n#w3#1 hammer#n#w4#1" > /tmp/context.txt
/var/www/html/erraztest/ukb-master/src/ukb_wsd --client --port 10000 /tmp/context.txt

}

instalandoeldiccionariodesambiguadorsemanticoparaeuskara()
{
#For Basque download the dictionary from /sc01a7/sisx09/sx09a1/jirhizts/Programak/WN-desanbiguazioa/Ukb/Data/Preproc/euwn3.0_index.sense_freq
#wordnet relation are englih relations that they can be downloaded from http://ixa2.si.ehu.es/ukb/lkb_sources.tar.bz2 .The files are 30/wnet30_rels.txt and 30/wnet30g_rels.txt 
#La bajamos a la carpeta recuros euskara que finalmente serán copiados a /var/www/html/erraztest/eu/euwn3.0_index.sense_freq 
# Y de aqui /var/www/html/erraztest/ukb-master/src
sudo cp /var/www/html/erraztest/eu/euwn3.0_index.sense_freq /var/www/html/erraztest/ukb-master/src
}
instalandoeldiccionariodesambiguadorsemanticoparaespanol()
{
#http://ixa2.si.ehu.es/ukb/
#->Downloads->Selected graphs:->Click here to get graph relations of some versions of the Spanish WordNet.
cd /var/www/html/erraztest/ukb-master/src
sudo wget http://ixa2.si.ehu.es/ukb/lkb_sources_es.tar.bz2
sudo bunzip2 lkb_sources_es.tar.bz2
sudo tar -xvf lkb_sources_es.tar
#ls lkb_sources_es/30sp/ili-wnet30g_rels.txt, ili-wnet30_rels.txt, wn30sp.lex
sudo cp lkb_sources_es/30sp/ili-wnet30g_rels.txt .
sudo cp lkb_sources_es/30sp/ili-wnet30_rels.txt .
sudo cp lkb_sources_es/30sp/wn30sp.lex .
#1.2 Compiling the KB: 
sudo cat ili-wnet30_rels.txt ili-wnet30g_rels.txt | sudo ./compile_kb -o wn30+gloss_es.bin -
}
arrancardesambiguadorsemanticoespanol()
{
# 3. Arrancar el servidor:
cd /var/www/html/erraztest/ukb-master/src      
#sudo ./ukb_wsd --daemon --port 10000 -K wn30+gloss.bin -D wnet30_dict.txt --ppr_w2w
sudo ./ukb_wsd --daemon --port 10002 -K wn30+gloss_es.bin -D wn30sp.lex --ppr_w2w
}
testeardesambiguadorsemanticoespanol()
{
echo -e "ctx_03\nhombre#n#w1#1 gato#n#w2#1" > /tmp/context3.txt
cd /var/www/html/erraztest/ukb-master/src
./ukb_wsd --client --port 10002 /tmp/context3.txt
}

arrancardesambiguadorsemanticoeuskara()
{
# 3. Arrancar el servidor:
#sudo ./ukb_wsd --daemon --port 10000 -K wn30+gloss.bin -D wnet30_dict.txt --ppr_w2w
sudo /var/www/html/erraztest/ukb-master/src/ukb_wsd --daemon --port 10001 -K /var/www/html/erraztest/ukb-master/src/wn30+gloss.bin -D /var/www/html/erraztest/ukb-master/src/euwn3.0_index.sense_freq --ppr_w2w

}
testeardesambiguadorsemanticoeuskara()
{
echo -e "ctx_02\ngizon#n#w1#1 katu#n#w2#1" > /tmp/context2.txt
cd /var/www/html/erraztest/ukb-master/src
./ukb_wsd --client --port 10001 /tmp/context2.txt
#/var/www/html/erraztest/ukb-master/src/ukb_wsd --client --port 10001 01d569e7-3f5e-4836-af68-3da21104d8b7
}


#############################################################
#               9) Visualizar la aplicación
function visualizandoAplicacion()
{
   #Paso la propiedad a www-data que será el usuario con lo que apache lo ejecutará.
sudo chown -R www-data:www-data /var/www
#Si lo ejecuto como www-data, para saber donde falla:
sudo su
su - www-data -s /bin/bash
cd /var/www/html/erraztest
chmod u+x ./nlpcubeparserprocess.sh
mkdir -p /var/www/html/erraztest/15e74ab6b36aea/
echo "Earth is truly a remarkable planet. It is the only planet in our solar system that has the components necessary to support life as we recognize it. The planet is only a tiny part of the universe, but it is the home of human beings and many other organisms. " > /var/www/html/erraztest/15e74ab6b36aea/text.txt
./nlpcubeparserprocess.sh text.txt b english /var/www/html/erraztest/15e74ab6b36aea/
echo "La leche materna humana es el alimento natural producido por la madre para alimentar al recién nacido. Se recomienda como alimento exclusivo para el lactante hasta los seis meses de edad y con alimentación complementaria hasta los dos años de edad,​ ya que contiene la mayoría de los nutrientes necesarios para su correcto crecimiento y desarrollo." > /var/www/html/erraztest/15e74ab6b36aea/text.txt
echo "La leche materna humana es el alimento natural producido por la madre para alimentar al recién nacido." > /var/www/html/erraztest/15e74ab6b36aea/text.txt


./nlpcubeparserprocess.sh text.txt b spanish /var/www/html/erraztest/15e74ab6b36aea/
echo "Esnea ugaztunen hesteetako guruinek sortutako elikagai likido zuria da. Haur ugaztunentzako elikadura iturri nagusia da." > /var/www/html/erraztest/15e74ab6b36aea/text.txt
./nlpcubeparserprocess.sh text.txt b basque /var/www/html/erraztest/15e74ab6b36aea/

#source python3envmetrix/bin/activate
#pip3 install -U wordcloud
#Descomentar en laguntest.py -> cargador.download_model()
#Y # Instalar 'wordnet' nltk.download('wordnet') # Add multilingual wordnet nltk.download('omw')
#He editado para quitar el warning: "The code that caused this warning is on line 389 of the file /var/www/html/erraztest/python3envmetrix/lib/python3.6/site-packages/wikipedia/wikipedia.py. To get rid of this warning, pass the additional argument 'features="lxml"' to the BeautifulSoup constructor. lis = BeautifulSoup(html,features="lxml").find_all('li') "OK
firefox http://127.0.0.1:8080/index.php 
#!Comenta  cargador.download_model() y Y # Instalar 'wordnet' nltk.download('wordnet') # Add multilingual wordnet nltk.download('omw')
}

##########################################################
#    10) Viendo los logs y errores de Apache
function viendoLogs()
{
  tail -n 100 /var/log/apache2/error.log
}

#################################################################
#    11) Gestionando los logs de ssh 
function gestionarLogs(){
cd /var/log/
archivoscomprimidos="/tmp/aux.txt"
archivosno="/tmp/aux2.txt"
verconzcat=`ls auth.log*.gz`
zcat $verconzcat > $archivoscomprimidos
cat /var/log/auth.log /var/log/auth.log.1 > $archivosno
echo -e "Los ficheros tratados son: $archivosno $archivoscomprimidos\n"
cat $archivosno $archivoscomprimidos | grep sshd| grep "Failed password" |tr -s " "|tr " " "@" > /tmp/logfailtratados.txt
echo -e "Los intentos de conexión por ssh, hoy, esta semana y este mes han sido:\n"
for linea in `less /tmp/logfailtratados.txt`
do
usuario=$(echo $linea | cut -f9 -d "@")
fecha=$(echo $linea | cut -f1-3 -d "@")
echo -e "Status: [fail] Account name: $usuario Date: `echo $fecha|tr "@" ","`"
done
sleep 1
}
########################################################
#     PASAR A SERVIDOR
###########################################################
function pasaraservidor()
{
#cd /media/datos/Dropbox/
#cp /media/datos/Dropbox/ikerkuntza/UZ/programak/jupyter/jupyter_env/textclassification/webtool1/nlpcubeparser.py /media/datos/Dropbox/ikerkuntza/UZ/programak/jupyter/jupyter_env/textclassification/webtool1/nlpcubeparserprocess.sh /media/datos/Dropbox/ikerkuntza/UZ/programak/jupyter/jupyter_env/textclassification/webtool1/nlpcubeparser.php /media/datos/Dropbox/ikerkuntza/UZ/programak/jupyter/jupyter_env/textclassification/webtool1/resume.png /media/datos/Dropbox/TFG1Instalador
#cp -r /media/datos/Dropbox/ikerkuntza/UZ/programak/jupyter/jupyter_env/textclassification/webtool1/DepSVG-master /media/datos/Dropbox/TFG1Instalador
#scp TFG1Instalador/nlpcubeparser* xxxxx@xxx.xxx.xxx.xxx:/home/xxxx
#scp -r TFG1Instalador/DepSVG-master xxxxx@xxx.xxx.xxx.xxx:/home/xxxx
#ssh xxxxx@xxx.xxx.xxx.xxx
#sudo cp nlpcubeparser* /var/www/html
#sudo cp -r DepSVG-master /var/www/html
#sudo chown -R www-data:www-data /var/www
#sudo su
#su - www-data -s /bin/bash
#firefox http://xxx.xxx.xxx.xxx/nlpcubeparser.php
#En cada cambio en una terminal:
sudo cp /media/datos/Dropbox/ikerkuntza/metrix-env/LagunTest/lactancia.txt /var/www/html/erraztest
sudo cp /media/datos/Dropbox/ikerkuntza/metrix-env/LagunTest/lactation.txt /var/www/html/erraztest
sudo cp /media/datos/Dropbox/ikerkuntza/metrix-env/LagunTest/edoskitze.txt /var/www/html/erraztest
sudo cp /media/datos/Dropbox/ikerkuntza/metrix-env/LagunTest/laguntest.py /var/www/html/erraztest
sudo chown -R www-data:www-data /var/www

#EN OTRA TERMINAL:
#Si lo ejecuto como www-data, para saber donde falla:
sudo su
su - www-data -s /bin/bash
cd /var/www/html/erraztest
chmod u+x ./nlpcubeparserprocess.sh
#english
cp /var/www/html/erraztest/lactation.txt /var/www/html/erraztest/tmp/text.txt
./nlpcubeparserprocess.sh text.txt b english /var/www/html/erraztest/tmp/
#basque
cp /var/www/html/erraztest/edoskitze.txt /var/www/html/erraztest/tmp/text.txt
./nlpcubeparserprocess.sh text.txt b basque /var/www/html/erraztest/tmp/
#español
cp /var/www/html/erraztest/lactancia.txt /var/www/html/erraztest/tmp/text.txt
./nlpcubeparserprocess.sh text.txt b spanish /var/www/html/erraztest/tmp/
#source python3envmetrix/bin/activate
#pip3 install -U wordcloud
#Descomentar en laguntest.py -> cargador.download_model()
#Y # Instalar 'wordnet' nltk.download('wordnet') # Add multilingual wordnet nltk.download('omw')
#He editado para quitar el warning: "The code that caused this warning is on line 389 of the file /var/www/html/erraztest/python3envmetrix/lib/python3.6/site-packages/wikipedia/wikipedia.py. To get rid of this warning, pass the additional argument 'features="lxml"' to the BeautifulSoup constructor. lis = BeautifulSoup(html,features="lxml").find_all('li') "OK
firefox http://127.0.0.1:8080/index.php 
#!Comenta  cargador.download_model() y Y # I
}

###########################################################
#                     13) SALIR                          #
###########################################################

function fin()
{
	echo -e "¿Quieres salir del programa?(S/N)\n"
        read respuesta
	if [ $respuesta == "N" ] 
		then
			opcionmenuppal=0
		fi	
}

### Main ###
opcionmenuppal=0
while test $opcionmenuppal -ne 23
do
	#Muestra el menu
        echo -e "0 Desinstalar\n"
	echo -e "1 Instala Apache \n"
	echo -e "2 Testea el servicio Web Apache \n"
        echo -e "3 Crear Virtual Host \n" 
        echo -e "4 Testea el virtual host \n" 
	echo -e "5 Instala el modulo php \n"
	echo -e "6 Testea PHP\n"
        echo -e "7 Creando un entorno virtual para Python3 \n"
	echo -e "8 Instala los paquetes necesarios en el entorno virtual y la aplicación \n"
	echo -e "9 Instala el desabiguador semantico \n"
        echo -e "10 configura el desambiguador semantico para ingles \n"
        echo -e "11 Servidor desambiguador semantico para ingles escuchando por el puerto 10000\n"
        echo -e "12 Cliente testea si desambiguador semantico para ingles esta escuchando por el puerto 10000\n"
        echo -e "13 Instala diccionario para el desambiguador de euskara\n"
        echo -e "14 Servidor desambiguador semantico para euskara escuchando por el puerto 10001\n"
        echo -e "15 Cliente testea si desambiguador semantico para euskara esta escuchando por el puerto 10001\n"
        echo -e "16 Instala diccionario para el desambiguador de espanol\n"
        echo -e "17 Servidor desambiguador semantico para espanol escuchando por el puerto 10002\n"
        echo -e "18 Cliente testea si desambiguador semantico para espanol esta escuchando por el puerto 10002\n"
        echo -e "19 Visualiza la aplicación \n"
        echo -e "20 Viendo los logs y errores de Apache \n"
        echo -e "21 Controla los intentos de conexión de ssh \n"
        echo -e "22 Pasar a servidor \n"
	echo -e "23 Exit \n"
	read -p "Elige una opcion:" opcionmenuppal
	case $opcionmenuppal in
                        0) desinstalar;;
			1) apacheInstall;;
			2) webApacheTest;;
                        3) createvirtualhost;;
                        4) webVirtualApacheTest;;
			5) phpInstall;;
			6) phpTest;;
			7) creandoEntornoVirtualPython3;;
			8) instalandoPaquetesEntornoVirtualPythonyAplicacion;;
                        9) instalandodesambiguadorsemantico;;
                        10) instalandoyconfigurandodesambiguadorsemanticoparaingles;;
                        11) arrancardesambiguadorsemanticoingles;;
                        12) testeardesambiguadorsemanticoingles;;
                      	13) instalandoeldiccionariodesambiguadorsemanticoparaeuskara;;
			14) arrancardesambiguadorsemanticoeuskara;;
                        15) testeardesambiguadorsemanticoeuskara;;
                        16) instalandoeldiccionariodesambiguadorsemanticoparaespanol;;
			17) arrancardesambiguadorsemanticoespanol;;
                        18) testeardesambiguadorsemanticoespanol;;
                        19) visualizandoAplicacion;;
			20) viendoLogs;;
			21) gestionarLogs;;
                        22) pasaraservidor;;
			23) fin;;
			*) ;;

	esac 
done 

echo "Fin del Programa" 
exit 0 
