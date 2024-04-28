<?php
session_start();

header('Content-Type: application/json');

// Database connection
$conn = new mysqli("localhost", "root", "", "test");
if ($conn->connect_error) {
    echo json_encode(['error' => "Connection failed: " . $conn->connect_error]);
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['userID'])) {
    echo json_encode(['error' => "User not logged in"]);
    exit();
}

// Get and sanitize input
$annonce_id = filter_input(INPUT_POST, 'annonce_id', FILTER_VALIDATE_INT);
$commentaire = filter_input(INPUT_POST, 'commentaire', FILTER_SANITIZE_STRING);
$user_id = $_SESSION['userID'];  // assuming you've stored user ID in session

// Check if all data is present
if ($annonce_id && $commentaire) {
    // Insert the comment
    $stmt = $conn->prepare("INSERT INTO annonce_comments (annonce_id, utilisateur_id, commentaire, date_creation) VALUES (?, ?, ?, NOW())");
    
    $stmt->bind_param("iss", $annonce_id, $user_id, $commentaire);

    if ($stmt->execute()) {
        // Fetch the username from the database
        $userQuery = $conn->prepare("SELECT nom, prenom FROM utilisateur WHERE id = ?");
        $userQuery->bind_param("i", $user_id);
        $userQuery->execute();
        $userResult = $userQuery->get_result();
        if ($userRow = $userResult->fetch_assoc()) {
            // Construct the new comment HTML with the username
            $userName = htmlspecialchars($userRow['prenom'] . " " . $userRow['nom']);
            $newCommentHtml = "
                <div class='comment'>
                    <p><strong>" . $userName . "</strong>: " . htmlspecialchars($commentaire) . "</p>
                    <p class='text-muted'>Just now</p>
                </div>";
            echo json_encode(['success' => true, 'commentHtml' => $newCommentHtml]);
        } else {
            echo json_encode(['error' => "Unable to retrieve user information"]);
        }
        $userQuery->close();
    } else {
        echo json_encode(['error' => "Error posting comment: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['error' => "Invalid input"]);
}

$conn->close();
?>