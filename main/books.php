<?php 
include '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['results'])) {
    // Decode JSON data into a PHP array
    $results = json_decode($_POST['results'], true);

    if (is_array($results)) {
        foreach ($results as $book) {
            // Generate card HTML for each book
            echo "
                <div class='col-md-4'>
                    <div class='card' style='width: 18rem;'>
                        <img src='../assets/img/{$book['book_img']}' class='card-img-top' alt='{$book['book_title']}'>
                        <div class='card-body'>
                            <h5 class='card-title'>{$book['book_title']}</h5>
                            <p class='card-text'>Author: " . (!empty($book['author_name']) ? $book['author_name'] : 'Unknown') . "</p>
                            <p class='card-text'>Price: " . number_format((float)$book['book_price'], 2) . "€</p>
                            <div class='d-flex justify-content-center'>
                                <a href='single-book.php?id={$book['book_id']}' class='btn btn-primary'>View Book</a>
                            </div>
                        </div>
                    </div>
                </div>
            ";
        }
    } else {
        echo '<p>Invalid data received.</p>';
    }
} else {
    echo '<p>No data received.</p>';
}
?>

<div class="container">
    <div id="search-area"></div>
    <div id="book-area"></div>
</div>

<?php 
include '../includes/footer.php';
?>
