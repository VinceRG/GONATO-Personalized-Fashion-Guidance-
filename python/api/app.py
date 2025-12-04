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
from sklearn.cluster import KMeans  # Required for Color Analysis

app = Flask(__name__)

# --- Configuration ---
# UPDATE THIS PATH if necessary
MODEL_DIR = r"D:\xammp\htdocs\projectCollab\new\GONATO-Personalized-Fashion-Guidance-\python" 

# --- Load Models (Global Scope) ---
print("Loading MediaPipe and ML Models...")
mp_pose = mp.solutions.pose
pose_estimator = mp_pose.Pose(static_image_mode=True, min_detection_confidence=0.7)
mp_segmentation = mp.solutions.selfie_segmentation
segmentation = mp_segmentation.SelfieSegmentation(model_selection=0)

# Load ML Models for Body Shape
try:
    ml_model = joblib.load(os.path.join(MODEL_DIR, "body_shape_model.pkl"))
    scaler = joblib.load(os.path.join(MODEL_DIR, "scaler.pkl"))
    label_encoder = joblib.load(os.path.join(MODEL_DIR, "label_encoder.pkl"))
    model_loaded = True
except Exception as e:
    print(f"Error loading ML models: {e}")
    model_loaded = False

# --- Helper Functions (Body Shape) ---
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

# --- Helper Functions (Color Analysis) ---
def get_dominant_color(image, k=1):
    pixels = image.reshape((-1, 3))
    kmeans = KMeans(n_clusters=k, n_init=10)
    kmeans.fit(pixels)
    return kmeans.cluster_centers_[0]

def determine_season(rgb_color):
    r, g, b = rgb_color
    r, g, b = r/255.0, g/255.0, b/255.0
    
    mx = max(r, g, b)
    mn = min(r, g, b)
    df = mx - mn
    if mx == mn:
        h = 0
    elif mx == r:
        h = (60 * ((g - b) / df) + 360) % 360
    elif mx == g:
        h = (60 * ((b - r) / df) + 120) % 360
    elif mx == b:
        h = (60 * ((r - g) / df) + 240) % 360
    v = mx * 100

    is_warm = (h < 40 or h > 330)
    
    if is_warm:
        if v > 60: return "Spring"
        else: return "Autumn"
    else:
        if v > 60: return "Summer"
        else: return "Winter"

# --- Routes ---

@app.route('/analyze_color', methods=['POST'])
def analyze_color():
    if 'face_image' not in request.files:
        return jsonify({
            "status": "error",
            "message": "No image was uploaded. Please choose a selfie and try again."
        }), 400

    try:
        file = request.files['face_image']
        img = np.array(Image.open(file.stream).convert("RGB"))

        mp_face_detection = mp.solutions.face_detection
        with mp_face_detection.FaceDetection(
            model_selection=1,
            min_detection_confidence=0.5
        ) as face_detection:

            results = face_detection.process(img)

            # ✅ STRICT: Require at least one face
            if not results.detections:
                return jsonify({
                    "status": "error",
                    "message": (
                        "We couldn’t detect a face in your photo. "
                        "Please upload a clear selfie where your face is fully visible, "
                        "looking at the camera, with good lighting."
                    )
                }), 400

            # Use the first detected face
            detection = results.detections[0]
            bbox = detection.location_data.relative_bounding_box
            h, w, _ = img.shape

            x = int(bbox.xmin * w)
            y = int(bbox.ymin * h)
            w_box = int(bbox.width * w)
            h_box = int(bbox.height * h)

            # Small safety padding crop around the face center
            center_x = x + w_box // 2
            center_y = y + h_box // 2
            crop_size = int(w_box * 0.4)  # a bit bigger than before for robustness

            y1 = max(center_y - crop_size, 0)
            y2 = min(center_y + crop_size, h)
            x1 = max(center_x - crop_size, 0)
            x2 = min(center_x + crop_size, w)

            face_crop = img[y1:y2, x1:x2]

            # ✅ Extra safety: if the crop fails for some reason, treat it as no valid face
            if face_crop.size == 0:
                return jsonify({
                    "status": "error",
                    "message": (
                        "We had trouble reading your face from the photo. "
                        "Please make sure your full face is visible and not cropped out, "
                        "then try again with a new selfie."
                    )
                }), 400

            # === Color Analysis on face_crop ===
            skin_tone_rgb = get_dominant_color(face_crop)
            season = determine_season(skin_tone_rgb)

            palettes = {
                "Spring": ["Coral", "Peach", "Golden Yellow"],
                "Summer": ["Lavender", "Powder Blue", "Soft Rose"],
                "Autumn": ["Olive", "Rust", "Mustard"],
                "Winter": ["Royal Blue", "Emerald", "Black"]
            }

            return jsonify({
                "status": "success",
                "season": season,
                "palette": palettes.get(season, []),
                "skin_tone_rgb": skin_tone_rgb.tolist()
            })

    except Exception as e:
        # You might later replace str(e) with a more generic message in production
        return jsonify({
            "status": "error",
            "message": "Something went wrong while analyzing your photo. Please try again with a clear selfie."
        }), 500


@app.route('/analyze', methods=['POST'])
def analyze():
    if not model_loaded:
        return jsonify({"status": "error", "message": "ML Models not loaded on server."}), 500

    if 'front_image' not in request.files or 'side_image' not in request.files or 'height_cm' not in request.form:
        return jsonify({"status": "error", "message": "Missing files or height data."}), 400

    try:
        height_cm = float(request.form['height_cm'])
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

        # 4. Calculate Widths
        f_l_sh = front_lm[mp_pose.PoseLandmark.LEFT_SHOULDER]
        f_r_sh = front_lm[mp_pose.PoseLandmark.RIGHT_SHOULDER]
        f_l_waist, f_r_waist = get_waist_landmarks(front_lm)
        f_l_hip = front_lm[mp_pose.PoseLandmark.LEFT_HIP]
        f_r_hip = front_lm[mp_pose.PoseLandmark.RIGHT_HIP]

        shoulder_px = math.dist((f_l_sh.x * w_front, f_l_sh.y * h_front), (f_r_sh.x * w_front, f_r_sh.y * h_front))
        waist_px = math.dist((f_l_waist.x * w_front, f_l_waist.y * h_front), (f_r_waist.x * w_front, f_r_waist.y * h_front))
        hip_px = math.dist((f_l_hip.x * w_front, f_l_hip.y * h_front), (f_r_hip.x * w_front, f_r_hip.y * h_front))

        # 5. Calculate Depths
        seg_results = segmentation.process(side_img)
        mask = seg_results.segmentation_mask
        
        s_l_waist, s_r_waist = get_waist_landmarks(side_lm)
        s_waist_y = (s_l_waist.y + s_r_waist.y) / 2
        s_hip_y = (side_lm[mp_pose.PoseLandmark.LEFT_HIP].y + side_lm[mp_pose.PoseLandmark.RIGHT_HIP].y) / 2

        waist_depth_px = get_depth_at_y(mask, s_waist_y * h_side)
        hip_depth_px = get_depth_at_y(mask, s_hip_y * h_side)

        # 6. Convert to CM
        shoulder_cm = shoulder_px / ratio
        waist_width_cm = waist_px / ratio
        hip_width_cm = hip_px / ratio
        waist_depth_cm = waist_depth_px / ratio
        hip_depth_cm = hip_depth_px / ratio

        waist_circ = calculate_ellipse_circumference(waist_width_cm, waist_depth_cm)
        hip_circ = calculate_ellipse_circumference(hip_width_cm, hip_depth_cm)

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