import random
import pickle
import numpy as np
import json
import click
import os
import nltk
import base64

from dotenv import load_dotenv
from nltk.stem import WordNetLemmatizer
from tensorflow.keras.models import load_model

nltk.download('punkt')
nltk.download('wordnet')
#from flask import Flask, request, jsonify
#from flask_cors import CORS

@click.group()
def cli():
    pass
@click.command()
@click.argument('message')
@click.option('-c', '--company', 'company')
@click.option('-t', '--tool', 'tool')
@click.option('-l', '--language', 'language')

def reply(message, company, tool, language):

    path = os.path.dirname(__file__)

    load_dotenv(os.path.join(path, '.env'))

    intents = 'intents.json'
    words_file = 'words.pkl'
    classe_file = 'classes.pkl'
    models_file = 'chatbot_model.model'

    wordsEnv = os.path.join(path, "Tools", "Company", company, tool, language, words_file)
    classeEnv = os.path.join(path, "Tools", "Company", company, tool, language, classe_file)
    modelsEnv = os.path.join(path, "Tools", "Company", company, tool, language, models_file)
    intentsEnv = os.path.join(path, "Tools", "Company", company, tool, language, intents)


    lemmatizer = WordNetLemmatizer()
    intents = json.loads(open(intentsEnv).read())

    words = pickle.load(open(wordsEnv, 'rb'))
    classes = pickle.load(open(classeEnv, 'rb'))
    model = load_model(modelsEnv)


    words = pickle.load(open(wordsEnv, 'rb'))
    classes = pickle.load(open(classeEnv, 'rb'))
    model = load_model(modelsEnv)
    def clean_up_sentence(sentence):
        sentence_words = nltk.word_tokenize(sentence)
        sentence_words = [lemmatizer.lemmatize(word) for word in sentence_words]
        return sentence_words
    def bag_of_words(sentece):
        sentence_words = clean_up_sentence(sentece)
        bag = [0] * len(words)
        for w in sentence_words:
            for i, word in enumerate(words):
                if word == w:
                    bag[i] = 1
        return np.array(bag, dtype=object)

    def predict_class(sentence):
        bow = bag_of_words(sentence)
        res = model.predict(np.array([bow],np.float32))[0]
        ERROR_THRESHOLD = 0.15
        results = [[i, r] for i, r in enumerate(res) if r > ERROR_THRESHOLD]

        results.sort(key=lambda x: x[1], reverse=True)
        return_list = []
        for r in results:
            return_list.append({'intent': classes[r[0]], 'probability': str(r[1])})
        return return_list
    def get_response(intents_list, intents_json):
        if len(intents_list) == 0:
            return intents_json['intents'][0]['responses'][0]
        else:
            tag = intents_list[0]['intent']
            probability = float(intents_list[0]['probability'])
            list_of_intents = intents_json['intents']
            min_probability = float(0.15)
            med_probability = float(0.4)
            max_probability = float(0.4)
            counter = 0
            for i in list_of_intents:
                if i['tag'] == tag:
                    # retorna uma resposta se a probabilidade for maior que 40%
                    if probability >= max_probability:
                        result = [random.choice(i['responses'])]
                    # retorna não entendi caso a probabilidade esteja menor que 15%
                    elif probability <= min_probability:
                        result = [intents_json['intents'][0]['responses'][0]]
                    # retorna a lista de intents caso a probabilidade esteja entre 15 a 40%
                    elif probability > min_probability and probability < med_probability:
                        result = []
                        for y in list_of_intents:
                            for l in intents_list:
                                if y['tag'] == l['intent']:
                                    if "alternative" in y['tag'] or "greetings" in y['tag']:
                                        if len(intents_list) == 2:
                                            result = [intents_json['intents'][0]['responses'][0]]
                                        else:
                                            continue
                                    else:
                                        result.append(random.choice(y['responses']))
                                        #adiciona apenas a quantidade definida de respostas
                                        counter += 1
                                        if counter == int(3):
                                            break
                            if counter == int(3):
                                break
                    #else:
                        #result = [random.choice(i['responses'])]
                    else:
                        return  intents_json['intents'][0]['responses'][0]
                    break
        return result

    decoded_bytes = base64.b64decode(message)
    decoded_string = decoded_bytes.decode('utf-8')

    intents_list = predict_class(decoded_string)
    response = get_response(intents_list, intents)
    print(response)

cli.add_command(reply)

if __name__ == '__main__':
    cli()


