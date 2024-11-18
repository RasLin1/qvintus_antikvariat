<?php 

// Query to select all authors, illustrators, publishers, and genres from the db
$stmt_fetchAuthors = $pdo->query("SELECT * FROM authors");
$authors = $stmt_fetchAuthors->fetchAll(PDO::FETCH_ASSOC);

$stmt_fetchIllustrators = $pdo->query("SELECT * FROM illustrators");
$illustrators = $stmt_fetchIllustrators->fetchAll(PDO::FETCH_ASSOC);

$stmt_fetchPublishers = $pdo->query("SELECT * FROM publishers");
$publishers = $stmt_fetchPublishers->fetchAll(PDO::FETCH_ASSOC);

// Handle adding new authors, illustrators, publishers, and genres
if (isset($_POST['formType']) && $_POST['formType'] === 'addAuthor') {
    $addAuthor = addAuthor($pdo, $_POST['authorName']);
}
if (isset($_POST['formType']) && $_POST['formType'] === 'addIllustrator') {
    $addIllustrator = addIllustrator($pdo, $_POST['illustratorName']);
}
if (isset($_POST['formType']) && $_POST['formType'] === 'addPublisher') {
    $addPublisher = addPublisher($pdo, $_POST['publisherName']);
}
if (isset($_POST['formType']) && $_POST['formType'] === 'addGenre') {
    $addGenre = addGenre($pdo, $_POST['genreName']);
}

// Query to select all genres from db
$stmt_fetchGenres = $pdo->query("SELECT * FROM genres");
$genres = $stmt_fetchGenres->fetchAll(PDO::FETCH_ASSOC);

?>

<!-- Add Book Modal -->
<div class="modal fade" id="addBookModal" tabindex="-1" aria-labelledby="addBookModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="POST">
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

                    <!-- Genre -->
                    <div class="mb-3">
                        <label for="bookGenre" class="form-label">Genre</label>
                        <select name="bookGenre" class="form-control" id="bookGenre" multiple="multiple" style="width: 100%;">
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
                        <select name="bookAuthor" class="form-control" id="bookAuthor" multiple="multiple" style="width: 100%;">
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
                        <select name="bookIllustrator" class="form-control" id="bookIllustrator" multiple="multiple" style="width: 100%;">
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
                        <select name="bookPublisher" class="form-control" id="bookPublisher" multiple="multiple" style="width: 100%;">
                            <?php foreach ($publishers as $publisher): ?>
                                <option value="<?php echo htmlspecialchars($publisher['publisher_id']); ?>">
                                    <?php echo htmlspecialchars($publisher['publisher_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addPublisherModal">
                            Add New Publisher
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="action" value="addBook" class="btn btn-primary">Save Book</button>
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

<!-- Include Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

<script>
$(document).ready(function () {
    // Initialize Select2 for the dropdowns
    $('#addBookModal').on('shown.bs.modal', function () {
        $('#bookAuthor, #bookIllustrator, #bookPublisher, #bookGenre').select2({
            placeholder: 'Select an option',
            allowClear: true,
            dropdownParent: $('#addBookModal'),
        });
    });

    // Handle adding a new author
    $('#addAuthorForm').on('submit', function (e) {
        var authorName = $('#authorName').val();

        if (authorName) {
            var newAuthorId = Date.now(); 
            var newOption = new Option(authorName, newAuthorId, false, false);

            $('#bookAuthor').append(newOption).trigger('change');
            return;
        }
    });

    // Handle adding a new illustrator
    $('#addIllustratorForm').on('submit', function (e) {
        var illustratorName = $('#illustratorName').val();

        if (illustratorName) {
            var newIllustratorId = Date.now(); 
            var newOption = new Option(illustratorName, newIllustratorId, false, false);

            $('#bookIllustrator').append(newOption).trigger('change');
            return;
        }
    });

    // Handle adding a new publisher
    $('#addPublisherForm').on('submit', function (e) {
        var publisherName = $('#publisherName').val();

        if (publisherName) {
            var newPublisherId = Date.now(); 
            var newOption = new Option(publisherName, newPublisherId, false, false);

            $('#bookPublisher').append(newOption).trigger('change');
            return;
        }
    });

    // Handle adding a new genre
    $('#addGenreForm').on('submit', function (e) {
        var genreName = $('#genreName').val();

        if (genreName) {
            var newGenreId = Date.now(); // Temporary unique ID
            var newOption = new Option(genreName, newGenreId, false, false);

            $('#bookGenre').append(newOption).trigger('change');
            return;
        }
    });
});
</script>

<style>
.select2-results__option {
    color: black; /* Text color */
    background-color: white; /* Background color */
}

.select2-results__option--highlighted {
    background-color: #e0e0e0; /* Highlighted option background color */
    color: black; /* Highlighted option text color */
}
</style>
