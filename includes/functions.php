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

function addBook($pdo){
// Sanitize and collect inputs
$bookTitle = cleanInput($_POST['bookTitle']);
$bookDescription = cleanInput($_POST['bookDescription']);
$bookGenre = isset($_POST['bookGenre']) ? $_POST['bookGenre'] : [];
$bookAuthor = isset($_POST['bookAuthor']) ? $_POST['bookAuthor'] : [];
$bookIllustrator = isset($_POST['bookIllustrator']) ? $_POST['bookIllustrator'] : [];
$bookPublisher = cleanInput($_POST['bookPublisher']);
$bookAgeRecommendation = cleanInput($_POST['bookAgeRecommendation']);
$bookCategory = cleanInput($_POST['bookCategory']);
$bookSeries = cleanInput($_POST['bookSeries']);
$bookLanguage = cleanInput($_POST['bookLanguage']);
$bookReleseDate = cleanInput($_POST['bookReleseDate']);
$bookPageCount = cleanInput($_POST['bookPageCount']);
$bookPrice = cleanInput($_POST['bookPrice']);

// Handle file upload
$bookImage = null;
if (isset($_FILES['fileToUpload']) && $_FILES['fileToUpload']['error'] === UPLOAD_ERR_OK) {
	$fileTmpPath = $_FILES['fileToUpload']['tmp_name'];
	$fileName = basename($_FILES['fileToUpload']['name']);
	$uploadDir = '../assets/img/';
	$fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
	$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
	
	if (in_array(strtolower($fileExtension), $allowedExtensions)) {
		$newFileName = uniqid() . '.' . $fileExtension;
		$destPath = $uploadDir . $newFileName;

		if (move_uploaded_file($fileTmpPath, $destPath)) {
			$bookImage = $newFileName;
		} else {
			die('Error uploading the file.');
		}
	} else {
		die('Invalid file type.');
	}
}

// Insert book details into the books table
$stmt_insertBook = $pdo->prepare("
	INSERT INTO books 
	(book_title, book_series_fk, book_summary, publisher_fk, book_age_rec_fk, book_category_fk, book_language_fk, book_publishing_date, book_side_count, book_price, book_img, employee_fk) 
	VALUES 
	(:title, :series_id, :description, :publisher_id, :age_id, :category_id, :language_id, :release_date, :page_count, :price, :image, :emp_id)
");

$stmt_insertBook->execute([
	':title' => $bookTitle,
	':description' => $bookDescription,
	':publisher_id' => $bookPublisher,
	':age_id' => $bookAgeRecommendation,
	':category_id' => $bookCategory,
	':series_id' => $bookSeries,
	':language_id' => $bookLanguage,
	':release_date' => $bookReleseDate,
	':page_count' => $bookPageCount,
	':price' => $bookPrice,
	':image' => $bookImage,
	':emp_id' => $_SESSION['uid']
]);

// Get the last inserted book ID
$bookId = $pdo->lastInsertId();

// Insert genres
foreach ($bookGenre as $genreId) {
	$stmt_insertGenre = $pdo->prepare("INSERT INTO book_genres (book_fk, genre_fk) VALUES (:book_id, :genre_id)");
	$stmt_insertGenre->execute([':book_id' => $bookId, ':genre_id' => $genreId]);
}

// Insert authors
foreach ($bookAuthor as $authorId) {
	$stmt_insertAuthor = $pdo->prepare("INSERT INTO book_author (book_fk, author_fk) VALUES (:book_id, :author_id)");
	$stmt_insertAuthor->execute([':book_id' => $bookId, ':author_id' => $authorId]);
}

// Insert illustrators
foreach ($bookIllustrator as $illustratorId) {
	$stmt_insertIllustrator = $pdo->prepare("INSERT INTO book_illustrators (book_fk, illustrator_fk) VALUES (:book_id, :illustrator_id)");
	$stmt_insertIllustrator->execute([':book_id' => $bookId, ':illustrator_id' => $illustratorId]);
}

// Redirect or provide success message
echo 'Book added successfully!';
}

function editBook($pdo){
	// Sanitize and collect inputs
	$bookTitle = cleanInput($_POST['bookTitleEdit']);
	$bookDescription = cleanInput($_POST['bookDescriptionEdit']);
	$bookReleseDate = $_POST['releaseDateEdit'];
	$bookPageCount = $_POST['pageCountEdit'];
	$bookPrice = $_POST['priceEdit'];
	$bookId = $_POST['bookIdEdit'];
	

	$stmt_updateBook = $pdo->prepare("
    UPDATE books
    SET 
        book_title = :title,
        book_summary = :description,
        book_publishing_date = :release_date,
        book_side_count = :page_count,
        book_price = :price
    WHERE 
        book_id = :book_id
	");

	$stmt_updateBook->execute([
		':title' => $bookTitle,
		':description' => $bookDescription,
		':release_date' => $bookReleseDate,
		':page_count' => $bookPageCount,
		':price' => $bookPrice,
		':book_id' => $bookId
	]);

	echo 'Book edited successfully!';
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

// Function to search books directly from the database
function searchBooksForTypeahead(PDO $pdo, string $query): array {
    $sql = "
        SELECT 
            b.book_id, 
            b.book_title, 
            b.book_img, 
            b.book_price,
            a.author_name
        FROM 
            books b
        JOIN 
            book_author ba ON b.book_id = ba.book_fk
        JOIN 
            authors a ON ba.author_fk = a.author_id
        WHERE 
            b.book_title LIKE :query1 
    		OR a.author_name LIKE :query2
        LIMIT 10;
    ";

    // Prepare and execute the query with bound parameter
    $stmt = $pdo->prepare($sql);

    // Make sure the query is bound with proper syntax
    $stmt->execute(['query1' => "%$query%", 'query2' => "%$query%"]);

    // Fetch all the matching results
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
	$stmt_addPublisher->bindParam(":pubName" ,$pubName, PDO::PARAM_STR);
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

//Adds a new age recomendation to db
function addAgeRec($pdo, $ageRecName, $redirectTo = 'book-editor.php') {
    $stmt_addAgeRec = $pdo->prepare("INSERT INTO book_age_rec (age_value) VALUES (:ageName)");
	$stmt_addAgeRec->bindParam(":ageName" ,$ageRecName, PDO::PARAM_STR);
    if($stmt_addAgeRec->execute()){
		// Store the success message in the session
        $_SESSION['message'] = 'Age Recomendation added successfully';
        
        // Redirect to the dynamic location with a success flag
        header('Location: ' . $redirectTo . '?age-rec-success');
		exit;
	}
	else{
		return "ERROR";
	}
}

//Adds a new age recomendation to db
function addCategory($pdo, $catName, $redirectTo = 'book-editor.php') {
    $stmt_addCategory = $pdo->prepare("INSERT INTO book_categories (cat_name) VALUES (:catName)");
	$stmt_addCategory->bindParam(":catName" ,$catName, PDO::PARAM_STR);
    if($stmt_addCategory->execute()){
		// Store the success message in the session
        $_SESSION['message'] = 'Category added successfully';
        
        // Redirect to the dynamic location with a success flag
        header('Location: ' . $redirectTo . '?cat-success');
		exit;
	}
	else{
		return "ERROR";
	}
}

//Adds a new age recomendation to db
function addSeries($pdo, $seriesName, $redirectTo = 'book-editor.php') {
    $stmt_addSeries = $pdo->prepare("INSERT INTO book_series (series_name) VALUES (:seriesName)");
	$stmt_addSeries->bindParam(":seriesName" ,$seriesName, PDO::PARAM_STR);
    if($stmt_addSeries->execute()){
		// Store the success message in the session
        $_SESSION['message'] = 'Book series added successfully';
        
        // Redirect to the dynamic location with a success flag
        header('Location: ' . $redirectTo . '?series-success');
		exit;
	}
	else{
		return "ERROR";
	}
}

//Adds a new age recomendation to db
function addLanguage($pdo, $langName, $redirectTo = 'book-editor.php') {
    $stmt_addLanguage = $pdo->prepare("INSERT INTO book_languages (lang_name) VALUES (:langName)");
	$stmt_addLanguage->bindParam(":langName" ,$langName, PDO::PARAM_STR);
    if($stmt_addLanguage->execute()){
		// Store the success message in the session
        $_SESSION['message'] = 'Language added successfully';
        
        // Redirect to the dynamic location with a success flag
        header('Location: ' . $redirectTo . '?lang-success');
		exit;
	}
	else{
		return "ERROR";
	}
}
?>