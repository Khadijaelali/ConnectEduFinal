<?php
session_start();

// Connexion à la base de données
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'test';
$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Vérification de la connexion et des permissions
if (!isset($_SESSION['userID'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_POST['devoir_id'])) {
    // Gérer l'erreur si devoir_id n'est pas défini
    die('Devoir non spécifié.');
}

$devoirId = $_POST['devoir_id'];
$etudiantId = $_SESSION['userID']; // Assurez-vous que l'ID de l'utilisateur est stocké dans la session lors de la connexion

// Vérifiez si le fichier a été téléchargé sans erreur
if (isset($_FILES['devoirFile']) && $_FILES['devoirFile']['error'] == 0) {
    
    $fileTmpPath = $_FILES['devoirFile']['tmp_name'];
    $fileName = $_FILES['devoirFile']['name'];
    $fileSize = $_FILES['devoirFile']['size'];
    $fileType = $_FILES['devoirFile']['type'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));
    
    // Renommez le fichier téléchargé
    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
    
    // Vérifier l'extension du fichier et la taille ici
    // ...

    // Définissez le chemin où le fichier sera enregistré sur le serveur
    $uploadFileDir = './uploads_Dev/';
    $dest_path = $uploadFileDir . $newFileName;

    if(move_uploaded_file($fileTmpPath, $dest_path)) 
    {
        $sql = "INSERT INTO soumissions_devoir (utilisateur_id, devoir_id, fichier_chemin) VALUES (?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sis", $etudiantId, $devoirId, $dest_path);
        
        if ($stmt->execute()) {
            header("Location: etudiant_cours.php?code=" . urlencode($_SESSION['current_course_code']));
        } else {
            echo 'Erreur lors de l\'enregistrement de la soumission.';
        }
    }
    else 
    {
        echo 'Il y a eu une erreur lors du téléchargement du fichier. Veuillez réessayer.';
    }
} else {
    // Gérer l'erreur de téléchargement
    echo 'Erreur de téléchargement du fichier. Code d\'erreur: ' . $_FILES['devoirFile']['error'];
}

$conn->close();
?>
