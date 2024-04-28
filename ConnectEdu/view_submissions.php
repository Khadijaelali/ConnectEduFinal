<?php
session_start();
// Inclure le fichier de connexion à la base de données ici
require 'db-connect.php';
$db = new PDO('mysql:host=localhost;dbname=test', 'root', '');

// Vérifiez si l'utilisateur est un professeur et est connecté ici
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'Professeur') {
    header('Location: login.php');
    exit();
}

$professeurId = $_SESSION['userID'];
// Sidebar courses
$sidebarHTML = '<div class="sidebar"><a href="prof.php"><i class="fas fa-home"></i> Accueil</a>';
$sqlSidebar = "SELECT id, titre FROM cours WHERE professeur_id = ?";
$stmtSidebar = $conn->prepare($sqlSidebar);
$stmtSidebar->bind_param("s", $professeurId);
$stmtSidebar->execute();
$resultSidebar = $stmtSidebar->get_result();

while($row = $resultSidebar->fetch_assoc()) {
    $sidebarHTML .= '<a href="course.php?courseID='.$row['id'].'">'.htmlspecialchars($row['titre']).'</a>';
}

$sidebarHTML .= '<a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></div>';

if (isset($_GET['devoir_id'])) {
    $devoirId = $_GET['devoir_id'];

    // Récupérer les informations du devoir
    $stmtDevoir = $db->prepare("SELECT * FROM devoir WHERE id = ?");
    $stmtDevoir->execute([$devoirId]);
    $devoir = $stmtDevoir->fetch(PDO::FETCH_ASSOC);

    // Récupérer les soumissions des étudiants pour ce devoir
    $stmtSoumissions = $db->prepare("
        SELECT soumissions_devoir.*, utilisateur.nom, utilisateur.prenom
        FROM soumissions_devoir
        JOIN utilisateur ON soumissions_devoir.utilisateur_id = utilisateur.utilisateur_id
        WHERE devoir_id = ?
    ");
    $stmtSoumissions->execute([$devoirId]);
    $soumissions = $stmtSoumissions->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Soumissions des Devoirs</title>
    <link rel="stylesheet" href="path_to_your_stylesheet.css"> <!-- Remplacez avec le chemin de votre CSS -->
    <style>
        html, body {
            height: 100%; 
            margin: 0;
            padding: 0;
            padding-top: 50px;
        }
        

        .container-fluid {
            min-height: calc(50vh - 50px); 
        }

        
        .sidebar {
            width: 250px; /* Sidebar width */
            position: fixed; 
            top: 0; /* Stay at the top */
            left: 0;
            height: 100vh; /* Full-height */
            padding-top: 100px; /* Place content below the top navigation */
            background-color: #f8f9fa; /* Sidebar background color */
        }
        /* Sidebar links */
        .sidebar a {
            padding: 10px 15px; /* Padding for sidebar links */
            text-decoration: none; /* No underlines on links */
            font-size: 1.1em; /* Increase font size */
            color: #333; /* Link color */
            display: block; /* Make the links appear below each other */
        }

        .sidebar a:hover {
            background-color: #ddd; /* Link hover color */
            border-radius: 5px; /* Rounded corners on hover */
        }
        .container {
    width: 80%;
    margin: auto;
    padding-top: 2rem;
}

.submission-card {
    background-color: #f9f9f9;
    border: 1px solid #e1e1e1;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.submission-card h1 {
    color: black;
    margin-bottom: 20px;
}

.submission-card a {
    color: #007bff;
    text-decoration: none;
    background-color: #f8f9fa;
    border-radius: 5px;
    padding: 5px 10px;
    border: 1px solid #e1e1e1;
    transition: background-color 0.3s ease;
}

.submission-card a:hover {
    background-color: #e1e1e1;
}

#submissionTable {
    width: 100%;
    border-collapse: collapse;
}

#submissionTable th, #submissionTable td {
    padding: 10px;
    border: 1px solid #ddd;
    text-align: left;
}

#submissionTable th {
    background-color: #06BBCC;
    color: white;
}


               



        /* Add Animation */
        @-webkit-keyframes animatetop {
            from {top:-300px; opacity:0} 
            to {top:0; opacity:1}
        }

        @keyframes animatetop {
            from {top:-300px; opacity:0}
            to {top:0; opacity:1}
        }

        /* The Close Button */
        


/* Application du style aux boutons de navigation */
.navigation-buttons button {
    padding: 10px 20px;
    margin-left: 5px;
    font-size: 16px;
    cursor: pointer;
    border: none;
    border-radius: 5px;
    background-color: #06BBCC;
    color: white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    transition: background-color 0.3s;
    margin-bottom: 70px;
}

.navigation-buttons button:hover {
    background-color: #5c6bc0;
}





    



       
        

       
       

        /* Add Animation */
        @-webkit-keyframes animatetop {
            from {top:-300px; opacity:0} 
            to {top:0; opacity:1}
        }

        @keyframes animatetop {
            from {top:-300px; opacity:0}
            to {top:0; opacity:1}
        }

        

/* Responsive adjustments */
@media (max-width: 768px) {
    .modal-content {
        width: 90%;
        margin-top: 20%; /* Adjust for smaller screens */
        margin-bottom: 20%;
    }
}


    </style>
     <!-- Google Web Fonts -->
     <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar Start -->
 <div id="file-preview-container" style="display:none;"></div>
 <nav class="navbar navbar-expand-lg bg-white navbar-light shadow fixed-top p-0">
        <a href="Prof.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
        <?php if (isset($professeurId)): ?>
            <h2 class="m-0 text-primary"><i class="fa fa-chalkboard-teacher me-3"></i>ConnectEdu Professeur:<?= htmlspecialchars($professeurId); ?></h2>    
        <?php endif; ?>
        </a>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
           
                
            </div> 
        </div>
    </nav>
    <!-- Navbar End -->
    <?php echo $sidebarHTML; ?>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">

<div class="container submission-card">
    <?php if (!empty($devoir)): ?>
        <h1>Soumissions pour : <?= htmlspecialchars($devoir['titre']) ?></h1>
        <?php if (!empty($soumissions)): ?>
            <table id="submissionTable" class="display">
                <thead>
                    <tr>
                        <th>Étudiant</th>
                        <th>Fichier de devoir</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($soumissions as $soumission): ?>
                        <tr>
                            <td><?= htmlspecialchars($soumission['nom']) . ' ' . htmlspecialchars($soumission['prenom']) ?></td>
                            <td><a href="<?= htmlspecialchars($soumission['fichier_chemin']) ?>" download><?= basename($soumission['fichier_chemin']) ?></a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucune soumission pour ce devoir.</p>
        <?php endif; ?>
    <?php else: ?>
        <p>Le devoir spécifié n'existe pas.</p>
    <?php endif; ?>
</div>

<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>

<script>
$(document).ready(function() {
    $('#submissionTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
        }
    });
});
</script>

</body>
</html>
