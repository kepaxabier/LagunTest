#!/bin/bash
cd /var/www/html/erraztest/$1
libreoffice --convert-to txt $2
mv "${2%.*}"'.txt' 'text.txt'
