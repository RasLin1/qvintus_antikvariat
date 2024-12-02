<?php
include_once '../includes/emp-header.php';

checkUserRole(1, "../main/index.php");




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
                                    <button 
                                        class="btn btn-primary edit-book-btn" data-bs-toggle="modal" data-bs-target="#editBookModal"
                                        data-book-id="${book.book_id}" 
                                        data-book-title="${book.book_title}" 
                                        data-book-summary="${book.book_summary}" 
                                        data-book-publishing-date="${book.book_publishing_date}" 
                                        data-book-side-count="${book.book_side_count}" 
                                        data-book-price="${book.book_price}" 
                                        data-book-publisher="${book.publisher_fk}" 
                                        data-book-language="${book.book_language_fk}" 
                                        data-book-category="${book.book_category_fk}" 
                                        data-book-age-rec="${book.book_age_rec_fk}" 
                                        data-book-series="${book.book_series_fk}" 
                                        data-book-genres="${book.genres}" 
                                        data-book-authors="${book.authors}" 
                                        data-book-illustrators="${book.illustrators}">
                                        
                                        Edit Book
                                    </button>
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
