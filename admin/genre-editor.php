<?php
include_once '../includes/emp-header.php';

checkUserRole(3, "book-editor.php");
?>

<div class="container" id="genre_editor_area">
<h3 class="my-4">Genre Editor</h3>
    <div class="row" id="genre_area">
    
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

<script>
document.addEventListener('DOMContentLoaded', () => {
    const genreContainer = document.getElementById('genre_area');
    const addGenreForm = document.getElementById('addGenreForm');

    // Fetch and render genres
    function fetchGenres() {
        fetch('../includes/dynamicAJAX/ajax_handler.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ action: 'fetch', type: 'genres' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.data) {
                genreContainer.innerHTML = '';
                data.data.forEach(genre => {
                    genreContainer.innerHTML += `
                        <div class="col-12 col-md-6 col-lg-2 mb-4 mx-4 d-flex justify-content-center">
                            <div class="card book-card flex-fill">
                                <h5 class="card-title my-2">${genre.genre_name}</h5>
                                <div class="card-footer d-flex justify-content-center align-items-center">
                                    <button class="btn btn-danger my-1" onclick="deleteGenre(${genre.genre_id})">Delete Genre</button>
                                </div>
                            </div>
                        </div>`;
                });
            }
        })
        .catch(err => console.error('Error fetching genres:', err));
    }

    // Delete genre
    window.deleteGenre = function(genreId) {
        fetch('../includes/dynamicAJAX/ajax_handler.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'delete',
                type: 'genres',
                id: genreId
            })
        })
        .then(response => response.json())
        .then(data => {
            fetchGenres(); // Refresh the list after deletion
        })
        .catch(err => console.error('Error deleting genre:', err));
    };

    // Add new genre
    addGenreForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(addGenreForm);
        const genreName = formData.get('genreName');

        fetch('../includes/dynamicAJAX/ajax_handler.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'insert',
                type: 'genres',
                'data[columns][]': 'genre_name',
                'data[values][]': genreName
            })
        })
        .then(response => response.json())
        .then(data => {
            addGenreForm.reset(); // Reset form fields
            fetchGenres(); // Refresh the list after addition
            const addModal = new bootstrap.Modal(document.getElementById('addGenreModal'));
            addModal.hide(); // Close the modal
        })
        .catch(err => console.error('Error adding genre:', err));
    });

    // Initial fetch of genres
    fetchGenres();
});
</script>

<?php 
include_once '../includes/emp-footer.php';
?>
