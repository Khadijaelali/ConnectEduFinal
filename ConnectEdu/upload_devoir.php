<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['devoirFile']) && $_FILES['devoirFile']['error'] == 0) {
    $devoirId = $_POST['devoir_id'];
    //$courseId = $_POST['course_id'];
    $file = $_FILES['devoirFile'];

    $uploadDir = 'uploads_Dev/';
    $fileName = time() . '_' . basename($file['name']); // Ajouter un préfixe pour éviter les conflits de nom
    $uploadFilePath = $uploadDir . $fileName;

    // Assurez-vous que le répertoire existe et est accessible en écriture
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Déplacez le fichier du dossier temporaire vers le dossier de destination
    if (move_uploaded_file($file['tmp_name'], $uploadFilePath)) {
        echo "Le fichier a été téléchargé avec succès.";
        // Enregistrez ici le chemin du fichier dans votre base de données
    } else {
        echo "Erreur lors du téléchargement du fichier.";
    }
} else {
    echo "Aucun fichier soumis ou erreur lors de l'envoi.";
}
?>
