
<?php
session_start();

header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0");

if (!isset($_SESSION['userID'])) {
    header('Location: login.php');
    exit;
}

$professeurId = $_SESSION['userID']; // ID du professeur connecté




// Connexion à la base de données
$conn = new mysqli("localhost", "root", "", "test");

// Vérifiez la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Requête pour obtenir les cours du professeur
$sql = "SELECT id, titre FROM cours WHERE professeur_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $professeurId); // 
$stmt->execute();
$result = $stmt->get_result();

// Start of your sidebar code
$sidebarHTML = '<div class="sidebar"><a href="prof.php"><i class="fas fa-home"></i> Accueil</a>';

// Loop through each course and add a link to the sidebar
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Update the href to point to 'course.php?courseID='
        $sidebarHTML .= '<a href="course.php?courseID='.$row['id'].'">'.htmlspecialchars($row['titre']).'</a>';
    }
} else {
    // If no courses were found, display a message
    $sidebarHTML .= '<p>Aucun cours trouvé.</p>';
}

// Finish building the sidebar HTML
$sidebarHTML .= '<a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></div>';
$stmt->close();
$conn->close();
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

        .container-fluid {
            min-height: calc(50vh - 50px); 
        }

        .footer {
            position: relative; /* Ensures that footer is placed at the end of the content */
            width: 100%;
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
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
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
</head>

<body>
    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
        <a href="Prof.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
        <?php if (isset($professeurId)): ?>
            <h2 class="m-0 text-primary"><i class="fa fa-chalkboard-teacher me-3"></i>ConnectEdu Professeur:<?= htmlspecialchars($professeurId); ?></h2>    
        <?php endif; ?>
        </a>
        <div class="collapse navbar-collapse" id="navbarCollapse">
           <!-- <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="create_course.html" class="nav-item nav-link">Acceuil</a>
                <a href="share_assignments.html" class="nav-item nav-link">Deconexion</a>
                
            </div> -->
        </div>
    </nav>
    <!-- Navbar End -->

    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="feature-item bg-light">
                        <div class="p-4">
                            <i class="fa fa-4x fa-file-upload text-primary mb-4"></i>
                            <h5 class="mb-3">Create a Course</h5>
                            <p>Share your knowledge by creating interactive courses for your students.</p>
                            <button id="myBtn" class="btn btn-primary">Start</button>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="feature-item bg-light">
                        <div class="p-4">
                            <i class="fa fa-4x fa-calendar-alt text-primary mb-4"></i>
                            <h5 class="mb-3">Calendar</h5>
                            <p>Manage your schedule, track important dates, and organize your time effectively.</p>
                            <a href="calendar.php" class="btn btn-primary">Start</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- The Modal -->
    <div id="myModal" class="modal">
        <!-- Modal content -->
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Create a course</h2>
            <!-- Your form content goes here -->
            <form id="createCourseForm" action="traitement_cours.php" method="post">
    <label for="courseName">Nom du cours (obligatoire) :</label>
    <input type="text" id="courseName" name="courseName" required><br><br> <!-- Assurez-vous que "name" est "courseName" -->
    <label for="section">Section :</label>
    <input type="text" id="section" name="section" required><br><br> <!-- Assurez-vous que "name" est "section" -->
    <label for="subject">Sujet :</label>
    <input type="text" id="subject" name="subject" required><br><br> <!-- Assurez-vous que "name" est "subject" -->
    <label for="room">Salle :</label>
    <input type="text" id="room" name="room" required><br><br> <!-- Assurez-vous que "name" est "room" -->
    <button type="submit">Créer</button>
    <button type="button" onclick="document.getElementById('myModal').style.display='none'">Annuler</button>
</form>
        </div>
    </div>
    
    <!-- Features End -->
    <?php echo $sidebarHTML; ?>

   
    

   

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <script>
        // Get the modal
        var modal = document.getElementById("myModal");

        // Get the button that opens the modal
        var btn = document.getElementById("myBtn");

        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];

        // When the user clicks the button, open the modal 
        btn.onclick = function() {
            modal.style.display = "block";
        }

        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>