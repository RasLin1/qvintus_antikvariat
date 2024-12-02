<?php
include_once '../includes/emp-header.php';

checkUserRole(3, "book-editor.php");

$stmt_fetchGenres = $pdo->query("SELECT * FROM genres");
$genres = $stmt_fetchGenres->fetchAll(PDO::FETCH_ASSOC);

if(isset($_POST['deleteGenre'])){
    $genreId = $_POST['deleteGenreId']; // Assume the book ID is passed from a form
    $message = deleteGenre($pdo, $genreId);
        if (isset($message)): ?>
            <div class="alert alert-info"><?= htmlspecialchars($message); ?></div>
        <?php endif;
}


?>

<div class="container" id="genre_editor_area">
<h3 class="my-4">Genre Editor</h3>
    <div class="row" id="genre_area">
    <?php 
    foreach ($genres as $genre) {
        // Generate card HTML for each book
        echo '
        <div class="col-12 col-md-6 col-lg-2 mb-4 mx-4 d-flex justify-content-center">
            <div class="card book-card flex-fill" style="height: ; overflow: hidden;">
                <h5 class="card-title my-2">' . htmlspecialchars($genre['genre_name']) . '</h5>
                <div class="card-footer d-flex justify-content-center align-items-center">
                    <form method="POST">
                        <input type="hidden" name="deleteGenreId" id="deleteGenreId" value="' . htmlspecialchars($genre['genre_id']) . '"/>
                        <input type="submit" id="deleteGenre" name="deleteGenre" class="btn btn-danger my-1" value="Delete Genre"/>
                    </form>
                </div>
            </div>
        </div>';
    }
    ?>
    </div>
    <div class="row" id="add_area">
        <div class="container">
            
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGenreModal">
                Add New Genre
            </button>
        </div>
        <?php include '../includes/modals.php'; ?>
    </div>
</div>

<?php 
include_once '../includes/emp-footer.php';
?>
