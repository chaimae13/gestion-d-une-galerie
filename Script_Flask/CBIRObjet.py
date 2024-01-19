import json
import trimesh

from flask import Flask, request, jsonify
import os
import numpy as np

app = Flask(__name__)
SIMILARITY_THRESHOLD = 0.2  # Example value
SOME_INCREMENT=0.2
SOME_LOWER_SIMILARITY_THRESHOLD=0.1

def calculate_principal_axes_of_inertia(model):
    # Assuming model is a structure containing vertices and faces of the 3D model
    # Compute the center of mass
    center_of_mass = np.mean(model.vertices, axis=0)

    # Translate vertices to center the model at the origin
    translated_vertices = model.vertices - center_of_mass

    # Compute covariance matrix
    covariance_matrix = np.cov(translated_vertices.T)

    # Compute eigenvectors (principal axes)
    eigenvalues, eigenvectors = np.linalg.eig(covariance_matrix)

    # Return the principal axes
    return eigenvectors


def calculate_moments_first_principal_axis(model, num_slabs):
    principal_axes = calculate_principal_axes_of_inertia(model)
    first_principal_axis = principal_axes[:, 0]

    # Assuming model.vertices is a numpy array of shape (n, 3)
    projected_vertices = np.dot(model.vertices, first_principal_axis)

    # Segment the model
    min_proj, max_proj = projected_vertices.min(), projected_vertices.max()
    slab_width = (max_proj - min_proj) / num_slabs
    slabs = [(min_proj + i * slab_width, min_proj + (i + 1) * slab_width) for i in range(num_slabs)]

    moments = []
    for slab in slabs:
        # Select vertices in the current slab
        slab_vertices = model.vertices[(projected_vertices >= slab[0]) & (projected_vertices < slab[1])]

        # Calculate moment of inertia for the slab
        # This is a simplification; actual calculation depends on the mass distribution
        distance_squared = np.sum((slab_vertices - np.mean(slab_vertices, axis=0)) ** 2, axis=1)
        moment_of_inertia = np.sum(distance_squared)
        moments.append(moment_of_inertia)

    return moments

def calculate_variance_distance_faces(model, num_slabs):
    principal_axes = calculate_principal_axes_of_inertia(model)
    first_principal_axis = principal_axes[:, 0]
    projected_vertices = np.dot(model.vertices, first_principal_axis)

    min_proj, max_proj = projected_vertices.min(), projected_vertices.max()
    slab_width = (max_proj - min_proj) / num_slabs
    slabs = [(min_proj + i * slab_width, min_proj + (i + 1) * slab_width) for i in range(num_slabs)]

    variances = []
    for slab in slabs:
        slab_vertices = model.vertices[(projected_vertices >= slab[0]) & (projected_vertices < slab[1])]
        distances = np.linalg.norm(slab_vertices - np.dot(slab_vertices, first_principal_axis[:, np.newaxis]), axis=1)
        variance = np.var(distances)
        variances.append(variance)

    return variances

def calculate_average_distance_faces(model, num_slabs):
    principal_axes = calculate_principal_axes_of_inertia(model)
    first_principal_axis = principal_axes[:, 0]
    projected_vertices = np.dot(model.vertices, first_principal_axis)

    min_proj, max_proj = projected_vertices.min(), projected_vertices.max()
    slab_width = (max_proj - min_proj) / num_slabs
    slabs = [(min_proj + i * slab_width, min_proj + (i + 1) * slab_width) for i in range(num_slabs)]

    average_distances = []
    for slab in slabs:
        slab_vertices = model.vertices[(projected_vertices >= slab[0]) & (projected_vertices < slab[1])]
        distances = np.linalg.norm(slab_vertices - np.dot(slab_vertices, first_principal_axis[:, np.newaxis]), axis=1)
        average_distance = np.mean(distances)
        average_distances.append(average_distance)

    return average_distances



def extract_features_and_store(model, num_slabs, file_path):
    """
    Extracts features from a 3D model and stores them in a JSON file.

    Parameters:
    model (object): The 3D model object.
    num_slabs (int): Number of slabs for segmentation.
    file_path (str): Path to the JSON file where features will be stored.

    Returns:
    None
    """
    # Extract features
    principal_axes = calculate_principal_axes_of_inertia(model)
    moments = calculate_parametrized_moments(model, num_slabs)
    variances = calculate_variance_distance_faces(model, num_slabs)
    average_distances = calculate_average_distance_faces(model, num_slabs)

    # Prepare the data to be stored
    data = {
        'principal_axes': principal_axes.tolist(),  # Convert numpy array to list for JSON serialization
        'moments': moments,
        'variances': variances,
        'average_distances': average_distances
    }

    # Load existing data from the central JSON database
    if os.path.exists(file_path):
        with open(file_path, 'r') as file:
            database = json.load(file)
    else:
        database = {'models': []}  # Initialize 'models' as an empty list if it doesn't exist

    # Ensure 'models' key exists in the database
    if 'models' not in database:
        database['models'] = []

    # Append new model's data
    database['models'].append(data)

    # Write updated data back to the central database
    with open(file_path, 'w') as file:
        json.dump(database, file, indent=4)
# Example usage
# model = load_your_model_function()  # Replace with your model loading function
# extract_features_and_store(model, num_slabs=10, file_path='path_to_your_json_file.json')

def search_similar_models(query_features, num_slabs, json_directory_path, query_filename):
    model_scores = []

    query_model_data = query_features['models'][0]

    for filename in os.listdir(json_directory_path):
        if filename.endswith('.json') and filename != query_filename:
            file_path = os.path.join(json_directory_path, filename)
            with open(file_path, 'r') as file:
                stored_models = json.load(file)

            if 'models' in stored_models and isinstance(stored_models['models'], list):
                for model_data in stored_models['models']:
                    if all(key in model_data for key in ['moments', 'variances', 'average_distances']):
                        # Calculate distance
                        dtw_dist = elastic_matching_distance(query_model_data['moments'],
                                                             model_data['moments']) + \
                                   elastic_matching_distance(query_model_data['variances'],
                                                             model_data['variances']) + \
                                   elastic_matching_distance(query_model_data['average_distances'],
                                                             model_data['average_distances'])
                        euclidean_dist = euclidean_distance(query_model_data['moments'],
                                                            model_data['moments']) + \
                                         euclidean_distance(query_model_data['variances'],
                                                            model_data['variances']) + \
                                         euclidean_distance(query_model_data['average_distances'],
                                                            model_data['average_distances'])

                        total_distance = dtw_dist + euclidean_dist

                        model_scores.append((filename, total_distance))

    # Sort and select top models
    model_scores.sort(key=lambda x: x[1])
    top_models = [model[0] for model in model_scores[:10]]

    return top_models


def dtw_distance(series1, series2, dist=lambda x, y: np.linalg.norm(np.array(x) - np.array(y))):
    n, m = len(series1), len(series2)
    dtw_matrix = np.zeros((n + 1, m + 1))
    for i in range(n + 1):
        for j in range(m + 1):
            dtw_matrix[i, j] = np.inf
    dtw_matrix[0, 0] = 0

    for i in range(1, n + 1):
        for j in range(1, m + 1):
            cost = dist(series1[i - 1], series2[j - 1])
            dtw_matrix[i, j] = cost + min(dtw_matrix[i - 1, j], dtw_matrix[i, j - 1], dtw_matrix[i - 1, j - 1])

    return dtw_matrix[n, m]


def elastic_matching_distance(features1, features2):
    return dtw_distance(features1, features2)


def load_model(file_path):
    """
    Loads a 3D model from the given file path using trimesh and preprocesses it.

    Parameters:
    file_path (str): The file path to the 3D model.

    Returns:
    Mesh: The loaded and preprocessed 3D model mesh.
    """
    try:
        model = trimesh.load(file_path)
        print(model)
        return model
    except Exception as e:
        print(f"An error occurred while loading the model: {e}")
        return None

def euclidean_distance(vector1, vector2):
    return np.linalg.norm(np.array(vector1) - np.array(vector2))


def extract_features(model, num_slabs):
    print("Model in extract_features:", model)

    # Rest of the function remains unchanged
    principal_axes = calculate_principal_axes_of_inertia(model)
    moments = calculate_moments_first_principal_axis(model, num_slabs)
    variances = calculate_variance_distance_faces(model, num_slabs)
    average_distances = calculate_average_distance_faces(model, num_slabs)

    features = {
        'models': [{  # Wrapping features in a 'models' key
            'principal_axes': principal_axes.tolist(),
            'moments': moments,
            'variances': variances,
            'average_distances': average_distances
        }]
    }

    return features


def normalize_pose(model):
    principal_axes = calculate_principal_axes_of_inertia(model)

    # Créer une matrice de rotation pour aligner les axes principaux avec les axes globaux
    # Ceci est un exemple simplifié et peut nécessiter des ajustements
    rotation_matrix = np.linalg.inv(principal_axes)

    # Appliquer la matrice de rotation aux sommets du modèle
    normalized_vertices = np.dot(model.vertices, rotation_matrix)

    # Créer un nouveau modèle avec les sommets normalisés
    normalized_model = trimesh.Trimesh(vertices=normalized_vertices, faces=model.faces)

    return normalized_model

def calculate_parametrized_moments(model, num_slabs):
    principal_axes = calculate_principal_axes_of_inertia(model)

    # Initialisation d'une liste pour stocker les moments d'inertie paramétrés
    parametrized_moments = []

    # Pour chaque axe principal
    for axis in principal_axes.T:
        projected_vertices = np.dot(model.vertices, axis)
        min_proj, max_proj = projected_vertices.min(), projected_vertices.max()
        slab_width = (max_proj - min_proj) / num_slabs
        slabs = [(min_proj + i * slab_width, min_proj + (i + 1) * slab_width) for i in range(num_slabs)]

        # Calculer les moments pour chaque segment (slab)
        moments = []
        for slab in slabs:
            slab_vertices = model.vertices[(projected_vertices >= slab[0]) & (projected_vertices < slab[1])]
            distance_squared = np.sum((slab_vertices - np.mean(slab_vertices, axis=0)) ** 2, axis=1)
            moment_of_inertia = np.sum(distance_squared)
            moments.append(moment_of_inertia)

        parametrized_moments.append(moments)

    return parametrized_moments
def dtw_distance(series1, series2):
    # Create cost matrix
    n, m = len(series1), len(series2)
    dtw_matrix = np.zeros((n + 1, m + 1))
    for i in range(n + 1):
        for j in range(m + 1):
            dtw_matrix[i, j] = np.inf
    dtw_matrix[0, 0] = 0

    # Define a function to calculate the distance between two elements
    def distance(elem1, elem2):
        return np.linalg.norm(np.array(elem1) - np.array(elem2))

    # Populate the matrix
    for i in range(1, n + 1):
        for j in range(1, m + 1):
            cost = distance(series1[i - 1], series2[j - 1])
            dtw_matrix[i, j] = cost + min(dtw_matrix[i - 1, j],    # insertion
                                          dtw_matrix[i, j - 1],    # deletion
                                          dtw_matrix[i - 1, j - 1]) # match

    return dtw_matrix[n, m]




@app.route('/upload_model', methods=['POST'])
def upload_model():
    data = request.json
    model_file_path = data['filename']

    # Check if the file exists
    if not os.path.exists(model_file_path):
        return jsonify({"error": "File not found"}), 404

    # Load and process the model
    model = load_model(model_file_path)
    normalized_model = normalize_pose(model)

    # Derive JSON file name and path
    json_filename = os.path.splitext(os.path.basename(model_file_path))[0] + '.json'

    print(json_filename)
    json_file_path = os.path.join('C:/Users/hp/Desktop/CBIR/ccbir/tesss/save_models', json_filename)

    # Extract features and store in JSON
    extract_features_and_store(normalized_model, num_slabs=10, file_path=json_file_path)

    return jsonify({"message": "Model uploaded and features extracted", "json_path": json_file_path})


@app.route('/search_similar', methods=['POST'])
def search_similar():
    data = request.json  # Access JSON data sent in the request
    file_path = data.get('file_path')  # Use .get() to avoid KeyError

    if not file_path:
        return jsonify({"error": "file_path not provided"}), 400

    # Extract the filename of the query model
    query_filename = os.path.basename(file_path)

    # Load query model features
    if file_path.endswith('.json'):
        with open(file_path, 'r') as file:
            query_features = json.load(file)
    else:
        model = load_model(file_path)
        query_features = extract_features(model, num_slabs=10)

    print("Query Features:", query_features)  # Debugging output

    # Path to the directory where JSON files are stored
    json_files_directory = 'C:/Users/hp/Desktop/CBIR/ccbir/tesss/save_models'

    # Search for similar models
    similar_models = search_similar_models(query_features, num_slabs=10, json_directory_path=json_files_directory, query_filename=query_filename)

    return jsonify({"similar_models": similar_models})

if __name__ == '__main__':
    app.run(debug=True)