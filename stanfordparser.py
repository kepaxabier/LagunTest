# coding=utf-8
import nltk
import sys
import re
from wordcloud import WordCloud
from wordfreq import zipf_frequency


####input file##################
input=sys.argv[1]

####nivel de dificultad#########
difficult = sys.argv[2]

####idioma###########
language = sys.argv[3]

if (language == 'eu'):
    if (difficult == 'b'):
        difficult = 100000
    elif (difficult == 'm'):
        difficult = 34
    else:
        difficult = 6
    
elif (language == 'en'):
    if (difficult == 'b'):
        difficult = 8
    elif (difficult == 'm'):
        difficult = 5
    else:
        difficult = 3

######################################EUSKERA####################################
if (language == 'eu'):
    import codecs
    d = {}
    with codecs.open('LB2014Maiztasunak_zenbakiakKenduta.csv',encoding='utf-8') as f:
        next(f)
        for line in f:
            (val, key) = line.split(",")
            d[key] = val

def zipf_frequency_eu(lemma):
    if d.get(lemma):
        return int(d.get(lemma))
    else:
        return 1



#################WORDNET##########################
#Instalar 'wordnet' 
nltk.download('wordnet')
#Add multilingual wordnet
nltk.download('omw')
from nltk.corpus import wordnet as wn
def set_is_empty(some_set):
    return some_set == set()

if (language == 'eu'):
    defi = {}
    with codecs.open('EusWN_definizioak.tsv',encoding='utf-8') as fe:
        next(fe)
        for line in fe:
            (hitza, synseta, definizioa) = line.split("\t")
            defi[synseta] = definizioa

def definition_eu(offset):
    #osatu synseta:eus-30-80000745-n
    synseta="eus-30-"+str(offset).zfill(8)+"-n"
    if defi.get(synseta):
        return (defi.get(synseta))
    else:
        return ""
##################DESAMBIGUADOR#####################
from nltk.wsd import lesk

###############WIKIPEDIA#################
import wikipedia
def wikipedia_offset2url(text):
    try:
        imagepage=wikipedia.page(text)
        url = imagepage.images[0]
    except:
        url=''
    return url

##########Wikidata###################################
#Wikidata query service -> code in python
#pip install wikidata
#https://pypi.org/project/Wikidata/
# pip install sparqlwrapper
# https://rdflib.github.io/sparqlwrapper/
import wikidata
from SPARQLWrapper import SPARQLWrapper, JSON
from wikidata.client import Client
def get_results(endpoint_url, query):
    sparql = SPARQLWrapper(endpoint_url)
    sparql.setQuery(query)
    sparql.setReturnFormat(JSON)
    return sparql.query().convert()
def wikidata_offset2url(offset):
    #WIKIDATA
    endpoint_url = "https://query.wikidata.org/sparql"
    soffset=str(offset).zfill(8)
    query="Select * where {?item wdt:P2888 <http://wordnet-rdf.princeton.edu/wn30/"+soffset+"-n> }"
    try:
        results = get_results(endpoint_url, query)
        ema=results["results"]["bindings"]
        item=ema[0].get('item')
        value=item.get('value')
        head, tail = value.split("http://www.wikidata.org/entity/")
        #for result in results["results"]["bindings"]:
          #    print(result)
        client = Client()
        entity = client.get(tail, load=True)
        image_prop = client.get('P18')
        image = entity[image_prop]
        #image-><wikidata.commonsmedia.File 'File:KBS "The Producers" press conference, 11 May 2015 10.jpg'>
        #image.image_resolution
        url=image.image_url
    except:
        url=''
    return url
    #######################################################
import os

#def plot_wordcloud(wordcloud):
#    plt.imshow(wordcloud)
#    plt.axis("off")
#    plt.show()
#Este método devuelve true en caso de que la palabra pasada como parámetro sea verbo. Para que una palabra sea 
#verbo se tiene que cumplir que sea VERB o que sea AUX y que su padre NO sea VERB.

####analizador sintactico#######################
import stanfordnlp
lang = ''
if (language == 'en'):
    lang = language+"_ewt"
elif (language == 'eu'):
    lang = language+"_bdt"
config = {'processors': 'tokenize,pos,lemma,depparse', # Comma-separated list of processors to use
'lang': 'eu', # Language code for the language to build the Pipeline in
'tokenize_model_path': '/var/www/stanfordnlp_resources/'+ lang +'_models/'+ lang +'_tokenizer.pt',
'pos_model_path': '/var/www/stanfordnlp_resources/'+ lang +'_models/'+ lang +'_tagger.pt',
'pos_pretrain_path': '/var/www/stanfordnlp_resources/'+ lang +'_models/'+ lang +'.pretrain.pt',
'lemma_model_path': '/var/www/stanfordnlp_resources/'+ lang +'_models/'+ lang +'_lemmatizer.pt',
'depparse_model_path': '/var/www/stanfordnlp_resources/'+ lang +'_models/'+ lang +'_parser.pt',
'depparse_pretrain_path': '/var/www/stanfordnlp_resources/'+ lang +'_models/'+ lang +'.pretrain.pt'}
#stanfordnlp.download("en") ENGLISH
#stanfordnlp.download('eu')  EUSKERA

stanford = stanfordnlp.Pipeline(**config)


####ouput files#################
#estadisticos
estadisticaoutput=input+".out.csv"
syntaxoutput=input+".syntax.csv"

#Write all the information in the file
estfile = open(estadisticaoutput, "w")
syntaxfile = open(syntaxoutput, "w")



###############Tratamiento de texto###############################################
#quitar todos los retornos \n si contiene
text = open(input).read().replace('\n', '')
#remove text inside parentheses
#text = re.sub(r'\([^)]*\)', '', text)
#separa , . ! ( ) ? ; del texto con espacios, teniendo en cuenta que los no son numeros en el caso de , y . 
#text = re.sub(r"\_", " ", text)
#text = re.sub(r"\-", " ", text)
#text = re.sub(r'[.]+(?![0-9])', r' . ', text)
#text = re.sub(r'[,]+(?![0-9])', r' , ', text)
#text = re.sub(r"!", " ! ", text)
#text = re.sub(r"\(", " ( ", text)
#text = re.sub(r"\)", " ) ", text)
#text = re.sub(r"\?", " ? ", text)
#text = re.sub(r";", " ; ", text)
#sustituye 2 espacios seguidos por 1
text = re.sub(r"\s{2,}", " ", text)


#######################STOPWORDS#############################
if (language == 'en'): ## english
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

if (language == 'eu'): ## euskera
    stopwords= set(line.strip() for line in open('stopwords_formaketakonektoreak.txt'))
    stopwords.add("ondo")
    stopwords.add("ordu")
    stopwords.add("jarraian")
    stopwords.add("igaro")

wordcloud = WordCloud(relative_scaling=1.0,stopwords=stopwords).generate(text)
#Finally, use matplotlib to render the word cloud:
#plot_wordcloud(wordcloud)
wordcloudfilename=input+".png"
#wordcloudfilename="resume.png"
wordcloud.to_file(wordcloudfilename) 

###################analizador morfosintactico#################################################
palabras_diferentes = []
doc=stanford(text)
for sent in doc.sentences:
        #Por cada sentencia
    for entry in sent.words:
        definition = ''
        examples = ''
        #es_list=[]
        synonyms = []
        offset = 0
        nueve = '_' 
        url = ''    
        lang = ''          
		#Por cada palabra
        if entry.text.isalpha() and (entry.upos == 'NOUN' or entry.upos == 'VERB') and entry.text.lower() not in palabras_diferentes:
            #Si la palabra es un content word
            palabras_diferentes.append(entry.text.lower())
            if (language == 'en'):
                wordfrequency = zipf_frequency(entry.text.lower(), 'en')
            elif (language == 'eu'):
                wordfrequency = zipf_frequency_eu(entry.lemma)

            #lemafrequency = zipf_frequency(entry.lemma, 'en')
            if wordfrequency <= int(difficult):
                lema=entry.lemma
                if (language == 'en'):
                    synset_ids = wn.synsets(lema)
                elif (language == 'eu'):
                    synset_ids = wn.synsets(lema, lang='eus')
                if entry.upos=='NOUN':
                    patron='n'
                if entry.upos=='VERB':
                    patron='v'
                
                try:
                    synset_desambiguado = lesk(text.split(), entry.text, pos=patron, synsets=synset_ids)
                    offset = synset_desambiguado.offset()
                    if (language == 'en'):
                        definition = synset_desambiguado.definition()
                        examples = synset_desambiguado.examples()
                    elif (language == 'eu'):
                        definition = definition_eu(offset)

                    #traduccion
                    #translate_es = synset_desambiguado.lemmas('spa')
                    #for l in translate_es:
                    #    es_list.append(l.name())

                    #sinonimos
                    if (language == 'eu'):
                        for l in synset_desambiguado.lemma_names('eus'):
                            synonyms.append(l.lower())
                        try:
                            synonyms.remove(entry.text.lower())
                        except:
                            pass
                        try:
                            synonyms.remove(entry.lemma)
                        except:
                            pass
                        
                    elif (language == 'en'):
                        for l in synset_desambiguado.lemma_names():
                            synonyms.append(l.lower())
                        try:
                            synonyms.remove(entry.text.lower())
                        except:
                            pass
                        try:
                            synonyms.remove(entry.lemma)
                        except:
                            pass
                    

                    #url imagen
                    if entry.upos=='NOUN':
                        url = wikidata_offset2url(offset)
                        if url=='':
                            url=wikipedia_offset2url(entry.text)
                
                except:
                    count = 0
                    for synset in synset_ids:
                        if entry.upos=='NOUN':
                            patron='.n.'
                        if entry.upos=='VERB':
                            patron='.v.'

                        if patron in synset.name() and count == 0:
                            count = 1
                            if (language == 'eu'):
                                lang = 'eus'
                            elif (language == 'en'):
                                lang = ''
                            for l in synset_desambiguado.lemma_names(lang):
                                synonyms.append(l.name())

                            try:
                                synonyms.remove(entry.text.lower())
                            except:
                                pass
                            try:
                                synonyms.remove(entry.lemma)
                            except:
                                pass

                            definition = synset.definition()
                            offset = synset.offset()
                            examples = synset.examples()

                             #url imagen
                           #url imagen
                            if entry.upos=='NOUN':
                                url = wikidata_offset2url(offset)
                                if url=='':
                                    url=wikipedia_offset2url(entry.text)

        if not set_is_empty(set(synonyms)):
            nueve=entry.text+":"+str(set(synonyms))
        else:
            nueve="_"
        
        print(str(entry.index)+"\t"+entry.text+"\t"+entry.lemma+"\t"+entry.upos+"\t"+entry.xpos+"\t"+entry.feats+"\t"+str(entry.governor)+"\t"+str(entry.dependency_relation)+"\t"+definition.replace('\n', '')+"\t"+nueve.replace('\n', '')+"\t"+url+"\t"+str(examples)+"\t_")
        syntaxfile.write("%s" % str(entry.index)+"\t"+entry.text+"\t"+entry.lemma+"\t"+entry.upos+"\t"+entry.xpos+"\t"+entry.feats+"\t"+str(entry.governor)+"\t"+str(entry.dependency_relation[:2])+"\t"+definition.replace('\n', '')+"\t_\t_")
        estfile.write("%s" % str(entry.index)+"\t"+entry.text+"\t"+entry.lemma+"\t"+entry.upos+"\t"+entry.xpos+"\t"+entry.feats+"\t"+str(entry.governor)+"\t"+str(entry.dependency_relation[:2])+"\t"+definition.replace('\n', '')+"\t"+nueve.replace('\n', '')+"\t"+url+"\t"+str(examples)+"\t_")
        syntaxfile.write("\n")
        estfile.write("\n")
    syntaxfile.write("\n")
    estfile.write("\n")
syntaxfile.close()
estfile.close()
