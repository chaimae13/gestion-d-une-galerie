from flask import Flask, request, jsonify
import cv2
import numpy as np
from sklearn.cluster import KMeans
import os

app = Flask(__name__)

@app.route('/ColorDominant', methods=['POST'])
def ColorDominant():
    data = request.get_json()
    image_path = data.get('image_path')

    # Check if the image file exists
    if not os.path.isfile(image_path):
        return jsonify({'error': 'Image file not found'}), 400

    # Load the image
    img = cv2.imread(image_path)

    # Check if the image was loaded successfully
    if img is None:
        return jsonify({'error': 'Failed to load the image'}), 500

    # Nombre de couleurs
    nbreDominantColors = 5

    # Changement d'echelle, pour avoir moins d'exemples
    width = 50  # largeur cible
    ratio = img.shape[0] / img.shape[1]
    height = int(img.shape[1] * ratio)
    dim = (width, height)
    img = cv2.resize(img, dim)

    # Paramètres d'apprentissage
    # Un triplet (B, G, R) par ligne
    examples = img.reshape((img.shape[0] * img.shape[1], 3))

    # Groupement par la technique des KMEANS
    kmeans = KMeans(n_clusters=nbreDominantColors)
    kmeans.fit(examples)

    # Les Centres des groupement représentent les couleurs dominantes (B, G, R)
    hex_color_codes = []

    colors = kmeans.cluster_centers_.astype(int)
    for i in range(nbreDominantColors):
        color = [int(x) for x in colors[i]]
        hex_code = "#{:02X}{:02X}{:02X}".format(color[2], color[1], color[0])
        hex_color_codes.append(hex_code)

    return jsonify({'hex_color_codes': hex_color_codes})

if __name__ == "__main__":
    app.run(debug=True)
