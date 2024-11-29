<?php 
include '../includes/header.php';

?>

<div class="container">
    <div id="search-area"></div>
    <div id="book-area" class="row">
        <?php 
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['results'])) {
            // Decode JSON data into a PHP array
            $results = json_decode($_POST['results'], true);
        
            if (is_array($results)) {
                foreach ($results as $book) {
                    // Generate card HTML for each book
                    echo '
                    <div class="col-12 col-md-6 col-lg-2 mb-4 mx-4 d-flex justify-content-center">
                        <div class="card book-card flex-fill" style="height: 400px; overflow: hidden;">
                            <!-- Background Image Section -->
                            <div class="card-image" style="background-image: url(\'../assets/img/' . htmlspecialchars($book['book_img']) . '\'); background-size: cover; background-position: center; height: 80%; position: relative;">
                                <div class="card-overlay" style="position: absolute; bottom: 0; left: 0; width: 100%; background: rgba(0, 0, 0, 0.5); color: #fff; text-align: center; padding: 10px;">
                                    <h5 class="card-title mb-0">' . htmlspecialchars($book['book_title']) . '</h5>
                                    <p class="card-text mb-0">' . htmlspecialchars($book['author_name']) . '</p>
                                    <p class="card-text mb-0">' . number_format($book['book_price'], 2) . '€</p>
                                </div>
                            </div>
                            <!-- Button Section -->
                            <div class="card-footer d-flex justify-content-center align-items-center" style="height: 20%; background: #f8f9fa;">
                                <a href="#" class="btn btn-primary">Learn More</a>
                            </div>
                        </div>
                    </div>';
                }
            } else {
                echo '<p>Invalid data received.</p>';
            }
        } else {
            $results = searchBooksForTypeahead($pdo, '');
            foreach ($results as $book) {
                // Generate card HTML for each book
                echo '
                    <div class="col-12 col-md-6 col-lg-2 mb-4 mx-4 d-flex justify-content-center">
                        <div class="card book-card flex-fill" style="height: 400px; overflow: hidden;">
                            <!-- Background Image Section -->
                            <div class="card-image" style="background-image: url(\'../assets/img/' . htmlspecialchars($book['book_img']) . '\'); background-size: cover; background-position: center; height: 80%; position: relative;">
                                <div class="card-overlay" style="position: absolute; bottom: 0; left: 0; width: 100%; background: rgba(0, 0, 0, 0.5); color: #fff; text-align: center; padding: 10px;">
                                    <h5 class="card-title mb-0">' . htmlspecialchars($book['book_title']) . '</h5>
                                    <p class="card-text mb-0">' . htmlspecialchars($book['author_name']) . '</p>
                                    <p class="card-text mb-0">' . number_format($book['book_price'], 2) . '€</p>
                                </div>
                            </div>
                            <!-- Button Section -->
                            <div class="card-footer d-flex justify-content-center align-items-center" style="height: 20%; background: #f8f9fa;">
                                <a href="#" class="btn btn-primary">Learn More</a>
                            </div>
                        </div>
                    </div>';
                }
            }
        ?>
    </div>
</div>

<?php 
include '../includes/footer.php';
?>
