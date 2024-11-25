<?php
require_once '../config.php';
require_once '../functions.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);



// Handle the request
if (isset($_GET['query'])) {
    $query = $_GET['query'];

    try {
        // Call the function to get search results
        $results = searchBooksForTypeahead($pdo, $query);

        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode($results);
    } catch (PDOException $e) {
        // Error handling
        http_response_code(500);
        echo json_encode(['error' => 'Database query failed', 'details' => $e->getMessage()]);
    }
    exit;
}

// Optional debugging for development mode
if (defined('DEBUG') && DEBUG) {
    echo "<pre>";
    print_r($stmt->errorInfo());
    $stmt->debugDumpParams();
    echo "</pre>";
    exit;
}
file_put_contents('debug.log', print_r($_GET, true), FILE_APPEND);
?>