import cv2
import mediapipe as mp
import numpy as np
import math
from PIL import Image
import joblib
import pandas as pd
import os
import sys
import json
# from analyze_body_shape_module import analyze_body_shape  <-- Removed this, it seemed like an error

# -------------------------
# THIS IS THE FIX: Get the absolute path of the directory this script is in
SCRIPT_DIR = os.path.dirname(os.path.abspath(__file__))
# -------------------------

# -------------------------
# Load ML Models
# -------------------------
def load_prediction_model():
    # Use SCRIPT_DIR to build absolute paths to your model files
    model_path = os.path.join(SCRIPT_DIR, "body_shape_model.pkl")
    scaler_path = os.path.join(SCRIPT_DIR, "scaler.pkl")
    encoder_path = os.path.join(SCRIPT_DIR, "label_encoder.pkl")

    if not (os.path.exists(model_path) and os.path.exists(scaler_path) and os.path.exists(encoder_path)):
        # This error message will now be sent to PHP
        raise FileNotFoundError(f"Model files not found in {SCRIPT_DIR}. Train the model first.")

    model = joblib.load(model_path)
    scaler = joblib.load(scaler_path)
    label_encoder = joblib.load(label_encoder.pkl)

    return {
        "model": model,
        "scaler": scaler,
        "label_encoder": label_encoder,
        "features": ["ShoulderWidth", "Waist", "Hips", "TotalHeight"]
    }

# -------------------------
# Helper Functions (Your code, unchanged)
# -------------------------
def get_pixel_distance(p1, p2):
    return math.dist(p1, p2)

class Point:
    def __init__(self, x, y, z=0, visibility=1):
        self.x = x
        self.y = y
        self.z = z
        self.visibility = visibility

# ... [Your other helper functions: get_waist_landmarks, calibrate_from_height, etc.] ...
# ... [No changes needed to the rest of your Python logic] ...

# --- PASTE ALL YOUR OTHER PYTHON FUNCTIONS HERE ---
# (get_waist_landmarks, calibrate_from_height, 
#  calculate_ellipse_circumference, get_depth_at_y,
#  analyze_3d_measurements, analyze_body_shape)

# Example (pasting in one of your functions for clarity)
def calculate_ellipse_circumference(width_cm, depth_cm):
    if width_cm == 0 or depth_cm == 0:
        return 0
    a = width_cm / 2.0
    b = depth_cm / 2.0
    h = ((a - b) ** 2) / ((a + b) ** 2)
    return math.pi * (a + b) * (1 + (3 * h) / (10 + math.sqrt(4 - 3 * h)))

# (Ensure all your functions are pasted here)

def analyze_3d_measurements(front_rgb, side_rgb, known_height_cm):
    mp_pose = mp.solutions.pose
    pose_model = mp_pose.Pose(static_image_mode=True, min_detection_confidence=0.7)

    mp_seg = mp.solutions.selfie_segmentation
    segmentation_model = mp_seg.SelfieSegmentation(model_selection=0)

    # Process Front Image
    h_front, w_front, _ = front_rgb.shape
    front_results = pose_model.process(front_rgb)
    if not front_results.pose_landmarks:
        return {"status": "error", "message": "No pose detected in front image."}
    front_landmarks = front_results.pose_landmarks.landmark

    # Process Side Image
    h_side, w_side, _ = side_rgb.shape
    side_results = pose_model.process(side_rgb)
    if not side_results.pose_landmarks:
        return {"status": "error", "message": "No pose detected in side image."}
    side_landmarks = side_results.pose_landmarks.landmark

    # Calibration (pixels → cm)
    ratio = calibrate_from_height(front_landmarks, h_front, known_height_cm, mp_pose)
    if ratio is None:
        return {"status": "error", "message": "Calibration failed. Full body needed."}

    # Front widths
    ls = front_landmarks[mp_pose.PoseLandmark.LEFT_SHOULDER]
    rs = front_landmarks[mp_pose.PoseLandmark.RIGHT_SHOULDER]
    lh = front_landmarks[mp_pose.PoseLandmark.LEFT_HIP]
    rh = front_landmarks[mp_pose.PoseLandmark.RIGHT_HIP]

    shoulder_width_px = get_pixel_distance((ls.x * w_front, ls.y * h_front),
                                            (rs.x * w_front, rs.y * h_front))
    hip_width_px = get_pixel_distance((lh.x * w_front, lh.y * h_front),
                                          (rh.x * w_front, rh.y * h_front))

    # Waist from interpolation
    w1, w2 = get_waist_landmarks(front_landmarks, mp_pose)
    waist_width_px = get_pixel_distance((w1.x * w_front, w1.y * h_front),
                                            (w2.x * w_front, w2.y * h_front))

    # Side depth via segmentation
    seg = segmentation_model.process(side_rgb)
    mask = (seg.segmentation_mask > 0.5).astype(np.uint8)

    s_sh_y = (side_landmarks[mp_pose.PoseLandmark.LEFT_SHOULDER].y +
              side_landmarks[mp_pose.PoseLandmark.RIGHT_SHOULDER].y) / 2
    s_hp_y = (side_landmarks[mp_pose.PoseLandmark.LEFT_HIP].y +
              side_landmarks[mp_pose.PoseLandmark.RIGHT_HIP].y) / 2
    s_w1, s_w2 = get_waist_landmarks(side_landmarks, mp_pose)
    s_ws_y = (s_w1.y + s_w2.y) / 2

    shoulder_depth_px = get_depth_at_y(mask, s_sh_y * h_side)[0]
    waist_depth_px = get_depth_at_y(mask, s_ws_y * h_side)[0]
    hip_depth_px = get_depth_at_y(mask, s_hp_y * h_side)[0]

    # Convert px → cm
    shoulder_width_cm = shoulder_width_px / ratio
    waist_width_cm = waist_width_px / ratio
    hip_width_cm = hip_width_px / ratio
    waist_depth_cm = waist_depth_px / ratio
    hip_depth_cm = hip_depth_px / ratio

    # Circumference
    waist_circ_cm = calculate_ellipse_circumference(waist_width_cm, waist_depth_cm)
    hip_circ_cm = calculate_ellipse_circumference(hip_width_cm, hip_depth_cm)

    return {
        "status": "success",
        "measurements": {
            "ShoulderWidth": round(shoulder_width_cm, 1),
            "Waist": round(waist_circ_cm, 1),
            "Hips": round(hip_circ_cm, 1)
        }
    }
    
def analyze_body_shape(front_img_path, side_img_path, height_cm):
    try:
        front_rgb = np.array(Image.open(front_img_path).convert("RGB"))
        side_rgb = np.array(Image.open(side_img_path).convert("RGB"))

        measurement_result = analyze_3d_measurements(front_rgb, side_rgb, height_cm)
        if measurement_result["status"] != "success":
            return measurement_result

        m = measurement_result["measurements"]

        # Load prediction model
        model_package = load_prediction_model()

        df = pd.DataFrame([{
            "ShoulderWidth": m["ShoulderWidth"],
            "Waist": m["Waist"],
            "Hips": m["Hips"],
            "TotalHeight": height_cm
        }])[model_package["features"]]

        scaled = model_package["scaler"].transform(df)
        pred = model_package["model"].predict(scaled)
        prob = model_package["model"].predict_proba(scaled)
        label = model_package["label_encoder"].inverse_transform(pred)[0]
        confidence = prob[0][pred[0]] * 100

        return {
            "status": "success",
            "measurements": m,
            "prediction": {
                "body_shape": label,
                "confidence": round(confidence, 2)
            }
        }
    except Exception as e:
        # Catch any other error and report it
        return {"status": "error", "message": f"Python Exception: {str(e)}"}


# -------------------------
# Example Run
# -------------------------
if __name__ == "__main__":
    # Wrap main execution in a try-except to catch all errors
    try:
        front_img = sys.argv[1]
        side_img  = sys.argv[2]
        height_cm = float(sys.argv[3])

        result = analyze_body_shape(front_img, side_img, height_cm)
        print(json.dumps(result, indent=2))
        
    except Exception as e:
        # Print errors as a JSON object so PHP can parse it
        print(json.dumps({
            "status": "error",
            "message": f"Fatal Python Error: {str(e)}",
            "args": sys.argv[1:] # Show what args were passed
        }, indent=2))