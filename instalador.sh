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
#o
#sudo service apache2 status
sudo /etc/init.d/apache2 start
echo "Apache2 esta en marcha"
sleep 1
#Para saber apache por que puerto esta escuchando 
echo "Apache esta escuchando por el puerto:"
sudo apt install net-tools
listening=$(sudo netstat -anp | grep "apache2")
if [ ! -z "$listening" ]
then
	#This displays no error on sensible-browser execution
		echo "Apache está escuchando por el puerto 80."
                sleep 1
		#"It Works"
		echo "Para saber si se visualiza la página por defecto index.html que esta en la carpeta /var/www/html"  
		sleep 5
		firefox http://127.0.0.1
else
		echo "Apache no está escuchando por el puerto 80."
fi

}

###########################################################
#                  3) PHP INSTALATU                       #
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
#                  4) PHP TESTEATU                        #
###########################################################
function phpTest(){
#Sortu “testphp.php” fitxategia  /var/www katalogoan hurrengo kodearekin  ( <?php phpinfo(); ?>)
    sudo echo "<?php phpinfo(); ?>" > test.php
	sudo cp test.php /var/www/html
	sleep 2
#Firefox-en ireki http://localhost/testphp.php  
	echo "Firefox-en ireki http://localhost/testphp.php irekiko da\n"
	sleep 2
	firefox http://localhost/test.php 
}	

############################################################
#               5) Creando Entorno Virtual Python3
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
 	#or python3 -m venv python3envmetrix
else
	#Instalatuta dago
	echo "Entorno virtual instalado\n" 5 40	
	sleep 1
fi
if [ -d "python3envmetrix" ]
then
   #Instalatuta dago
  echo "Entorno virtual creado\n" 5 40	
  sleep 1
  
else
  #Para crear un entorno virtual con Python 3, simplemente ejecutamos el comando virtualenv de la siguiente manera:
  virtualenv python3envmetrix --python=python3
fi
}

############################################################
#               6) Instala los paquetes necesarios, para la aplicación “Complejidad Textual”
#############################################################



function instalandoPaquetesEntornoVirtualPython3(){
 #ir a la carpeta python3envmetrix
 cd python3envmetrix
 #activando el entorno virtual:https://www.recurse.com/blog/14-there-is-no-magic-virtualenv-edition
 #source runs the file provided in your current shell
 #mantiene las variables que crea o modifica alrededor de la ejecución del archivo. Dado que (casi) todo lo que hace virtualenv es modificar las variables ambientales, esto importa:
 #Establece una variable de entorno bash 
 #************VIRTUAL_ENV que contiene el directorio de entorno virtual*************************
 #VIRTUAL_ENV=/media/datos/Dropbox/docencia/isobilbo/GaiakBerriak/Proyecto/Profesor/AplicacionWeb_ComplejidadTextual/fich/python3envmetrix
 #Exporta la varible a global
 #export VIRTUAL_ENV
 #************PATH que contiene el directorio de los ejecutables**********************************
 #PATH=/media/datos/Dropbox/docencia/isobilbo/GaiakBerriak/Proyecto/Profesor/AplicacionWeb_ComplejidadTextual/fich/python3envmetrix/bin:$PATH
 #Exporta a varibale global
 #export PATH
 #También cambia el prompt (PS1). 
 source bin/activate
#which python3 ->/media/datos/Dropbox/docencia/isobilbo/GaiakBerriak/Proyecto/Profesor/AplicacionWeb_ComplejidadTextual/fich/python3envmetrix/bin/python not the system python (which is found in usr/bin/).
 sudo apt install python3-pip
 
 
 pip3 install -U nltk
 pip3 install -U wordcloud
 pip3 install matplotlib
pip3 install textract
pip3 install -U numpy

pip3 install pandas
pip3 install -U nlpcube
sudo apt install build-essential
pip3 install -U wordfreq
pip3 install pywsd
pip3 install wikipedia
pip3 install wikidata
pip3 install sparqlwrapper
pip3 install request
pip3 install beautifulsoup4
pip3 install stanfordnlp
#Instalar jupiter:
#python3 -m pip install --upgrade pip
#python3 -m pip install jupyter
#python3 -m pip install opencv-python
#python3 -m pip install xlrd


 #dos2unix
 sudo apt install dos2unix
 #deactivate: es un comando del bash: desactivar garantiza que cualquier entorno virtual existente se desactive antes de crear uno nuevo. Los entornos virtuales están separados unos de otros; no pueden ser anidados
 #deactivate calls export to restore the old environmental variables, then calls unset to remove unneeded variables from the environment. (You can verify this from the terminal by using the command env to view all your environmental variables.) Finally, deactivate calls unset -f deactivate to remove the deactivate function itself. (-f removes a function.) The function is now gone from the environment, which you can easily verify:
 deactivate
 cd ..
}




############################################################
#               7) Ejecuta aplicación “Complejidad Textual”
#############################################################
function ejecutandoProgramaPythonEntornoVirtual(){
#ir a la carpeta python3envmetrix
 cd python3envmetrix
 #activando el entorno virtual
 source bin/activate
 python3 ../nlpcubeparser.py ../Primerparrafo.txt
 sleep 5
 #desactiva
 deactivate
 cd ..
}

##############################################################
#               8)instalandoAplicacionComplejidadTextual
function instalandoAplicacionComplejidadTextual()
{
#Borramos por si existen
sudo rm -rf /var/www/html/erraztest/*
#copiamos los programas y ficheros necesarios a /var/www/html

sudo cp -R * /var/www/html/erraztest

#sudo cp index.php nlpcubeparser.php nlpcubeparserprocess.sh nlpcubeparser.py Primerparrafo.txt wordcloud.ipynb wordnet.ipynb nlpcubeparser.ipynb processing.gif resume.png /var/www/html

#sudo mkdir /var/www/html/DepSVG-master
#sudo mkdir /var/www/html/DepSVG-master/demo
#sudo mkdir /var/www/html/DepSVG-master/img
#sudo mkdir /var/www/html/DepSVG-master/src
#sudo mkdir /var/www/html/DepSVG-master/tests

#cd DepSVG-master
#sudo cp LICENSE.txt README.md /var/www/html/DepSVG-master

#cd demo
#sudo cp conll_to_svg.perl treebank.tsv /var/www/html/DepSVG-master/demo

#cd ..
#cd src
#sudo cp DepSVG.pm DepUtils.pm /var/www/html/DepSVG-master/src

#cd ..
#cd testsnlpcubeparser.py
#sudo cp regression_tester.perl /var/www/html/DepSVG-master/tests


#cd ..
#cd ..
#sudo cp -R python3envmetrix /var/www/html

#Paso la propiedad a www-data que será el usuario con lo que apache lo ejecutará.
sudo chown -R www-data:www-data /var/www
cd /var/www/html/erraztest
#sudo sed -i "s/DIRVIRTPYTHON=.*/DIRVIRTPYTHON='python3envmetrix'/g" nlpcubeparserprocess.sh
sudo sed -i "s/VIRTUAL_ENV=.*/VIRTUAL_ENV=\/var\/www\/html\/erraztest\/python3envmetrix/g" python3envmetrix/bin/activate
#Si lo ejecuto como www-data, para saber donde falla:
#sudo su
#su - www-data -s /bin/bash
#./nlpcubeparserprocess.sh Primerparrafo.txt
#Una opcion es eliminar python3envmetrix y crear el entorno virtual desde usuario www-data
}

#############################################################
#               9) Visualizar la aplicación
function visualizandoAplicacionComplejidadTextual()
{
   firefox http://localhost/index.php 
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
###########################################################
#                     12) SALIR                          #
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
while test $opcionmenuppal -ne 12
do
	#Muestra el menu
        echo -e "0 Desinstalar\n"
	echo -e "1 Instala Apache \n"
	echo -e "2 Activa el servicio Web Apache \n"
	echo -e "3 Instala el modulo php \n"
	echo -e "4 Testea PHP\n"
        echo -e "5 Creando un entorno virtual para Python3 \n"
	echo -e "6 Instala los paquetes necesarios, para la aplicación Complejidad Textual \n"
	echo -e "7 Prueba  el programa .py en el entorno virtual \n" 
        echo -e "8 Instala la aplicacion Complejidad Textual \n"
        echo -e "9 Visualiza la aplicación web de Complejidad Textual \n"
        echo -e "10 Viendo los logs y errores de Apache \n"
        echo -e "11 Controla los intentos de conexión de ssh \n"
	echo -e "12 Exit \n"
	read -p "Elige una opcion:" opcionmenuppal
	case $opcionmenuppal in
                        0) desinstalar;;
			1) apacheInstall;;
			2) webApacheTest;;
			3) phpInstall;;
			4) phpTest;;
			5) creandoEntornoVirtualPython3;;
			6) instalandoPaquetesEntornoVirtualPython3;;
                        7) ejecutandoProgramaPythonEntornoVirtual;;
			8) instalandoAplicacionComplejidadTextual;;
			9) visualizandoAplicacionComplejidadTextual;;
			10) viendoLogs;;
			11) gestionarLogs;;
			12) fin;;
			*) ;;

	esac 
done 

echo "Fin del Programa" 
exit 0 
