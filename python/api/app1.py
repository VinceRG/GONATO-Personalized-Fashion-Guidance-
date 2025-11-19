# Save this as app.py
from flask import Flask, request, jsonify
import cv2
import numpy as np
import mediapipe as mp
from sklearn.cluster import KMeans
import os
import warnings
import traceback 
import sys 

warnings.filterwarnings('ignore', category=UserWarning, module='sklearn')
app = Flask(__name__)


### --- FACIAL FEATURE COLOR ANALYSIS --- ###
LIP_INDICES = [61, 91, 181, 84, 17, 314, 405, 321, 375, 409, 270, 269, 267, 0, 37, 39, 40]
LEFT_EYE_INDICES = [469, 470, 471, 472] # Iris indices
RIGHT_EYE_INDICES = [474, 475, 476, 477] # Iris indices
CHEEK_INDICES = [234, 454, 361, 132] # Approx cheek region

def get_pixels_in_polygon(image, landmarks):
    height, width, _ = image.shape
    points = np.array([(int(lm.x * width), int(lm.y * height)) for lm in landmarks], dtype=np.int32)
    mask = np.zeros((height, width), dtype=np.uint8)
    cv2.fillPoly(mask, [points], 255)
    return image[mask == 255]

def find_dominant_color_kmeans(pixels, n_clusters=3):
    if len(pixels) < n_clusters:
        if len(pixels) == 0:
            return np.array([0, 0, 0])
        return pixels.mean(axis=0).astype(int)
    kmeans = KMeans(n_clusters=n_clusters, n_init=10, random_state=0).fit(pixels)
    dominant_cluster = np.bincount(kmeans.labels_).argmax()
    return kmeans.cluster_centers_[dominant_cluster].astype(int)

def rgb_to_int(rgb):
    return (rgb[2] << 16) + (rgb[1] << 8) + rgb[0]

def extract_dominant_color(image, landmarks, indices):
    if landmarks:
        try:
            region_landmarks = [landmarks[0].landmark[i] for i in indices]
            pixels = get_pixels_in_polygon(image, region_landmarks)
            dominant_color = find_dominant_color_kmeans(pixels)
            return rgb_to_int(dominant_color)
        except Exception as e:
            print(f"Error extracting color for indices {indices}: {e}")
            return None
    return None

### --- SEASONAL COLOR ANALYSIS --- ###

def rgb_to_hsv(rgb):
    r, g, b = rgb[0] / 255.0, rgb[1] / 255.0, rgb[2] / 255.0
    max_c = max(r, g, b); min_c = min(r, g, b); diff = max_c - min_c
    if diff == 0: h = 0
    elif max_c == r: h = (60 * ((g - b) / diff) + 360) % 360
    elif max_c == g: h = (60 * ((b - r) / diff) + 120) % 360
    else: h = (60 * ((r - g) / diff) + 240) % 360
    s = 0 if max_c == 0 else (diff / max_c) * 100
    v = max_c * 100
    return h, s, v

def calculate_undertone(skin_rgb):
    r, g, b = skin_rgb
    if r > g and r > b and g > b: return "warm"
    if b > r and b > g: return "cool"
    if (r > b and g > b) and abs(r - g) < 20: return "warm"
    if (r > g and b > g) and abs(r-b) < 20: return "cool"
    return "neutral"

def calculate_contrast_level(skin_rgb, eye_rgb):
    skin_intensity = 0.299 * skin_rgb[0] + 0.587 * skin_rgb[1] + 0.114 * skin_rgb[2]
    eye_intensity = 0.299 * eye_rgb[0] + 0.587 * eye_rgb[1] + 0.114 * eye_rgb[2]
    contrast = abs(skin_intensity - eye_intensity)
    if contrast > 70: return "high"
    elif contrast > 35: return "medium"
    else: return "low"

def determine_seasonal_color(skin_rgb, eye_rgb, undertone, contrast):
    h, s, v = rgb_to_hsv(skin_rgb); is_light = v > 60; is_muted = s < 35
    if undertone == "warm":
        if contrast == "low" or is_light: return "Spring", "Warm, bright, and light"
        else: return "Autumn", "Warm, deep, and muted"
    elif undertone == "cool":
        if is_light or is_muted: return "Summer", "Cool, light, and soft"
        else: return "Winter", "Cool, deep, and bright"
    else:
        if contrast == "high" or not is_muted: return "Winter", "Cool, deep, and bright (often Neutral-Cool)"
        else: return "Summer", "Cool, light, and soft (often Neutral-Cool)"

def get_seasonal_palette(season):
    palettes = {
        "Spring": {"best": ["Peach", "Coral", "Light Aqua", "Golden Yellow"], "neutrals": ["Cream", "Warm Gray"], "metals": "Gold"},
        "Summer": {"best": ["Powder Blue", "Lavender", "Soft Pink", "Mauve"], "neutrals": ["Soft Gray", "Rose Beige"], "metals": "Silver"},
        "Autumn": {"best": ["Olive Green", "Rust", "Mustard", "Terracotta"], "neutrals": ["Chocolate Brown", "Camel"], "metals": "Gold"},
        "Winter": {"best": ["True Red", "Royal Blue", "Emerald Green", "Black", "Pure White"], "neutrals": ["Black", "White", "Navy"], "metals": "Silver"}
    }
    return palettes.get(season, {})

def analyze_skin_for_seasonal_colors(image_path):
    mp_face_mesh = mp.solutions.face_mesh
    face_mesh = mp_face_mesh.FaceMesh(
        static_image_mode=True, 
        max_num_faces=1, 
        refine_landmarks=True,
        min_detection_confidence=0.5
    )
    
    image = cv2.imread(image_path)
    if image is None: return {"error": "Failed to read image"}
    
    rgb_image = cv2.cvtColor(image, cv2.COLOR_BGR2RGB)
    results = face_mesh.process(rgb_image)
    
    if not results.multi_face_landmarks:
        face_mesh.close()
        return {"error": "No face detected in image"}
    
    landmarks = results.multi_face_landmarks
    
    # --- THIS IS THE FIX ---
    
    # 1. Get the list of CHEEK landmark points
    cheek_landmarks = [landmarks[0].landmark[i] for i in CHEEK_INDICES]
    # 2. Pass that specific list to the function
    cheek_pixels = get_pixels_in_polygon(rgb_image, cheek_landmarks)
    
    # --- (END OF FIX) ---
    
    if len(cheek_pixels) == 0:
        face_mesh.close()
        return {"error": "Could not detect skin region"}
    skin_rgb = find_dominant_color_kmeans(cheek_pixels).astype(int)

    # --- APPLY THE SAME FIX FOR EYES ---

    # 1. Get the list of LEFT EYE landmark points
    left_eye_landmarks = [landmarks[0].landmark[i] for i in LEFT_EYE_INDICES]
    # 2. Pass that specific list to the function
    left_eye_pixels = get_pixels_in_polygon(rgb_image, left_eye_landmarks)
    
    # 1. Get the list of RIGHT EYE landmark points
    right_eye_landmarks = [landmarks[0].landmark[i] for i in RIGHT_EYE_INDICES]
    # 2. Pass that specific list to the function
    right_eye_pixels = get_pixels_in_polygon(rgb_image, right_eye_landmarks)
    
    # --- (END OF FIX) ---

    eye_pixels = np.concatenate((left_eye_pixels, right_eye_pixels))
    
    if len(eye_pixels) == 0:
        face_mesh.close()
        return {"error": "Could not detect eye region"}
    eye_rgb = find_dominant_color_kmeans(eye_pixels).astype(int)
    
    undertone = calculate_undertone(skin_rgb)
    contrast = calculate_contrast_level(skin_rgb, eye_rgb)
    season, description = determine_seasonal_color(skin_rgb, eye_rgb, undertone, contrast)
    palette = get_seasonal_palette(season)
    
    face_mesh.close()
    
    return {
        "seasonal_color": season, "description": description, "undertone": undertone,
        "contrast_level": contrast,
        "skin_tone": {"rgb": skin_rgb.tolist(), "hex": f"#{skin_rgb[0]:02x}{skin_rgb[1]:02x}{skin_rgb[2]:02x}"},
        "eye_tone": {"rgb": eye_rgb.tolist(), "hex": f"#{eye_rgb[0]:02x}{eye_rgb[1]:02x}{eye_rgb[2]:02x}"},
        "color_palette": palette
    }

@app.route('/seasonal_analysis', methods=['POST'])
def seasonal_analysis_endpoint():
    if 'image' not in request.files:
        return jsonify({"error": "Missing image file"}), 400

    image_file = request.files['image']
    temp_path = 'temp_seasonal_analysis.jpg'
    image_file.save(temp_path)
    
    try:
        result = analyze_skin_for_seasonal_colors(temp_path)
        if os.path.exists(temp_path): os.remove(temp_path)
        
        if "error" in result:
             return jsonify(result), 400
        return jsonify(result)
    
    except Exception as e:
        print("!!!!!!!!!!!!!!!!! AN ERROR OCCURRED !!!!!!!!!!!!!!!!!!!", file=sys.stderr)
        traceback.print_exc()
        print("!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!", file=sys.stderr)
        
        if os.path.exists(temp_path): os.remove(temp_path)
        return jsonify({"error": "Analysis failed", "details": str(e)}), 500

@app.route('/health', methods=['GET'])
def health_check():
    return jsonify({"status": "healthy"})

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=8001, debug=False)