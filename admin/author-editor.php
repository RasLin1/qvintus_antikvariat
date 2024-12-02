<?php
include_once '../includes/emp-header.php';

checkUserRole(3, "book-editor.php");

$stmt_fetchAuthors = $pdo->query("SELECT * FROM authors");
$authors = $stmt_fetchAuthors->fetchAll(PDO::FETCH_ASSOC);

if(isset($_POST['deleteAuthor'])){
    $authId = $_POST['deleteAuthorId']; // Assume the book ID is passed from a form
    $message = deleteIllorAuth($pdo, $authId, "book_author", "author_fk", "authors", "author_id");
        if (isset($message)): ?>
            <div class="alert alert-info"><?= htmlspecialchars($message); ?></div>
        <?php endif;
}


?>

<div class="container" id="author_editor_area">
<h3 class="my-4">Author Editor</h3>
    <div class="row" id="author_">
    <?php 
    foreach ($authors as $author) {
        // Generate card HTML for each book
        echo '
        <div class="col-12 col-md-6 col-lg-2 mb-4 mx-4 d-flex justify-content-center">
            <div class="card book-card flex-fill">
                <h5 class="card-title my-2">' . htmlspecialchars($author['author_name']) . '</h5>
                <div class="card-footer d-flex justify-content-center align-items-center">
                    <form method="POST">
                        <input type="hidden" name="deleteAuthorId" id="deleteAuthorId" value="' . htmlspecialchars($author['author_id']) . '"/>
                        <input type="submit" id="deleteAuthor" name="deleteAuthor" class="btn btn-danger my-1" value="Delete Author"/>
                    </form>
                </div>
            </div>
        </div>';
    }
    ?>
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

<?php 
include_once '../includes/emp-footer.php';
?>
