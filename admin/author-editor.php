<?php
include_once '../includes/emp-header.php';

checkUserRole(3, "book-editor.php");

// Initially fetch the authors for display
$stmt_fetchAuthors = $pdo->query("SELECT * FROM authors");
$authors = $stmt_fetchAuthors->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['deleteAuthor'])) {
    $authId = $_POST['deleteAuthorId']; // Assume the author ID is passed from a form
    $message = deleteObject($pdo, $authId, "book_author", "author_fk", "authors", "author_id");
    if (isset($message)): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message); ?></div>
    <?php endif;
}
?>

<div class="container" id="author_editor_area">
    <h3 class="my-4" id="site-title">Author Editor</h3>
    <div class="row" id="author_">
        <!-- Author Cards will be dynamically loaded here -->
    </div>
    <div class="row" id="add_area">
        <div class="container">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAuthorModal">
                Add New Author
            </button>
        </div>
        <?php include '../includes/modals.php'; ?>
    </div>
</div>

<script>
// Fetch the authors from the server and display them dynamically
document.addEventListener('DOMContentLoaded', fetchAuthors);

function fetchAuthors() {
    fetch('../includes/dynamicAJAX/ajax_handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ action: 'fetch', type: 'authors' }) // Fetch authors
    })
    .then(response => response.json())
    .then(data => {
        if (data.data) {
            const authorContainer = document.getElementById('author_');
            authorContainer.innerHTML = ''; // Clear previous content
            data.data.forEach(author => {
                authorContainer.innerHTML += `
                    <div class="col-12 col-md-6 col-lg-2 mb-4 mx-4 d-flex justify-content-center">
                        <div class="card book-card flex-fill">
                            <h5 class="card-title my-2">${author.author_name}</h5>
                            <div class="card-footer d-flex justify-content-center align-items-center">
                                <button class="btn btn-danger my-1" onclick="deleteAuthor(${author.author_id})">Delete Author</button>
                            </div>
                        </div>
                    </div>`;
            });
        }
    })
    .catch(err => console.error('Error fetching authors:', err));
}

// Add new author to the database
function addAuthor() {
    const authorName = document.getElementById('authorName').value;

    fetch('../includes/dynamicAJAX/ajax_handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            action: 'insert',
            type: 'authors',
            data: JSON.stringify({
                columns: ['author_name'],
                values: [authorName],
                redirectTo: ['author-editor.php']
            })
        })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            fetchAuthors(); // Reload authors after adding
            document.getElementById('authorName').value = ''; // Clear input
        }
    })
    .catch(err => console.error('Error adding author:', err));
}

// Delete an author from the database
function deleteAuthor(authorId) {
    fetch('../includes/dynamicAJAX/ajax_handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            action: 'delete',
            type: 'author',  // This should be the entity type like 'author', 'publisher', etc.
            id: authorId,    // The ID of the author to be deleted
            depTab: 'book_author', // Table that holds foreign key references
            depCol: 'author_fk', // Column name in dependent table
            tab: 'authors', // The main table
            col: 'author_id' // Column name in the main table
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            fetchAuthors(); // Reload authors after deletion
        }
    })
    .catch(err => console.error('Error deleting author:', err));
}
</script>

<style>
#site-title {
    color: white; 
}
</style>

<?php 
include_once '../includes/emp-footer.php';
?>