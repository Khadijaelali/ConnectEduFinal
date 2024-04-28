<?php 
session_start(); // Démarrez la session en premier
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0");

if (!isset($_SESSION['userID'])) {
    header('Location: login.php');
    exit();
}

// Set up database connection variables
$host = 'localhost';
$dbName = 'test'; // Modifié pour éviter la confusion
$user = 'root';
$pass = '';
$conn = new mysqli("localhost", "root", "", "test");
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}
$studentId = $_SESSION['userID']; // ID du etudiant connecté
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['courseCode'])) {
    $courseCode = $_POST['courseCode']; // Get the course code from the submitted form data
    $currentTime = date('Y-m-d H:i:s'); // Get the current time
    
    // Prepare an INSERT statement to add the record to the database
    $stmt = $conn->prepare("INSERT INTO student_course_access (student_id, course_code, access_time) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $studentId, $courseCode, $currentTime);
    
    // Execute the prepared statement
    if ($stmt->execute()) {
        echo "Course accessed successfully.";
        // Redirect or perform other actions if needed
    } else {
        echo "Error accessing course: " . $conn->error;
    }

    $stmt->close();
    // Redirect to prevent form resubmission
    header('Location: prof2.php');
    exit();
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
        
        html, body {
            height: 100%; 
            margin: 0;
            padding: 0;
        }

        .container-xxl {
            padding-top: 20px;
            padding-bottom: 20px;
        }

        .feature-item {
            display: flex;
            flex-direction: column;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 5px;
            height: 100%;
        }

        .feature-item .content {
            padding: 20px;
            flex: 1; /* Makes this div flexible to fill the space */
        }

        .feature-item .footer {
            margin-top: auto; /* Pushes the footer to the bottom */
            padding: 20px;
            text-align: center; /* Center align the button */
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none; /* Removes underline from links */
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .feature-item i {
            margin-bottom: 15px;
        }
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
    <title>Student Interface - ConnectEdu</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <!-- Reuse the same stylesheets for consistency -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <!-- Navbar for students -->
   <nav class="navbar navbar-expand-lg bg-white navbar-light shadow fixed-top p-0">
        <a href="student_dashboard.html" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
        <?php if (isset($studentId)): ?>
            <h2 class="m-0 text-primary"><i class="fa fa-chalkboard-teacher me-3"></i>ConnectEdu Student: <?= htmlspecialchars($studentId); ?></h2>    
        <?php endif; ?>
        </a>
    </nav><br><br>
    <!-- Navbar End -->

    
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-4 d-flex align-items-stretch">
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="feature-item bg-light h-100">
                        <div class="p-4">
                            <i class="fa fa-4x fa-book-open text-primary mb-4"></i>
                            <h5 class="mb-3">View Courses</h5>
                            <p>Access Pedagogical Materials Published by Your Professors.</p>
                            <a id="exploreBtn" class="btn btn-primary">Access</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="feature-item bg-light h-100">
                        <div class="p-4">
                            <i class="fa fa-4x fa-calendar-check text-primary mb-4"></i>
                            <h5 class="mb-3">View Calendar</h5>
                            <p>Check out the academic calendar for important dates and events.</p>
                            <a href="student_calendar.html" class="btn btn-primary">View</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="feature-item bg-light h-100">
                        <div class="p-4">
                            <i class="fa fa-4x fa-tasks text-primary mb-4"></i>
                            <h5 class="mb-3">Assignments</h5>
                            <p>Access, Complete, and Submit Your Assignments Effortlessly.</p>
                            <a href="view_assignments.html" class="btn btn-primary">Access</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="myModal" class="modal">
        <!-- Modal content -->
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Access the course</h2>
            <!-- Your form content goes here -->
            <form id="createCourseForm" method="post">
    <label for="courseCode">Course's code  :</label>
    <input type="text" id="courseCode" name="courseCode" required><br><br> <!-- Assurez-vous que "name" est "courseName" -->
    <button type="submit">Access</button>
    <button type="button" onclick="document.getElementById('myModal').style.display='none'">Cancel</button>
</form>
        </div>
    </div>
    
    

    <div class="sidebar">
    <a href="index.html"><i class="fas fa-home"></i> Home</a>
    
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

    <!-- Reuse the same JavaScript libraries for functionality -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="js/main.js"></script>
    <script>
        // Get the modal
        var modal = document.getElementById("myModal");

        // Get the button that opens the modal
        var btn = document.getElementById("exploreBtn");

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