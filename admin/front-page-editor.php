<?php
include_once '../includes/emp-header.php';

checkUserRole(3, "book-editor.php");

$rareItem = 1;
$popGenre = 2;
$popBook = 3;

$stmt_fetchRareItems = $pdo->prepare("SELECT book_title, feat_item_id FROM featured_items fi JOIN books b ON fi.book_fk = b.book_id WHERE feat_item_type_fk = :typeId");
$stmt_fetchRareItems->bindParam(":typeId", $rareItem, PDO::PARAM_INT);
$stmt_fetchRareItems->execute(); // Execute the prepared statement
$rareItems = $stmt_fetchRareItems->fetchAll(PDO::FETCH_ASSOC);

$stmt_fetchPopGenre = $pdo->prepare("SELECT genre_name, feat_item_id FROM featured_items fi JOIN genres g ON fi.genre_fk = g.genre_id WHERE feat_item_type_fk = :typeId");
$stmt_fetchPopGenre->bindParam(":typeId", $popGenre, PDO::PARAM_INT);
$stmt_fetchPopGenre->execute(); // Execute the prepared statement
$popGenres = $stmt_fetchPopGenre->fetchAll(PDO::FETCH_ASSOC);

$stmt_fetchPopBook = $pdo->prepare("SELECT book_title, feat_item_id FROM featured_items fi JOIN books b ON fi.book_fk = b.book_id WHERE feat_item_type_fk = :typeId");
$stmt_fetchPopBook->bindParam(":typeId", $popBook, PDO::PARAM_INT);
$stmt_fetchPopBook->execute(); // Execute the prepared statement
$popBooks = $stmt_fetchPopBook->fetchAll(PDO::FETCH_ASSOC);

$stmt_fetchFrontPageContent = $pdo->query("SELECT * FROM front_page_content");
$fpContent = $stmt_fetchFrontPageContent->fetchAll(PDO::FETCH_ASSOC);

if(isset($_POST['updateFrontpageText'])){
    $deleteResult = updateFrontpageText($pdo, $_POST['contentId'], $_POST['contentData'], "front-page-editor.php");
};

if(isset($_POST['deleteFeaturedItem'])){
    $deleteResult = deleteFeatItem($pdo, $_POST['featItemId'], "front-page-editor.php");
};


?>
<div class="container">
<div class="row" id="search_title_edit_area">
    <form method="POST">
        <?php foreach ($fpContent as $cont) {
            if ($cont['cont_id'] == 1) {
                echo "
                <input type='hidden' name='contentId' value='" . htmlspecialchars($cont['cont_id'], ENT_QUOTES, 'UTF-8') . "' />
                <input type='text' name='contentData' value='" . htmlspecialchars($cont['cont_data'], ENT_QUOTES, 'UTF-8') . "' />
                <input type='submit' value='Update Text' name='updateFrontpageText' />
                ";
                break; // Exit after the first match, as you're only displaying one form
            }
        }?>
    </form><br><br><br>
</div>
<div class="row" name="rare_item_edit_area">
    <form method="POST">
        <?php foreach ($fpContent as $cont) {
            if ($cont['cont_id'] == 2) {
                echo "
                <input type='hidden' name='contentId' value='" . htmlspecialchars($cont['cont_id'], ENT_QUOTES, 'UTF-8') . "' />
                <input type='text' name='contentData' value='" . htmlspecialchars($cont['cont_data'], ENT_QUOTES, 'UTF-8') . "' />
                <input type='submit' value='Update Text' name='updateFrontpageText' />
                ";
                break; // Exit after the first match, as you're only displaying one form
            }
        }?>
    </form>
    <?php 
    foreach ($rareItems as $ftItem) {
        // Generate card HTML for each book
        echo "
            <div class='col-md-4 col-12 d-flex'>
                <div class='card flex-fill' style='width: 12rem;'>
                    <div class='card-body'>
                        <h5 class='card-title'>{$ftItem['book_title']}</h5>
                        <div class='d-flex justify-content-center'>
                            <form method='POST'>
                            <input type='hidden' name='featItemId' value='{$ftItem['feat_item_id']}'/>
                            <input type='submit' value='Delete Item' name='deleteFeaturedItem' id='deleteFeaturedItem'/>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        ";
        }
        ?>
</div>
<div class="row">
    <div class="d-flex justify-content-center">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFeatItemModal">
            Add New Featured Item
        </button>
    </div>
</div>
</div>
<?php include '../includes/modals.php'; ?>



<?php 
include_once '../includes/emp-footer.php';
?>
