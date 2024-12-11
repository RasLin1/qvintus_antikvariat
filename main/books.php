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

$stmt_fetchFrontPageContent = $pdo->query("SELECT * FROM front_page_content");
$fpContent = $stmt_fetchFrontPageContent->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <div id="search-area">
            <!-- Search Bar -->
        <h4 class="search-label">
        <?php
            foreach ($fpContent as $cont) {
            if ($cont['cont_id'] == 1) {
                echo  htmlspecialchars($cont['cont_data']);
                break;
            }}
        ?>
        </h4>
        <div class="input-group row">
            <input type="text" id="searchInput" placeholder="Search by title, author, or genre..." style="width: 100%; padding: 10px; margin-bottom: 20px;">
            <ul id="searchResults"></ul>
        </div>
    </div>
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
});
</script>

<style>
    .container {
        color: white;
    }
</style>

<?php 
include '../includes/footer.php';
?>
