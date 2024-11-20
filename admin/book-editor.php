<?php
include_once '../includes/emp-header.php';

checkUserRole(1, "../main/index.php");

$searchTerm = '%'; // Default to match all books

if (isset($_GET['title'])) {
    $searchTerm = '%' . $_GET['title'] . '%'; // Adjust search term based on user input
}

$bookQuery = "
    SELECT 
        b.book_id, 
        b.book_title, 
        b.book_img, 
        b.book_price,
        a.author_name
    FROM 
        books b
    JOIN 
        book_author ba ON b.book_id = ba.book_fk
    JOIN 
        authors a ON ba.author_fk = a.author_id
    WHERE 
        b.book_title LIKE :searchTerm;
";

$stmt_retrieveBooks = $pdo->prepare($bookQuery);
$stmt_retrieveBooks->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR);
$stmt_retrieveBooks->execute();
$books = $stmt_retrieveBooks->fetchAll(PDO::FETCH_ASSOC);



?>

<div class="container" id="book_editor_area">
<h3 class="my-4">Book Editor</h3>
    <div class="row" id="search_area">
        <!-- Search form if needed -->
    </div>
    <div class="row" id="book_area">
    <?php 
    if ($books) {
        // Start the foreach loop if there are books
        foreach ($books as $book) {
            echo "
            <div class='col-md-4'>
                <div class='card' style='width: 18rem;'>
                    <img src='../assets/img/" . htmlspecialchars($book['book_img']) . "' class='card-img-top' alt='" . htmlspecialchars($book['book_title']) . "'>
                    <div class='card-body'>
                        <h5 class='card-title'>" . htmlspecialchars($book['book_title']) . "</h5>
                        <p class='card-text'>Author: " . htmlspecialchars($book['author_name']) . "</p>
                        <p class='card-text'>Price: $" . number_format($book['book_price'], 2) . "</p>
                        <div class='d-flex justify-content-between'>
                            <!-- Edit Button -->
                            <a href='#' class='btn btn-warning' data-bs-toggle='modal' data-bs-target='#editBookModal' data-book-id='" . htmlspecialchars($book['book_id']) . "'>
                                Edit
                            </a>
                            <a href='delete_book.php?id=" . htmlspecialchars($book['book_id']) . "' class='btn btn-danger'>Delete</a>
                        </div>
                    </div>
                </div>
            </div>
            ";
        }
    } else {
        echo "<p>No books found.</p>";
    }    
    ?>

    </div>
    <div class="row" id="add_area">
        <div class="container">
            
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBookModal">
                Add New Book
            </button>
        </div>
        <?php include '../includes/modals.php'; ?>
    </div>
</div>

<?php 
include_once '../includes/emp-footer.php';
?>