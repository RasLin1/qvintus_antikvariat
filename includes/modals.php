<?php 


// Query to select all authors from db
$stmt_fetchAuthors = $pdo->query("SELECT * FROM authors");
$authors = $stmt_fetchAuthors->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['formType']) && $_POST['formType'] === 'addAuthor') {
    $addAuthor = addAuthor($pdo, $_POST['authorName']);
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
                    <div class="mb-3">
                        <label for="bookTitle" class="form-label">Book Title</label>
                        <input type="text" name="bookTitle" class="form-control" id="bookTitle" required>
                    </div>
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
<!-- Initialize Select2 and Modal handling -->
<script>
$(document).ready(function () {
    // Initialize Select2 for the author and genre dropdowns
    $('#addBookModal').on('shown.bs.modal', function () {
        $('#bookAuthor').select2({
            placeholder: 'Select an author',
            allowClear: true,
            dropdownParent: $('#addBookModal'),
        });

        $('#bookGenre').select2({
            placeholder: 'Select a genre',
            allowClear: true,
            dropdownParent: $('#addBookModal'),
        });
    });

    // Close any open dropdowns when modal is reopened
    $('#addBookModal').on('hidden.bs.modal', function () {
        $('#bookAuthor').select2('close'); // Ensure the dropdown isn't left open
        $('#bookGenre').select2('close');
    });

    // Handle adding a new author
    $('#addAuthorForm').on('submit', function (e) {
    var authorName = $('#authorName').val();

    if (authorName) {
        var newAuthorId = Date.now();
        var newOption = new Option(authorName, newAuthorId, false, false);

        $('#bookAuthor').append(newOption).trigger('change');

        // Do not prevent default; allow the form to submit
        return; // Ends the function, but form submission continues
    }
});

    // Handle adding a new genre
    $('#addGenreForm').on('submit', function (e) {
        var genreName = $('#genreName').val();

        if (genreName) {
            var newGenreId = Date.now(); // Temporary unique ID for the new genre
            var newOption = new Option(genreName, newGenreId, false, false);

            // Add new genre to the Select2 dropdown
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