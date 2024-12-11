<?php
ob_start();


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

function fetchAllFromTable($pdo, $tableName) {
    // Define allowed table names for validation
    $allowedTables = ['authors', 'genres', 'illustrators', 'publishers', 'books', 'book_categories']; // Add your table names here

    if (!in_array($tableName, $allowedTables)) {
        throw new Exception("Invalid table name: " . htmlspecialchars($tableName));
    }

    try {
        $stmt_fetchAll = $pdo->query("SELECT * FROM $tableName");
        return $stmt_fetchAll->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching from table $tableName: " . $e->getMessage());
        return [];
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

function createUser(PDO $pdo, $username, $firstName, $lastName, $password, $confirmPassword, $role)
{
    // Check if the passwords match
    if ($password !== $confirmPassword) {
        return "Error: Passwords do not match.";
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Prepare the insert query
    $sql = "INSERT INTO employee (emp_uname, emp_fname, emp_lname, emp_pass, emp_role_fk) 
            VALUES (:username, :firstName, :lastName, :password, :role)";
    
    $stmt = $pdo->prepare($sql);

    // Bind the parameters
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':firstName', $firstName, PDO::PARAM_STR);
    $stmt->bindParam(':lastName', $lastName, PDO::PARAM_STR);
    $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
    $stmt->bindParam(':role', $role, PDO::PARAM_INT);

    // Execute the query
    if ($stmt->execute()) {
        return "User created successfully!";
    } else {
        return "Error: Could not create user.";
    }
}

function updateUser(PDO $pdo, $userId, $username, $firstName, $lastName, $password, $confirmPassword, $role)
{
    // Check if passwords are provided and match
    if (!empty($password) || !empty($confirmPassword)) {
        if ($password !== $confirmPassword) {
            return "Error: Passwords do not match.";
        }
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    }

    // Prepare the update query
    $sql = "UPDATE employee 
            SET emp_uname = :username, emp_fname = :firstName, emp_lname = :lastName, 
                emp_role_fk = :role" . (!empty($password) ? ", emp_pass = :password" : "") . " 
            WHERE emp_id = :userId";

    $stmt = $pdo->prepare($sql);

    // Bind parameters
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':firstName', $firstName, PDO::PARAM_STR);
    $stmt->bindParam(':lastName', $lastName, PDO::PARAM_STR);
    $stmt->bindParam(':role', $role, PDO::PARAM_INT);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    if (!empty($password)) {
        $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
    }

    // Execute the query
    if ($stmt->execute()) {
        return "User updated successfully!";
    } else {
        return "Error: Could not update user.";
    }
}

function deleteUser($pdo, $userId, $redirectTo) {
	$stmt_deleteUser = $pdo->prepare('DELETE FROM employee WHERE emp_id = :uid');
    $stmt_deleteUser->bindParam(":uid", $userId, PDO::PARAM_INT);
    if ($stmt_deleteUser->execute()) {
        // Store the success message in the session
        $_SESSION['message'] = 'User deleted successfully';
        
        // Redirect to the dynamic location with a success flag
        header('Location: ' . $redirectTo . '?user-del-success');
        exit;
    } else {
        return "ERROR: Failed to delete user.";
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

}

function updateBook($pdo) {
    // Sanitize and collect inputs
    $bookId = $_POST['bookIdEdit'];
    $bookTitle = cleanInput($_POST['bookTitleEdit']);
    $bookDescription = cleanInput($_POST['bookDescriptionEdit']);
    $bookGenre = isset($_POST['bookGenreEdit']) ? $_POST['bookGenreEdit'] : [];
    $bookAuthor = isset($_POST['bookAuthorEdit']) ? $_POST['bookAuthorEdit'] : [];
    $bookIllustrator = isset($_POST['bookIllustratorEdit']) ? $_POST['bookIllustratorEdit'] : [];
    $bookPublisher = cleanInput($_POST['bookPublisherEdit']);
    $bookAgeRecommendation = cleanInput($_POST['bookAgeRecommendationEdit']);
    $bookCategory = cleanInput($_POST['bookCategoryEdit']);
    $bookSeries = cleanInput($_POST['bookSeriesEdit']);
    $bookLanguage = cleanInput($_POST['bookLanguageEdit']);
    $bookReleaseDate = cleanInput($_POST['bookReleseDateEdit']);
    $bookPageCount = cleanInput($_POST['bookPageCountEdit']);
    $bookPrice = cleanInput($_POST['bookPriceEdit']);

    // Prepare the update query
    $stmt_updateBook = $pdo->prepare("
        UPDATE books
        SET 
            book_title = :title, 
            book_summary = :description,
            publisher_fk = :publisher_id, 
            book_age_rec_fk = :age_id, 
            book_category_fk = :category_id, 
            book_language_fk = :language_id, 
            book_publishing_date = :release_date, 
            book_side_count = :page_count, 
            book_price = :price,
            book_series_fk = :series_id
        WHERE book_id = :book_id
    ");

    $stmt_updateBook->execute([
        ':title' => $bookTitle,
        ':description' => $bookDescription,
        ':publisher_id' => $bookPublisher,
        ':age_id' => $bookAgeRecommendation,
        ':category_id' => $bookCategory,
        ':language_id' => $bookLanguage,
        ':release_date' => $bookReleaseDate,
        ':page_count' => $bookPageCount,
        ':price' => $bookPrice,
        ':series_id' => $bookSeries,
        ':book_id' => $bookId
    ]);

    // Delete current genres, authors, and illustrators to avoid duplication
    $stmt_deleteGenres = $pdo->prepare("DELETE FROM book_genres WHERE book_fk = :book_id");
    $stmt_deleteGenres->execute([':book_id' => $bookId]);

    $stmt_deleteAuthors = $pdo->prepare("DELETE FROM book_author WHERE book_fk = :book_id");
    $stmt_deleteAuthors->execute([':book_id' => $bookId]);

    $stmt_deleteIllustrators = $pdo->prepare("DELETE FROM book_illustrators WHERE book_fk = :book_id");
    $stmt_deleteIllustrators->execute([':book_id' => $bookId]);

    // Re-insert new genres
    foreach ($bookGenre as $genreId) {
        // Check if the genre exists before inserting
        $stmt_checkGenre = $pdo->prepare("SELECT COUNT(*) FROM genres WHERE genre_id = :genre_id");
        $stmt_checkGenre->execute([':genre_id' => $genreId]);
        if ($stmt_checkGenre->fetchColumn() > 0) {
            $stmt_insertGenre = $pdo->prepare("INSERT INTO book_genres (book_fk, genre_fk) VALUES (:book_id, :genre_id)");
            $stmt_insertGenre->execute([':book_id' => $bookId, ':genre_id' => $genreId]);
        }
    }

    // Re-insert new authors
    foreach ($bookAuthor as $authorId) {
        // Check if the author exists before inserting
        $stmt_checkAuthor = $pdo->prepare("SELECT COUNT(*) FROM authors WHERE author_id = :author_id");
        $stmt_checkAuthor->execute([':author_id' => $authorId]);
        if ($stmt_checkAuthor->fetchColumn() > 0) {
            $stmt_insertAuthor = $pdo->prepare("INSERT INTO book_author (book_fk, author_fk) VALUES (:book_id, :author_id)");
            $stmt_insertAuthor->execute([':book_id' => $bookId, ':author_id' => $authorId]);
        }
    }

    // Re-insert new illustrators
    foreach ($bookIllustrator as $illustratorId) {
        // Check if the illustrator exists before inserting
        $stmt_checkIllustrator = $pdo->prepare("SELECT COUNT(*) FROM illustrators WHERE illustrator_id = :illustrator_id");
        $stmt_checkIllustrator->execute([':illustrator_id' => $illustratorId]);
        if ($stmt_checkIllustrator->fetchColumn() > 0) {
            $stmt_insertIllustrator = $pdo->prepare("INSERT INTO book_illustrators (book_fk, illustrator_fk) VALUES (:book_id, :illustrator_id)");
            $stmt_insertIllustrator->execute([':book_id' => $bookId, ':illustrator_id' => $illustratorId]);
        }
    }
}

function deleteBook($pdo, $bookId) {
    try {
        // Begin a transaction
        $pdo->beginTransaction();

        // Retrieve the image file name
        $stmt_image = $pdo->prepare("SELECT book_img FROM books WHERE book_id = :book_id");
        $stmt_image->execute([':book_id' => $bookId]);
        $book = $stmt_image->fetch(PDO::FETCH_ASSOC);

        if ($book && !empty($book['book_img'])) {
            $imagePath = "../assets/img/" . $book['book_img']; // Adjust the path as needed
            if (file_exists($imagePath)) {
                unlink($imagePath); // Delete the file
            }
        }

        // Delete dependencies in book_genres
        $stmt_genres = $pdo->prepare("DELETE FROM book_genres WHERE book_fk = :book_id");
        $stmt_genres->execute([':book_id' => $bookId]);

        // Delete dependencies in book_illustrators
        $stmt_illustrators = $pdo->prepare("DELETE FROM book_illustrators WHERE book_fk = :book_id");
        $stmt_illustrators->execute([':book_id' => $bookId]);

        // Delete dependencies in book_authors
        $stmt_authors = $pdo->prepare("DELETE FROM book_author WHERE book_fk = :book_id");
        $stmt_authors->execute([':book_id' => $bookId]);

        // Delete dependencies in featured_items
        $stmt_featured = $pdo->prepare("DELETE FROM featured_items WHERE book_fk = :book_id");
        $stmt_featured->execute([':book_id' => $bookId]);

        // Delete the book itself
        $stmt_book = $pdo->prepare("DELETE FROM books WHERE book_id = :book_id");
        $stmt_book->execute([':book_id' => $bookId]);

        // Commit the transaction
        $pdo->commit();

        return true; // Indicate success
    } catch (PDOException $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        error_log("Error deleting book: " . $e->getMessage());
        return false; // Indicate failure
    }
}

// Function to search books directly from the database
function searchBooksForTypeahead(PDO $pdo, string $query): array {
    $sql = "
    SELECT 
        b.book_id,
        b.book_title,
        b.book_summary,
        b.book_publishing_date,
        b.book_side_count,
        b.book_price,
        b.book_img,
        b.publisher_fk,
        b.book_language_fk,
        b.book_category_fk,
        b.book_age_rec_fk,
        b.book_series_fk,
        a.author_name,
        GROUP_CONCAT(DISTINCT g.genre_id) AS genres,
        GROUP_CONCAT(DISTINCT a.author_id) AS authors,
        GROUP_CONCAT(DISTINCT i.illustrator_id) AS illustrators
    FROM books b
    LEFT JOIN book_genres bg ON b.book_id = bg.book_fk
    LEFT JOIN genres g ON bg.genre_fk = g.genre_id
    LEFT JOIN book_author ba ON b.book_id = ba.book_fk
    LEFT JOIN authors a ON ba.author_fk = a.author_id
    LEFT JOIN book_illustrators bi ON b.book_id = bi.book_fk
    LEFT JOIN illustrators i ON bi.illustrator_fk = i.illustrator_id
    LEFT JOIN book_series bs ON bs.series_id = b.book_series_fk
    LEFT JOIN publishers p ON p.pub_id = b.publisher_fk
    WHERE 
        (:query IS NULL OR 
         b.book_title LIKE :query1 OR 
         a.author_name LIKE :query2 OR 
         bs.series_name LIKE :query3 OR 
         p.pub_name LIKE :query4)
    GROUP BY b.book_id
    LIMIT 24
";


    // Prepare the SQL query
    $stmt_fetchAllBookData = $pdo->prepare($sql);

    // Bind parameters
    $queryParam = $query !== '' ? "%$query%" : null;
    $stmt_fetchAllBookData->execute([
        'query'  => $queryParam,
        'query1' => $queryParam,
        'query2' => $queryParam,
        'query3' => $queryParam,
        'query4' => $queryParam
    ]);

    // Fetch all matching results
    return $stmt_fetchAllBookData->fetchAll(PDO::FETCH_ASSOC);
}

function universalInsert($pdo, $tableName, $columns, $values, $redirectTo = null) {
    try {
        // Build the placeholders for the prepared statement
        $placeholders = array_map(function($col) {
            return ":" . $col;
        }, $columns);

        // Construct the SQL query dynamically
        $sql = "INSERT INTO $tableName (" . implode(", ", $columns) . ") VALUES (" . implode(", ", $placeholders) . ")";

        // Prepare the statement
        $stmt_universalInsert = $pdo->prepare($sql);

        // Bind each value to its corresponding placeholder
        foreach ($columns as $index => $column) {
            $stmt_universalInsert->bindValue(":" . $column, $values[$index]);
        }

        // Execute the query
        if ($stmt_universalInsert->execute()) {
            // Redirect after successful insert
            if ($redirectTo) {
                $_SESSION['message'] = 'Record added successfully';
                header('Location: ' . $redirectTo .'add-success'); // Redirect to the given page
                exit;
            }
            return true; // Return true if successful and no redirect
        } else {
            return false; // Return false if the query fails
        }
    } catch (Exception $e) {
        error_log("Error in universalInsert: " . $e->getMessage());
        return false; // Handle any exceptions gracefully
    }
}

function canDelete($pdo, $objectId, $table, $columnName) {
    try {
        // Prepare the query
        $stmt_count = $pdo->prepare("
            SELECT COUNT(*) AS count 
            FROM $table 
            WHERE $columnName = :id
        ");
        $stmt_count->execute([':id' => $objectId]);
        $result = $stmt_count->fetch(PDO::FETCH_ASSOC);

        // Deletion is allowed only if the count is 0
        return $result['count'] == 0;
    } catch (Exception $e) {
        error_log("Error in canDelete function: " . $e->getMessage());
        return false; // Fail safe: assume cannot delete in case of an error
    }
}

function deleteGenre($pdo, $genreId) {
    if (!canDelete($pdo, $genreId, "book_genres", "genre_fk")) {
        return "Cannot delete genre; it is associated with existing books.";
    }

    try {
        $pdo->beginTransaction();

        // Delete from featured_items
        $stmt_deleteFeatured = $pdo->prepare("DELETE FROM featured_items WHERE genre_fk = :genre_id");
        $stmt_deleteFeatured->execute([':genre_id' => $genreId]);

        // Delete the genre itself
        $stmt_deleteGenre = $pdo->prepare("DELETE FROM genres WHERE genre_id = :genre_id");
        $stmt_deleteGenre->execute([':genre_id' => $genreId]);

        $pdo->commit();
        return "Genre deleted successfully.";
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Error deleting genre: " . $e->getMessage());
        return "Failed to delete genre.";
    }
}

function deleteObject($pdo, $objectId, $tableDependencyName, $columnDependencyName, $tableName, $columnName) {
    try {
        // Check if the table/column names are valid to prevent SQL injection
        $allowedTables = ['illustrators', 'authors', 'book_illustrators', 'book_author', 'publishers', 'book_categories', 'books']; // Allowed tables
        $allowedColumns = ['illustrator_id', 'author_id', 'illustrator_fk', 'author_fk', 'pub_id', 'publisher_fk', 'book_category_fk', 'cat_id']; // Allowed columns

        if (!in_array($tableName, $allowedTables) || !in_array($columnName, $allowedColumns)) {
            throw new Exception("Invalid table or column name.");
        }


        if (!canDelete($pdo, $objectId, $tableDependencyName, $columnDependencyName)) {
        return "Cannot delete object; it is associated with existing books.";
        }

        // Begin the transaction
        $pdo->beginTransaction();

        // Delete the object itself
        $stmt_delete = $pdo->prepare("DELETE FROM $tableName WHERE $columnName = :object_id");
        $stmt_delete->execute([':object_id' => $objectId]);

        // Commit the transaction
        $pdo->commit();
        return "Object deleted successfully.";
    } catch (Exception $e) {
        // Roll back in case of error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("Error deleting object: " . $e->getMessage());
        return "Failed to delete object.";
    }
}

function addFeatItem($pdo, $redirectLink) {
    $itemType = $_POST['featType'];
    $genreFk = $_POST['featCategory'];
    $bookFk = $_POST['featBook'];

    // Sanitize input for possible null values
    $genreFk = empty($genreFk) ? null : $genreFk;
    $bookFk = empty($bookFk) ? null : $bookFk;

    // Step 1: Fetch the max limit for the item type from the `featured_item_type_limits` table
    $stmt_getLimit = $pdo->prepare("SELECT type_max_limit FROM featured_item_type_limits WHERE type_id = :typeFk");
    $stmt_getLimit->bindParam(":typeFk", $itemType, PDO::PARAM_INT);
    $stmt_getLimit->execute();
    $limitResult = $stmt_getLimit->fetch(PDO::FETCH_ASSOC);

    // Check if a limit was found
    if (!$limitResult) {
        $_SESSION['message'] = "Error: No limit found for this item type.";
        header('Location: '. $redirectLink .'?feat-item-limit-error');
        exit;
    }

    $maxItems = $limitResult['type_max_limit'];  // Get the max limit for the current item type

    // Step 2: Check how many items of the same type already exist in the `featured_items` table
    $stmt_checkLimit = $pdo->prepare("SELECT COUNT(*) FROM featured_items WHERE feat_item_type_fk = :typeFk");
    $stmt_checkLimit->bindParam(":typeFk", $itemType, PDO::PARAM_INT);
    $stmt_checkLimit->execute();
    $currentCount = $stmt_checkLimit->fetchColumn();

    // Step 3: If the current count exceeds the maximum, prevent insert
    if ($currentCount > $maxItems) {
        $_SESSION['message'] = "Error: You can only have up to $maxItems featured items of this type.";
        header('Location: '. $redirectLink .'?feat-item-limit-exceeded');
        exit;

    }
    elseif ($currentCount < $maxItems){
    // Step 4: Proceed with inserting the new featured item if the limit is not exceeded
    $stmt_addFeatItem = $pdo->prepare("INSERT INTO featured_items (feat_item_type_fk, genre_fk, book_fk) VALUES (:typeFk, :genreFk, :bookFk)");
    $stmt_addFeatItem->bindParam(":typeFk", $itemType, PDO::PARAM_INT);
    $stmt_addFeatItem->bindParam(":genreFk", $genreFk, PDO::PARAM_INT);
    $stmt_addFeatItem->bindParam(":bookFk", $bookFk, PDO::PARAM_INT);

    // Step 5: Execute the insert and check for success
    if ($stmt_addFeatItem->execute()) {
        // Store the success message in the session
        $_SESSION['message'] = 'Featured Item added successfully';
        header('Location: '. $redirectLink .'?feat-item-add-success');
        exit;
    } else {
        return "ERROR: Failed to add Featured Item.";
    }}
}

function deleteFeatItem($pdo, $itemId, $redirectTo) {
	$stmt_deleteFeatItem = $pdo->prepare('DELETE FROM featured_items WHERE feat_item_id = :itemId');
    $stmt_deleteFeatItem->bindParam(":itemId", $itemId, PDO::PARAM_INT);
    if ($stmt_deleteFeatItem->execute()) {
        // Store the success message in the session
        $_SESSION['message'] = 'Featured Item deleted successfully';
        
        // Redirect to the dynamic location with a success flag
        header('Location: ' . $redirectTo . '?feat-item-del-success');
        exit;
    } else {
        return "ERROR: Failed to delete Featured Item.";
    }
}

function updateFrontpageText($pdo, $contId, $contData, $redirectTo) {
	$stmt_uppdateFrontPageText = $pdo->prepare("UPDATE front_page_content SET cont_data = :contData WHERE cont_id = :contId");
    $stmt_uppdateFrontPageText->bindParam(":contData", $contData, PDO::PARAM_INT);
    $stmt_uppdateFrontPageText->bindParam(":contId", $contId, PDO::PARAM_INT);
    if ($stmt_uppdateFrontPageText->execute()) {
        // Store the success message in the session
        $_SESSION['message'] = 'Data edited successfully';
        
        // Redirect to the dynamic location with a success flag
        header('Location: ' . $redirectTo . '?data-edit-success');
        exit;
    } else {
        return "ERROR: Failed to delete Featured Item.";
    }
}