<?php 
include '../includes/header.php';

$bookId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($bookId > 0) {
    // Fetch the book details using $bookId
    $stmt_fetchSingleBook = $pdo->prepare("
        SELECT 
            b.book_id,
            b.book_title,
            b.book_summary,
            b.book_publishing_date,
            b.book_side_count,
            b.book_price,
            b.book_img,
            p.pub_name AS publisher,
            a.age_value AS age_recommendation,
            c.cat_name AS category,
            l.lang_name AS language,
            s.series_name AS series,
            GROUP_CONCAT(DISTINCT g.genre_name SEPARATOR ', ') AS genres,
            GROUP_CONCAT(DISTINCT auth.author_name SEPARATOR ', ') AS authors,
            GROUP_CONCAT(DISTINCT ill.illustrator_name SEPARATOR ', ') AS illustrators
        FROM books b
        LEFT JOIN publishers p ON b.publisher_fk = p.pub_id
        LEFT JOIN book_age_rec a ON b.book_age_rec_fk = a.age_id
        LEFT JOIN book_categories c ON b.book_category_fk = c.cat_id
        LEFT JOIN book_languages l ON b.book_language_fk = l.lang_id
        LEFT JOIN book_series s ON b.book_series_fk = s.series_id
        LEFT JOIN book_genres bg ON b.book_id = bg.book_fk
        LEFT JOIN genres g ON bg.genre_fk = g.genre_id
        LEFT JOIN book_author ba ON b.book_id = ba.book_fk
        LEFT JOIN authors auth ON ba.author_fk = auth.author_id
        LEFT JOIN book_illustrators bi ON b.book_id = bi.book_fk
        LEFT JOIN illustrators ill ON bi.illustrator_fk = ill.illustrator_id
        WHERE b.book_id = :book_id
        GROUP BY b.book_id
    ");

    $stmt_fetchSingleBook->execute([':book_id' => $bookId]);
    $bookDetails = $stmt_fetchSingleBook->fetch(PDO::FETCH_ASSOC); // Fetch as associative array

} else {
    echo "Invalid book ID.";
}

?>

<div class="container" id="main-container">
    <div id="search-area">

    </div>
    <div class="container book-details-container">
        <div class="row">
            <div class="col-md-4">
                <!-- Book cover placeholder -->
                <img src="../assets/img/<?php echo htmlspecialchars($bookDetails['book_img']); ?>" alt="<?php echo htmlspecialchars($bookDetails['book_title']); ?>" class="book-cover img-fluid">
            </div>
            <div class="col-md-8 book-info">
                <h2><?php echo htmlspecialchars($bookDetails['book_title']); ?></h2>
                <p><strong>Author(s):</strong> <?php echo htmlspecialchars($bookDetails['authors']); ?></p>
                <p><strong>Illustrator(s):</strong> <?php echo htmlspecialchars($bookDetails['illustrators']); ?></p>
                <p><strong>Publisher:</strong> <?php echo htmlspecialchars($bookDetails['publisher']); ?></p>
                <p><strong>Genres:</strong> <?php echo htmlspecialchars($bookDetails['genres']); ?></p>
                <p><strong>Category:</strong> <?php echo htmlspecialchars($bookDetails['category']); ?></p>
                <p><strong>Age Recommendation:</strong> <?php echo htmlspecialchars($bookDetails['age_recommendation']); ?></p>
                <p><strong>Language:</strong> <?php echo htmlspecialchars($bookDetails['language']); ?></p>
                <p><strong>Pages:</strong> <?php echo htmlspecialchars($bookDetails['book_side_count']); ?></p>
                <p><strong>Published:</strong> <?php echo htmlspecialchars($bookDetails['book_publishing_date']); ?></p>
                <p><strong>Series:</strong> <?php echo htmlspecialchars($bookDetails['series']); ?></p>
                <h4>€<?php echo number_format($bookDetails['book_price'], 2); ?></h4>
                <button class="btn btn-primary mt-3">Add to Cart</button>
            </div>
        </div>
        <div class="mt-4">
            <h3>Description</h3>
            <p><?php echo nl2br(htmlspecialchars($bookDetails['book_summary'])); ?></p>
        </div>
    </div>
</div>

<style>
#main-container {
    color: white;
}


.book-details-container {
    margin: 20px auto;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    background-color: #f9f9f9;
    background: black;
}
.book-cover {
    width: auto;
    height: 100%;
}
.book-info h2 {
    margin-bottom: 15px;
}
.book-meta span {
    display: inline-block;
    margin-right: 10px;
}
    
</style>

<?php 
include '../includes/footer.php';
?>
