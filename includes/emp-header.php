<?php 
include 'config.php';
include 'functions.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

if(isset($_POST['logout'])){
    session_unset();
    session_destroy();
    header("Location: ../main/index.php");
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Qvintus Antikvariat</title>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous" defer></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" ></script>
    <script src="https://cdn.jsdelivr.net/npm/typeahead.js@0.11.1/dist/typeahead.bundle.min.js" defer></script>
    <script src="../assets/script/script.js" defer></script>
    <link rel="stylesheet" href="../assets/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .container{
            color: white;
        }
    </style>
</head>

<body>
<div class="container">
<div id="nav">
    <div class="row">
        <h2 class="col-12 col-md-8">Qvintus Antikvariat Admin</h2>
        <ul class="col-12 col-md-4">
            <li><a href="admin-panel.php">Admin Panel</a></li>
        </ul>
    </div>
</div>
