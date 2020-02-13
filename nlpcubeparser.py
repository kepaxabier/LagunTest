# coding=utf-8
import nltk
import sys
import re
from wordcloud import WordCloud
from wordfreq import zipf_frequency
nltk.download('wordnet')
from nltk.corpus import wordnet as wn
from nltk.wsd import lesk
import wikipedia
import os


def set_is_empty(some_set):
    return some_set == set()
#def plot_wordcloud(wordcloud):
#    plt.imshow(wordcloud)
#    plt.axis("off")
#    plt.show()
#Este método devuelve true en caso de que la palabra pasada como parámetro sea verbo. Para que una palabra sea 
#verbo se tiene que cumplir que sea VERB o que sea AUX y que su padre NO sea VERB.

####analizador sintactico#######################
from cube.api import Cube
cube=Cube(verbose=True)
#sudo chown -R www-data:www-data /var/www ya que baja el modelo a /var/www/.nlpcube/models/en-1.1
cube.load("en") 

####input file##################
input=sys.argv[1]

####nivel de dificultad#########
difficult = int(sys.argv[2])

####ouput files#################
#estadisticos
estadisticaoutput=input+".out.csv"

#Write all the information in the file
estfile = open(estadisticaoutput, "w")


###############Tratamiento de texto###############################################
#quitar todos los retornos \n si contiene
text = open(input).read().replace('\n', '')
#remove text inside parentheses
#text = re.sub(r'\([^)]*\)', '', text)
#separa , . ! ( ) ? ; del texto con espacios, teniendo en cuenta que los no son numeros en el caso de , y . 
text = re.sub(r'[.]+(?![0-9])', r' . ', text)
text = re.sub(r'[,]+(?![0-9])', r' , ', text)
text = re.sub(r"!", " ! ", text)
text = re.sub(r"\(", " ( ", text)
text = re.sub(r"\)", " ) ", text)
text = re.sub(r"\?", " ? ", text)
text = re.sub(r";", " ; ", text)
#sustituye 2 espacios seguidos por 1
text = re.sub(r"\s{2,}", " ", text)
##############################################################################################
#Generating a word cloud with no optional parameters based on the above string:
from wordcloud import STOPWORDS
stopwords = set(STOPWORDS)
#stopwords.add("every")
#stopwords.add("will")
#stopwords={'to', 'of', 'us'}
#Generating a word cloud with no optional parameters based on the above string:
# #This is because the wordcloud module ignores stopwords by default. Refer to Part 1 of the NLTK tutorial if the concept of stopwords is new to you.If we wish, we can specify our own set of stopwords, instead of the stopwords provided by default.
# #Con relative_scaling = 0, solo se consideran los rangos de las palabras. Si modificamos esto a relative_scaling = 1.0, entonces una palabra que aparece dos veces más frecuentemente aparecerá dos veces el tamaño. Por defecto, relative_scaling = 0.5.
# wordcloud = WordCloud(relative_scaling=1.0, stopwords={'to', 'of'}).generate(text)
wordcloud = WordCloud(relative_scaling=1.0,stopwords=stopwords).generate(text)

#Finally, use matplotlib to render the word cloud:
#plot_wordcloud(wordcloud)
wordcloudfilename=input+".png"
#wordcloudfilename="resume.png"
wordcloud.to_file(wordcloudfilename) 

###################analizador morfosintactico#################################################
palabras_raras = []
palabras_diferentes = []
sequences=cube(text)
for sequence in sequences:
        #Por cada sentencia
	for entry in sequence:
		nueve="_"                
		#Por cada palabra
		if entry.word.isalpha() and (entry.upos == 'ADJ' or entry.upos == 'NOUN' or entry.upos == 'VERB' or entry.upos == 'AUX' or entry.upos == 'NOUN' or entry.upos == 'ADV') and entry.word not in palabras_diferentes:
			#Si la palabra es un content word
			palabras_diferentes.append(entry.word)
			wordfrequency = zipf_frequency(entry.word, 'en')
			#lemafrequency = zipf_frequency(entry.lemma, 'en')
			if wordfrequency <= int(sys.argv[2]):
                                #Si es rara
				palabras_raras.append(entry.word)
				lema=entry.lemma
				synset_ids = wn.synsets(lema)
				if entry.upos=='NOUN':
					patron='.n.'
				if entry.upos=='VERB':
					patron='.v.'
				if entry.upos=='ADJ':
					patron='.a.'
				if entry.upos=='ADV':
					patron='.r.'
				contador=1
				for synset in synset_ids:
					#si tiene sinonimos
					if patron in synset.name():
                                                #si sinonimo del mismo patron 
						if contador==1:
							#La primera inicializo el conjunto a 0
							synonyms = []
							for l in synset.lemmas():
								synonyms.append(l.name())
							try:
								synonyms.remove(entry.word)
							except:
								pass
							try:
								synonyms.remove(entry.lemma)
							except:
								pass
							if not set_is_empty(set(synonyms)):
								nueve=entry.word+":"+str(set(synonyms))
							else:
								nueve="_"
							contador=contador+1

		#Definiciones
		# shows all the available synsets
		definiciones = "{"
		word = entry.word
		word_syns = wn.synsets(word)
		syns_defs = [word_syns[i].definition() for i in range(len(word_syns))]
		#print(len(syns_defs))

		for i in range(len(syns_defs)):
			if i < 3:
				definiciones = definiciones+word_syns[i].name()+":"+syns_defs[i]+", "
		tmp_def = len(definiciones)
		definiciones = definiciones[:tmp_def - 2]
		definiciones = definiciones+"}"

		definicion= ''
		#Desambiguador
		stopwords_aux_verbs = ['be', 'can', 'could', 'dare', 'do', 'have', 'may', 'might', 'must', 'need', 'ought', 'shall', 'should', 'will', 'would']
		if entry.upos=='NOUN':
			definicion = lesk(input, entry.word, pos='n').definition()
			#print(lesk(input, entry.word).definition())
		if (entry.upos=='VERB') and (entry.word not in stopwords_aux_verbs):
			definicion = lesk(input, entry.word, pos='v').definition()

		
		###DRAWING WORDS
		drawing = ' '
		ext = ' '
		
		svg = False
		i = 0
		if entry.upos=='NOUN':
			while (svg==False):
				drawing = wikipedia.page(entry.word).images[i]
				name, ext =os.path.splitext(drawing)
				if ext=='.svg':
					i = i+1
				else:
					svg = True
		


        
		print(str(entry.index)+"\t"+entry.word+"\t"+entry.lemma+"\t"+entry.upos+"\t"+entry.xpos+"\t"+entry.attrs+"\t"+str(entry.head)+"\t"+str(entry.label)+"\t"+definicion+"\t"+nueve+"\t_")
		estfile.write("%s" % str(entry.index)+"\t"+entry.word+"\t"+entry.lemma+"\t"+entry.upos+"\t"+entry.xpos+"\t"+entry.attrs+"\t"+str(entry.head)+"\t"+str(entry.label[:2])+"\t"+definicion+"\t"+nueve+"\t_"+drawing+"\t_"+ext+"\t_")
		
		estfile.write("\n")
	estfile.write("\n")
estfile.close()
