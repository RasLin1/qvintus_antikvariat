<?php 

// Query's for everything that requires a foreign key except employee number
$stmt_fetchAuthors = $pdo->query("SELECT * FROM authors");
$authors = $stmt_fetchAuthors->fetchAll(PDO::FETCH_ASSOC);

$stmt_fetchIllustrators = $pdo->query("SELECT * FROM illustrators");
$illustrators = $stmt_fetchIllustrators->fetchAll(PDO::FETCH_ASSOC);

$stmt_fetchPublishers = $pdo->query("SELECT * FROM publishers");
$publishers = $stmt_fetchPublishers->fetchAll(PDO::FETCH_ASSOC);

$stmt_fetchGenres = $pdo->query("SELECT * FROM genres");
$genres = $stmt_fetchGenres->fetchAll(PDO::FETCH_ASSOC);

$stmt_fetchAgeRecommendations = $pdo->query("SELECT * FROM book_age_rec");
$ageRecommendations = $stmt_fetchAgeRecommendations->fetchAll(PDO::FETCH_ASSOC);

$stmt_fetchCategories = $pdo->query("SELECT * FROM 	book_categories");
$categories = $stmt_fetchCategories->fetchAll(PDO::FETCH_ASSOC);

$stmt_fetchSeries = $pdo->query("SELECT * FROM 	book_series");
$series = $stmt_fetchSeries->fetchAll(PDO::FETCH_ASSOC);

$stmt_fetchLanguages = $pdo->query("SELECT * FROM book_languages");
$languages = $stmt_fetchLanguages->fetchAll(PDO::FETCH_ASSOC);

$stmt_fetchBooks = $pdo->query("SELECT * FROM books b JOIN book_author ba ON b.book_id = ba.book_fk JOIN authors a ON ba.author_fk = a.author_id");
$allBooks = $stmt_fetchBooks->fetchAll(PDO::FETCH_ASSOC);

$stmt_fetchFeatItemTypes = $pdo->query("SELECT * FROM featured_item_types");
$featItemTypes = $stmt_fetchFeatItemTypes->fetchAll(PDO::FETCH_ASSOC);

$stmt_fetchUserRoles = $pdo->query("SELECT * FROM employee_roles");
$userRoles = $stmt_fetchUserRoles->fetchAll(PDO::FETCH_ASSOC);


if (isset($_POST['addNewUser'])) {
    // Assuming the form's POST data is available
    $username = $_POST['username'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $role = $_POST['role'];

    // Call the function to create the user
    $result = createUser($pdo, $username, $firstName, $lastName, $password, $confirmPassword, $role);
}

if (isset($_POST['editExistingUser'])) {
    // Assuming the form's POST data is available
    $userId = $_POST['userId'];
    $username = $_POST['username'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $role = $_POST['role'];

    // Call the function to create the user
    $result = updateUser($pdo, $userId, $username, $firstName, $lastName, $password, $confirmPassword, $role);
}

// Handle adding new authors, illustrators, publishers, genres etc.
if (isset($_POST['formType']) && $_POST['formType'] === 'addAuthor') {
    $cleanAuthorName = cleanInput($_POST['authorName']);
    $addAuthor = universalInsert($pdo, 'authors', ['author_name'], [$cleanAuthorName]);
}
if (isset($_POST['formType']) && $_POST['formType'] === 'addIllustrator') {
    $cleanIllName = cleanInput($_POST['illustratorName']);
    $addIllustrator = universalInsert($pdo, 'illustrators', ['illustrator_name'], [$cleanIllName]);
}
if (isset($_POST['formType']) && $_POST['formType'] === 'addPublisher') {
    $cleanPubName = cleanInput($_POST['publisherName']);
    $addPublisher = universalInsert($pdo, 'publishers', ['pub_name'], [$cleanPubName]);
}
if (isset($_POST['formType']) && $_POST['formType'] === 'addGenre') {
    $cleanGenreName = cleanInput($_POST['genreName']);
    $addGenre = universalInsert($pdo, 'genres', ['genre_name'], [$cleanGenreName]);
}

if (isset($_POST['formType']) && $_POST['formType'] === 'addAgeRecommendation') {
    $cleanAgeName = cleanInput($_POST['ageRecommendationName']);
    $addAgeRec = universalInsert($pdo, 'book_age_rec', ['age_value'], [$cleanAgeName]);
}

if (isset($_POST['formType']) && $_POST['formType'] === 'addCategory') {
    $cleanCatName = cleanInput($_POST['categoryName']);
    $addCategory = universalInsert($pdo, 'book_categories', ['cat_name'], [$cleanCatName]);
}

if (isset($_POST['formType']) && $_POST['formType'] === 'addSeries') {
    $cleanSeriesName = cleanInput($_POST['seriesName']);
    $addSeries = universalInsert($pdo, 'book_series', ['series_name'], [$cleanSeriesName]);
}

if (isset($_POST['formType']) && $_POST['formType'] === 'addLanguage') {
    $cleanLangName = cleanInput($_POST['languageName']);
    $addSeries = universalInsert($pdo, 'book_languages', ['lang_name'], [$cleanLangName]);
}

// Handle adding new book
if (isset($_POST['formType']) && $_POST['formType'] === 'addBook') {
    $addBook = addBook($pdo);
}

if (isset($_POST['formType']) && $_POST['formType'] === 'editBook') {
    $addBook = updateBook($pdo);
}

if (isset($_POST['formType']) && $_POST['formType'] === 'addFeatItem') {
    $addFeatItem = addFeatItem($pdo, 'front-page-editor.php');
}


?>

<!-- Add Book Modal -->
<div class="modal fade" id="addBookModal" tabindex="-1" aria-labelledby="addBookModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="formType" value="addBook">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBookModalLabel">Add New Book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Book Title -->
                    <div class="mb-3">
                        <label for="bookTitle" class="form-label">Book Title</label>
                        <input type="text" name="bookTitle" class="form-control" id="bookTitle" required>
                    </div>

                    <!-- Book Description -->
                    <div class="mb-3">
                        <label for="bookDescription" class="form-label">Book Description</label>
                        <textarea name="bookDescription" class="form-control" id="bookDescription" rows="3" required></textarea>
                    </div>

                    <!-- Genre -->
                    <div class="mb-3">
                        <label for="bookGenre" class="form-label">Genre</label>
                        <select name="bookGenre[]" class="form-control" id="bookGenre" multiple="multiple" style="width: 100%;">
                            <?php foreach ($genres as $genre): ?>
                                <option value="<?php echo htmlspecialchars($genre['genre_id']); ?>">
                                    <?php echo htmlspecialchars($genre['genre_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addGenreModal">
                            Add New Genre
                        </button>
                    </div>

                    <!-- Author -->
                    <div class="mb-3">
                        <label for="bookAuthor" class="form-label">Author</label>
                        <select name="bookAuthor[]" class="form-control" id="bookAuthor" multiple="multiple" style="width: 100%;">
                            <?php foreach ($authors as $author): ?>
                                <option value="<?php echo htmlspecialchars($author['author_id']); ?>">
                                    <?php echo htmlspecialchars($author['author_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addAuthorModal">
                            Add New Author
                        </button>
                    </div>

                    <!-- Illustrator -->
                    <div class="mb-3">
                        <label for="bookIllustrator" class="form-label">Illustrator</label>
                        <select name="bookIllustrator[]" class="form-control" id="bookIllustrator" multiple="multiple" style="width: 100%;">
                            <?php foreach ($illustrators as $illustrator): ?>
                                <option value="<?php echo htmlspecialchars($illustrator['illustrator_id']); ?>">
                                    <?php echo htmlspecialchars($illustrator['illustrator_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addIllustratorModal">
                            Add New Illustrator
                        </button>
                    </div>

                    <!-- Publisher -->
                    <div class="mb-3">
                        <label for="bookPublisher" class="form-label">Publisher</label>
                        <select name="bookPublisher" class="form-control" id="bookPublisher" style="width: 100%;">
                            <option value="" disabled selected>Select a publisher</option> <!-- Empty default option -->
                            <?php foreach ($publishers as $publisher): ?>
                                <option value="<?php echo htmlspecialchars($publisher['pub_id']); ?>">
                                    <?php echo htmlspecialchars($publisher['pub_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addPublisherModal">
                            Add New Publisher
                        </button>
                    </div>

                    <!-- Age Recommendation -->
                    <div class="mb-3">
                        <label for="bookAgeRecommendation" class="form-label">Age Recommendation</label>
                        <select name="bookAgeRecommendation" class="form-control" id="bookAgeRecommendation" style="width: 100%;">
                            <option value="" disabled selected>Select an age recommendation</option>
                            <?php foreach ($ageRecommendations as $age): ?>
                                <option value="<?php echo htmlspecialchars($age['age_id']); ?>">
                                    <?php echo htmlspecialchars($age['age_value']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addAgeRecommendationModal">
                            Add New Age Recommendation
                        </button>
                    </div>

                    <!-- Category -->
                    <div class="mb-3">
                        <label for="bookCategory" class="form-label">Category</label>
                        <select name="bookCategory" class="form-control" id="bookCategory" style="width: 100%;">
                            <option value="" disabled selected>Select a category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category['cat_id']); ?>">
                                    <?php echo htmlspecialchars($category['cat_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                            Add New Category
                        </button>
                    </div>

                    <!-- Series -->
                    <div class="mb-3">
                        <label for="bookSeries" class="form-label">Series</label>
                        <select name="bookSeries" class="form-control" id="bookSeries" style="width: 100%;">
                            <option value="" disabled selected>Select a series</option>
                            <?php foreach ($series as $seriesItem): ?>
                                <option value="<?php echo htmlspecialchars($seriesItem['series_id']); ?>">
                                    <?php echo htmlspecialchars($seriesItem['series_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addSeriesModal">
                            Add New Series
                        </button>
                    </div>

                    <!-- Language -->
                    <div class="mb-3">
                        <label for="bookLanguage" class="form-label">Language</label>
                        <select name="bookLanguage" class="form-control" id="bookLanguage" style="width: 100%;">
                            <option value="" disabled selected>Select a language</option>
                            <?php foreach ($languages as $language): ?>
                                <option value="<?php echo htmlspecialchars($language['lang_id']); ?>">
                                    <?php echo htmlspecialchars($language['lang_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addLanguageModal">
                            Add New Language
                        </button>
                    </div>

                    <!-- Book Relese Date -->
                    <div class="mb-3">
                        <label for="bookReleseDate" class="form-label">Book Relese Date</label>
                        <input type="date" name="bookReleseDate" class="form-control" id="bookReleseDate" required>
                    </div>

                    <!-- Book Page Count -->
                    <div class="mb-3">
                        <label for="bookPageCount" class="form-label">Book Page Count</label>
                        <input type="number" name="bookPageCount" class="form-control" id="bookPageCount" required>
                    </div>

                    <!-- Book Price -->
                    <div class="mb-3">
                        <label for="bookPrice" class="form-label">Book Price</label>
                        <input type="number" name="bookPrice" class="form-control" id="bookPrice" step=".01" required>
                    </div>

                    <!-- Book Image -->
                    <div class="mb-3">
                        <label for="fileToUpload" class="form-label">Book Image</label>
                        <input type="file" name="fileToUpload" class="form-control" id="fileToUpload" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="action" value="addBook" class="btn btn-primary">Save Book</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editBookModal" tabindex="-1" aria-labelledby="editBookModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="formType" value="editBook">
                <input type="hidden" name="bookIdEdit" id="bookIdEdit">
                <div class="modal-header">
                    <h5 class="modal-title" id="editBookModalLabel">Edit Book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Book Title -->
                    <div class="mb-3">
                        <label for="bookTitleEdit" class="form-label">Book Title</label>
                        <input type="text" name="bookTitleEdit" class="form-control" id="bookTitleEdit" required>
                    </div>

                    <!-- Book Description -->
                    <div class="mb-3">
                        <label for="bookDescriptionEdit" class="form-label">Book Description</label>
                        <textarea name="bookDescriptionEdit" class="form-control" id="bookDescriptionEdit" rows="3" required></textarea>
                    </div>

                    <!-- Genre -->
                    <div class="mb-3">
                        <label for="bookGenreEdit" class="form-label">Genre</label>
                        <select name="bookGenreEdit[]" class="form-control" id="bookGenreEdit" multiple="multiple" style="width: 100%;">
                            <?php foreach ($genres as $genre): ?>
                                <option value="<?php echo htmlspecialchars($genre['genre_id']); ?>">
                                    <?php echo htmlspecialchars($genre['genre_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Author -->
                    <div class="mb-3">
                        <label for="bookAuthorEdit" class="form-label">Author</label>
                        <select name="bookAuthorEdit[]" class="form-control" id="bookAuthorEdit" multiple="multiple" style="width: 100%;">
                            <?php foreach ($authors as $author): ?>
                                <option value="<?php echo htmlspecialchars($author['author_id']); ?>">
                                    <?php echo htmlspecialchars($author['author_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Illustrator -->
                    <div class="mb-3">
                        <label for="bookIllustratorEdit" class="form-label">Illustrator</label>
                        <select name="bookIllustratorEdit[]" class="form-control" id="bookIllustratorEdit" multiple="multiple" style="width: 100%;">
                            <?php foreach ($illustrators as $illustrator): ?>
                                <option value="<?php echo htmlspecialchars($illustrator['illustrator_id']); ?>">
                                    <?php echo htmlspecialchars($illustrator['illustrator_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Publisher -->
                    <div class="mb-3">
                        <label for="bookPublisherEdit" class="form-label">Publisher</label>
                        <select name="bookPublisherEdit" class="form-control" id="bookPublisherEdit" style="width: 100%;">
                            <option value="" disabled selected>Select a publisher</option> <!-- Empty default option -->
                            <?php foreach ($publishers as $publisher): ?>
                                <option value="<?php echo htmlspecialchars($publisher['pub_id']); ?>">
                                    <?php echo htmlspecialchars($publisher['pub_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addPublisherModal">
                            Add New Publisher
                        </button>
                    </div>

                    <!-- Age Recommendation -->
                    <div class="mb-3">
                        <label for="bookAgeRecommendationEdit" class="form-label">Age Recommendation</label>
                        <select name="bookAgeRecommendationEdit" class="form-control" id="bookAgeRecommendationEdit" style="width: 100%;">
                            <option value="" disabled selected>Select an age recommendation</option>
                            <?php foreach ($ageRecommendations as $age): ?>
                                <option value="<?php echo htmlspecialchars($age['age_id']); ?>">
                                    <?php echo htmlspecialchars($age['age_value']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addAgeRecommendationModal">
                            Add New Age Recommendation
                        </button>
                    </div>

                    <!-- Category -->
                    <div class="mb-3">
                        <label for="bookCategoryEdit" class="form-label">Category</label>
                        <select name="bookCategoryEdit" class="form-control" id="bookCategoryEdit" style="width: 100%;">
                            <option value="" disabled selected>Select a category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category['cat_id']); ?>">
                                    <?php echo htmlspecialchars($category['cat_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                            Add New Category
                        </button>
                    </div>

                    <!-- Series -->
                    <div class="mb-3">
                        <label for="bookSeriesEdit" class="form-label">Series</label>
                        <select name="bookSeriesEdit" class="form-control" id="bookSeriesEdit" style="width: 100%;">
                            <option value="" disabled selected>Select a series</option>
                            <?php foreach ($series as $seriesItem): ?>
                                <option value="<?php echo htmlspecialchars($seriesItem['series_id']); ?>">
                                    <?php echo htmlspecialchars($seriesItem['series_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addSeriesModal">
                            Add New Series
                        </button>
                    </div>

                    <!-- Language -->
                    <div class="mb-3">
                        <label for="bookLanguageEdit" class="form-label">Language</label>
                        <select name="bookLanguageEdit" class="form-control" id="bookLanguageEdit" style="width: 100%;">
                            <option value="" disabled selected>Select a language</option>
                            <?php foreach ($languages as $language): ?>
                                <option value="<?php echo htmlspecialchars($language['lang_id']); ?>">
                                    <?php echo htmlspecialchars($language['lang_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addLanguageModal">
                            Add New Language
                        </button>
                    </div>

                    <!-- Book Relese Date -->
                    <div class="mb-3">
                        <label for="bookReleseDateEdit" class="form-label">Book Relese Date</label>
                        <input type="date" name="bookReleseDateEdit" class="form-control" id="bookReleseDateEdit" required>
                    </div>

                    <!-- Book Page Count -->
                    <div class="mb-3">
                        <label for="bookPageCountEdit" class="form-label">Book Page Count</label>
                        <input type="number" name="bookPageCountEdit" class="form-control" id="bookPageCountEdit" required>
                    </div>

                    <!-- Book Price -->
                    <div class="mb-3">
                        <label for="bookPriceEdit" class="form-label">Book Price</label>
                        <input type="number" name="bookPriceEdit" class="form-control" id="bookPriceEdit" step=".01" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="action" value="editBook" id="editBookSubmit" name="editBookSubmit" class="btn btn-primary">Update Book</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Author Modal -->
<div class="modal fade" id="addAuthorModal" tabindex="-1" aria-labelledby="addAuthorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="POST" id="addAuthorForm">
                <input type="hidden" name="formType" value="addAuthor">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAuthorModalLabel">Add New Author</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="authorName" class="form-label">Author Name</label>
                        <input type="text" name="authorName" class="form-control" id="authorName" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="addAuthor" value="Add Author" class="btn btn-secondary">Save Author</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Illustrator Modal -->
<div class="modal fade" id="addIllustratorModal" tabindex="-1" aria-labelledby="addIllustratorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="POST" id="addIllustratorForm">
                <input type="hidden" name="formType" value="addIllustrator">
                <div class="modal-header">
                    <h5 class="modal-title" id="addIllustratorModalLabel">Add New Illustrator</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="illustratorName" class="form-label">Illustrator Name</label>
                        <input type="text" name="illustratorName" class="form-control" id="illustratorName" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="addIllustrator" value="Add Illustrator" class="btn btn-secondary">Save Illustrator</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Publisher Modal -->
<div class="modal fade" id="addPublisherModal" tabindex="-1" aria-labelledby="addPublisherModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="POST" id="addPublisherForm">
                <input type="hidden" name="formType" value="addPublisher">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPublisherModalLabel">Add New Publisher</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="publisherName" class="form-label">Publisher Name</label>
                        <input type="text" name="publisherName" class="form-control" id="publisherName" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="addPublisher" value="Add Publisher" class="btn btn-secondary">Save Publisher</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Genre Modal -->
<div class="modal fade" id="addGenreModal" tabindex="-1" aria-labelledby="addGenreModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="POST" id="addGenreForm">
                <input type="hidden" name="formType" value="addGenre">
                <div class="modal-header">
                    <h5 class="modal-title" id="addGenreModalLabel">Add New Genre</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="genreName" class="form-label">Genre Name</label>
                        <input type="text" name="genreName" class="form-control" id="genreName" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="addGenre" value="Add Genre" class="btn btn-secondary">Save Genre</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Age Recommendation Modal -->
<div class="modal fade" id="addAgeRecommendationModal" tabindex="-1" aria-labelledby="addAgeRecommendationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="POST" id="addAgeRecommendationForm">
                <input type="hidden" name="formType" value="addAgeRecommendation">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAgeRecommendationModalLabel">Add New Age Recommendation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="ageRecommendationName" class="form-label">Age Recommendation Name</label>
                        <input type="text" name="ageRecommendationName" class="form-control" id="ageRecommendationName" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="addAgeRecommendation" value="Add Age Recommendation" class="btn btn-secondary">Save Age Recommendation</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="POST" id="addCategoryForm">
                <input type="hidden" name="formType" value="addCategory">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModalLabel">Add New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="categoryName" class="form-label">Category Name</label>
                        <input type="text" name="categoryName" class="form-control" id="categoryName" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="addCategory" value="Add Category" class="btn btn-secondary">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Series Modal -->
<div class="modal fade" id="addSeriesModal" tabindex="-1" aria-labelledby="addSeriesModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="POST" id="addSeriesForm">
                <input type="hidden" name="formType" value="addSeries">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSeriesModalLabel">Add New Series</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="seriesName" class="form-label">Series Name</label>
                        <input type="text" name="seriesName" class="form-control" id="seriesName" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="addSeries" value="Add Series" class="btn btn-secondary">Save Series</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Language Modal -->
<div class="modal fade" id="addLanguageModal" tabindex="-1" aria-labelledby="addLanguageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="POST" id="addLanguageForm">
                <input type="hidden" name="formType" value="addLanguage">
                <div class="modal-header">
                    <h5 class="modal-title" id="addLanguageModalLabel">Add New Language</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="languageName" class="form-label">Language Name</label>
                        <input type="text" name="languageName" class="form-control" id="languageName" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="addLanguage" value="Add Language" class="btn btn-secondary">Save Language</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal for adding featured items to front page -->
<div class="modal fade" id="addFeatItemModal" tabindex="-1" aria-labelledby="addFeatItemModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="POST" id="addFeatItemForm">
                <input type="hidden" name="formType" value="addFeatItem">
                <div class="modal-header">
                    <h5 class="modal-title" id="addFeatItemModalLabel">Add New Featured Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="featCategory" class="form-label">Genre</label>
                        <select name="featCategory" class="form-control" id="featCategory" style="width: 100%;">
                            <option value="" selected>Select a genre</option>
                            <?php foreach ($genres as $genre): ?>
                                <option value="<?php echo htmlspecialchars($genre['genre_id']); ?>">
                                    <?php echo htmlspecialchars($genre['genre_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="featBook" class="form-label">Book</label>
                        <select name="featBook" class="form-control select2" id="featBook" style="width: 100%;">
                            <option value="" selected>Select a book</option>
                            <?php foreach ($allBooks as $books): ?>
                                <option value="<?php echo htmlspecialchars($books['book_id']); ?>">
                                    <?php echo htmlspecialchars($books['book_title']) . " - " . htmlspecialchars($books['author_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="featType" class="form-label">Item Type</label>
                        <select name="featType" class="form-control" id="featType" style="width: 100%;">
                            <option value="" disabled selected>Select a type</option>
                            <?php foreach ($featItemTypes as $types): ?>
                                <option value="<?php echo htmlspecialchars($types['type_id']); ?>">
                                    <?php echo htmlspecialchars($types['type_name'])?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="addFeatItem" value="Add Featured Item" class="btn btn-primary">Save Featured Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createUserModalLabel">Create New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <!-- Username -->
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" id="username" class="form-control" required>
                    </div>
                    <!-- First Name -->
                    <div class="mb-3">
                        <label for="firstName" class="form-label">First Name</label>
                        <input type="text" name="firstName" id="firstName" class="form-control" required>
                    </div>
                    <!-- Last Name -->
                    <div class="mb-3">
                        <label for="lastName" class="form-label">Last Name</label>
                        <input type="text" name="lastName" id="lastName" class="form-control" required>
                    </div>
                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>
                    <!-- Confirm Password -->
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Confirm Password</label>
                        <input type="password" name="confirmPassword" id="confirmPassword" class="form-control" required>
                    </div>
                    <!-- Role -->
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select name="role" id="role" class="form-select" required>
                            <option value="" disabled selected>Select a role</option>
                            <?php foreach ($userRoles as $role): ?>
                                <option value="<?php echo htmlspecialchars($role['role_id']); ?>">
                                    <?php echo htmlspecialchars($role['role_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="addNewUser" id="addNewUser" class="btn btn-primary">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <!-- Hidden User ID -->
                    <input type="hidden" name="userId" id="userId">

                    <!-- Username -->
                    <div class="mb-3">
                        <label for="editUsername" class="form-label">Username</label>
                        <input type="text" name="username" id="editUsername" class="form-control" required>
                    </div>
                    <!-- First Name -->
                    <div class="mb-3">
                        <label for="editFirstName" class="form-label">First Name</label>
                        <input type="text" name="firstName" id="editFirstName" class="form-control" required>
                    </div>
                    <!-- Last Name -->
                    <div class="mb-3">
                        <label for="editLastName" class="form-label">Last Name</label>
                        <input type="text" name="lastName" id="editLastName" class="form-control" required>
                    </div>
                    <!-- Password -->
                    <div class="mb-3">
                        <label for="editPassword" class="form-label">Password (Leave blank to keep current password)</label>
                        <input type="password" name="password" id="editPassword" class="form-control">
                    </div>
                    <!-- Confirm Password -->
                    <div class="mb-3">
                        <label for="editConfirmPassword" class="form-label">Confirm Password</label>
                        <input type="password" name="confirmPassword" id="editConfirmPassword" class="form-control">
                    </div>
                    <!-- Role -->
                    <div class="mb-3">
                        <label for="editRole" class="form-label">Role</label>
                        <select name="role" id="editRole" class="form-select" required>
                            <option value="" disabled>Select a role</option>
                            <?php foreach ($userRoles as $role): ?>
                                <option value="<?php echo htmlspecialchars($role['role_id']); ?>">
                                    <?php echo htmlspecialchars($role['role_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="editExistingUser" id="editExistingUser" class="btn btn-primary">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Include Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

<script>

    
$(document).ready(function () {
    // Initialize Select2 for dropdowns in the Add Book Modal
    $('#addBookModal').on('shown.bs.modal', function () {
        $('#bookAuthor, #bookGenre, #bookIllustrator').select2({
            placeholder: 'Select an option',
            allowClear: true,
            dropdownParent: $('#addBookModal'), // Ensure dropdown stays within the modal
        });

        // Initialize Select2 for the Publisher dropdown as single-select
        $('#bookPublisher').select2({
            placeholder: 'Select a publisher',
            allowClear: true,
            multiple: false, // Only allow single selection for Publisher
            dropdownParent: $('#addBookModal'), // Ensure dropdown stays within the modal
        });
    });

    $(document).ready(function() {
        // Initialize Select2 on the select element
        $('#featBook').select2({
            placeholder: 'Select a book',
            allowClear: true, // Optional: Adds a clear button
            multiple: false, // Only allow single selection for Publisher
            dropdownParent: $('#addFeatItemModal'), // Ensure dropdown stays within the modal
        });
    });

    // Adjust dropdown positioning manually on scroll
    function adjustDropdownPosition() {
        $('#bookAuthor, #bookGenre, #bookIllustrator, #bookPublisher').each(function () {
            const select2Container = $(this).data('select2').$dropdown; // Access the dropdown
            if (select2Container.is(':visible')) {
                $(this).select2('close'); // Close dropdown to force recalculation
                $(this).select2('open'); // Reopen dropdown to adjust position
            }
        });
    }

    // Trigger position adjustment on modal scroll
    $('#addBookModal').on('scroll', adjustDropdownPosition);

    // Ensure correct dropdown position when opened
    $('#bookAuthor, #bookGenre, #bookIllustrator, #bookPublisher').on('select2:open', function () {
        const select2Container = $(this).data('select2').$dropdown;
        const modalOffset = $('#addBookModal').offset().top;
        const elementOffset = $(this).offset().top;
        const scrollTop = $('#addBookModal').scrollTop();

        const topPosition = elementOffset - modalOffset + scrollTop + $(this).outerHeight();
        select2Container.css('top', topPosition + 'px'); // Manually adjust dropdown position
    });

    // Handle Add Author Form Submission
    $('#addAuthorForm').on('submit', function (e) {
        var authorName = $('#authorName').val();

        if (authorName) {
            var newAuthorId = Date.now(); // Temporary unique ID
            var newOption = new Option(authorName, newAuthorId, false, false);

            $('#bookAuthor').append(newOption).trigger('change');

            return; // Allow form to submit if needed
        }
    });

    // Handle Add Genre Form Submission
    $('#addGenreForm').on('submit', function (e) {
        var genreName = $('#genreName').val();

        if (genreName) {
            var newGenreId = Date.now(); // Temporary unique ID for the new genre
            var newOption = new Option(genreName, newGenreId, false, false);

            $('#bookGenre').append(newOption).trigger('change');

            return;
        }
    });

    // Handle Add Illustrator Form Submission
    $('#addIllustratorForm').on('submit', function (e) {
        var illustratorName = $('#illustratorName').val();

        if (illustratorName) {
            var newIllustratorId = Date.now(); // Temporary unique ID
            var newOption = new Option(illustratorName, newIllustratorId, false, false);

            $('#bookIllustrator').append(newOption).trigger('change');

            return;
        }
    });

    // Handle Add Publisher Form Submission
    $('#addPublisherForm').on('submit', function (e) {
        var publisherName = $('#publisherName').val();

        if (publisherName) {
            var newPublisherId = Date.now(); // Temporary unique ID
            var newOption = new Option(publisherName, newPublisherId, false, false);

            $('#bookPublisher').append(newOption).trigger('change');

            return;
        }
    });
});


$(document).on('click', '.edit-book-btn', function () {
    // Get book data from data attributes
    const bookId = $(this).data('book-id');
    const bookTitle = $(this).data('book-title');
    const bookSummary = $(this).data('book-summary');
    const bookPublishingDate = $(this).data('book-publishing-date');
    const bookSideCount = $(this).data('book-side-count');
    const bookPrice = $(this).data('book-price');
    const bookPublisher = $(this).data('book-publisher');
    const bookLanguage = $(this).data('book-language');
    const bookCategory = $(this).data('book-category');
    const bookAgeRec = $(this).data('book-age-rec');
    const bookSeries = $(this).data('book-series');
    const bookGenres = $(this).data('book-genres');
    const bookAuthors = $(this).data('book-authors');
    const bookIllustrators = $(this).data('book-illustrators');

    // Populate multiple select fields using Select2 (ensure you initialize Select2 for these fields)
    const genreArray = bookGenres ? String(bookGenres).split(',') : [];
    const authorArray = bookAuthors ? String(bookAuthors).split(',') : [];
    const illustratorArray = bookIllustrators ? String(bookIllustrators).split(',') : [];

    // Populate hidden input for bookId
    $('#editBookModal #bookIdEdit').val(bookId);

    // Populate modal fields
    $('#editBookModal #bookTitleEdit').val(bookTitle);
    $('#editBookModal #bookDescriptionEdit').val(bookSummary);
    $('#editBookModal #bookReleseDateEdit').val(bookPublishingDate);
    $('#editBookModal #bookPageCountEdit').val(bookSideCount);
    $('#editBookModal #bookPriceEdit').val(bookPrice);
    $('#editBookModal #bookPublisherEdit').val(bookPublisher).change();
    $('#editBookModal #bookLanguageEdit').val(bookLanguage).change();
    $('#editBookModal #bookCategoryEdit').val(bookCategory).change();
    $('#editBookModal #bookAgeRecommendationEdit').val(bookAgeRec).change();
    $('#editBookModal #bookSeriesEdit').val(bookSeries).change();

    // Populate genres, authors, illustrators (assuming these are arrays of options)
    $('#bookGenreEdit').val(bookGenres).trigger('change');
    $('#bookAuthorEdit').val(bookAuthors).trigger('change');
    $('#bookIllustratorEdit').val(bookIllustrators).trigger('change');

    // Re-initialize select2
    $('#bookGenreEdit').select2();
    $('#bookAuthorEdit').select2();
    $('#bookIllustratorEdit').select2();
});

$('#bookGenreEdit, #bookAuthorEdit, #bookIllustratorEdit').select2({
    width: '100%',
    placeholder: 'Select options',
    allowClear: true
});



document.querySelectorAll('.edit-user-btn').forEach(button => {
    button.addEventListener('click', () => {
        const userId = button.getAttribute('data-user-id');
        const username = button.getAttribute('data-username');
        const firstName = button.getAttribute('data-first-name');
        const lastName = button.getAttribute('data-last-name');
        const role = button.getAttribute('data-role');

        // Populate the modal fields
        document.getElementById('userId').value = userId;
        document.getElementById('editUsername').value = username;
        document.getElementById('editFirstName').value = firstName;
        document.getElementById('editLastName').value = lastName;
        document.getElementById('editRole').value = role;

        // Show the modal
        const modalElement = document.getElementById('editUserModal');
        const modalInstance = bootstrap.Modal.getOrCreateInstance(modalElement);
        modalInstance.show();
    });
});


</script>

<style>
    /* Optional: Improve the dropdown's appearance inside the modal */
.select2-container--bootstrap .select2-dropdown {
    border: 1px solid #ccc;
    border-radius: 4px;
    background: white;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* Adjust modal scrolling behavior */
#addBookModal {
    overflow-y: auto; /* Ensure the modal scrolls */
    position: relative; /* Prevent dropdown misalignment */
}

#addBookModal .modal-body {
    max-height: 70vh; /* Limit modal content height */
    overflow-y: auto; /* Enable vertical scrolling */
}

/* Adjust modal scrolling behavior */
#editBookModal {
    overflow-y: auto; /* Ensure the modal scrolls */
    position: relative; /* Prevent dropdown misalignment */
}

#editBookModal .modal-body {
    max-height: 70vh; /* Limit modal content height */
    overflow-y: auto; /* Enable vertical scrolling */
}
    
.select2-results__option {
    color: black; /* Text color */
    background-color: white; /* Background color */
}

.select2-results__option--highlighted {
    background-color: #e0e0e0; /* Highlighted option background color */
    color: black; /* Highlighted option text color */
}
</style>