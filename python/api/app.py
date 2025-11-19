from flask import Flask, request, jsonify
import cv2
import mediapipe as mp
import numpy as np
import math
from PIL import Image
import joblib
import pandas as pd
import os
import io

app = Flask(__name__)

# --- Configuration ---
# UPDATE THIS PATH to where your .pkl files are located
MODEL_DIR = r"D:\xammp\htdocs\projectCollab\new\GONATO-Personalized-Fashion-Guidance-\python" 

# --- Load Models (Global Scope) ---
print("Loading MediaPipe and ML Models...")
mp_pose = mp.solutions.pose
pose_estimator = mp_pose.Pose(static_image_mode=True, min_detection_confidence=0.7)
mp_segmentation = mp.solutions.selfie_segmentation
segmentation = mp_segmentation.SelfieSegmentation(model_selection=0)

try:
    ml_model = joblib.load(os.path.join(MODEL_DIR, "body_shape_model.pkl"))
    scaler = joblib.load(os.path.join(MODEL_DIR, "scaler.pkl"))
    label_encoder = joblib.load(os.path.join(MODEL_DIR, "label_encoder.pkl"))
    model_loaded = True
except Exception as e:
    print(f"Error loading ML models: {e}")
    model_loaded = False

# --- Helper Functions ---
def get_pixel_distance(p1, p2):
    return math.dist(p1, p2)

class Point:
    def __init__(self, x, y):
        self.x = x
        self.y = y

def get_waist_landmarks(landmarks):
    left_hip = landmarks[mp_pose.PoseLandmark.LEFT_HIP]
    right_hip = landmarks[mp_pose.PoseLandmark.RIGHT_HIP]
    left_shoulder = landmarks[mp_pose.PoseLandmark.LEFT_SHOULDER]
    right_shoulder = landmarks[mp_pose.PoseLandmark.RIGHT_SHOULDER]

    ratio = 0.4
    left_waist = Point(
        left_hip.x + (left_shoulder.x - left_hip.x) * ratio,
        left_hip.y + (left_shoulder.y - left_hip.y) * ratio
    )
    right_waist = Point(
        right_hip.x + (right_shoulder.x - right_hip.x) * ratio,
        right_hip.y + (right_shoulder.y - right_hip.y) * ratio
    )
    return left_waist, right_waist

def calibrate_from_height(landmarks, image_height, known_height_cm):
    try:
        top_y = landmarks[mp_pose.PoseLandmark.NOSE].y * image_height
        bottom_y_left = landmarks[mp_pose.PoseLandmark.LEFT_HEEL].y * image_height
        bottom_y_right = landmarks[mp_pose.PoseLandmark.RIGHT_HEEL].y * image_height
        bottom_y = (bottom_y_left + bottom_y_right) / 2
        pixel_height = bottom_y - top_y
        if pixel_height <= 0: return None
        return pixel_height / known_height_cm
    except:
        return None

def calculate_ellipse_circumference(width_cm, depth_cm):
    if width_cm == 0 or depth_cm == 0: return 0
    a, b = width_cm / 2.0, depth_cm / 2.0
    h = ((a - b) ** 2) / ((a + b) ** 2)
    return math.pi * (a + b) * (1 + (3 * h) / (10 + math.sqrt(4 - 3 * h)))

def get_depth_at_y(segmentation_mask, y_pixel):
    y_pixel = int(np.clip(y_pixel, 0, segmentation_mask.shape[0] - 1))
    row = segmentation_mask[y_pixel, :]
    indices = np.where(row > 0.5)[0]
    if len(indices) == 0: return 0
    return indices[-1] - indices[0]

# --- Main Logic ---
@app.route('/analyze', methods=['POST'])
def analyze():
    if not model_loaded:
        return jsonify({"status": "error", "message": "ML Models not loaded on server."}), 500

    if 'front_image' not in request.files or 'side_image' not in request.files or 'height_cm' not in request.form:
        return jsonify({"status": "error", "message": "Missing files or height data."}), 400

    try:
        # Read Inputs
        height_cm = float(request.form['height_cm'])
        
        # Convert uploaded files to numpy arrays (RGB)
        front_file = request.files['front_image']
        side_file = request.files['side_image']
        
        front_img = np.array(Image.open(front_file.stream).convert("RGB"))
        side_img = np.array(Image.open(side_file.stream).convert("RGB"))

        # 1. Process Front Image
        h_front, w_front, _ = front_img.shape
        front_results = pose_estimator.process(front_img)
        if not front_results.pose_landmarks:
            return jsonify({"status": "error", "message": "No pose detected in front image"}), 400
        
        front_lm = front_results.pose_landmarks.landmark

        # 2. Process Side Image
        h_side, w_side, _ = side_img.shape
        side_results = pose_estimator.process(side_img)
        if not side_results.pose_landmarks:
            return jsonify({"status": "error", "message": "No pose detected in side image"}), 400
        side_lm = side_results.pose_landmarks.landmark

        # 3. Calibration
        ratio = calibrate_from_height(front_lm, h_front, height_cm)
        if not ratio:
            return jsonify({"status": "error", "message": "Calibration failed. Stand fully visible."}), 400

        # 4. Calculate Widths (Front)
        f_l_sh = front_lm[mp_pose.PoseLandmark.LEFT_SHOULDER]
        f_r_sh = front_lm[mp_pose.PoseLandmark.RIGHT_SHOULDER]
        f_l_hip = front_lm[mp_pose.PoseLandmark.LEFT_HIP]
        f_r_hip = front_lm[mp_pose.PoseLandmark.RIGHT_HIP]
        f_l_waist, f_r_waist = get_waist_landmarks(front_lm)

        shoulder_px = math.dist((f_l_sh.x * w_front, f_l_sh.y * h_front), (f_r_sh.x * w_front, f_r_sh.y * h_front))
        hip_px = math.dist((f_l_hip.x * w_front, f_l_hip.y * h_front), (f_r_hip.x * w_front, f_r_hip.y * h_front))
        waist_px = math.dist((f_l_waist.x * w_front, f_l_waist.y * h_front), (f_r_waist.x * w_front, f_r_waist.y * h_front))

        # 5. Calculate Depths (Side - using Segmentation)
        seg_results = segmentation.process(side_img)
        mask = seg_results.segmentation_mask
        
        s_sh_y = (side_lm[mp_pose.PoseLandmark.LEFT_SHOULDER].y + side_lm[mp_pose.PoseLandmark.RIGHT_SHOULDER].y) / 2
        s_hip_y = (side_lm[mp_pose.PoseLandmark.LEFT_HIP].y + side_lm[mp_pose.PoseLandmark.RIGHT_HIP].y) / 2
        s_l_waist, s_r_waist = get_waist_landmarks(side_lm)
        s_waist_y = (s_l_waist.y + s_r_waist.y) / 2

        # Get depth in pixels
        waist_depth_px = get_depth_at_y(mask, s_waist_y * h_side)
        hip_depth_px = get_depth_at_y(mask, s_hip_y * h_side)

        # 6. Convert to CM and Circumferences
        shoulder_cm = shoulder_px / ratio
        waist_width_cm = waist_px / ratio
        hip_width_cm = hip_px / ratio
        waist_depth_cm = waist_depth_px / ratio
        hip_depth_cm = hip_depth_px / ratio

        waist_circ = calculate_ellipse_circumference(waist_width_cm, waist_depth_cm)
        hip_circ = calculate_ellipse_circumference(hip_width_cm, hip_depth_cm)

        # Logic adjustment from original code
        if waist_circ > hip_circ * 0.95:
            waist_circ = hip_circ * 0.85

        # 7. ML Prediction
        features = pd.DataFrame([{
            "ShoulderWidth": shoulder_cm,
            "Waist": waist_circ,
            "Hips": hip_circ,
            "TotalHeight": height_cm
        }])
        
        scaled_features = scaler.transform(features)
        pred_idx = ml_model.predict(scaled_features)[0]
        pred_label = label_encoder.inverse_transform([pred_idx])[0]

        return jsonify({
            "status": "success",
            "body_shape": pred_label,
            "measurements": {
                "ShoulderWidth": round(shoulder_cm, 1),
                "Waist": round(waist_circ, 1),
                "Hips": round(hip_circ, 1)
            }
        })

    except Exception as e:
        return jsonify({"status": "error", "message": str(e)}), 500

if __name__ == '__main__':
    app.run(debug=True, port=5000)