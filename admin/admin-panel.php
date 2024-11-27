<?php
include_once '../includes/emp-header.php';

checkUserRole(3, "book-editor.php");
?>

<div class="container" id="button_area">
    <div class="row">
        <div class="col-12 col-md-6">
            <a href="front-page-editor.php" class="btn btn-primary">Framsida</a><br><br>
            <a href="book-editor.php" class="btn btn-primary">Böcker</a><br><br>
            <a href="genre-editor.php" class="btn btn-primary">Genrer</a><br><br>
            <a href="author-editor.php" class="btn btn-primary">Författare</a><br><br>
        </div>
        <div class="col-12 col-md-6">
            <a href="illustrator-editor.php" class="btn btn-primary">Illustratörer</a><br><br>
            <a href="category-editor.php" class="btn btn-primary">Kategorier</a><br><br>
            <a href="publisher-editor.php" class="btn btn-primary">Förlager</a><br><br>
            <a href="user-editor.php" class="btn btn-primary">Användar Konton</a><br><br>
        </div>
    </div>
</div>


<?php 
include_once '../includes/emp-footer.php';
?>
