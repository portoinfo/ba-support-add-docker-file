import random
import json
import pickle
import numpy as np
import os


import nltk
from nltk.stem import WordNetLemmatizer
import tensorflow as tf
import click

from tensorflow.keras.models import Sequential
from tensorflow.keras.layers import Dense, Activation, Dropout
from tensorflow.keras.optimizers import SGD
from tensorflow.keras.optimizers.schedules import ExponentialDecay

nltk.download('punkt')
nltk.download('wordnet')
@click.group()
def cli():
    pass
@click.command()
@click.option('-c', '--company', 'company')
@click.option('-t', '--tool', 'tool')
@click.option('-l', '--language', 'language')


def rebuild(company, tool, language):

    path = os.path.dirname(__file__)
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

    #primeira parte

    words = []
    classes = []
    documents = []
    ignore_letters = ['?', '!', '.', ',']

    for intent in intents['intents']:
        for pattern in intent['patterns']:
            word_list = nltk.word_tokenize(pattern)
            words.extend(word_list)
            documents.append((word_list, intent['tag']))
            if intent['tag'] not in classes:
                classes.append(intent['tag'])


    words = [lemmatizer.lemmatize(word) for word in words if word not in ignore_letters]
    words = sorted(set(words))

    classes = sorted(set(classes))
    pickle.dump(words, open(wordsEnv, 'wb'))
    pickle.dump(classes, open(classeEnv, "wb"))

    #segunda parte
    training = []
    output_empty = [0] * len(classes)

    for document in documents:
        bag = []
        word_patterns = document[0]
        word_patterns = [lemmatizer.lemmatize(word.lower()) for word in word_patterns]
        for word in words:
            if word in word_patterns:
                bag.append(1) if word in word_patterns else bag.append(0)
            else:
              bag.append(0)

        output_row = list(output_empty)
        output_row[classes.index(document[1])] = 1
        training.append([bag, output_row])

    random.shuffle(training)
    training = np.array(training, dtype=object)

    train_x = np.asarray(list(training[:, 0]), np.float32)
    train_y = np.asarray(list(training[:, 1]), np.float32)

    #rede reural
    model = Sequential()
    model.add(Dense(128, input_shape=(len(train_x[0]),), activation='relu'))
    model.add(Dropout(0.5))
    model.add(Dense(64, activation='relu'))
    model.add(Dropout(0.5))
    model.add(Dense(len(train_y[0]), activation='softmax'))

    @tf.function
    def initialRate(epoch):
        initial_learning_rate = 0.1
        decay_steps = 10000
        decay_rate = 0.96
        staircase = True
        learning_rate = tf.compat.v1.train.exponential_decay(initial_learning_rate, epoch, decay_steps, decay_rate,
                                                             staircase)
        return learning_rate

    initial_learning_rate = initialRate(epoch=0)
    optimizer = tf.keras.optimizers.SGD(learning_rate=initial_learning_rate)
    model.compile(optimizer=optimizer, loss='categorical_crossentropy', metrics=['accuracy'])

    # Treine o modelo

    arr1 = tf.convert_to_tensor(train_x, np.float32)
    arr2 = tf.convert_to_tensor(train_y, np.float32)

    hist = model.fit(arr1, arr2, epochs=200, batch_size=5, verbose=1)
    model.save(modelsEnv, hist)
    print("Done" + tool)

cli.add_command(rebuild)

if __name__ == '__main__':
    cli()