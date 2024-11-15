<?php 
include '../includes/header.php';
?>

<div class="container">


<div class="search-area container mt-5">
    <!-- Search Bar -->
    <h4 class="search-label">Vad letar du efter?</h4>
    <div class="input-group mb-3">
        <input type="text" class="form-control" placeholder="Search" aria-label="Search" aria-describedby="search-button">
        <button class="btn btn-outline-secondary" type="button" id="search-button">
            <i class="bi bi-search">🔍</i> <!-- Bootstrap Icons -->
        </button>
    </div>
</div>


<div class="rare-books-area"></div>

<div class="featured-genres-area"></div>

<div class="popular-books-area"></div>

<div class="request-area"></div>

<div class="qvintus-greeting-area"></div>

<div class="customer-stories-area"></div>



</div>

<?php 
include '../includes/footer.php';
?>