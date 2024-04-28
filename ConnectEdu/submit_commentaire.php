<?php
session_start();

header('Content-Type: application/json'); // Nous allons envoyer une réponse JSON

// Établir la connexion à la base de données.
$conn = new mysqli("localhost", "root", "", "test");

// Vérifier la connexion.
if ($conn->connect_error) {
    echo json_encode(['error' => "Échec de la connexion : " . $conn->connect_error]);
    exit();
}

// S'assurer que l'utilisateur est connecté.
if (!isset($_SESSION['userID'])) {
    echo json_encode(['error' => "Utilisateur non connecté"]);
    exit();
}

// Récupération et filtrage des données.
$material_id = filter_input(INPUT_POST, 'material_id', FILTER_VALIDATE_INT);
$commentaire = filter_input(INPUT_POST, 'commentaire', FILTER_SANITIZE_STRING);
$user_id = $_SESSION['userID'];

if ($material_id && $commentaire) {
    // Préparation de la requête pour éviter les injections SQL.
    $stmt = $conn->prepare("INSERT INTO commentaires (parent_id, type, user_id, commentaire) VALUES (?, 'materiel', ?, ?)");
    $stmt->bind_param("iss", $material_id, $user_id, $commentaire);

    if ($stmt->execute()) {
        // Génération du HTML pour le commentaire à afficher.
        $newCommentHtml = "<div class='comment'><p>".htmlspecialchars($commentaire)."</p><p class='text-muted'>Juste maintenant</p></div>";
        echo json_encode(['success' => "Commentaire ajouté avec succès.", 'commentHtml' => $newCommentHtml]);
    } else {
        echo json_encode(['error' => "Erreur lors de l'ajout du commentaire."]);
    }

    $stmt->close();
} else {
    echo json_encode(['error' => "Données de commentaire invalides."]);
}


$conn->close();
?> 