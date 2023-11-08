import os

from flask import Flask, request, jsonify
import cv2
import numpy as np

app = Flask(__name__)

def calculate_color_features(image_path):
    # Lire l'image
    img = cv2.imread(image_path)

    if img is None:
        return None

    # Convertir l'image en espace de couleur HSV
    hsv_img = cv2.cvtColor(img, cv2.COLOR_BGR2HSV)

    # Calculer la moyenne de teinte, saturation et luminosité
    hue_mean = np.mean(hsv_img[:, :, 0])
    saturation_mean = np.mean(hsv_img[:, :, 1])
    value_mean = np.mean(hsv_img[:, :, 2])

    # Calculer le contraste de luminosité
    gray_img = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)
    brightness_contrast = cv2.convertScaleAbs(gray_img)

    # Variance des valeurs de contraste
    contrast_var = np.var(brightness_contrast)

    return {
        # value hue
        'hue_moyenne': hue_mean,
        # saturation
        'saturation_moyenne': saturation_mean,
        # la moyenne de teinte, saturation et luminosité
        'value_moyenne': value_mean,
        # Variance des valeurs de contraste
        'contrast': contrast_var
    }

@app.route('/momentColeur', methods=['POST'])
def colorFeatures():
    data = request.get_json()
    image_path = data.get('image_path')

    # Vérifier si le fichier image existe
    if not os.path.isfile(image_path):
        return jsonify({'error': 'Image file not found'}), 400

    color_features = calculate_color_features(image_path)

    if color_features is None:
        return jsonify({'error': 'Failed to load or process the image'}), 500

    return jsonify(color_features)

if __name__ == "__main__":
    app.run(debug=True,port=5580)
