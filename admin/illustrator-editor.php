<?php
include_once '../includes/emp-header.php';

checkUserRole(3, "book-editor.php");

$stmt_fetchIllustrators = $pdo->query("SELECT * FROM illustrators");
$illustrators = $stmt_fetchIllustrators->fetchAll(PDO::FETCH_ASSOC);

if(isset($_POST['deleteIllustrator'])){
    $illId = $_POST['deleteIllustratorId']; // Assume the book ID is passed from a form
    $message = deleteObject($pdo, $illId, "book_illustrators", "illustrator_fk", "illustrators", "illustrator_id");
        if (isset($message)): ?>
            <div class="alert alert-info"><?= htmlspecialchars($message); ?></div>
        <?php endif;
}


?>

<div class="container" id="illustrator_editor_area">
<h3 class="my-4">Illustrator Editor</h3>
    <div class="row" id="illustrator_">
    <?php 
    foreach ($illustrators as $ill) {
        // Generate card HTML for each book
        echo '
        <div class="col-12 col-md-6 col-lg-2 mb-4 mx-4 d-flex justify-content-center">
            <div class="card book-card flex-fill">
                <h5 class="card-title my-2">' . htmlspecialchars($ill['illustrator_name']) . '</h5>
                <div class="card-footer d-flex justify-content-center align-items-center">
                    <form method="POST">
                        <input type="hidden" name="deleteIllustratorId" id="deleteIllustratorId" value="' . htmlspecialchars($ill['illustrator_id']) . '"/>
                        <input type="submit" id="deleteIllustrator" name="deleteIllustrator" class="btn btn-danger my-1" value="Delete illustrator"/>
                    </form>
                </div>
            </div>
        </div>';
    }
    ?>
    </div>
    <div class="row" id="add_area">
        <div class="container">
            
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addIllustratorModal">
                Add New Illustrator 
            </button>
        </div>
        <?php include '../includes/modals.php'; ?>
    </div>
</div>

<?php 
include_once '../includes/emp-footer.php';
?>
