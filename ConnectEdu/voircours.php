<?php
// Assurez-vous de démarrer la session si vous utilisez les sessions
session_start();

// Connexion à la base de données
// Remplacer avec vos paramètres de connexion réels
$db = new PDO('mysql:host=localhost;dbname=test', 'root', '');

// Vérifier si le code du cours a été soumis
if (isset($_GET['code'])) {
    $courseCode = $_GET['code']; 

    // Préparer la requête pour trouver le cours correspondant au code
    $stmt = $db->prepare("SELECT * FROM cours WHERE code_cours = ?");
    $stmt->execute([$courseCode]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifier si le cours a été trouvé
    if ($course) {
        // Récupérer les matériaux pour le cours trouvé
        $materialsStmt = $db->prepare("SELECT * FROM materials WHERE course_id = ?  ORDER BY uploaded_at DESC");
        $materialsStmt->execute([$course['id']]);
        $materials = $materialsStmt->fetchAll(PDO::FETCH_ASSOC);
          // Récupérer les annonces pour le cours trouvé
          $announcementsStmt = $db->prepare("SELECT * FROM annonces WHERE course_id = ?  ORDER BY date_creation DESC");
          $announcementsStmt->execute([$course['id']]);
          $announcements = $announcementsStmt->fetchAll(PDO::FETCH_ASSOC);


          $assignmentsStmt = $db->prepare("SELECT * FROM devoir WHERE cours_id = ? ORDER BY date_limite DESC");
$assignmentsStmt->execute([$course['id']]);
$assignments = $assignmentsStmt->fetchAll(PDO::FETCH_ASSOC);

// Vérifiez si des données sont renvoyées et affichez-les
if ($assignments) {
    

    // Stockez les devoirs dans la variable de session
    $_SESSION['course_devoir'] = $assignments;
} else {
    // Affichez un message d'erreur si aucun devoir n'a été trouvé
    echo "Aucun devoir trouvé pour ce cours.";
    // Vous pouvez choisir de ne pas stocker une variable de session si aucun devoir n'est trouvé
    // Ou de stocker un tableau vide pour indiquer qu'il n'y a pas de devoirs
    $_SESSION['course_devoir'] = [];
}


        // Stocker les matériaux dans la session ou les passer directement à la vue
        $_SESSION['course_materials'] = $materials;
        $_SESSION['course_announcements'] = $announcements;
        $_SESSION['course_devoir'] = $assignments;
        $_SESSION['currentCourseTitle'] = $course['titre'];






        
        // Rediriger vers la page du cours pour l'étudiant
        $_SESSION['course_code'] = $courseCode;
        header('Location: etudiant_cours.php');
        exit();
    } else {
        // Rediriger avec un message d'erreur si le cours n'est pas trouvé
        $_SESSION['error'] = "Code de cours invalide.";
        header('Location: prof2.php');
        exit();
    }
} else {
    // Rediriger avec un message d'erreur si aucun code n'a été soumis
    $_SESSION['error'] = "Aucun code de cours soumis.";
    header('Location: prof2.php');
    exit();
}



?>