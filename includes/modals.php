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


/*if (isset($_GET['book_id'])) {
    $book_id = intval($_GET['book_id']);

    // Example function to fetch book data
    $book = getBookById($book_id); 

    if ($book) {
        // Ensure valid JSON is returned
        header('Content-Type: application/json');
        echo json_encode($book);
    } else {
        // Handle case when no book is found
        http_response_code(404);
        echo json_encode(['error' => 'Book not found']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid or missing book ID']);
}*/

// Handle adding new authors, illustrators, publishers, genres etc.
if (isset($_POST['formType']) && $_POST['formType'] === 'addAuthor') {
    $cleanData['authorName'] = cleanInput($_POST['authorName']);
    $addAuthor = addAuthor($pdo, $cleanData['authorName']);
}
if (isset($_POST['formType']) && $_POST['formType'] === 'addIllustrator') {
    $cleanData['illustratorName'] = cleanInput($_POST['illustratorName']);
    $addIllustrator = addIllustrator($pdo, $cleanData['illustratorName']);
}
if (isset($_POST['formType']) && $_POST['formType'] === 'addPublisher') {
    $cleanData['publisherName'] = cleanInput($_POST['publisherName']);
    $addPublisher = addPublisher($pdo, $cleanData['publisherName']);
}
if (isset($_POST['formType']) && $_POST['formType'] === 'addGenre') {
    $cleanData['genreName'] = cleanInput($_POST['genreName']);
    $addGenre = addGenre($pdo, $cleanData['genreName']);
}

if (isset($_POST['formType']) && $_POST['formType'] === 'addAgeRecommendation') {
    $addAgeRec = addAgeRec($pdo, $_POST['ageRecommendationName']);
}

if (isset($_POST['formType']) && $_POST['formType'] === 'addCategory') {
    $addCategory = addCategory($pdo, $_POST['categoryName']);
}

if (isset($_POST['formType']) && $_POST['formType'] === 'addSeries') {
    $addSeries = addSeries($pdo, $_POST['seriesName']);
}

if (isset($_POST['formType']) && $_POST['formType'] === 'addLanguage') {
    $addLanguage = addLanguage($pdo, $_POST['languageName']);
}

// Handle adding new book
if (isset($_POST['formType']) && $_POST['formType'] === 'addBook') {
    $addBook = addBook($pdo);
}

if (isset($_POST['formType']) && $_POST['formType'] === 'addFeatItem') {
    $addFeatItem = addFeatItem($pdo);
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

                            <!-- Book editing modal, can't edit neither genres, author nor illustrator -->
<div class="modal fade" id="editBookModal" tabindex="-1" aria-labelledby="editBookModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="POST">
                <input type="hidden" name="bookIdEdit" id="bookIdEdit">
                <div class="modal-header">
                    <h5 class="modal-title" id="editBookModalLabel">Edit Book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Book Title -->
                    <div class="mb-3">
                        <label for="bookTitleEdit" class="form-label">Book Title</label>
                        <input type="text" name="bookTitleEdit" id="bookTitleEdit" class="form-control" required>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="bookDescriptionEdit" class="form-label">Description</label>
                        <textarea name="bookDescriptionEdit" id="bookDescriptionEdit" class="form-control" rows="3" required></textarea>
                    </div>

                    <!-- Release Date -->
                    <div class="mb-3">
                        <label for="releaseDateEdit" class="form-label">Release Date</label>
                        <input type="date" name="releaseDateEdit" id="releaseDateEdit" class="form-control" required>
                    </div>

                    <!-- Page Count -->
                    <div class="mb-3">
                        <label for="pageCountEdit" class="form-label">Page Count</label>
                        <input type="number" name="pageCountEdit" id="pageCountEdit" class="form-control" required>
                    </div>

                    <!-- Price -->
                    <div class="mb-3">
                        <label for="priceEdit" class="form-label">Price</label>
                        <input type="number" name="priceEdit" id="priceEdit" step="0.01" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="editBookSubmit" class="btn btn-primary">Save Changes</button>
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
<div class="modal fade" id="addFeatItemModal" tabindex="-1" aria-labelledby="addFeatItemModalLabel" aria-hidden="true">
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

function populateAndOpenModal(data) {
    // Populate the modal inputs
    document.getElementById('bookIdEdit').value = data.book_id;
    document.getElementById('bookTitleEdit').value = data.book_title;
    document.getElementById('bookDescriptionEdit').value = data.book_summary;
    document.getElementById('releaseDateEdit').value = data.book_publishing_date;
    document.getElementById('pageCountEdit').value = data.book_side_count;
    document.getElementById('priceEdit').value = data.book_price;

    // Open the modal
    var modal = new bootstrap.Modal(document.getElementById('editBookModal'));
    modal.show();
}

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