<?php
session_start();
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0");

if (!isset($_SESSION['userID'])) {
    header('Location: login.php');
    exit();
}



$professeurId = $_SESSION['userID'];
$conn = new mysqli("localhost", "root", "", "test");
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

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

// Course details
if (isset($_GET['courseID'])) {
    $courseID = $_GET['courseID'];
    // Modifier la requête pour inclure la vérification du créateur du cours
    $sqlCourse = "SELECT * FROM cours WHERE id = ? AND professeur_id = ?";
    $stmtCourse = $conn->prepare($sqlCourse);
    // Utiliser $_SESSION['userID'] pour vérifier le créateur du cours
    $stmtCourse->bind_param("is", $courseID, $_SESSION['userID']);
    $stmtCourse->execute();
    $resultCourse = $stmtCourse->get_result();
    
    if ($resultCourse->num_rows > 0) {
        $course = $resultCourse->fetch_assoc();
        // Continuez avec l'affichage des détails du cours ou d'autres traitements
    } else {
        header('Location: index.html');
        exit();
    }
} else {
    echo "L'ID du cours est requis.";
    exit();
}


// Materials for the course
$sqlMaterials = "SELECT id_materiel, file_name, file_path, description, uploaded_at FROM materials WHERE course_id = ? ORDER BY uploaded_at DESC";
$stmtMaterials = $conn->prepare($sqlMaterials);
$stmtMaterials->bind_param("i", $courseID);
$stmtMaterials->execute();
$resultMaterials = $stmtMaterials->get_result();
$materials = [];

while($material = $resultMaterials->fetch_assoc()) {
    $materials[] = $material;
}
// Assurez-vous que $courseID est défini et correspond à l'ID du cours actuel.
// ...

// Devoirs pour le cours
$sqlDevoirs = "SELECT id, titre, description, date_limite, cours_id, professeur_id, fichier_chemin FROM devoir WHERE cours_id = ? ORDER BY date_limite DESC";
$stmtDevoirs = $conn->prepare($sqlDevoirs);

// Vérifiez que le $stmtDevoirs est bien un objet pour éviter les erreurs
if (!$stmtDevoirs) {
    die("Erreur de préparation de la requête : " . $conn->error);
}

$stmtDevoirs->bind_param("i", $courseID);
$stmtDevoirs->execute();
$resultDevoirs = $stmtDevoirs->get_result();
$devoirs = [];

while ($devoir = $resultDevoirs->fetch_assoc()) {
    $devoirs[] = $devoir;
}

$stmtDevoirs->close();


// Annonces pour le cours
$annonces = [];
$sqlAnnonces = "SELECT id, message, date_creation FROM annonces WHERE course_id = ? ORDER BY date_creation DESC";
;
$stmtAnnonces = $conn->prepare($sqlAnnonces);
$stmtAnnonces->bind_param("i", $courseID);
$stmtAnnonces->execute();
$resultAnnonces = $stmtAnnonces->get_result();

while($annonce = $resultAnnonces->fetch_assoc()) {
    $annonces[] = $annonce;
}

$stmtAnnonces->close();

$stmtMaterials->close();
$stmtSidebar->close();
$stmtCourse->close();

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($cours['titre']); ?></title>
    <!-- Styles and scripts here -->
    <style>
        html, body {
            height: 100%; 
            margin: 0;
            padding: 0;
            padding-top: 50px;
        }
        .announcement-section {
            background: #ffffff;
            border-radius: 4px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        .announcement-header {
            margin-bottom: 20px;
        }
        .icon-button {
            cursor: pointer;
            margin-right: 10px;
        }
        .icon-button:last-child {
            margin-right: 0;
        }
        .announcement-textarea {
            border: 1px solid #e1e1e1;
            border-radius: 4px;
            resize: none;
        }
        .file-input {
            display: none;
        }
        .upload-button {
            margin-right: 10px;
        }
        .announcement-buttons {
            text-align: right;
            margin-top: 10px;
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
        .course-code {
    font-weight: bold;
    padding-left: 5px; /* Ajoute un peu d'espace après le titre */
    font-size: 0.9em; /* Si vous voulez que le code du cours soit un peu plus petit que le titre */
}       


.annonce {
    background-color: #fff;
    border-left: 5px solid #007bff; /* Une bordure sur le côté pour un effet visuel */
    margin-top: 20px;
    padding: 15px;
    border-radius: 4px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    position: relative;
}
.announcement-sectionBtn {
    display: none; /* Cache la section des annonces par défaut */
}

.annonce p {
    margin: 0;
    color: #333;
}

.annonce .text-muted {
    font-size: 0.8em;
    margin-top: 10px;
}

/* Un bouton pour éventuellement cacher les annonces individuelles */
.annonce .hide-annonce-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    border: none;
    background: transparent;
    cursor: pointer;
    color: #007bff;
}
.material-card {
    margin-bottom: 15px; /* Ajoutez de l'espace en bas de chaque carte pour la séparer de la suivante */
    margin-top: 30px;
}
.material-section {
    display: none; /* Cacher la section par défaut */
}

.comment-form {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 15px;
    }
    .comment-content {
        width: 100%;
        margin-right: 10px; /* Espace entre le textarea et le bouton */
    }
    .comment {
        border: 1px solid #dedede;
        padding: 10px;
        margin-top: 10px;
        border-radius: 5px;
        background-color: #f8f8f8;
    }
    .comment p {
        margin: 0;
        padding: 3px 0;
    }
    .comment .comment-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #e9e9e9;
        padding: 5px 10px;
        border-radius: 5px 5px 0 0;
        border-bottom: 1px solid #dedede;
    }
    .comment .comment-info .text-muted {
        margin-left: 10px;
    }
    .comments-container {
        margin-bottom: 30px; /* Add this line to push the comments container down */
    }
    .material-image {
  max-width: 100%; /* This makes image responsive to its container */
  max-height: 500px; /* This sets the maximum height */
  display: block; 
  margin: 0 auto; 
}
.assignment-section {
    background-color: #ffffff; /* Couleur de fond légère pour la section */
    padding: 20px; /* Espacement à l'intérieur de la section */
    border-radius: 5px; /* Bords arrondis */
    box-shadow: 0 2px 4px rgba(0,0,0,0.1); /* Ombre subtile pour la profondeur */
}
.modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
        }

        /* Modal Content Box */
        .modal-content {
            position: relative;
            background-color: #fefefe;
            margin: 10% auto; /* 10% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 80%; /* Could be more or less, depending on screen size */
            box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);
            -webkit-animation-name: animatetop;
            -webkit-animation-duration: 0.4s;
            animation-name: animatetop;
            animation-duration: 0.4s
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
        .close {
            color: #aaaaaa;
            float: right;
            font-size: 10px;
            font-weight: bold;
            line-height: 0.5;
        }

        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }
        .close span {
    font-size: 20px; /* Réduisez la taille de la police de l'icône */
}
        .modal-content {
    background-color: #fefefe;
    margin: 10% auto; /* Keeps it centered vertically and horizontally */
    padding: 20px;
    border: 1px solid #888;
    border-radius: 10px; /* Rounded corners */
    width: auto; /* Adjust width to fit content */
    max-width: 600px; /* Maximum width of the modal - adjust as necessary */
    box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);
    animation-name: animatetop;
    animation-duration: 0.4s;
    overflow: hidden; /* Ensures the content does not spill out */
}

.modal-content h2 {
    color: #333;
    margin-bottom: 1em;
}

.modal-content label {
    display: block;
    margin-bottom: .5em;
    color: #666;
    font-size: 1em;
}

.modal-content input[type="text"] {
    width: calc(100% - 20px);
    padding: 10px;
    margin-bottom: 1em;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.modal-content button {
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    background-color: #06BBCC; /* Example primary color */
    color: white;
    font-size: 1em;
    cursor: pointer;
    transition: background-color 0.3s;
}

.modal-content button:hover {
    background-color: #5c6bc0; /* A darker shade for hover effect */
}

.modal-content .close {
    position: absolute;
    top: 10px;
    right: 20px;
    font-size: 2em;
    color: #999;
}
/* Style général des boutons */
.button-style {
    padding: 10px 20px;
    margin-left: 5px; /* Espacement entre les boutons */
    font-size: 1em; /* Taille de la police ajustée pour l'accessibilité */
    cursor: pointer; /* Curseur en forme de pointeur pour indiquer l'interactivité */
    border: none; /* Pas de bordure par défaut */
    border-radius: 5px; /* Bords arrondis pour un look moderne */
    background-color: #06BBCC; /* Couleur de fond */
    color: white; /* Couleur du texte */
    box-shadow: 0 2px 4px rgba(0,0,0,0.2); /* Ombre portée pour un effet de profondeur */
    transition: background-color 0.3s; /* Transition lisse pour l'effet hover */
}

.button-style:hover {
    background-color: #5c6bc0; /* Changement de couleur au survol */
}

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
#file-preview-container {
    position: fixed; /* Fixed position to cover the whole page */
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
    z-index: 9999; /* High z-index to be on top of other content */
    overflow-y: auto; /* Enable scroll within the container */
    display: none; /* Initially hidden */
}

#file-preview-container iframe {
    width: 80%; /* Adjust as necessary */
    height: 90%; /* Adjust as necessary */
    border: 3px solid white; /* Optional: to create a frame around the PDF */
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.5); /* Optional: to create a shadow effect */
    margin: 5% auto; /* Center the iframe in the container */
    display: block;
}
#file-list-container {
    margin-top: 10px;
}

#file-list {
    list-style: none;
    padding-left: 0;
}

#file-list li {
    margin-bottom: 5px;
    padding: 5px;
    background-color: #f2f2f2;
    border: 1px solid #dcdcdc;
    border-radius: 4px;
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
    <meta charset="utf-8">
    <title>Interface Professeur - ConnectEdu</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

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
    
     
    
<!-- Main Content Area -->
<!-- Main Content Area -->

<div class="container">

        <div class="announcement-section">
        
        <div class="announcement-header">
        <h2><i class="fas fa-chalkboard-teacher"></i> 
        <?php echo htmlspecialchars($course['titre']); ?>
        <?php if (isset($course['code_cours'])): ?>
            : <span class="course-code"><strong><?php echo htmlspecialchars($course['code_cours']); ?></strong></span>
        <?php endif; ?>
        </h2>
        </div>


<!-- Start of the form for file upload -->
<form action="upload.php" method="post" enctype="multipart/form-data">
    <!-- Textarea for announcement -->
    <textarea class="form-control announcement-textarea" placeholder="Annoncez quelque chose à votre classe" rows="4" name="announcement"></textarea>

    <!-- Hidden input to send courseID -->
    <input type="hidden" name="courseID" value="<?php echo $courseID; ?>">

    <div class="my-3">
        <!-- File input for course material -->
        <label class="btn btn-primary upload-button icon-button">
<i class="fas fa-file-import"></i> Importer des fichiers
<input type="file" name="fileToUpload[]" class="file-input" accept=".pdf, .jpeg, .jpg, .png" multiple onchange="updateFileList(this)">
</label>
        
<div id="file-list-container">
<ul id="file-list"></ul>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
var fileInput = document.querySelector('input[type="file"]');
var fileListContainer = document.getElementById('file-list');

fileInput.addEventListener('change', function(event) {
var files = event.target.files;
fileListContainer.innerHTML = ''; // Clear the list

for(var i = 0; i < files.length; i++) {
var li = document.createElement('li');
li.textContent = files[i].name;
fileListContainer.appendChild(li);
}
});
});
</script>

                <!-- Buttons for submit and cancel -->
                <div class="announcement-buttons">
                    <button type="button" class="btn btn-secondary">Annuler</button>
                    <button type="submit" class="btn btn-primary">Publier</button>
                </div>
             </form>
         </div>
      </div>
      <div class="container">
    <div class="assignment-section mt-5">
        <h3>Assigner des devoirs et des exercices</h3>
        <p>Assignez des devoirs et des exercices pour évaluer la compréhension de vos élèves.</p>
        <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createAssignmentModal">
    Créer un Devoir
</button>

</div>

    </div>
    <div class="navigation-buttons text-right mt-3">
    <button onclick="toggleMaterials()">Matériaux du Cours</button>
    <button onclick="toggleAnnouncements()">Annonces du Cours</button>
    <button onclick="toggleDevoirs()">Devoirs du Cours</button>


    
    <script>
function toggleMaterials() {
    var materialSection = document.getElementById('materialSection');
    var announcementSection = document.getElementById('announcementSection');
    var devoirSection = document.getElementById('devoirSection');

    materialSection.style.display = toggleDisplay(materialSection);
    announcementSection.style.display = 'none';
    devoirSection.style.display = 'none';
}

function toggleAnnouncements() {
    var materialSection = document.getElementById('materialSection');
    var announcementSection = document.getElementById('announcementSection');
    var devoirSection = document.getElementById('devoirSection');

    materialSection.style.display = 'none';
    announcementSection.style.display = toggleDisplay(announcementSection);
    devoirSection.style.display = 'none';
}

function toggleDevoirs() {
    var materialSection = document.getElementById('materialSection');
    var announcementSection = document.getElementById('announcementSection');
    var devoirSection = document.getElementById('devoirSection');

    materialSection.style.display = 'none';
    announcementSection.style.display = 'none';
    devoirSection.style.display = toggleDisplay(devoirSection);
}

function toggleDisplay(section) {
    return section.style.display === 'none' || section.style.display === '' ? 'block' : 'none';
}


</script>


</div>

</div>
  
<div class="modal fade" id="createAssignmentModal" tabindex="-1" role="dialog" aria-labelledby="createAssignmentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: white; color: #333;">
                <h5 class="modal-title" id="createAssignmentModalLabel">Créer un Devoir</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="createAssignmentForm" action="create_devoir.php" method="post" enctype="multipart/form-data" >
                    <div class="form-group">
                        <label for="assignmentTitle">Titre du Devoir</label>
                        <input type="text" class="form-control" name="assignmentTitle" id="assignmentTitle" required>
                    </div>
                    <div class="form-group">
                        <label for="assignmentDescription">Description</label>
                        <textarea class="form-control" name="assignmentDescription" id="assignmentDescription" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
    <label for="assignmentDeadline">Date Limite</label>
    <input type="datetime-local" class="form-control" name="assignmentDeadline" id="assignmentDeadline" required>
</div>

                    <div class="form-group">
                        <label for="courseID">Cours</label>
                        <input type="text" name="courseID" value="<?php echo htmlspecialchars($courseID); ?>">
                    </div>
                    <div class="form-group">
                        <label for="assignmentFile">Fichier du devoir</label>
                        <input type="file" class="form-control-file" name="assignmentFile" id="assignmentFile">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="submit" form="createAssignmentForm" class="btn btn-success">Créer</button>
            </div>
        </div>
    </div>
</div>



<!-- Scripts de Bootstrap pour le modal -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.9.2/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


<div class="container">
      <div class="announcement-section material-section" id="materialSection">
        <h3>Matériaux du cours:</h3>
        <?php foreach ($materials as $material): ?>
            <div class="card material-card">
    <div class="card-body">
        <!-- File name with download link -->
        <h5 class="card-title">
        <a href="#" class="material-link" data-file-path="<?php echo htmlspecialchars($material['file_path']); ?>">
    <?php echo htmlspecialchars($material['file_name']); ?>
</a>
        </h5>

        <!-- Check if file is an image and display it -->
        <?php 
        // Check the file extension to see if it's an image
        $file_extension = strtolower(pathinfo($material['file_name'], PATHINFO_EXTENSION));
        $image_extensions = ['jpg', 'jpeg', 'png', 'gif']; // Add or remove file extensions as needed
        if (in_array($file_extension, $image_extensions)): ?>
            <img src="<?php echo htmlspecialchars($material['file_path']); ?>" alt="<?php echo htmlspecialchars($material['file_name']); ?>" class="img-fluid material-image">

        <?php endif; ?>

        <!-- Description -->
        <p class="card-text"><?php echo htmlspecialchars($material['description']); ?></p>
        <!-- Upload date -->
        <p class="text-muted">Uploadé le: <?php echo htmlspecialchars($material['uploaded_at']); ?></p>
         <!-- Comment submission form -->
         <div class="comment-form">
            <textarea class="form-control comment-content" placeholder="Ajoutez un commentaire..."></textarea>
            <button class="btn btn-primary submit-comment" data-material-id="<?= $material['id_materiel']; ?>">Commenter</button>
        </div>
    </div>
</div>

            <div class="comments-container">
                  <?php
                  // Récupère les commentaires et les userID pour ce matériel spécifique
                  
                  $commentQuery = $conn->prepare(" SELECT c.commentaire, c.date_creation, u.nom, u.prenom FROM commentaires c JOIN utilisateur u ON c.user_id = u.utilisateur_id WHERE c.parent_id = ? ORDER BY c.date_creation DESC");
              
                  $commentQuery->bind_param("i", $material['id_materiel']);
                  $commentQuery->execute();
                  $commentResult = $commentQuery->get_result();
                  if ($commentResult->num_rows > 0) {
                    while ($comment = $commentResult->fetch_assoc()) {
                        // Display the comment with the user's nom and prenom
                        echo "<div class='comment'>";
                         echo "<p><strong>" . htmlspecialchars($comment['nom']) . " " . htmlspecialchars($comment['prenom']) . "</strong>: " . htmlspecialchars($comment['commentaire']) . "</p>";
                         echo "<p class='text-muted'>" . date('d-m-Y H:i:s', strtotime($comment['date_creation'])) . "</p>";
                         echo "</div>";
                        }
                    } else {
                        echo "<p>Aucun commentaire pour le moment.</p>";
                    }
                    $commentQuery->close();
                    ?>
                    </div>
        <?php endforeach; ?>
    </div>
</div>

</div>
<div class="container">
    <div class="announcement-sectionbtn announcement-section material-section" id="devoirSection">
        <h3>Devoirs du cours :</h3>
        <?php foreach ($devoirs as $devoir): ?>
            <div class="card devoir-card">
                <div class="card-body">
                    <!-- Titre et lien de téléchargement du devoir -->
                    <h5 class="card-title">
                        <a href="<?php echo htmlspecialchars($devoir['fichier_chemin']); ?>" class="devoir-link" target="_blank">
                            <?php echo htmlspecialchars($devoir['titre']); ?>
                        </a>
                    </h5>
                    <!-- Description -->
                    <p class="card-text"><?php echo htmlspecialchars($devoir['description']); ?></p>
                    <!-- Date limite -->
                    <p class="text-muted">Date limite : <?php echo htmlspecialchars($devoir['date_limite']); ?></p>
                    <!-- Formulaire de soumission de devoir
                    <form action="upload_devoir.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="devoir_id" value="<?php echo $devoir['id']; ?>">
                        <div class="form-group">
                            <label for="devoirFile">Choisir un fichier à soumettre :</label>
                            <input type="file" name="devoirFile" id="devoirFile" class="form-control-file" required>
                        </div>
                        
                    </form> -->
                    <button type="button"  class="btn btn-primary" onclick="window.location.href='view_submissions.php?devoir_id=<?php echo $devoir['id']; ?>'">Voir les Soumissions</button>


                    
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var links = document.querySelectorAll('.devoir-link'); // S'assurer que c'est la classe des liens de téléchargement

    links.forEach(function(link) {
        link.addEventListener('click', function(event) {
            event.preventDefault();
            var filePath = this.getAttribute('href'); // Utiliser 'href' pour le chemin

            var previewContainer = document.getElementById('file-preview-container');
            previewContainer.innerHTML = `
                <div style="position: absolute; top: 20px; right: 20px; font-size: 30px; color: white; cursor: pointer;" onclick="document.getElementById('file-preview-container').style.display='none';">&times;</div>
                <iframe src="${filePath}" style="width:100%; height:100%;" frameborder="0"></iframe>`;
            previewContainer.style.display = 'block';
        });
    });
});
</script>



            


<div class="container">
<div class="announcement-sectionbtn material-section"  id="announcementSection">
        <h2>Annonces du cours</h2>
        <?php if (!empty($annonces)): ?>
            <?php foreach ($annonces as $annonce): ?>
                <div class="annonce">
    <p><?php echo htmlspecialchars($annonce['message']); ?></p>
    <p class="text-muted">Posté le : <?php echo date('d-m-Y H:i', strtotime(htmlspecialchars($annonce['date_creation']))); ?></p>
    <div class="comment-form">
        <textarea class="form-control comment-content" placeholder="Ajoutez un commentaire..."></textarea>
        
        <button class="btn btn-primary submit-comment-annonce" data-annonce-id="<?php echo $annonce['id']; ?>">Commenter</button>
    </div>
    <!-- Le conteneur où les commentaires seront affichés -->
    <div class="comments-container">
            <?php
            // Récupère les commentaires pour cette annonce spécifique
            $commentQuery = $conn->prepare("SELECT ac.commentaire, ac.date_creation, u.nom, u.prenom FROM annonce_comments ac JOIN utilisateur u ON ac.utilisateur_id = u.utilisateur_id WHERE ac.annonce_id = ? ORDER BY ac.date_creation DESC");
            // $commentQuery->bind_param("i", $annonce['annonce_id']); 
            $commentQuery->bind_param("i", $annonce['id']);
            $commentQuery->execute();
            $commentResult = $commentQuery->get_result();
            if ($commentResult->num_rows > 0) {
                while ($comment = $commentResult->fetch_assoc()) {
                    // Affiche chaque commentaire avec le nom et le prénom de l'utilisateur
                    echo "<div class='comment'>";
                    echo "<p><strong>" . htmlspecialchars($comment['nom']) . " " . htmlspecialchars($comment['prenom']) . "</strong>: " . htmlspecialchars($comment['commentaire']) . "</p>";
                    echo "<p class='text-muted'>" . date('d-m-Y H:i:s', strtotime($comment['date_creation'])) . "</p>";
                    echo "</div>";
                }
            } else {
                echo "<p>Aucun commentaire pour le moment.</p>";
            }
            $commentQuery->close();
            ?>
</div>
</div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucune annonce pour le moment.</p>
        <?php endif; ?>












    <!-- Scripts for Bootstrap and Font Awesome -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.9.2/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Function to hide an announcement
        function hideAnnonce(element) {
            element.parentElement.style.display = 'none';
        }

        // Function to delete an announcement
        function deleteAnnonce(id, element) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette annonce ?')) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'delete_annonce.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                        if (this.responseText.includes("supprimée avec succès")) {
                            element.parentNode.remove();
                        } else {
                            alert('Erreur lors de la suppression de l\'annonce');
                        }
                    }
                }
                xhr.send('id=' + id);
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            // Functionality to submit material comment
            document.querySelectorAll('.submit-comment').forEach(button => {
                button.addEventListener('click', function() {
                    var materialId = this.dataset.materialId;
                    var commentBox = this.previousElementSibling;
                    var commentContent = commentBox.value.trim();
                    if (!commentContent) {
                        alert('Veuillez écrire un commentaire avant de soumettre.');
                        return; // Exit the function if no content
                    }
                    var formData = new FormData();
                    formData.append('material_id', materialId);
                    formData.append('commentaire', commentContent);

                    fetch('submit_commentaire.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            var commentsContainer = this.closest('.card-body').querySelector('.comments-container');
                            commentsContainer.innerHTML += data.commentHtml; // Add the new comment
                            commentBox.value = ''; // Clear the textarea
                        } else {
                            console.error('Error:', data.error);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                });
            });
        })
    
            // Functionality to submit announcement comment
            document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll('.submit-comment-annonce').forEach(button => {
        button.addEventListener('click', function() {
            var annonceId = this.dataset.annonceId;
            var commentBox = this.closest('.comment-form').querySelector('.comment-content');
            var commentContent = commentBox.value.trim();
            if (!commentContent) {
                alert('Veuillez écrire un commentaire avant de soumettre.');
                return; // Exit the function if no content
            }
            var formData = new FormData();
            formData.append('annonce_id', annonceId);
            formData.append('commentaire', commentContent);

            fetch('submit_commentaire_annonce.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
    if (!response.ok) {
        throw new Error('Network response was not ok');
    }
    return response.text(); // Use text() here to see the raw response
})
.then(text => {
    try {
        const data = JSON.parse(text); // Try to parse it as JSON
        // Handle your JSON data here
    } catch (error) {
        console.error('Could not parse JSON:', text);
        throw new Error('Response is not valid JSON');
    }
})
.catch(error => {
    console.error('Fetch error:', error);
});
                });
            });
        });
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var links = document.querySelectorAll('.material-link');
    links.forEach(function(link) {
        link.addEventListener('click', function(event) {
            event.preventDefault(); // Stop the default download or navigation
            var filePath = this.getAttribute('data-file-path');

            var previewContainer = document.getElementById('file-preview-container');
            previewContainer.innerHTML = `
                <div style="position: absolute; top: 20px; right: 20px; font-size: 30px; color: white; cursor: pointer;" onclick="document.getElementById('file-preview-container').style.display='none';">&times;</div>
                <iframe src="${filePath}" frameborder="0"></iframe>`;
            previewContainer.style.display = 'block';
        });
    });
});
</script>
<script>
    // Ensure the DOM is fully loaded before adding event listeners
    document.addEventListener('DOMContentLoaded', function() {
       
        
        // Function that updates the file list when new files are selected
        function updateFileList(fileInput) {
            var fileListContainer = document.getElementById('file-list');
            fileListContainer.innerHTML = ''; // Clear the list
            var files = fileInput.files;
            for (var i = 0; i < files.length; i++) {
                var li = document.createElement('li');
                li.textContent = files[i].name;
                fileListContainer.appendChild(li);
            }
        }

        // Add the change event listener to your file input
        var fileInput = document.querySelector('input[type="file"]');
        fileInput.addEventListener('change', function(event) {
            updateFileList(this); // Call your new function
        });
    });
</script>












     
 </body>
 <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
 <script src="https://cdn.jsdelivr.net/npm/popper.js@1.9.2/umd/popper.min.js"></script>
 <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
 
 </body>
 </html>