<?php 
include '../includes/header.php';

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

<div class="container">


<div class="search-area container mt-5">
    <!-- Search Bar -->
    <h4 class="search-label">Vad letar du efter?</h4>
    <div class="input-group row">
        <input type="text" id="searchInput" placeholder="Search by title, author, or genre..." style="width: 100%; padding: 10px; margin-bottom: 20px;">
        <ul id="searchResults"></ul>
    </div>
</div>


<div class="rare-books-area"></div>

<div class="featured-genres-area"></div>

<div class="popular-books-area"></div>

<div class="request-area"></div>

<div class="qvintus-greeting-area"></div>

<div class="customer-stories-area"></div>



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

    // Event listener for "Enter" key to fetch and redirect with all matching books
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
                        // Create a form dynamically and submit it with POST
                        const form = $('<form>', {
                            action: 'books.php',
                            method: 'POST',
                        });

                        const input = $('<input>', {
                            type: 'hidden',
                            name: 'results',
                            value: JSON.stringify(results), // Pass results as JSON string
                        });

                        form.append(input);
                        $('body').append(form);
                        form.submit();
                    } else {
                        alert('No books found for your search.');
                    }
                },
                error: function () {
                    alert('Error fetching search results. Please try again.');
                },
            });
        }
    });

    // Helper function to generate book cards
    function generateBookCardHTML(books) {
        return books
            .map((book) => {
                return `
                    <div class='col-md-4'>
                        <div class='card' style='width: 18rem;'>
                            <img src='../assets/img/${book.book_img}' class='card-img-top' alt='${book.book_title}'>
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
include '../includes/footer.php';
?>