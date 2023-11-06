from flask import Flask, request, jsonify
import cv2
from flask_cors import CORS

app = Flask(__name__)
CORS(app)  # Enable CORS for your app

def calculate_color_histogram(image_path):
    # Read the image
    img = cv2.imread(image_path, 1)
    chans = cv2.split(img)
    histograms = []

    for chan in chans:
        hist = cv2.calcHist([chan], [0], None, [256], [0, 256])
        # Convert the histogram to a list for easy serialization
        histograms.append(hist.ravel().tolist())  # Use .ravel() to flatten the histogram

    return histograms

@app.route('/image', methods=['POST'])
def process_image():
    data = request.get_json()
    image_path = data.get('imagePath')

    histograms = calculate_color_histogram(image_path)

    # Return a single structure with all three histograms
    response_data = {
        "histogram1": histograms[0],
        "histogram2": histograms[1],
        "histogram3": histograms[2]
    }

    return jsonify(response_data)

if __name__ == "__main__":
    app.run(debug=True,port=5555)