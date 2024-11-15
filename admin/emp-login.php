<?php
include_once '../includes/emp-header.php';

if(isset($_POST['login'])){
    $loginUser = login($pdo);

    if($loginUser == "true"){
        if($_SESSION['urole'] == "");
    }

    elseif($loginUser == "falsen"){
        echo "<div class='alert alert-danger' role='alert'>
        Wrong username or email!
        </div>";
    }

    elseif($loginUser == "falsep"){
        echo "<div class='alert alert-danger' role='alert'>
        Wrong password!
        </div>";
    }
}

if(isset($_POST['register'])){
    $registerUser = register($pdo);
}

?>



<div class="container" id="loginPage">
    <div class="row">
        <h2>User Login</h2>
        <form action="" method="POST" target="">
            <label for="u_name">Username:</label><br>
            <input type="text" id="u_name" name="u_name" required="required"><br><br>
            <label for="u_pass">Enter Password:</label><br>
            <input type="password" id="u_pass" name="u_pass" required="required"><br><br>
            <input type="submit" name="login" value="Login">
            <input type="submit" name="register" value="register">
        </form><br>
    </div>
</div>


<?php 
include_once '../includes/emp-footer.php';
?>
