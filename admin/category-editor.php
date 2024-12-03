<?php
include_once '../includes/emp-header.php';

checkUserRole(3, "book-editor.php");
?>

<div class="container" id="category_editor_area">
<h3 class="my-4" id="site-title">Category Editor</h3>
    <div class="row" id="category_">
    
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

<script>
// Fetch the categories from the server and display them dynamically
document.addEventListener('DOMContentLoaded', fetchCategories);

function fetchCategories() {
    fetch('../includes/dynamicAJAX/ajax_handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ action: 'fetch', type: 'book_categories' }) // Fetch categories
    })
    .then(response => response.json())
    .then(data => {
        if (data.data) {
            const categoryContainer = document.getElementById('category_');
            categoryContainer.innerHTML = ''; // Clear previous content
            data.data.forEach(category => {
                categoryContainer.innerHTML += `
                    <div class="col-12 col-md-6 col-lg-2 mb-4 mx-4 d-flex justify-content-center">
                        <div class="card book-card flex-fill">
                            <h5 class="card-title my-2">${category.cat_name}</h5>
                            <div class="card-footer d-flex justify-content-center align-items-center">
                                <button class="btn btn-danger my-1" onclick="deleteCategory(${category.cat_id})">Delete Category</button>
                            </div>
                        </div>
                    </div>`;
            });
        }
    })
    .catch(err => console.error('Error fetching categories:', err));
}

// Add a new category to the database
function addCategory() {
    const categoryName = document.getElementById('categoryName').value;

    fetch('../includes/dynamicAJAX/ajax_handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            action: 'insert',
            type: 'categories',
            data: JSON.stringify({
                columns: ['cat_name'],
                values: [categoryName]
            })
        })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            fetchCategories(); // Reload categories after adding
            document.getElementById('categoryName').value = ''; // Clear input
        }
    })
    .catch(err => console.error('Error adding category:', err));
}

// Delete a category from the database
function deleteCategory(categoryId) {
    fetch('../includes/dynamicAJAX/ajax_handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            action: 'delete',
            type: 'category',  // This should be the entity type like 'category', 'publisher', etc.
            id: categoryId,    // The ID of the category to be deleted
            depTab: 'books',   // Table that holds foreign key references
            depCol: 'book_category_fk', // Column name in dependent table
            tab: 'book_categories', // The main table
            col: 'cat_id' // Column name in the main table
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            fetchCategories(); // Reload categories after deletion
        }
    })
    .catch(err => console.error('Error deleting category:', err));
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