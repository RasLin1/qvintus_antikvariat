<?php
include_once '../config.php'; // Ensure this contains your database connection
include_once '../functions.php'; // Link your functions.php file where universalInsert, fetchAllFromTable, and deleteObject are defined

// Check if a valid action and type are provided in the request
if (isset($_POST['action']) && isset($_POST['type'])) {
    $action = $_POST['action'];
    $type = $_POST['type']; // 'authors', 'categories', 'publishers', etc.

    // Handle different actions based on request
    try {
        if ($action == 'fetch') {
            // Fetch data based on the entity type using fetchAllFromTable
            $data = fetchAllFromTable($pdo, $type);
            echo json_encode(['data' => $data]);
        } elseif ($action == 'delete' && isset($_POST['id']) && isset($_POST['depTab']) && isset($_POST['depCol']) && isset($_POST['tab']) && isset($_POST['col'])) {
            // Get the necessary POST data
            $id = $_POST['id'];
            $depTab = $_POST['depTab']; // Dependent table name (e.g., 'books')
            $depCol = $_POST['depCol']; // Dependent column name (e.g., 'author_fk')
            $tab = $_POST['tab']; // Main table name (e.g., 'authors')
            $col = $_POST['col']; // Column name in the main table (e.g., 'author_id')
        
            // Call the deleteObject function with these parameters
            $message = deleteObject($pdo, $id, $depTab, $depCol, $tab, $col);
            
            // Respond with success or failure
            if ($message == "Object deleted successfully.") {
                echo json_encode(['success' => true, 'message' => ucfirst($tab) . ' deleted successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => $message]); // Send back the error message
            }
        } elseif ($action == 'insert' && isset($_POST['data'])) {
            // Insert data based on entity type using universalInsert
            $data = $_POST['data'];
            $columns = $data['columns'];
            $values = $data['values'];
            $redirectTo = isset($data['redirectTo']) ? $data['redirectTo'] : null;
            $result = universalInsert($pdo, $type, $columns, $values, $redirectTo);
            if ($result) {
                echo json_encode(['success' => true, 'message' => ucfirst($type) . ' added successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to add ' . $type]);
            }
        } else {
            echo json_encode(['error' => 'Invalid action or missing data']);
        }
    } catch (Exception $e) {
        echo json_encode(['error' => 'An error occurred: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Action or type missing']);
}
?>