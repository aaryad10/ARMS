# importing necessary libraries
import cv2 as cv
import os
import time
import geocoder

def detectPotholeonVideo(filename):
    # Get the directory where this script is located
    current_dir = os.path.dirname(os.path.abspath(__file__))

    # Define paths for the project files
    project_files_path = os.path.join(current_dir, 'project_files')
    obj_names_path = os.path.join(project_files_path, 'obj.names')
    yolov4_weights_path = os.path.join(project_files_path, 'yolov4_tiny.weights')
    yolov4_cfg_path = os.path.join(project_files_path, 'yolov4_tiny.cfg')

    # Ensure necessary files exist
    for path in [obj_names_path, yolov4_weights_path, yolov4_cfg_path]:
        if not os.path.exists(path):
            raise FileNotFoundError(f"Required file missing: {path}")

    # Load the YOLO model
    net = cv.dnn.readNet(yolov4_weights_path, yolov4_cfg_path)
    model = cv.dnn_DetectionModel(net)
    model.setInputParams(size=(640, 480), scale=1 / 255, swapRB=True)

    # Open the video
    cap = cv.VideoCapture(filename)
    if not cap.isOpened():
        raise FileNotFoundError(f"Video file '{filename}' not found or could not be opened")

    width, height = int(cap.get(cv.CAP_PROP_FRAME_WIDTH)), int(cap.get(cv.CAP_PROP_FRAME_HEIGHT))
    output_video_path = os.path.join(current_dir, "PotholeVideoResult.mp4")
    result = cv.VideoWriter(output_video_path, cv.VideoWriter_fourcc(*'MP4V'), 10, (width, height))

    # Result folder for frames and coordinates
    result_path = os.path.join(current_dir, "pothole_results")
    os.makedirs(result_path, exist_ok=True)

    g = geocoder.ip('me')
    frame_counter = 0

    while True:
        ret, frame = cap.read()
        if not ret:
            break

        frame_counter += 1
        classes, scores, boxes = model.detect(frame, confThreshold=0.5, nmsThreshold=0.4)
        for (classid, score, box) in zip(classes, scores, boxes):
            label = "Pothole"
            x, y, w, h = box
            if score >= 0.7:
                cv.rectangle(frame, (x, y), (x + w, y + h), (0, 255, 0), 2)
                cv.putText(frame, f"{round(score * 100, 2)}% {label}", (x, y - 10),
                           cv.FONT_HERSHEY_SIMPLEX, 0.5, (255, 0, 0), 1)

                # Save frame and coordinates
                frame_path = os.path.join(result_path, f"frame_{frame_counter}.jpg")
                coords_path = os.path.join(result_path, f"frame_{frame_counter}.txt")
                cv.imwrite(frame_path, frame)
                with open(coords_path, 'w') as f:
                    f.write(str(g.latlng))

        # Save processed frame to video
        result.write(frame)

    cap.release()
    result.release()
    print(f"Video processing complete. Output saved at: {output_video_path}")
    print(f"Details saved in: {result_path}")
