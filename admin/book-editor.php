<?php
include_once '../includes/emp-header.php';

checkUserRole(1, "../main/index.php");
?>

<div class="container" id="book_editor_area">
    <div class="row" id="search_area">
        
    </div>
    <div class="row" id="book_area">
        
    </div>
    <div class="row" id="add_area">
    <div class="container">
        <h3 class="my-4">Book Editor</h3>
        
        <!-- Button to trigger modals -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBookModal">
            Add New Book
        </button>
        
    </div>

    <!-- Include modals -->
    <?php include '../includes/modals.php'; ?>
    </div>
</div>


<?php 
include_once '../includes/emp-footer.php';
?>