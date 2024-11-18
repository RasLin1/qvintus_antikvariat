<?php

function cleanInput($data){
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

function checkUserRole($requiredRole = 1, $redirectLink = "index.php") {

    // Check if urole exists in the session
    if (!isset($_SESSION['urole'])) {
        // Redirect if urole is not set
        header("Location: $redirectLink");
        exit(); // Stop further script execution
    } elseif ($_SESSION['urole'] < $requiredRole) {
        // Redirect if the user's role is less than the allowed minimum
        header("Location: $redirectLink");
        exit();
    }
}

function login($pdo){
	$stmt_checkIfUserExists = $pdo->prepare("SELECT * FROM employee WHERE emp_uname = :uname");
	$stmt_checkIfUserExists->bindValue(":uname", $_POST['u_name'], PDO::PARAM_STR);
	$stmt_checkIfUserExists->execute();
	//Creates an array for the selected data
	$userData = $stmt_checkIfUserExists->fetch();
	
	if(!$userData){
		$errorMessages = "No such user in database.";
		$errorState = 1;
		return "falsen";
	}
	
	//checks that the passwords match
	elseif($userData){
	   $checkPasswordMatch = password_verify($_POST['u_pass'], $userData['emp_pass']);
	}

	   if($checkPasswordMatch == true) {
			$_SESSION['uname'] = $userData['emp_uname'];
			$_SESSION['urole'] = $userData['emp_role_fk'];
			$_SESSION['uid'] = $userData['emp_id'];
			return TRUE;
	   } 
	   else {
		  $errorMessages = "INVALID password";     
		  return "falsep";
	   }
}

function register($pdo){
	$regUserName = cleanInput($_POST['u_name']);
	//encrypts the password with password_hash()
	$encryptedPassword = password_hash($_POST['u_pass'], PASSWORD_DEFAULT);
	$urole = "3";

	$stmt_registerUser = $pdo->prepare('INSERT INTO employee(emp_uname, emp_pass, emp_role_fk)values(:uname, :upass, :urole)');
	$stmt_registerUser->bindParam(":uname" ,$regUserName, PDO::PARAM_STR);
	$stmt_registerUser->bindParam(":upass" ,$encryptedPassword, PDO::PARAM_STR);
	$stmt_registerUser->bindParam(":urole" ,$urole, PDO::PARAM_INT);

	if($stmt_registerUser->execute()){
		header("Location: emp-login.php?newuser=1");
	}
	else{
		return "Something went wrong";
	}
}


// Adds a new author to db and redirects
function addAuthor($pdo, $authName, $redirectTo = 'book-editor.php') {
    $stmt_addAuthor = $pdo->prepare("INSERT INTO authors (author_name) VALUES (:authName)");
    $stmt_addAuthor->bindParam(":authName" ,$authName, PDO::PARAM_STR);
    
    if ($stmt_addAuthor->execute()) {
        
        // Store the success message in the session
        $_SESSION['message'] = 'Author added successfully';
        
        // Redirect to the dynamic location with a success flag
        header('Location: ' . $redirectTo . '?auth-success');
        exit;
    } else {
        return "ERROR";
    }
}

//Adds a new genre to db
function addGenre($pdo, $genName, $redirectTo = 'book-editor.php') {
    $stmt_addGenre = $pdo->prepare("INSERT INTO genres (genre_name) VALUES (:genName)");
	$stmt_addGenre->bindParam(":genName" ,$genName, PDO::PARAM_STR);
    if($stmt_addGenre->execute()){
		// Store the success message in the session
        $_SESSION['message'] = 'Genre added successfully';
        
        // Redirect to the dynamic location with a success flag
        header('Location: ' . $redirectTo . '?gen-success');
		exit;
	}
	else{
		return "ERROR";
	}
}

//Adds a new illustrator to db
function addIllustrator($pdo, $illName, $redirectTo = 'book-editor.php') {
    $stmt_addIllustrator = $pdo->prepare("INSERT INTO illustrators (illustrator_name) VALUES (:illName)");
	$stmt_addIllustrator->bindParam(":illName" ,$illName, PDO::PARAM_STR);
    if($stmt_addIllustrator->execute()){
		// Store the success message in the session
        $_SESSION['message'] = 'Illustrator added successfully';
        
        // Redirect to the dynamic location with a success flag
        header('Location: ' . $redirectTo . '?ill-success');
		exit;
	}
	else{
		return "ERROR";
	}
}

//Adds a new illustrator to db
function addPublisher($pdo, $pubName, $redirectTo = 'book-editor.php') {
    $stmt_addPublisher = $pdo->prepare("INSERT INTO publishers (pub_name) VALUES (:pubName)");
	$stmt_addPublisher->bindParam(":illName" ,$illName, PDO::PARAM_STR);
    if($stmt_addPublisher->execute()){
		// Store the success message in the session
        $_SESSION['message'] = 'Publisher added successfully';
        
        // Redirect to the dynamic location with a success flag
        header('Location: ' . $redirectTo . '?pub-success');
		exit;
	}
	else{
		return "ERROR";
	}
}
?>