<?php
include_once '../includes/emp-header.php';

checkUserRole(1, "../main/index.php");

if(isset($_POST['deleteBook'])){
    $bookId = $_POST['deleteBookId']; // Assume the book ID is passed from a form
    $success = deleteBook($pdo, $bookId);

    if ($success) {
        echo "Book and associated image deleted successfully.";
    } else {
        echo "Failed to delete book.";
    }
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
// Helper function to generate book cards
function generateBookCardHTML(books) {
        return books
            .map((book) => {
                return `
            <div class="col-8 col-md-6 col-lg-2 mb-4 mx-4 d-flex justify-content-center">
                <div class="card book-card flex-fill" style="height: 400px; overflow: hidden;">
                    <!-- Background Image Section -->
                    <div class="card-image" style="background-image: url('../assets/img/${book.book_img}'); background-size: cover; background-position: center; height: 80%; position: relative;">
                        <div class="card-overlay" style="position: absolute; bottom: 0; left: 0; width: 100%; background: rgba(0, 0, 0, 0.5); color: #fff; text-align: center; padding: 10px;">
                            <h5 class="card-title mb-0">${book.book_title}</h5>
                            <p class="card-text mb-0">${parseFloat(book.book_price).toFixed(2)}€</p>
                        </div>
                    </div>
                    <!-- Button Section -->
                    <div class="card-footer d-flex flex-column justify-content-center align-items-center" style="height: 20%; background: #f8f9fa;">
                        <button 
                            class="btn btn-warning edit-book-btn my-1" data-bs-toggle="modal" data-bs-target="#editBookModal"
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
                        <button class="btn btn-danger delete-book-btn my-1" data-book-id="${book.book_id}">Delete</button>
                    </div>
                </div>
            </div>
            `;
            })
            .join('');
    }

$(document).ready(function () {
    // Fetch and render books
    function fetchBooks() {
    $.ajax({
        url: '../includes/searching/searchSpecificBook.php', // Link to the search endpoint
        method: 'GET',
        data: { query: '' },  // Pass an empty query to fetch all books
        dataType: 'json',
        success: function (data) {
            if (data.length > 0) {
                $('#book_area').html(generateBookCardHTML(data));  // Use the data returned
            } else {
                $('#book_area').html('<p>No books available.</p>');
            }
        },
        error: function () {
            $('#book_area').html('<p>Error fetching books.</p>');
        }
    });
}
    // Create a Bloodhound engine for Typeahead
    const books = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('title', 'author'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: '../includes/searching/searchSpecificBook.php?query=%QUERY', // Backend endpoint
            wildcard: '%QUERY',
        },
    });


    $('#book_area').on('click', '.delete-book-btn', function () {
        const bookId = $(this).data('book-id');

        $.ajax({
            url: '../includes/dynamicAJAX/ajax_handler.php',
            method: 'POST',
            data: { action: 'delete', type: 'books', id: bookId },
            success: function (data) {
                if (data.success) {
                    fetchBooks();
                } else {
                    alert('Error deleting book: ' + data.message);
                }
            }
        });
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

    fetchBooks(); // Fetch books on site load
});
</script>

<?php 
include_once '../includes/emp-footer.php';
?>
