<?php
session_start();
if (!isset($_SESSION['userID'])) {
    header('Location: login.php');
    exit();
}

$conn = new mysqli("localhost", "root", "", "test");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$professeurId = $_SESSION['userID'];
$courseID = $_POST['courseID'];
$uploadDir = "uploads/"; // Ensure this directory exists and is writable
$description = $_POST['announcement'] ?? ''; // Text from the announcement field
$allowedFileTypes = ['pdf', 'jpeg', 'jpg', 'png'];

$uploadSuccess = false; // Flag to track if any file was successfully uploaded

// Check if there are files being uploaded
if (!empty($_FILES['fileToUpload']['name'][0])) {
    // Loop through each file uploaded
    foreach ($_FILES['fileToUpload']['name'] as $i => $name) {
        if ($_FILES['fileToUpload']['error'][$i] === UPLOAD_ERR_OK) {
            $fileName = basename($_FILES["fileToUpload"]["name"][$i]);
            $fileTmpName = $_FILES["fileToUpload"]["tmp_name"][$i];
            $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            // Check if file type is allowed
            if (in_array($fileType, $allowedFileTypes)) {
                $newFileName = md5(time() . $fileName) . ".$fileType";
                $uploadFilePath = $uploadDir . $newFileName;

                // Move the file to the upload directory
                if (move_uploaded_file($fileTmpName, $uploadFilePath)) {
                    $stmt = $conn->prepare("INSERT INTO materials (course_id, file_name, file_path, description, file_type) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("issss", $courseID, $fileName, $uploadFilePath, $description, $fileType);
                    $stmt->execute();

                    if ($stmt->affected_rows > 0) {
                        $uploadSuccess = true; // File uploaded successfully
                    }
                    $stmt->close();
                }
            }
        }
    }
}

// If an announcement was provided without a file, insert it into the database as an announcement
if (!$uploadSuccess && !empty($description)) {
    $stmt = $conn->prepare("INSERT INTO annonces (course_id, message) VALUES (?, ?)");
    $stmt->bind_param("is", $courseID, $description);
    $stmt->execute();
    $stmt->close();
}

// Redirect to the course page or output an error message
if ($uploadSuccess || !empty($description)) {
    header('Location: course.php?courseID=' . $courseID);
    exit();
} else {
    echo "No files uploaded and no announcement submitted.<br>";
}

$conn->close();
?>