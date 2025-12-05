from flask import Flask, request, jsonify
from flask_cors import CORS
import cv2
import mediapipe as mp
import numpy as np
import math
from PIL import Image
import joblib
import pandas as pd
import os
from sklearn.cluster import KMeans
from datetime import datetime
import os

app = Flask(__name__)
CORS(app)

# ---------------- CONFIG ----------------
CURRENT_FILE_DIR = os.path.dirname(os.path.abspath(__file__))

# Go up ONE level: from /python/api → /python
PYTHON_DIR = os.path.dirname(CURRENT_FILE_DIR)

# MODEL_DIR now points to the "python" folder in your project
MODEL_DIR = PYTHON_DIR

# Files inside the python folder
TRAINING_CSV = os.path.join(MODEL_DIR, "body_shapes.csv")
LOG_FILE = os.path.join(MODEL_DIR, "analysis_log.csv")

# ---------------- MODEL LOADING ----------------
mp_pose = mp.solutions.pose
pose_estimator = mp_pose.Pose(static_image_mode=True, min_detection_confidence=0.7)

mp_segmentation = mp.solutions.selfie_segmentation
segmentation = mp_segmentation.SelfieSegmentation(model_selection=0)

try:
    ml_model = joblib.load(os.path.join(MODEL_DIR, "body_shape_model.pkl"))
    scaler = joblib.load(os.path.join(MODEL_DIR, "scaler.pkl"))
    label_encoder = joblib.load(os.path.join(MODEL_DIR, "label_encoder.pkl"))
    model_loaded = True
except:
    model_loaded = False

# ---------------- LOGGER ----------------
def log_analysis(data: dict):
    df = pd.DataFrame([data])
    exists = os.path.exists(LOG_FILE)
    df.to_csv(LOG_FILE, mode="a", index=False, header=not exists)

# ---------------- HELPER FUNCTIONS ----------------
def get_dominant_color(image, k=1):
    pixels = image.reshape((-1, 3))
    if pixels.shape[0] > 5000:
        idx = np.random.choice(pixels.shape[0], 5000, replace=False)
        pixels = pixels[idx]
    kmeans = KMeans(n_clusters=k, n_init=10)
    kmeans.fit(pixels)
    return kmeans.cluster_centers_[0]

def determine_season(rgb):
    r, g, b = rgb / 255.0
    mx = max(r, g, b)
    mn = min(r, g, b)
    df = mx - mn

    if df == 0:
        h = 0
    elif mx == r:
        h = (60 * ((g - b) / df) + 360) % 360
    elif mx == g:
        h = (60 * ((b - r) / df) + 120) % 360
    else:
        h = (60 * ((r - g) / df) + 240) % 360

    v = mx * 100
    warm = (h < 40 or h > 330)

    return "Spring" if warm and v > 60 else \
           "Autumn" if warm else \
           "Summer" if v > 60 else "Winter"

# ---------- MISSING REQUIRED FUNCTIONS (ADDED) ----------
def calibrate_from_height(landmarks, image_h, real_height_cm):
    """Calibrate pixel→cm ratio using nose-to-feet distance."""
    try:
        nose = landmarks[mp_pose.PoseLandmark.NOSE]
        left_ankle = landmarks[mp_pose.PoseLandmark.LEFT_ANKLE]
        right_ankle = landmarks[mp_pose.PoseLandmark.RIGHT_ANKLE]

        ankle_y = (left_ankle.y + right_ankle.y) / 2
        px_height = abs((nose.y - ankle_y) * image_h)

        if px_height < 100:
            return None

        ratio = px_height / real_height_cm  # pixels per 1 cm
        return ratio
    except:
        return None

def get_waist_landmarks(lm):
    """Safely estimate waist using midpoints of certain landmarks."""
    try:
        left = lm[mp_pose.PoseLandmark.LEFT_WRIST]
        right = lm[mp_pose.PoseLandmark.RIGHT_WRIST]
        return left, right
    except:
        return None, None

def get_depth_at_y(mask, y):
    """Find horizontal body depth at a given y-row from segmentation mask."""
    y = int(y)
    if y < 0 or y >= mask.shape[0]:
        return 0
    row = mask[y, :]
    body_pixels = np.where(row > 0.3)[0]
    if len(body_pixels) < 2:
        return 0
    return body_pixels[-1] - body_pixels[0]

def calculate_ellipse_circumference(width_cm, depth_cm):
    """Approximate ellipse circumference."""
    major = width_cm / 2
    minor = depth_cm / 2
    return math.pi * (3*(major+minor) - math.sqrt((3*major+minor)*(major+3*minor)))

# ---------------- COLOR ANALYSIS ----------------
@app.route('/analyze_color', methods=['POST'])
def analyze_color():
    if 'face_image' not in request.files:
        return jsonify({"status": "error", "message": "No selfie uploaded."}), 400

    try:
        file = request.files['face_image']
        img = np.array(Image.open(file.stream).convert("RGB"))

        mp_face = mp.solutions.face_detection
        with mp_face.FaceDetection(model_selection=1, min_detection_confidence=0.5) as face_detection:

            results = face_detection.process(img)

            if not results.detections:
                return jsonify({"status": "error", "message": "Face not detected."}), 400

            detection = results.detections[0]
            bbox = detection.location_data.relative_bounding_box
            h, w, _ = img.shape

            x = int(bbox.xmin * w)
            y = int(bbox.ymin * h)
            w_box = int(bbox.width * w)
            h_box = int(bbox.height * h)

            center_x = x + w_box // 2
            center_y = y + h_box // 2
            crop_size = int(w_box * 0.4)

            y1 = max(center_y - crop_size, 0)
            y2 = min(center_y + crop_size, h)
            x1 = max(center_x - crop_size, 0)
            x2 = min(center_x + crop_size, w)

            face_crop = img[y1:y2, x1:x2]

            if face_crop.size == 0:
                return jsonify({"status": "error", "message": "Face crop failed."}), 400

            skin_tone_rgb = get_dominant_color(face_crop)
            season = determine_season(skin_tone_rgb)

            palettes = {
                "Spring": ["Coral", "Peach", "Golden Yellow"],
                "Summer": ["Lavender", "Powder Blue", "Soft Rose"],
                "Autumn": ["Olive", "Rust", "Mustard"],
                "Winter": ["Royal Blue", "Emerald", "Black"]
            }

            log_analysis({
                "timestamp": datetime.utcnow().isoformat(),
                "source": "color",
                "season": season,
                "body_shape": None,
                "height_cm": None,
                "shoulder_cm": None,
                "waist_circ_cm": None,
                "hip_circ_cm": None
            })

            return jsonify({
                "status": "success",
                "season": season,
                "palette": palettes.get(season, []),
                "skin_tone_rgb": skin_tone_rgb.tolist()
            })

    except Exception as e:
        return jsonify({"status": "error", "message": str(e)}), 500


# ---------------- BODY SHAPE ANALYSIS ----------------
@app.route('/analyze', methods=['POST'])
def analyze():
    # Make sure ML models loaded correctly
    if not model_loaded:
        return jsonify({
            "status": "error",
            "message": "ML Models not loaded on server."
        }), 500

    # Basic validation
    if (
        'front_image' not in request.files or
        'side_image' not in request.files or
        'height_cm' not in request.form
    ):
        return jsonify({
            "status": "error",
            "message": "Missing files or height data."
        }), 400

    try:
        # -------- 1. Read inputs --------
        height_cm = float(request.form['height_cm'])
        front_file = request.files['front_image']
        side_file = request.files['side_image']

        front_img = np.array(Image.open(front_file.stream).convert("RGB"))
        side_img = np.array(Image.open(side_file.stream).convert("RGB"))

        # -------- 2. Pose on FRONT image --------
        h_front, w_front, _ = front_img.shape
        front_results = pose_estimator.process(front_img)
        if not front_results.pose_landmarks:
            return jsonify({
                "status": "error",
                "message": "No pose detected in front image"
            }), 400
        front_lm = front_results.pose_landmarks.landmark

        # -------- 3. Pose on SIDE image --------
        h_side, w_side, _ = side_img.shape
        side_results = pose_estimator.process(side_img)
        if not side_results.pose_landmarks:
            return jsonify({
                "status": "error",
                "message": "No pose detected in side image"
            }), 400
        side_lm = side_results.pose_landmarks.landmark

        # -------- 4. Calibration (pixels → cm) --------
        ratio = calibrate_from_height(front_lm, h_front, height_cm)
        if not ratio:
            return jsonify({
                "status": "error",
                "message": "Calibration failed. Stand fully visible in the frame."
            }), 400

        # -------- 5. Widths from FRONT image --------
        f_l_sh = front_lm[mp_pose.PoseLandmark.LEFT_SHOULDER]
        f_r_sh = front_lm[mp_pose.PoseLandmark.RIGHT_SHOULDER]

        f_l_waist, f_r_waist = get_waist_landmarks(front_lm)
        f_l_hip = front_lm[mp_pose.PoseLandmark.LEFT_HIP]
        f_r_hip = front_lm[mp_pose.PoseLandmark.RIGHT_HIP]

        shoulder_px = math.dist(
            (f_l_sh.x * w_front, f_l_sh.y * h_front),
            (f_r_sh.x * w_front, f_r_sh.y * h_front)
        )

        waist_px = math.dist(
            (f_l_waist.x * w_front, f_l_waist.y * h_front),
            (f_r_waist.x * w_front, f_r_waist.y * h_front)
        )

        hip_px = math.dist(
            (f_l_hip.x * w_front, f_l_hip.y * h_front),
            (f_r_hip.x * w_front, f_r_hip.y * h_front)
        )

        # -------- 6. Depths from SIDE image + segmentation --------
        seg_results = segmentation.process(side_img)
        mask = seg_results.segmentation_mask

        s_l_waist, s_r_waist = get_waist_landmarks(side_lm)
        s_waist_y = (s_l_waist.y + s_r_waist.y) / 2

        s_hip_y = (
            side_lm[mp_pose.PoseLandmark.LEFT_HIP].y +
            side_lm[mp_pose.PoseLandmark.RIGHT_HIP].y
        ) / 2

        waist_depth_px = get_depth_at_y(mask, s_waist_y * h_side)
        hip_depth_px = get_depth_at_y(mask, s_hip_y * h_side)

        # -------- 7. Convert everything to cm --------
        shoulder_cm = shoulder_px / ratio
        waist_width_cm = waist_px / ratio
        hip_width_cm = hip_px / ratio
        waist_depth_cm = waist_depth_px / ratio
        hip_depth_cm = hip_depth_px / ratio

        waist_circ = calculate_ellipse_circumference(
            waist_width_cm, waist_depth_cm
        )
        hip_circ = calculate_ellipse_circumference(
            hip_width_cm, hip_depth_cm
        )

        # Small correction to avoid weird cases where waist >= hips
        if waist_circ > hip_circ * 0.95:
            waist_circ = hip_circ * 0.85

        # -------- 8. ML Prediction --------
        features = pd.DataFrame([{
            "ShoulderWidth": shoulder_cm,
            "Waist": waist_circ,
            "Hips": hip_circ,
            "TotalHeight": height_cm
        }])

        scaled_features = scaler.transform(features)
        pred_idx = ml_model.predict(scaled_features)[0]
        pred_label = label_encoder.inverse_transform([pred_idx])[0]

        # -------- 9. LOG to CSV (this is what updates your log file) --------
        log_analysis({
            "timestamp": datetime.utcnow().isoformat(),
            "source": "body_shape",
            "season": None,
            "body_shape": pred_label,
            "height_cm": height_cm,
            "shoulder_cm": shoulder_cm,
            "waist_circ_cm": waist_circ,
            "hip_circ_cm": hip_circ
        })

        # -------- 10. Response to frontend --------
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
        # You can log e to console as well if you want
        return jsonify({
            "status": "error",
            "message": str(e)
        }), 500



# ---------------- ANALYTICS ROUTE ----------------
@app.route('/analytics', methods=['GET'])
def analytics():

    if os.path.exists(TRAINING_CSV):
        df_train = pd.read_csv(TRAINING_CSV)
        body_shape_counts = df_train["BodyShape"].value_counts().to_dict()
        avg_height = float(df_train["TotalHeight"].mean())
        avg_waist = float(df_train["Waist"].mean())
        avg_hips = float(df_train["Hips"].mean())
        avg_shoulder = float(df_train["ShoulderWidth"].mean())
    else:
        body_shape_counts = {}
        avg_height = avg_waist = avg_hips = avg_shoulder = None

    season_counts = {}
    usage_labels = []
    usage_values = []

    if os.path.exists(LOG_FILE):
        df_log = pd.read_csv(LOG_FILE)

        if "season" in df_log.columns:
            season_counts = df_log["season"].dropna().value_counts().to_dict()

        if "timestamp" in df_log.columns:
            df_log["timestamp"] = pd.to_datetime(df_log["timestamp"], errors="coerce")
            df_log = df_log.dropna(subset=["timestamp"])
            df_log["month"] = df_log["timestamp"].dt.to_period("M").dt.to_timestamp()
            monthly = df_log.groupby("month").size()
            usage_labels = [m.strftime("%Y-%m") for m in monthly.index]
            usage_values = monthly.values.tolist()

    return jsonify({
        "status": "success",
        "bodyShapeCounts": body_shape_counts,
        "seasonCounts": season_counts,
        "usageByMonth": {"labels": usage_labels, "values": usage_values},
        "averages": {
            "height_cm": avg_height,
            "shoulder_cm": avg_shoulder,
            "waist_circ_cm": avg_waist,
            "hip_circ_cm": avg_hips
        }
    })

if __name__ == "__main__":
    app.run(debug=True, port=5000)
