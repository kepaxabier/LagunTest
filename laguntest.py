# coding=utf-8
import os
import nltk
import sys
import re
import csv
from wordcloud import WordCloud
import textract
from cube.api import Cube

# genera CADENAS aleatorias
import uuid

# WORDFREQ
from wordfreq import zipf_frequency

#################WORDNET##########################
# Instalar 'wordnet'
#nltk.download('wordnet')
# Add multilingual wordnet
#nltk.download('omw')
from nltk.corpus import wordnet as wn


def set_is_empty(some_set):
    return some_set == set()


import codecs


class WN_definizioak():
    defi = {}

    def __init__(self, language):
        self.lang = language

    def print(self):
        print(WN_definizioak.defi)

    def load(self):
        if self.lang == "basque":
            WN_definizioak.defi = {}
            with codecs.open('/var/www/html/erraztest/eu/EusWN_definizioak.tsv', encoding='utf-8') as fe:
                next(fe)
                for line in fe:
                    (hitza, synseta, definizioa) = line.split("\t")
                    WN_definizioak.defi[synseta] = definizioa
        if self.lang == "spanish":
            WN_definizioak.defi={}
            with codecs.open('/var/www/html/erraztest/es/wei_spa-30_synset_9.csv', encoding='utf-8') as fe:
                next(fe)
                for line in fe:
                    (synseta,pos,number1,gidoi1,gidoi2,number2,definizioa,number3,number4)=line.split(",")
                    WN_definizioak.defi[synseta] = definizioa


    def definition_eu(offset):
        # osatu synseta:eus-30-80000745-n
        synseta = "eus-30-" + str(offset).zfill(8) + "-n"
        if WN_definizioak.defi.get(synseta):
            return (WN_definizioak.defi.get(synseta))
        else:
            return ""
    def definition_es(offset):
        # osatu synseta:eus-30-80000745-n
        synseta = "spa-30-" + str(offset).zfill(8) + "-n"
        if WN_definizioak.defi.get(synseta):
            if WN_definizioak.defi.get(synseta) == "NULL":
                return ""
            else:
                return (WN_definizioak.defi.get(synseta))
        else:
            return ""


##################DESAMBIGUADOR#####################
from nltk.wsd import lesk

###############WIKIPEDIA#################
import wikipedia


def wikipedia_offset2url(text):
    try:
        # First of all wikipedia.page() will store all the relevant informations
        # from the requested page in the variable imagepage.
        imagepage = wikipedia.page(text)
        # Existen muchas funciones. imagepage.url devuelve la url, imagepage.url.content el contenido, title el título....
        # imagepage.images[0] will return the URL of the image that is present at index 0.
        # If you want to fetch another image use index as 1, 2, 3, etc, according to images present in the page.
        url = imagepage.images[0]
    except:
        url = ''
    return url


##########Wikidata###################################
# Wikidata query service -> code in python
# pip install wikidata
# https://pypi.org/project/Wikidata/
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
    # WIKIDATA
    endpoint_url = "https://query.wikidata.org/sparql"
    soffset = str(offset).zfill(8)
    query = "Select * where {?item wdt:P2888 <http://wordnet-rdf.princeton.edu/wn30/" + soffset + "-n> }"
    try:
        results = get_results(endpoint_url, query)
        ema = results["results"]["bindings"]
        item = ema[0].get('item')
        value = item.get('value')
        head, tail = value.split("http://www.wikidata.org/entity/")
        # for result in results["results"]["bindings"]:
        #    print(result)
        client = Client()
        entity = client.get(tail, load=True)
        image_prop = client.get('P18')
        image = entity[image_prop]
        # image-><wikidata.commonsmedia.File 'File:KBS "The Producers" press conference, 11 May 2015 10.jpg'>
        # image.image_resolution
        url = image.image_url
    except:
        url = ''
    return url


# IMAGENET
import requests
from bs4 import BeautifulSoup


def imagenet_offset2url(offset):
    lista=["farm5.static.flickr.com","farm3.static.flickr.com","farm2.static.flickr.com","farm4.static.flickr.com","http://static.flickr.com"]
    soffset=str(offset).zfill(8)
    query="http://www.image-net.org/api/text/imagenet.synset.geturls?wnid=n"+str(soffset)
    page = requests.get(query)#ship synset
    if (page.status_code == 200):
        #BeautifulSoup is an HTML parsing library puts the content of the website into the soup variable, each url on a different line
        soup = BeautifulSoup(page.content, 'html.parser')
        #soup = BeautifulSoup(page.content, 'lxml')
        str_soup=str(soup)#convert soup to string so it can be split
        split_urls=str_soup.split('\r\n')#split so each url is a different possition on a list
        for url in split_urls:
            if any(x in url for x in lista):
                try:
                    response1 = requests.get(url)
                    if(response1.status_code == 200):
                        return(url)
                except:
                    pass
        for url in split_urls:
            try:
                response1 = requests.get(url)
                if(response1.status_code == 200):
                     return(url)
            except:
                pass
    return ''



#######################################################
#from pywsd import disambiguate
#from pywsd.similarity import max_similarity as maxsim


#def sentencewsd(contexts, word):
#    for context in contexts:
#        if context[0] == word:
#            return context[1]
#    return None


####analizador sintactico#######################
import stanfordnlp


class Maiztasuna:
    freq_list = {}

    def __init__(self, path):
        self.path = path

    def load(self):
        with open(self.path, encoding='utf-8') as csv_file:
            csv_reader = csv.reader(csv_file, delimiter=',')
            for row in csv_reader:
                Maiztasuna.freq_list[row[1].strip()] = row[0]


class Stopwords:
    stop_words = []

    def __init__(self, language):
        self.lang = language

    def print(self):
        for stopword in Stopwords.stop_words:
            print(stopword)

    def download(self):
        nltk.download('stopwords')

    def load(self):
        if self.lang == "english":
            # Stopwords.stop_words = stopwords.words('english')
            # Open the file with read only permit
            f = open('/var/www/html/erraztest/en/stopwords.txt', encoding='utf-8')
            # use readline() to read the first line
            line = f.readline()
            # use the read line to read further.
            # If the file is not empty keep reading one line
            # at a time, till the file is empty
            while line:
                # print(line)
                Stopwords.stop_words.append(line.strip())
                # use realine() to read next line
                line = f.readline()
            f.close()
            #Stopwords.stop_words = set(line.strip() for line in open('/var/www/html/erraztest/en/stopwords.txt'))
        if self.lang == "spanish":
            # Stopwords.stop_words = stopwords.words('spanish')
            # Open the file with read only permit
            f = open('/var/www/html/erraztest/es/stopwords.txt', encoding='utf-8')
            # use readline() to read the first line
            line = f.readline()
            # use the read line to read further.
            # If the file is not empty keep reading one line
            # at a time, till the file is empty
            while line:
                # print(line)
                Stopwords.stop_words.append(line.strip())
                # use realine() to read next line
                line = f.readline()
            f.close()
            #Stopwords.stop_words = set(line.strip() for line in open('/var/www/html/erraztest/es/stopwords.txt'))
        if self.lang == "basque":
            # Open the file with read only permit
            f = open('/var/www/html/erraztest/eu/stopwords.txt', encoding='utf-8')
            # use readline() to read the first line
            line = f.readline()
            # use the read line to read further.
            # If the file is not empty keep reading one line
            # at a time, till the file is empty
            while line:
                # print(line)
                Stopwords.stop_words.append(line.strip())
                # use realine() to read next line
                line = f.readline()
            f.close()
            #Stopwords.stop_words = set(line.strip() for line in open('/var/www/html/erraztest/eu/stopwords.txt'))
            #line.decode('utf-8').strip()


class NLPCharger:

    def __init__(self, language, library, directory, difficult_level):
        self.lang = language
        self.lib = library
        self.dir = directory
        self.difficult_level = difficult_level
        self.text = None
        self.textwithparagraphs = None
        self.parser = None

    '''
    Download the respective model depending of the library and language. 
    '''

    def download_model(self):
        if self.lib == "stanford":
            print("-----------You are going to use Stanford library-----------")
            if self.lang == "basque":
                print("-------------You are going to use Basque model-------------")
                MODELS_DIR = self.dir + '/eu'
                stanfordnlp.download('eu', MODELS_DIR)  # Download the Basque models
            elif self.lang == "english":
                print("-------------You are going to use English model-------------")
                MODELS_DIR = self.dir + '/en'
                print("-------------Downloading Stanford Basque model-------------")
                stanfordnlp.download('en', MODELS_DIR)  # Download the English models
            elif self.lang == "spanish":
                print("-------------You are going to use Spanish model-------------")
                MODELS_DIR = self.dir + '/es'
                stanfordnlp.download('es', MODELS_DIR)  # Download the Spanish models
            else:
                print("........You cannot use this language...........")
        elif self.lib == "cube":
            print("-----------You are going to use Cube Library-----------")
        else:
            print("You cannot use this library. Introduce a valid library (Cube or Stanford)")

    '''
    load model in parser object 
    '''

    def load_model(self):
        if self.lib == "stanford":
            print("-----------You are going to use Stanford library-----------")
            if self.lang == "basque":
                print("-------------You are going to use Basque model-------------")
                MODELS_DIR = self.dir + '/eu'
                config = {'processors': 'tokenize,pos,lemma,depparse',  # Comma-separated list of processors to use
                          'lang': 'eu',  # Language code for the language to build the Pipeline in
                          'tokenize_model_path': MODELS_DIR + '/eu_bdt_models/eu_bdt_tokenizer.pt',
                          # Processor-specific arguments are set with keys "{processor_name}_{argument_name}"
                          'pos_model_path': MODELS_DIR + '/eu_bdt_models/eu_bdt_tagger.pt',
                          'pos_pretrain_path': MODELS_DIR + '/eu_bdt_models/eu_bdt.pretrain.pt',
                          'lemma_model_path': MODELS_DIR + '/eu_bdt_models/eu_bdt_lemmatizer.pt',
                          'depparse_model_path': MODELS_DIR + '/eu_bdt_models/eu_bdt_parser.pt',
                          'depparse_pretrain_path': MODELS_DIR + '/eu_bdt_models/eu_bdt.pretrain.pt'
                          }
                self.parser = stanfordnlp.Pipeline(**config)

            elif self.lang == "english":
                print("-------------You are going to use English model-------------")
                MODELS_DIR = self.dir + '/en'
                config = {'processors': 'tokenize,mwt,pos,lemma,depparse',  # Comma-separated list of processors to use
                          'lang': 'en',  # Language code for the language to build the Pipeline in
                          'tokenize_model_path': MODELS_DIR + '/en_ewt_models/en_ewt_tokenizer.pt',
                          'pos_model_path': MODELS_DIR + '/en_ewt_models/en_ewt_tagger.pt',
                          'pos_pretrain_path': MODELS_DIR + '/en_ewt_models/en_ewt.pretrain.pt',
                          'lemma_model_path': MODELS_DIR + '/en_ewt_models/en_ewt_lemmatizer.pt',
                          'depparse_model_path': MODELS_DIR + '/en_ewt_models/en_ewt_parser.pt',
                          'depparse_pretrain_path': MODELS_DIR + '/en_ewt_models/en_ewt.pretrain.pt'
                          }
                self.parser = stanfordnlp.Pipeline(**config)
            elif self.lang == "spanish":
                print("-------------You are going to use Spanish model-------------")
                MODELS_DIR = self.dir + '/es'
                config = {'processors': 'tokenize,pos,lemma,depparse',  # Comma-separated list of processors to use
                          'lang': 'es',  # Language code for the language to build the Pipeline in
                          'tokenize_model_path': MODELS_DIR + '/es_ancora_models/es_ancora_tokenizer.pt',
                          # Processor-specific arguments are set with keys "{processor_name}_{argument_name}"
                          'pos_model_path': MODELS_DIR + '/es_ancora_models/es_ancora_tagger.pt',
                          'pos_pretrain_path': MODELS_DIR + '/es_ancora_models/es_ancora.pretrain.pt',
                          'lemma_model_path': MODELS_DIR + '/es_ancora_models/es_ancora_lemmatizer.pt',
                          'depparse_model_path': MODELS_DIR + '/es_ancora_models/es_ancora_parser.pt',
                          'depparse_pretrain_path': MODELS_DIR + '/es_ancora_models/es_ancora.pretrain.pt'
                          }
                self.parser = stanfordnlp.Pipeline(**config)
            else:
                print("........You cannot use this language...........")
        elif self.lib == "cube":
            print("-----------You are going to use Cube Library-----------")
            if self.lang == "basque":
                # initialize it
                cube = Cube(verbose=True)
                # load(self, language_code, version="latest",local_models_repository=None,
                # local_embeddings_file=None, tokenization=True, compound_word_expanding=False,
                # tagging=True, lemmatization=True, parsing=True).
                # Ejemplo:load("es",tokenization=False, parsing=False)
                ## select the desired language (it will auto-download the model on first run)
                cube.load("eu", "latest")
                self.parser = cube
            elif self.lang == "english":
                cube = Cube(verbose=True)
                cube.load("en", "latest")
                self.parser = cube
            elif self.lang == "spanish":
                cube = Cube(verbose=True)
                cube.load("es", "latest")
                self.parser = cube
            else:
                print("........You cannot use this language...........")
        else:
            print("You cannot use this library. Introduce a valid library (Cube or Stanford)")

    def process_text(self, text):
        self.text = text.replace('\n', '@')
        self.text = re.sub(r'@+', '@', self.text)
        # separa , . ! ( ) ? ; del texto con espacios, teniendo en cuenta que los no son numeros en el caso de , y .
        self.text = re.sub(r"\_", " ", self.text)
        # self.text = re.sub(r'[.]+(?![0-9])', r' . ', self.text)
        # self.text = re.sub(r'[,]+(?![0-9])', r' , ', self.text)
        self.text = re.sub(r"!", " ! ", self.text)
        self.text = re.sub(r"\(", " ( ", self.text)
        self.text = re.sub(r"\)", " ) ", self.text)
        self.text = re.sub(r"\?", " ? ", self.text)
        self.text = re.sub(r";", " ; ", self.text)
        self.text = re.sub(r"\-", " - ", self.text)
        self.text = re.sub(r"\—", " - ", self.text)
        self.text = re.sub(r"\“", " \" ", self.text)
        self.text = re.sub(r"\”", " \" ", self.text)
        # sustituye 2 espacios seguidos por 1
        self.text = re.sub(r"\s{2,}", " ", self.text)
        return self.text

    '''
    Transform data into a unified structure.
    '''

    def get_estructure(self, text):
        self.text = text
        # Loading a text with paragraphs
        self.textwithparagraphs = self.process_text(self.text)
        # Getting a unified structure [ [sentences], [sentences], ...]
        return self.adapt_nlp_model()

    def adapt_nlp_model(self):
        ma = ModelAdapter(self.parser, self.lib)
        return ma.model_analysis(self.textwithparagraphs, self.lang, self.difficult_level)


class ModelAdapter:

    def __init__(self, model, lib):
        # parser
        self.model = model
        # model_name
        self.lib = lib

    def get_difficult(self, language, difficult_level):
        if (language == 'basque'):
            if (difficult_level == 'b'):
                difficult = 100000
            elif (difficult_level == 'm'):
                difficult = 34
            else:
                difficult = 6

        elif (language == 'english' or language == 'spanish'):
            if (difficult_level == 'b'):
                difficult = 8
            elif (difficult_level == 'm'):
                difficult = 5
            else:
                difficult = 3
        else:
            if (difficult_level == 'b'):
                difficult = 8
            elif (difficult_level == 'm'):
                difficult = 5
            else:
                difficult = 3
        return difficult

    def model_analysis(self, text, language, difficult_level):
        difficult = self.get_difficult(language, difficult_level)
        d = Document(text, language, difficult)  # ->data = []
        if self.lib == "stanford":
            lines = text.split('@')
            for line in lines:  # paragraph
                p = Paragraph()  # -> paragraph = []
                p.text = line
                if not line.strip() == '':
                    doc = self.model(line)
                    for sent in doc.sentences:
                        s = Sentence()
                        sequence = self.sent2sequenceStanford(sent)
                        # print(sequence)
                        s.text = sequence
                        for word in sent.words:
                            # Por cada palabra de cada sentencia, creamos un objeto Word que contendra los attrs
                            w = Word()
                            w.index = str(word.index)
                            w.text = word.text
                            w.lemma = word.lemma
                            w.upos = word.upos
                            w.xpos = word.xpos
                            w.feats = word.feats
                            w.governor = word.governor
                            w.dependency_relation = word.dependency_relation
                            s.word_list.append(w)
                            # print(str(w.index) + "\t" + w.text + "\t" + w.lemma + "\t" + w.upos + "\t" + w.xpos + "\t" + w.feats + "\t" + str(w.governor) + "\t" + str(w.dependency_relation) +"\t")
                        p.sentence_list.append(s)  # ->paragraph.append(s)
                    d.paragraph_list.append(p)  # ->data.append(paragraph)

        elif self.lib == "cube":
            lines = text.split('@')
            for line in lines:
                p = Paragraph()  # -> paragraph = []
                p.text = line
                if not line.strip() == '':
                    sequences = self.model(line)
                    for seq in sequences:
                        s = Sentence()
                        sequence = self.sent2sequenceCube(seq)
                        s.text = sequence
                        for entry in seq:
                            # Por cada palabra de cada sentencia, creamos un objeto Word que contendra los attrs
                            w = Word()
                            w.index = str(entry.index)
                            w.text = entry.word
                            w.lemma = entry.lemma
                            w.upos = entry.upos
                            w.xpos = entry.xpos
                            w.feats = entry.attrs
                            w.governor = int(entry.head)
                            w.dependency_relation = str(entry.label)
                            s.word_list.append(w)
                            # print(str(
                            #     w.index) + "\t" + w.text + "\t" + w.lemma + "\t" + w.upos + "\t" + w.xpos + "\t" + w.feats + "\t" + str(
                            #     w.governor) + "\t" + str(w.dependency_relation) + "\t")
                        p.sentence_list.append(s)  # ->paragraph.append(s)
                    d.paragraph_list.append(p)  # ->data.append(paragraph)
        return d

    def sent2sequenceStanford(self, sent):
        conllword = ""
        for word in sent.words:
            conllword = conllword + " " + str(word.text)
        return conllword

    def sent2sequenceCube(self, sent):
        conllword = ""
        for entry in sent:
            conllword = conllword + " " + str(entry.word)
        return conllword


class Document:
    def __init__(self, text, language, difficult):
        self._text = text
        self.language = language
        self.difficult = difficult
        self._paragraph_list = []
        self.words_freq = {}

    @property
    def text(self):
        """ Access text of this document. Example: 'This is a sentence.'"""
        return self._text

    @text.setter
    def text(self, value):
        """ Set the document's text value. Example: 'This is a sentence.'"""
        self._text = value

    @property
    def paragraph_list(self):
        """ Access list of sentences for this document. """
        return self._paragraph_list

    @paragraph_list.setter
    def paragraph_list(self, value):
        """ Set the list of tokens for this document. """
        self._paragraph_list = value



    def calculate_all_atributes(self,input):
        ####ouput files#################
        #estadisticos
        estadisticaoutput=input+".out.csv"
        syntaxoutput=input+".syntax.csv"

        #Write all the information in the file
        estfile = open(estadisticaoutput, "w",encoding='utf-8')
        syntaxfile = open(syntaxoutput, "w",encoding='utf-8')
        #Si pywsd=True
        #pywsd=False
        palabras_diferentes = []
        for p in self.paragraph_list:
            for s in p.sentence_list:
                #if pywsd==True:
                    #text=s.sent2text()
                    #contexts=disambiguate(text)
                #else:
                s.set_ukb_sense(self.language)
                #s.printwordsense()
                if not s.text == "":
                    for w in s.word_list:
                        synset_desambiguado=None
                        definition = '_'
                        examples = '_'
                        synonyms = []
                        offset = 0
                        sinonimoak = '_' 
                        url = '_'   
                        #print(str(w.index) + "\t" + w.text + "\t" + w.lemma + "\t" + w.upos + "\t" + w.xpos + "\t" + w.feats + "\t" + str(w.governor) + "\t" + str(w.dependency_relation) +"\t")
                        #Si la palabra tiene más de un caracter
                        #Si es alfabético {a-z,A-Z, pero tambien ñ y acentos}
                        #Si es nombre, nombre propio o verbo ppal
                        #Si es rara o dificil
                        #Si no ha sido tratada lema minúscula
                        if (not len(w.text) == 1) and w.text.isalpha() and (w.upos == 'PROPN' or w.upos == 'NOUN' or w.upos == 'VERB') and (int(w.get_rare_level(self.language))<=int(self.difficult)) and w.lemma.lower() not in palabras_diferentes:
                            #Si la palabra es un content word
                            palabras_diferentes.append(w.lemma.lower())
                            if w.upos=='NOUN' or w.upos=='PROPN':
                                patron='n'
                            if w.upos=='VERB':
                                patron='v'
                            #si ingles 
                            if (self.language == 'english'):
                                #obtengo los posibles synsets del lema
                                synset_ids = wn.synsets(w.lemma)
                            if (self.language == 'basque'):
                                synset_ids = wn.synsets(w.lemma, lang='eus')
                            if (self.language == 'spanish'):
                                synset_ids= wn.synsets(w.lemma, lang='spa')
                                #print(w.lemma)
                                #print("syssets_ids:")
                                #print(synset_ids)
                            #Si UKB o PyWsd
                            #if pywsd==True:
                                #synset_desambiguado=sentencewsd(contexts,w.text)
                            #else:
                            #UKB
                            offset_desambiguado=s.get_ukb_sense(w.lemma)
                            #print("offset desambiguado:")
                            #print(offset_desambiguado)
                            if offset_desambiguado=='':
                                synset_desambiguado=None
                            else:
                                #obtener el synset para ese offset delvuelto por UKB
                                for synset in synset_ids:
                                    synsetoffset=synset.offset()
                                    if str(synsetoffset) in offset_desambiguado:
                                        synset_desambiguado=synset
                                        #print("obtener el synset para ese offset devuelto por UKB")
                                        #print(synset_desambiguado)
                            #Si desambigua
                            if synset_desambiguado is not None:
                                pass
                                #print("desambiguador:"+str(synset_desambiguado))
                            else:
                            #No desambiguado, el primero
                                count = 0
                                for synset in synset_ids:
                                    if w.upos=='NOUN' or w.upos=='PROPN':
                                        patron='.n.'
                                    if w.upos=='VERB':
                                        patron='.v.'
                                    if patron in synset.name() and count == 0:
                                        count = 1
                                        synset_desambiguado=synset
                                        #print("Selecciono el primer synset:"+str(synset_desambiguado))
                            #hay un synset seleccionado, obtengo offset, def, ex,syn depende del idioma?
                            if synset_desambiguado is not None:
                                offset = synset_desambiguado.offset()
                                if (self.language == 'english'):
                                    definition = synset_desambiguado.definition()
#                                     #traduccion es
#                                     traduccion_es=synset_desambiguado.lemmas('spa')
#                                     es_list=[]
#                                     for l in traduccion_es:
#                                         es_list.append(l.name())
                                    #traduccion eu
#                                     traduccion_eu=synset_desambiguado.lemmas('eus') 
#                                     eu_list=[]
#                                     for l in traduccion_eu:
#                                         eu_list.append(l.name())
                                    examples = synset_desambiguado.examples()
                                    #sinonimos
                                    for l in synset_desambiguado.lemma_names():
                                        synonyms.append(l.lower())
                                        try:
                                            synonyms.remove(w.text.lower())
                                        except:
                                            pass
                                        try:
                                            synonyms.remove(w.lemma.lower())
                                        except:
                                            pass
                                if (self.language == 'basque'):
                                    definition = WN_definizioak.definition_eu(offset)
                                    #sinonimos
                                    for l in synset_desambiguado.lemma_names('eus'):
                                        synonyms.append(l.lower())
                                        try:
                                            synonyms.remove(w.text.lower())
                                        except:
                                            pass
                                        try:
                                            synonyms.remove(w.lemma.lower())
                                        except:
                                            pass
                                if (self.language =='spanish'):
                                    definition = WN_definizioak.definition_es(offset)
                                    # sinonimos
                                    for l in synset_desambiguado.lemma_names('spa'):
                                        synonyms.append(l.lower())
                                        try:
                                            synonyms.remove(w.text.lower())
                                        except:
                                            pass
                                        try:
                                            synonyms.remove(w.lemma.lower())
                                        except:
                                            pass

                                #url imagen
                                if w.upos=='NOUN' or w.upos=='PROPN':
                                    url=imagenet_offset2url(offset)
                                    #print("imagenet:"+url)
                                    if url=='':
                                        url = wikidata_offset2url(offset)
                                        #print("wikidata:"+url)
                                        if url=='':
                                            text=w.text.strip()
                                            text=text.lower()
                                            url=wikipedia_offset2url(text)
                                            #print("wikipedia:"+url)
                                if not set_is_empty(set(synonyms)):
                                    sinonimoak=w.text+":"+str(set(synonyms))
                                    sinonimoak=sinonimoak.replace('\n', '')
                                    sinonimoak=sinonimoak.replace('\t', '')
                                else:
                                    sinonimoak="_"
                                #print(sinonimoak)
                                if definition=='':
                                    definition="_"
                                else:
                                    definition=definition.replace('\n', '')
                                    definition=definition.replace('\t', ' ')
                                    definition=definition.replace('\r', '')
                                if len(examples) == 0:
                                    examples="_"
                                else:
                                    examples=str('. '.join(examples))
                                    examples=examples.replace('\n', '')
                                    examples=examples.replace('\t', ' ')
                                if url=='':
                                    url="_"
                                else:
                                    url=url.replace('\n', '')
                                    url=url.replace('\t', ' ')
                                #print("\nSinonimoak:"+sinonimoak+":Sinofin")
                                #print("\nDefinizioak:"+definition+":Deffin")
                                #print("\nexamples:"+examples+":Exafin")
                                #print("\nurl:"+url+":Urlfin")
                            else:
                                #print("Ningun synset!!!")
                                pass
                        #print(str(w.index)+"\t"+w.text+"\t"+w.lemma+"\t"+w.upos+"\t"+w.xpos+"\t"+w.feats+"\t"+str(w.governor)+"\t"+str(w.dependency_relation[:4])+"\t"+definition+"\t"+sinonimoak+"\t"+url+"\t"+examples+"\t_")
                        syntaxfile.write("%s" % str(w.index)+"\t"+w.text+"\t"+w.lemma+"\t"+w.upos+"\t"+w.xpos+"\t"+w.feats+"\t"+str(w.governor)+"\t"+str(w.get_dependentzia(self.language))+"\t_\t_")
                        estfile.write("%s" % str(w.index)+"\t"+w.text+"\t"+w.lemma+"\t"+w.upos+"\t"+w.xpos+"\t"+w.feats+"\t"+str(w.governor)+"\t"+str(w.get_dependentzia(self.language))+"\t"+definition+"\t"+sinonimoak+"\t"+url+"\t"+examples+"\t_")
                        syntaxfile.write('\n')
                        estfile.write('\n')
                syntaxfile.write('\n')
                estfile.write('\n')
        syntaxfile.close()
        estfile.close()

class Paragraph:

    def __init__(self):
        self._sentence_list = []
        self.text = None

    @property
    def sentence_list(self):
        """ Access list of sentences for this document. """
        return self._sentence_list

    @sentence_list.setter
    def sentence_list(self, value):
        """ Set the list of tokens for this document. """
        self.sentence_list = value


class Sentence:

    def __init__(self):
        self._word_list = []
        self.wordsense = {}
        self.text = None

    @property
    def word_list(self):
        """ Access list of words for this sentence. """
        return self._word_list

    @word_list.setter
    def word_list(self, value):
        """ Set the list of words for this sentence. """
        self._word_list = value

    def print(self):
        for words in self.word_list:
            print(words.text)

    def printwordsense(self):
        print(self.wordsense)

    def sent2text(self):
        self.text = ""
        for words in self.word_list:
            self.text = self.text + " " + words.text
        return self.text

    def get_ukb_sense(self, lema):
        try:
            x = self.wordsense[lema.lower()]
        except:
            x = ""
        return x

    def set_ukb_sense(self, language):
        # Write all the information in the file
        contextname = str(uuid.uuid4())
        # contextname="context.txt"
        context = open(contextname, "w",encoding='utf-8')
        #print(contextname)
        ukboutname = str(uuid.uuid4())
        # ukboutname="ukbout.txt"
        cadena = ""
        id = 1
        for words in self.word_list:
            if (
                    words.upos == "NOUN" or words.upos == 'PROPN' or words.upos == "VERB" or words.upos == "ADJ" or words.upos == 'ADV'):
                if words.upos == "NOUN" or words.upos == 'PROPN':
                    postag = 'n'
                if words.upos == "VERB":
                    postag = 'v'
                if words.upos == "ADJ":
                    postag = 'a'
                if words.upos == 'ADV':
                    postag = 'r'
                cadena = cadena + " " + words.lemma.lower() + "#" + postag + "#w" + str(id) + "#1"
                id = id + 1
        context.write("ctx_01\n" + cadena)
        context.close()
        # print("ctx_01\n"+cadena)
        # print(cadena) #man#n#w1#1 kill#v#w2#1 cat#n#w3#1 hammer#n#w4#1")
        if language == 'english':
            # os.system("/home/kepa/ukb-master/src/ukb_wsd --client --port 10000 "+ str(contextname) + " > "+str(ukboutname))
            os.system("/var/www/html/erraztest/ukb-master/src/ukb_wsd --client --port 10000 " + str(
                contextname) + " > " + str(ukboutname))
        if language == 'basque':
            # os.system("/home/kepa/ukb-master/src/ukb_wsd --client --port 10001 "+ str(contextname) + " > "+str(ukboutname))
            os.system("/var/www/html/erraztest/ukb-master/src/ukb_wsd --client --port 10001 " + str(
                contextname) + " > " + str(ukboutname))
        if language == 'spanish':
            os.system(
                "/var/www/html/erraztest/ukb-master/src/ukb_wsd --client --port 10002 " + str(contextname) + " > " + str(ukboutname))
        # os.system("/home/kepa/ukb-master/src/ukb_wsd --client --port 10000 /media/datos/Dropbox/ikerkuntza/metrix-env/LagunTest/context.txt > /media/datos/Dropbox/ikerkuntza/metrix-env/LagunTest/ukbout.txt")
        ukbout = open(ukboutname, "r",encoding='utf-8')
        lerro = ukbout.readline()
        # print(lerro)
        while lerro:
            if "ctx_01" in lerro:
                lerro = lerro.rstrip('\n')
                lema = lerro.split('!!', 1)[1]  # ['ctx_01 w4  03481172-n', 'hammer']
                resto = lerro.split('!!', 1)[0]
                sense = resto.split('  ', 2)[1]
                wordid = resto.split(' ', 3)[1]
                # print(lema+"\t"+sense+"\t"+wordid)
                self.wordsense[lema.strip()] = sense
            lerro = ukbout.readline()
            # print(lerro)
        ukbout.close()
        #os.system("rm " + str(ukboutname))
        #os.system("rm " + str(contextname))


class Word:
    def __init__(self):
        self._index = None
        self._text = None
        self._lemma = None
        self._upos = None
        self._xpos = None
        self._feats = None
        self._governor = None
        self._dependency_relation = None
        self.wordfrequency = None

    @property
    def dependency_relation(self):
        """ Access dependency relation of this word. Example: 'nmod'"""
        return self._dependency_relation

    @dependency_relation.setter
    def dependency_relation(self, value):
        """ Set the word's dependency relation value. Example: 'nmod'"""
        self._dependency_relation = value

    @property
    def lemma(self):
        """ Access lemma of this word. """
        return self._lemma

    @lemma.setter
    def lemma(self, value):
        """ Set the word's lemma value. """
        self._lemma = value

    @property
    def governor(self):
        """ Access governor of this word. """
        return self._governor

    @governor.setter
    def governor(self, value):
        """ Set the word's governor value. """
        self._governor = value

    @property
    def pos(self):
        """ Access (treebank-specific) part-of-speech of this word. Example: 'NNP'"""
        return self._xpos

    @pos.setter
    def pos(self, value):
        """ Set the word's (treebank-specific) part-of-speech value. Example: 'NNP'"""
        self._xpos = value

    @property
    def text(self):
        """ Access text of this word. Example: 'The'"""
        return self._text

    @text.setter
    def text(self, value):
        """ Set the word's text value. Example: 'The'"""
        self._text = value

    @property
    def xpos(self):
        """ Access treebank-specific part-of-speech of this word. Example: 'NNP'"""
        return self._xpos

    @xpos.setter
    def xpos(self, value):
        """ Set the word's treebank-specific part-of-speech value. Example: 'NNP'"""
        self._xpos = value

    @property
    def upos(self):
        """ Access universal part-of-speech of this word. Example: 'DET'"""
        return self._upos

    @upos.setter
    def upos(self, value):
        """ Set the word's universal part-of-speech value. Example: 'DET'"""
        self._upos = value

    @property
    def feats(self):
        """ Access morphological features of this word. Example: 'Gender=Fem'"""
        return self._feats

    @feats.setter
    def feats(self, value):
        """ Set this word's morphological features. Example: 'Gender=Fem'"""
        self._feats = value

    @property
    def parent_token(self):
        """ Access the parent token of this word. """
        return self._parent_token

    @parent_token.setter
    def parent_token(self, value):
        """ Set this word's parent token. """
        self._parent_token = value

    @property
    def index(self):
        """ Access index of this word. """
        return self._index

    @index.setter
    def index(self, value):
        """ Set the word's index value. """
        self._index = value

    def get_rare_level(self, language):
        self.wordfrequency = 0
        if language == "spanish":
            self.wordfrequency = zipf_frequency(self.lemma, 'es')
        elif language == "english":
            self.wordfrequency = zipf_frequency(self.lemma, 'en')
        elif language == "basque":
            if self.lemma in Maiztasuna.freq_list:
                self.wordfrequency = Maiztasuna.freq_list[self.lemma]
            else:
                self.wordfrequency = 0
        return self.wordfrequency

    
    def get_dependentzia(self, language):
        dep = "_"
#acl(adjectival clause)	MOD	Modifier	Aditzlaguna	Complemento circustancial
#amod(adjectival modifier)	MOD	Modifier	Aditzlaguna	Complemento circustancial
#advcl(adverbial clause modifier)	MOD	Modifier	Aditzlaguna	Complemento circustancial
#conj(conjunct)	lot			
#advmod(adverbial modifier)	MOD	Modifier	Aditzlaguna	Complemento circustancial
#appos(appositional modifier)	MOD	Modifier	Aditzlaguna	Complemento circustancial
#aux(auxiliary verb)				
#case(case marking)				
#ccomp(clausal complement)	A	Core argument (other)	Bestelako osagarria	otro complemento
#clf(classifier)				
#compound(compound)				
#cc(coordinating conjunction)				
#cop(copula)	C	Copula verb (‘to be’) 	'Izan’ edo ‘egon’ aditza	Verbo copulativo (‘ser’ o ‘estar’)
#csubj(clausal subject)	S	Subject	Subjektua	Sujecto
#det(determiner)				
#discourse(discourse element)				
#dislocated(dislocated elements)				
#expl(expletive)				
#fixed(fixed multiword expression)				
#flat(flat multiword expression)				
#iobj(indirect object)	IO	Indirect object	Zehar osagarria	Complemento indirecto
#list(list)				
#nmod(nominal modifier)	MOD	Modifier	Aditzlaguna	Complemento circustancial
#nsubj(nominal subject)	S	Subject	Subjektua	Sujecto
#nummod(numeric modifier)	MOD	Modifier	Aditzlaguna	Complemento circustancial
#mark(marker)				
#obj(object)	O	Object	Osagarri zuzena	Complemento directo
#obl(oblique nominal)				
#orphan(orphan)				
#parataxis(parataxis)				
#punct(punctuation)				
#root(root)	V	Main verb	Aditz nagusia	Verbo principal
#vocative(vocative)	VOC	Vocative	Bokatiboa	Vocativo
#xcomp(open clausal complement)	A	Core argument (other)	Bestelako osagarria	otro complemento
        if language == "spanish" or language == "english" or language == "basque":
            if self.dependency_relation == "acl" or self.dependency_relation == "amod" or self.dependency_relation == "advcl" or self.dependency_relation == "advmod" or self.dependency_relation == "appos" or self.dependency_relation == "nmod" or self.dependency_relation == "nummod" or self.dependency_relation == "relcl" or self.dependency_relation == "npmod" or self.dependency_relation == "poss" or self.dependency_relation == "npmod":
                dep="m"
            if self.dependency_relation == "csubj" or self.dependency_relation == "nsubj" or self.dependency_relation == "pass":
                dep="s"
            if self.dependency_relation == "punct":
                dep="p"
            if self.dependency_relation == "root":
                dep="v"
            if self.dependency_relation == "obj":
                dep="o"
            if self.dependency_relation == "iobj":
                dep="io"
            if self.dependency_relation == "conj" or  self.dependency_relation == "cc" or self.dependency_relation == "preconj":
                dep="lo"
            if self.dependency_relation == "aux" or self.dependency_relation == "pass":
                dep="au"
            if self.dependency_relation == "case":
                dep="ca"
            if self.dependency_relation == "ccomp" or self.dependency_relation == "xcomp":
                dep="a"
            if self.dependency_relation == "cop":
                dep="c"            
            if self.dependency_relation == "det" or self.dependency_relation == "predet":
                dep="de"
            if self.dependency_relation == "vocative":
                dep="vo"
            if self.dependency_relation == "compound" or self.dependency_relation == "fixed" or self.dependency_relation == "flat" or self.dependency_relation == "prt":
                dep="mw"
            if self.dependency_relation == "discourse":
                dep="_"
            if self.dependency_relation == "dislocated":
                dep="_"
            if self.dependency_relation == "expl":
                dep="_"
            if self.dependency_relation == "list":
                dep="_"
            if self.dependency_relation == "mark":
                dep="_"
            if self.dependency_relation == "orphan":
                dep="_"
            if self.dependency_relation == "parataxis":
                dep="_"
            if self.dependency_relation == "clf":
                dep="_"
           
        #elif language == "english":
        #elif language == "basque":
        #else:
        return dep



class Main(object):
    __instance = None

    def __new__(cls):
        if Main.__instance is None:
            Main.__instance = object.__new__(cls)
        return Main.__instance

    def extract_text_from_file(self, input):
        # Si el fichero de entrada no tiene extension .txt
        if ".txt" not in input:
            # textract extrae el texto de todo tipo de formatos (odt, docx, doc ..)
            pre_text = textract.process(input)
            # decode(encoding='UTF-8',errors='strict') convierte a utf8 y si no puede lanza un error
            text = pre_text.decode()
        else:
            # Si extensión .txt convierte texto a utf-8
            with open(input, encoding='utf-8') as f:
                text = f.read()
        return text

    def start(self):
        ####input file##################
        # input="lactation.txt"
        # input="planet.txt"
        # input="edoskitze.doc"
        input = sys.argv[1]

        ####nivel de dificultad#########
        # difficult_level='b'
        difficult_level = sys.argv[2]

        ####idioma###########
        # language='english'
        # language='basque'
        language = sys.argv[3]
        #####carpeta temporal############
        carpeta = sys.argv[4]
        #carpeta = "/media/datos/Dropbox/ikerkuntza/metrix-env/LagunTest2/tmp/"
        input = carpeta + input

        # difficult=difficultlevel(language,difficult_level)
        # print(difficult)
        # Carga wordfrequency euskara eta definizioak
        if language == "basque":
            maiztasuna = Maiztasuna("/var/www/html/erraztest/eu/LB2014Maiztasunak_zenbakiakKenduta.csv")
            maiztasuna.load()
        if language == "basque" or language == "spanish":
            WNdef= WN_definizioak(language)
            WNdef.load()
            #WNdef.print()


        directory = '/var/www'
        model = "stanford"

        # Carga del modelo Stanford/NLPCube
        cargador = NLPCharger(language, model, directory, difficult_level)
        cargador.download_model()
        cargador.load_model()
        # Carga StopWords
        stopw = Stopwords(language)
        #stopw.download()
        stopw.load()
        # stopw.print()

        ###############Tratamiento de texto###############################################
        # quitar todos los retornos \n si contiene
        # remove text inside parentheses
        # separa , . ! ( ) ? ; del texto con espacios, teniendo en cuenta que los no son numeros en el caso de , y .
        # sustituye 2 espacios seguidos por 1
        text = self.extract_text_from_file(input)

        #####WordCloud
        wordcloud = WordCloud(relative_scaling=1.0, stopwords=set(Stopwords.stop_words)).generate(text)
        # Finally, use matplotlib to render the word cloud:
        # plot_wordcloud(wordcloud)
        wordcloudfilename = input + ".png"
        # wordcloudfilename="resume.png"
        wordcloud.to_file(wordcloudfilename)

        document = cargador.get_estructure(text)
        indicators = document.calculate_all_atributes(input)
        #print(text)


main = Main()
main.start()



