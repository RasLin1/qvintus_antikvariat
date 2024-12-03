<?php
include_once '../includes/emp-header.php';

checkUserRole(3, "book-editor.php");

$stmt_fetchCategories = $pdo->query("SELECT * FROM book_categories");
$categories = $stmt_fetchCategories->fetchAll(PDO::FETCH_ASSOC);

if(isset($_POST['deleteCategory'])){
    $catId = $_POST['deleteCategoryId']; // Assume the book ID is passed from a form
    $message = deleteObject($pdo, $catId, "books", "book_category_fk", "book_categories", "cat_id");
        if (isset($message)): ?>
            <div class="alert alert-info"><?= htmlspecialchars($message); ?></div>
        <?php endif;
}


?>

<div class="container" id="category_editor_area">
<h3 class="my-4">Category Editor</h3>
    <div class="row" id="category_">
    <?php 
    foreach ($categories as $cat) {
        // Generate card HTML for each book
        echo '
        <div class="col-12 col-md-6 col-lg-2 mb-4 mx-4 d-flex justify-content-center">
            <div class="card book-card flex-fill">
                <h5 class="card-title my-2">' . htmlspecialchars($cat['cat_name']) . '</h5>
                <div class="card-footer d-flex justify-content-center align-items-center">
                    <form method="POST">
                        <input type="hidden" name="deleteCategoryId" id="deleteCategoryId" value="' . htmlspecialchars($cat['cat_id']) . '"/>
                        <input type="submit" id="deleteCategory" name="deleteCategory" class="btn btn-danger my-1" value="Delete Category"/>
                    </form>
                </div>
            </div>
        </div>';
    }
    ?>
    </div>
    <div class="row" id="add_area">
        <div class="container">
            
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                Add New Category 
            </button>
        </div>
        <?php include '../includes/modals.php'; ?>
    </div>
</div>

<?php 
include_once '../includes/emp-footer.php';
?>