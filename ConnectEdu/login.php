<?php
session_start();

// Paramètres de connexion à la base de données.
$host = '127.0.0.1';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'test';

// Création de la connexion.
$conn = new mysqli($host, $dbUsername, $dbPassword, $dbName);

// Vérification de la connexion.
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Vérifiez que les données POST existent avant de les utiliser.
if (!isset($_POST['userID']) || !isset($_POST['password'])) {
    die("Les données de formulaire ne sont pas envoyées.");
}

// Récupération et nettoyage des données du formulaire.
$userID = $conn->real_escape_string($_POST['userID']);
$password = $_POST['password']; // Pas besoin de nettoyer le mot de passe.

// Requête pour vérifier l'utilisateur.
$sql = "SELECT utilisateur_id, role, password FROM utilisateur WHERE utilisateur_id = ?";

// Préparation de la requête.
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Erreur de préparation de la requête : " . $conn->error);
}

$stmt->bind_param("s", $userID);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // Utilisez `password_verify()` pour vérifier que le mot de passe entré correspond au hachage stocké.
    if (password_verify($password, $row['password'])) {
        // Informations d'utilisateur valides.
        $_SESSION['userID'] = $row['utilisateur_id'];
        $_SESSION['role'] = $row['role'];

        // Redirection en fonction du rôle.
        if ($row['role'] == 'Professeur') {
            header("Location: Prof.php");
            exit();
        } else {
            // Redirection pour d'autres rôles si nécessaire.
            header("Location: prof2.php");
            exit();
        }
    } else {
        // Mot de passe incorrect.
        echo "Identifiant ou mot de passe incorrect.";
    }
} else {
    // Utilisateur non trouvé.
    echo "Identifiant ou mot de passe incorrect.";
}

$stmt->close();
$conn->close();
?>
