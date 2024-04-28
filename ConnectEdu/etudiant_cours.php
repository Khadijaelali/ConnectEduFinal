<?php
session_start();
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0");

// Check user authentication
if (!isset($_SESSION['userID'])) {
    echo "User not authenticated.";
    exit();
}
$studentId = $_SESSION['userID']; // ID du etudiant connecté

// Database connection setup
$host = 'localhost';
$dbName = 'test';
$user = 'root';
$pass = '';
$dsn = "mysql:host=$host;dbname=$dbName;charset=utf8";


try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    // Check if the course code is provided in the URL
    if (isset($_GET['code'])) {
        $courseCode = $_GET['code'];
        $_SESSION['current_course_code'] = $courseCode;
        $stmt = $pdo->prepare("SELECT titre FROM cours WHERE code_cours = ?");
        $stmt->execute([$courseCode]);
        $courseTitle = $stmt->fetch(PDO::FETCH_ASSOC);

        // Fetch course details
        $stmt = $pdo->prepare("SELECT * FROM cours WHERE code_cours = ?");
        $stmt->execute([$courseCode]);
        $course = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($course) {
            // Fetch related materials
            $materialsStmt = $pdo->prepare("SELECT * FROM materials WHERE course_id = ? ORDER BY uploaded_at DESC");
            $materialsStmt->execute([$course['id']]);
            $materials = $materialsStmt->fetchAll(PDO::FETCH_ASSOC);

            // Fetch related announcements
            $announcementsStmt = $pdo->prepare("SELECT * FROM annonces WHERE course_id = ? ORDER BY date_creation DESC");
            $announcementsStmt->execute([$course['id']]);
            $announcements = $announcementsStmt->fetchAll(PDO::FETCH_ASSOC);

            // Fetch related assignments
            $assignmentsStmt = $pdo->prepare("SELECT * FROM devoir WHERE cours_id = ? ORDER BY date_limite DESC");
            $assignmentsStmt->execute([$course['id']]);
            $assignments = $assignmentsStmt->fetchAll(PDO::FETCH_ASSOC);

            
        } else {
            // Handle case where course is not found
            echo "Code de cours invalide.";
        }
    } else {
        // Handle case where no course code is provided
        echo "Aucun code de cours soumis.";
    }
} catch (PDOException $e) {
    // Handle any PDO exceptions (database errors)
    echo "Erreur de connexion à la base de données: " . $e->getMessage();
}
$conn = new mysqli("localhost", "root", "", "test");

// Vérifiez la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

?>



<!DOCTYPE html>
<html lang="en">

<head>
<style>
        html, body {
            height: 100%; 
            margin: 0;
            padding: 0;
            
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
    border-left: 5px solid #58dce8; /* Une bordure sur le côté pour un effet visuel */
    margin-top: 20px;
    padding: 15px;
    border-radius: 4px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    position: relative;
}

.annonce p {
    margin: 0;
    color: #333;
}

.annonce .text-muted {
    font-size: 0.8em;
    margin-top: 10px;
}


.material-card {
    margin-bottom: 15px; /* Ajoutez de l'espace en bas de chaque carte pour la séparer de la suivante */
    margin-top: 30px;
}
.material-section {
    display: none; /* Cacher la section par défaut */
}
.material-image {
  max-width: 100%; /* This makes image responsive to its container */
  max-height: 500px; /* This sets the maximum height */
  display: block; 
  margin: 0 auto; 
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
.form-group label.btn {
    cursor: pointer;
}

#file-chosen {
    margin-left: 10px;
    font-style: italic;
}

/* Mettez en surbrillance la date limite si elle est passée */
.text-muted.past-due {
    color: red;
    font-weight: bold;
}

/* Styliser le bouton de soumission désactivé */
.btn-secondary[disabled] {
    background-color: red;
    border-color: red;
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
    <title>Student Interface - ConnectEdu</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <!-- Reuse the same stylesheets for consistency -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<body>
    <!-- Navbar for students -->
   <nav class="navbar navbar-expand-lg bg-white navbar-light shadow fixed-top p-0">
        <a href="index.html" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
        <?php if (isset($studentId)): ?>
            <h2 class="m-0 text-primary"><i class="fa fa-chalkboard-teacher me-3"></i>ConnectEdu Student: <?= htmlspecialchars($studentId); ?></h2>    
        <?php endif; ?>
        </a>
    </nav>

 

    
    <!-- Navbar End -->

   


<?php

    
// Démarrer la session pour accéder aux variables de session


// Vérifier s'il y a des erreurs ou des matériaux à afficher
if (isset($_SESSION['error'])) {
    echo "<p>" . $_SESSION['error'] . "</p>";
    unset($_SESSION['error']); // Effacer le message d'erreur après l'affichage
} elseif (isset($_SESSION['course_materials'])) {
    $materials = $_SESSION['course_materials'];}


    // Vérifier s'il y a des erreurs ou des matériaux à afficher
if (isset($_SESSION['error'])) {
    echo "<p>" . $_SESSION['error'] . "</p>";
    unset($_SESSION['error']); // Effacer le message d'erreur après l'affichage
} elseif (isset($_SESSION['course_devoir'])) {
    $devoirs = $_SESSION['course_devoir'];}



// Vérifier s'il y a des erreurs ou des matériaux à afficher
if (isset($_SESSION['error'])) {
    echo "<p>" . $_SESSION['error'] . "</p>";
    unset($_SESSION['error']); // Effacer le message d'erreur après l'affichage
} elseif (isset($_SESSION['course_announcements'])) {
    $announcements = $_SESSION['course_announcements'];}
    ?>
    

<div class="container"><br> <br><br>
<div class="announcement-section">
     <?php  echo "<h1><i class='fa fa-chalkboard-teacher'></i> " . htmlspecialchars($courseTitle['titre']) . "</h1>";?><br>
     <div class="navigation-buttons text-right mt-3">
    <button onclick="toggleMaterials()">Matériaux du Cours</button>
    <button onclick="toggleAnnouncements()">Annonces du Cours</button>
    <button onclick="toggleDevoirs()">Devoirs du Cours</button>
</div>
</div>
</div>
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


<div class="container">
    <div class="announcement-section material-section" id="materialSection">
 
  
        <h3>Matériaux du cours:</h3>
<?php foreach ($materials as $material): ?>
            <div class="card material-card">
                <div class="card-body">
                    <!-- File name with download link -->
                    <h5 class="card-title">
                        <a href="<?php echo htmlspecialchars($material['file_path']); ?>" target="_blank">
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
    <div class="sidebar">
        <a href="prof2.php"><i class="fas fa-home"></i> Home</a>
        <?php
    // The PHP code to fetch and display course titles from the database
    $sidebarQuery = "
        SELECT c.titre, c.code_cours 
        FROM cours c
        INNER JOIN student_course_access sca ON c.code_cours = sca.course_code 
        WHERE sca.student_id = ?
        ORDER BY sca.access_time DESC
    ";

    $sidebarStmt = $conn->prepare($sidebarQuery);
    $sidebarStmt->bind_param("s", $studentId);
    $sidebarStmt->execute();
    $result = $sidebarStmt->get_result();

    while ($course = $result->fetch_assoc()) {
        echo '<a href="etudiant_cours.php?code=' . htmlspecialchars($course['code_cours']) . '">' . htmlspecialchars($course['titre']) . '</a>';
    }

    $sidebarStmt->close();
    ?>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Log out</a>
    </div>
    <div class="container">
    <div class="announcement-section material-section"  id="announcementSection">
        <h2>Annonces du cours</h2>
        <?php 
        
        if (!empty($announcements)): ?>
            <?php foreach ($announcements as $annonce): ?>
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
            // $commentQuery->bind_param("i", $annonce['annonce_id']); // Ici, utilisez l'ID de l'annonce au lieu de l'ID du matériel
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
    </div>
</div>
<div class="container">
    <div class="announcement-section" id="devoirSection">
        <h3>Devoirs du cours :</h3>
        <?php 
        // S'assurer que $assignments est un tableau avant de l'itérer
        if (is_array($assignments) && !empty($assignments)) {
            foreach ($assignments as $assignment): 
                $dueTime = strtotime($assignment['date_limite']);
                $isPastDue = time() > $dueTime;
                ?>
                <div class="card devoir-card">
                    <div class="card-body">
                        <!-- Titre et lien de téléchargement du devoir -->
                        <h5 class="card-title">
                            <a href="<?php echo htmlspecialchars($assignment['fichier_chemin']); ?>" target="_blank">
                                <?php echo htmlspecialchars($assignment['titre']); ?>
                            </a>
                        </h5>
                        <!-- Description -->
                        <p class="card-text"><?php echo htmlspecialchars($assignment['description']); ?></p>
                        <!-- Date limite -->
                        <p class="text-muted.past-due" style="<?php echo $isPastDue ? 'color: red;' : ''; ?>">
                            Date limite : <?php echo htmlspecialchars($assignment['date_limite']); ?>
                        </p>
                        <!-- Formulaire de soumission de devoir -->
                        <?php if (!$isPastDue): ?>
                            <form action="submit_assignment.php" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="devoir_id" value="<?php echo $assignment['id']; ?>">
                                <div class="form-group">
                                    <label for="devoirFile<?php echo $assignment['id']; ?>" class="btn btn-secondary">Choisir un fichier</label>
                                    <input type="file" name="devoirFile" id="devoirFile<?php echo $assignment['id']; ?>" hidden required>
                                    <span id="file-chosen<?php echo $assignment['id']; ?>">Aucun fichier choisi</span>
                                    <button type="submit" class="btn btn-primary mt-2">Soumettre</button>
                                </form>
                        <?php else: ?>
                            <button class="btn btn-secondary mt-2 " disabled>Soumission fermée</button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; 
        } else {
            echo "<p>Aucun devoir à afficher.</p>";
        }
        ?>
    </div>
</div>

<!-- Votre code JavaScript ici pour la gestion des noms de fichiers sélectionnés -->
<script>
document.querySelectorAll('[id^="devoirFile"]').forEach(input => {
    input.addEventListener('change', function() {
        document.getElementById('file-chosen' + this.id.match(/\d+/)[0]).textContent = this.files[0].name;
    });
});
</script>



   <div class="sidebar">
    <a href="prof2.php"><i class="fas fa-home"></i> Home</a>
    
    <?php
    // The PHP code to fetch and display course titles from the database
    $sidebarQuery = "
        SELECT c.titre, c.code_cours 
        FROM cours c
        INNER JOIN student_course_access sca ON c.code_cours = sca.course_code 
        WHERE sca.student_id = ?
        ORDER BY sca.access_time DESC
    ";

    $sidebarStmt = $conn->prepare($sidebarQuery);
    $sidebarStmt->bind_param("s", $studentId);
    $sidebarStmt->execute();
    $result = $sidebarStmt->get_result();

    while ($course = $result->fetch_assoc()) {
        echo '<a href="etudiant_cours.php?code=' . htmlspecialchars($course['code_cours']) . '">' . htmlspecialchars($course['titre']) . '</a>';
    }

    $sidebarStmt->close();
    ?>
    
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Log out</a>
</div>




 <!-- Scripts for Bootstrap and Font Awesome -->
 <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.9.2/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
 
      


        // When the DOM is ready
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


    <!-- Template Javascript -->
    <script src="js/main.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
 <script src="https://cdn.jsdelivr.net/npm/popper.js@1.9.2/umd/popper.min.js"></script>
 <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>