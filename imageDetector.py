# importing necessary libraries
import cv2
import os

def detectPotholeonImage(filename):
    # Get the directory where this script is located
    current_dir = os.path.dirname(os.path.abspath(__file__))

    # Define the path to the 'project_files' directory
    project_files_path = os.path.join(current_dir, 'project_files')

    # Ensure the 'project_files' directory exists
    if not os.path.exists(project_files_path):
        raise FileNotFoundError(f"'project_files' directory not found in {current_dir}")

    # Define the paths for the 'obj.names', 'yolov4_tiny.weights', and 'yolov4_tiny.cfg' files
    obj_names_path = os.path.join(project_files_path, 'obj.names')
    yolov4_weights_path = os.path.join(project_files_path, 'yolov4_tiny.weights')
    yolov4_cfg_path = os.path.join(project_files_path, 'yolov4_tiny.cfg')

    # Ensure all necessary files exist
    for path in [obj_names_path, yolov4_weights_path, yolov4_cfg_path]:
        if not os.path.exists(path):
            raise FileNotFoundError(f"Required file missing: {path}")

    # Load the test image
    img = cv2.imread(filename)
    if img is None:
        raise FileNotFoundError(f"Image file '{filename}' not found or could not be opened")

    # Read class labels from obj.names
    with open(obj_names_path, 'r') as f:
        classes = f.read().splitlines()

    # Load YOLO model
    net = cv2.dnn.readNet(yolov4_weights_path, yolov4_cfg_path)
    model = cv2.dnn_DetectionModel(net)
    model.setInputParams(scale=1 / 255, size=(416, 416), swapRB=True)

    # Detect objects
    classIds, scores, boxes = model.detect(img, confThreshold=0.6, nmsThreshold=0.4)

    # Draw detections on the image
    for (classId, score, box) in zip(classIds, scores, boxes):
        cv2.rectangle(img, (box[0], box[1]), (box[0] + box[2], box[1] + box[3]), color=(0, 255, 0), thickness=2)
        cv2.putText(img, f"{round(score * 100, 2)}% Pothole", (box[0], box[1] - 10),
                    cv2.FONT_HERSHEY_COMPLEX, 0.5, (255, 0, 0), 1)

    # Save the result
    output_path = os.path.join(current_dir, "PotholeDetectedImage.jpg")
    cv2.imwrite(output_path, img)
    print(f"Detection complete. Result saved at: {output_path}")
