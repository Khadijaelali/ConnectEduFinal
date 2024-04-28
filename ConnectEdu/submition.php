<?php
// Vérifiez si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Connexion à la base de données
    $conn = new mysqli("localhost", "root", "", "test");
    if ($conn->connect_error) {
        die("Échec de la connexion : " . $conn->connect_error);
    }

    // Récupération et hachage du mot de passe
    $utilisateur_id = $conn->real_escape_string($_POST['utilisateur_id']);

    $nom = $conn->real_escape_string($_POST['nom']);
    $prenom = $conn->real_escape_string($_POST['prenom']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hachage du mot de passe
    $role = $conn->real_escape_string($_POST['role']);

    // Préparation de la requête
    $stmt = $conn->prepare("INSERT INTO utilisateur (utilisateur_id, nom, prenom, password, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $utilisateur_id, $nom, $prenom, $password, $role);

    // Exécution de la requête
    if ($stmt->execute()) {
        header('Location: logging.html');
    } else {
        echo "Erreur lors de l'inscription: " . $conn->error;
    }

    // Fermeture de la connexion
    $stmt->close();
    $conn->close();
} else {
    echo "Méthode de requête non autorisée.";
}
?>
