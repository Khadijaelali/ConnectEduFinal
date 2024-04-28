<?php
session_start();
if (!isset($_SESSION['userID'])) {
    exit('Utilisateur non authentifié');
}

if (isset($_POST['id'])) {
    $annonceId = intval($_POST['id']);
    $conn = new mysqli("localhost", "root", "", "test");

    if ($conn->connect_error) {
        die("Échec de la connexion : " . $conn->connect_error);
    }

    $stmt = $conn->prepare("DELETE FROM annonces WHERE id = ?");
    $stmt->bind_param("i", $annonceId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Annonce supprimée avec succès";
    } else {
        echo "Erreur lors de la suppression de l'annonce";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "ID d'annonce non fourni";
}
?>
