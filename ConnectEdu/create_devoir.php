<?php
session_start();

// Vérifiez que l'utilisateur est bien connecté
if (!isset($_SESSION['userID'])) {
    header('Location: login.php');
    exit;
}
$professeurId = $_SESSION['userID'];

// Établir la connexion à la base de données
$conn = new mysqli("localhost", "root", "", "test");

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Récupérer les données du formulaire
$titre = $_POST['assignmentTitle'];
$description = $_POST['assignmentDescription'];
$dateLimite = $_POST['assignmentDeadline'];
$coursId = $_POST['courseID'];

// Gestion du fichier téléchargé
$uploadDir = "uploads_Dev/";
$fichierPath = '';

// Vérifiez s'il y a un téléchargement de fichier et aucune erreur
if (isset($_FILES['assignmentFile']) && $_FILES['assignmentFile']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['assignmentFile']['tmp_name'];
    $fileName = $_FILES['assignmentFile']['name'];
    $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Générer un nom de fichier unique
    $newFileName = md5(time() . $fileName) . '.' . $fileType;
    $fichierPath = $uploadDir . $newFileName;

    // Déplacer le fichier vers le répertoire de téléchargement
    if (move_uploaded_file($fileTmpPath, $fichierPath)) {
        error_log("Fichier téléchargé avec succès : " . $fichierPath);
    } else {
        error_log("Erreur lors du déplacement du fichier.");
    }
}

// Insérer les informations du devoir et du fichier dans la base de données
$stmt = $conn->prepare("INSERT INTO devoir (titre, description, date_limite, cours_id, professeur_id, fichier_chemin) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssiss", $titre, $description, $dateLimite, $coursId, $professeurId, $fichierPath);


$stmt->execute();
    //$last_id = $conn->insert_id;
    header('Location: Prof.php' );
    //exit();
//} else {
    //echo "Erreur lors de la création du devoir : " . $stmt->error;
//}

$stmt->close();
$conn->close();
?>
