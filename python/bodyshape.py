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

SCRIPT_DIR = os.path.dirname(os.path.abspath(__file__))

def load_prediction_model():
    model_path = os.path.join(SCRIPT_DIR, "body_shape_model.pkl")
    scaler_path = os.path.join(SCRIPT_DIR, "scaler.pkl")
    encoder_path = os.path.join(SCRIPT_DIR, "label_encoder.pkl")

    if not (os.path.exists(model_path) and os.path.exists(scaler_path) and os.path.exists(encoder_path)):
        raise FileNotFoundError("Model files missing. Train the model first.")

    model = joblib.load(model_path)
    scaler = joblib.load(scaler_path)
    label_encoder = joblib.load(encoder_path)

    return {
        "model": model,
        "scaler": scaler,
        "label_encoder": label_encoder,
        "features": ["ShoulderWidth", "Waist", "Hips", "TotalHeight"]
    }

def get_pixel_distance(p1, p2):
    return math.dist(p1, p2)

class Point:
    def __init__(self, x, y, z=0, visibility=1):
        self.x = x
        self.y = y
        self.z = z
        self.visibility = visibility

# -------------------------------
# REQUIRED MISSING FUNCTIONS
# -------------------------------

def calibrate_from_height(landmarks, img_height, known_height_cm, mp_pose):
    top = landmarks[mp_pose.PoseLandmark.NOSE].y * img_height
    left_heel = landmarks[mp_pose.PoseLandmark.LEFT_HEEL].y * img_height
    right_heel = landmarks[mp_pose.PoseLandmark.RIGHT_HEEL].y * img_height
    bottom = max(left_heel, right_heel)
    pixel_height = bottom - top

    if pixel_height <= 0:
        return None

    return pixel_height / known_height_cm

def get_waist_landmarks(landmarks, mp_pose):
    lh = landmarks[mp_pose.PoseLandmark.LEFT_HIP]
    rh = landmarks[mp_pose.PoseLandmark.RIGHT_HIP]
    ls = landmarks[mp_pose.PoseLandmark.LEFT_SHOULDER]
    rs = landmarks[mp_pose.PoseLandmark.RIGHT_SHOULDER]

    waist_y = (lh.y + rh.y + ls.y + rs.y) / 4
    mid_x = (lh.x + rh.x) / 2

    return Point(mid_x - 0.07, waist_y), Point(mid_x + 0.07, waist_y)

def get_depth_at_y(mask, y):
    y = int(y)
    if y < 0 or y >= mask.shape[0]:
        return (0, 0)

    row = mask[y, :]
    xs = np.where(row == 1)[0]

    if len(xs) < 2:
        return (0, 0)

    return (xs[-1] - xs[0], xs[0])

# -------------------------------
# OTHER REQUIRED FUNCTIONS
# -------------------------------

def calculate_ellipse_circumference(width_cm, depth_cm):
    if width_cm == 0 or depth_cm == 0:
        return 0
    a = width_cm / 2
    b = depth_cm / 2
    h = ((a - b) ** 2) / ((a + b) ** 2)
    return math.pi * (a + b) * (1 + (3 * h) / (10 + math.sqrt(4 - 3 * h)))

def analyze_3d_measurements(front_rgb, side_rgb, known_height_cm):
    mp_pose = mp.solutions.pose
    pose_model = mp_pose.Pose(static_image_mode=True, min_detection_confidence=0.7)

    mp_seg = mp.solutions.selfie_segmentation
    segmentation_model = mp_seg.SelfieSegmentation(model_selection=0)

    h_front, w_front, _ = front_rgb.shape
    front_results = pose_model.process(front_rgb)
    if not front_results.pose_landmarks:
        return {"status": "error", "message": "No pose detected in front image."}
    front_landmarks = front_results.pose_landmarks.landmark

    h_side, w_side, _ = side_rgb.shape
    side_results = pose_model.process(side_rgb)
    if not side_results.pose_landmarks:
        return {"status": "error", "message": "No pose detected in side image."}
    side_landmarks = side_results.pose_landmarks.landmark

    ratio = calibrate_from_height(front_landmarks, h_front, known_height_cm, mp_pose)
    if ratio is None:
        return {"status": "error", "message": "Calibration failed."}

    ls = front_landmarks[mp_pose.PoseLandmark.LEFT_SHOULDER]
    rs = front_landmarks[mp_pose.PoseLandmark.RIGHT_SHOULDER]
    lh = front_landmarks[mp_pose.PoseLandmark.LEFT_HIP]
    rh = front_landmarks[mp_pose.PoseLandmark.RIGHT_HIP]

    shoulder_width_px = get_pixel_distance((ls.x * w_front, ls.y * h_front),
                                           (rs.x * w_front, rs.y * h_front))
    hip_width_px = get_pixel_distance((lh.x * w_front, lh.y * h_front),
                                      (rh.x * w_front, rh.y * h_front))

    w1, w2 = get_waist_landmarks(front_landmarks, mp_pose)
    waist_width_px = get_pixel_distance((w1.x * w_front, w1.y * h_front),
                                        (w2.x * w_front, w2.y * h_front))

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

    shoulder_width_cm = shoulder_width_px / ratio
    waist_width_cm = waist_width_px / ratio
    hip_width_cm = hip_width_px / ratio
    waist_depth_cm = waist_depth_px / ratio
    hip_depth_cm = hip_depth_px / ratio

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
        return {"status": "error", "message": f"Python Exception: {str(e)}"}

if __name__ == "__main__":
    try:
        front_img = sys.argv[1]
        side_img = sys.argv[2]
        height_cm = float(sys.argv[3])
        result = analyze_body_shape(front_img, side_img, height_cm)
        print(json.dumps(result, indent=2))
    except Exception as e:
        print(json.dumps({
            "status": "error",
            "message": f"Fatal Python Error: {str(e)}",
            "args": sys.argv[1:]
        }, indent=2))
