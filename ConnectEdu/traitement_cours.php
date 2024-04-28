<?php
session_start();

// Assurez-vous que l'utilisateur est bien connecté et a un ID valide.
if (!isset($_SESSION['userID']) || empty($_SESSION['userID'])) {
    die("Erreur : Vous devez être connecté pour créer un cours.");
}

$professeurId = $_SESSION['userID'];

// Établir la connexion à la base de données.
$conn = new mysqli("localhost", "root", "", "test");

// Vérifier la connexion.
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Fonction pour générer un code de cours aléatoire.
function genererCodeCours($length = 10) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

$titre = $_POST['courseName'];
$section = $_POST['section'];
$sujet = $_POST['subject'];
$salle = $_POST['room'];
$codeCours = genererCodeCours(); // Générer un code aléatoire pour le cours.

$stmt = $conn->prepare("INSERT INTO cours (titre, section, sujet, salle, professeur_id, code_cours) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $titre, $section, $sujet, $salle, $professeurId, $codeCours);

if ($stmt->execute()) {
    // Récupérer l'ID du cours inséré
    $last_id = $conn->insert_id;

    // Rediriger vers la page de détail du cours
    header('Location: course.php?courseID=' . $last_id);
    exit();
} else {
    echo "Erreur lors de l'insertion du cours : " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
