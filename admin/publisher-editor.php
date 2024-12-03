<?php
include_once '../includes/emp-header.php';

checkUserRole(3, "book-editor.php");

$stmt_fetchPublishers = $pdo->query("SELECT * FROM publishers");
$publishers = $stmt_fetchPublishers->fetchAll(PDO::FETCH_ASSOC);

if(isset($_POST['deletePublisher'])){
    $pubId = $_POST['deletePublisherId']; // Assume the book ID is passed from a form
    $message = deleteObject($pdo, $pubId, "books", "publisher_fk", "publishers", "pub_id");
        if (isset($message)): ?>
            <div class="alert alert-info"><?= htmlspecialchars($message); ?></div>
        <?php endif;
}


?>

<div class="container" id="publisher_editor_area">
<h3 class="my-4">Publisher Editor</h3>
    <div class="row justify-content-center" id="publisher_">
    <?php 
    foreach ($publishers as $pub) {
        // Generate card HTML for each book
        echo '
        <div class="col-12 col-md-4 col-lg-3 mb-4 mx-2 d-flex justify-content-center">
            <div class="card book-card flex-fill">
                <h5 class="card-title my-2">' . htmlspecialchars($pub['pub_name']) . '</h5>
                <div class="card-footer d-flex justify-content-center align-items-center">
                    <form method="POST">
                        <input type="hidden" name="deletePublisherId" id="deletePublisherId" value="' . htmlspecialchars($pub['pub_id']) . '"/>
                        <input type="submit" id="deletePublisher" name="deletePublisher" class="btn btn-danger my-1" value="Delete illustrator"/>
                    </form>
                </div>
            </div>
        </div>';
    }
    ?>
    </div>
    <div class="row" id="add_area">
        <div class="container">
            
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPublisherModal">
                Add New Publisher 
            </button>
        </div>
        <?php include '../includes/modals.php'; ?>
    </div>
</div>

<?php 
include_once '../includes/emp-footer.php';
?>