<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

if (isset($_GET['fetch_book_data']) && isset($_GET['book_id'])) {
    // Sanitize and validate the book ID
    $bookId = filter_var($_GET['book_id'], FILTER_VALIDATE_INT);
    if (!$bookId) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Invalid book ID']);
        exit;
    }

    try {
        // Fetch book details
        $bookStmt = $pdo->prepare("
            SELECT 
                book_id, book_title, book_description, book_price, book_release_date, book_page_count
            FROM books
            WHERE book_id = :book_id
        ");
        $bookStmt->execute(['book_id' => $bookId]);
        $book = $bookStmt->fetch(PDO::FETCH_ASSOC);

        if (!$book) {
            echo json_encode(['error' => 'Book not found']);
            exit;
        }

        // Fetch related genres
        $genresStmt = $pdo->prepare("
            SELECT genre_id 
            FROM book_genres 
            WHERE book_id = :book_id
        ");
        $genresStmt->execute(['book_id' => $bookId]);
        $book['genres'] = $genresStmt->fetchAll(PDO::FETCH_COLUMN);

        // Fetch related authors
        $authorsStmt = $pdo->prepare("
            SELECT author_id 
            FROM book_authors 
            WHERE book_id = :book_id
        ");
        $authorsStmt->execute(['book_id' => $bookId]);
        $book['authors'] = $authorsStmt->fetchAll(PDO::FETCH_COLUMN);

        // Fetch related illustrators
        $illustratorsStmt = $pdo->prepare("
            SELECT illustrator_id 
            FROM book_illustrators 
            WHERE book_id = :book_id
        ");
        $illustratorsStmt->execute(['book_id' => $bookId]);
        $book['illustrators'] = $illustratorsStmt->fetchAll(PDO::FETCH_COLUMN);

        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode($book);
        exit;
    } catch (PDOException $e) {
        // Handle database error
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        exit;
    }
}
?>