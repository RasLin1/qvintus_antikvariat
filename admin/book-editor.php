<?php
include_once '../includes/emp-header.php';

checkUserRole(1, "../main/index.php");


if(isset($_POST['editBook'])){
$singleBookQuery = "
    SELECT 
        *
    FROM 
        books b
    JOIN 
        book_author ba ON b.book_id = ba.book_fk
    JOIN 
        authors a ON ba.author_fk = a.author_id
    JOIN 
        book_illustrators bi ON b.book_id = bi.book_fk
    JOIN 
        illustrators i ON bi.illustrator_fk = i.illustrator_id
    JOIN 
        book_genres bg ON b.book_id = bg.book_fk
    JOIN 
        genres g ON bg.genre_fk = g.genre_id
    WHERE 
        b.book_id LIKE :currBookId;
";

$currBookId = (int) ($_POST['currBookId'] ?? 0);

$stmt_retrieveSingleBook = $pdo->prepare($singleBookQuery);
$stmt_retrieveSingleBook->bindParam(':currBookId', $currBookId, PDO::PARAM_INT);
$stmt_retrieveSingleBook->execute();
$singleBook = $stmt_retrieveSingleBook->fetch(PDO::FETCH_ASSOC);

if($singleBook){
    $singleBookData = json_encode($singleBook);
    echo "<script>
                var singleBookData = $singleBookData;
                document.addEventListener('DOMContentLoaded', function() {
                    populateAndOpenModal(singleBookData);
                });
              </script>";
}
}

if(isset($_POST['editBookSubmit'])){
    $editBookConfirmation = editBook($pdo);
}

if (isset($_GET['query']) && !empty($_GET['query'])) {
    $query = $_GET['query'];
    $results = searchBooksForTypeahead($pdo, $query);
    
    header('Content-Type: application/json');
    
    // Debugging step
    if (empty($results)) {
        echo json_encode(['error' => 'No results found', 'query' => $query]);
    } else {
        echo json_encode($results);
    }
    exit;
}

?>

<div class="container" id="book_editor_area">
<h3 class="my-4">Book Editor</h3>
    <div class="row" id="search_area">
        <h4>Search for books</h4>
        <input type="text" id="searchInput" placeholder="Search by title, author, or genre..." style="width: 100%; padding: 10px; margin-bottom: 20px;">
        <ul id="searchResults"></ul>
    </div>
    <div class="row" id="book_area">
    

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

<script>
$(document).ready(function () {
    // Create a Bloodhound engine for Typeahead
    const books = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('title', 'author'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: '../includes/searching/searchSpecificBook.php?query=%QUERY', // Backend endpoint
            wildcard: '%QUERY',
        },
    });

    // Initialize Typeahead on the input field
    $('#searchInput').typeahead(
        {
            hint: true,
            highlight: true,
            minLength: 1, // Minimum characters to start search
        },
        {
            name: 'books',
            display: 'book_title', // What to show in the dropdown
            source: books,
            templates: {
                suggestion: function (data) {
                    return `<div><strong>${data.book_title}</strong> - ${data.author_name || 'Unknown Author'}</div>`;
                },
            },
        }
    );

    // Event listener for selection from dropdown
    $('#searchInput').bind('typeahead:select', function (event, suggestion) {
        const cardHTML = generateBookCardHTML([suggestion]);
        $('#book_area').html(cardHTML);
    });

    // Event listener for "Enter" key to fetch and display all matching books
    $('#searchInput').on('keypress', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault(); // Prevent default form submission
            const query = $(this).val();

            // Fetch matching books
            $.ajax({
                url: '../includes/searching/searchSpecificBook.php',
                method: 'GET',
                data: { query },
                dataType: 'json',
                success: function (results) {
                    if (results.length > 0) {
                        const allBooksHTML = generateBookCardHTML(results);
                        $('#book_area').html(allBooksHTML);
                    } else {
                        $('#book_area').html('<p>No books found for your search.</p>');
                    }
                },
                error: function () {
                    $('#book_area').html('<p>Error fetching search results. Please try again.</p>');
                },
            });
        }
    });

    // Helper function to generate book cards
    function generateBookCardHTML(books) {
        return books
            .map((book) => {
                return `
                    <div class='col-md-4 d-flex'>
                        <div class='card ' style='width: 18rem;'>
                            <img src='../assets/img/${book.book_img}' class='card-img-top flex-fill' alt='${book.book_title}'>
                            <div class='card-body'>
                                <h5 class='card-title'>${book.book_title}</h5>
                                <p class='card-text'>Author: ${book.author_name || 'Unknown'}</p>
                                <p class='card-text'>Price: ${parseFloat(book.book_price).toFixed(2)}€</p>
                                <div class='d-flex justify-content-between'>
                                    <form method='POST'>
                                        <input type='hidden' name='currBookId' value='${book.book_id}'/>
                                        <input type='submit' value='Edit' name='editBook' class='btn btn-primary'/>
                                    </form>
                                    <a href='delete_book.php?id=${book.book_id}' class='btn btn-danger'>Delete</a>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            })
            .join('');
    }
});
</script>

<?php 
include_once '../includes/emp-footer.php';
?>
