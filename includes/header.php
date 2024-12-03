<?php 
include 'config.php';
include 'functions.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

if(isset($_POST['logout'])){
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Qvintus Antikvariat</title>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous" defer></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" ></script>
    <script src="https://cdn.jsdelivr.net/npm/typeahead.js@0.11.1/dist/typeahead.bundle.min.js" defer></script>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/script/script.js" defer></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
</head>

<body>
<div class="container">
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <!-- Branding -->
        <a class="navbar-brand" href="#">
            <h3 class="m-0 text-white">Qvintus Antikvariat</h3>
        </a>

        <!-- Toggler Button for Mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Links -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link text-white" href="index.php">Hem</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="books.php">Böcker</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="books.php">Exklusivt</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="about.php">Verksamhet</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="contact.php">Kontakt</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
</body>
</html>