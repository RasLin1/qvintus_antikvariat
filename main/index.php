<?php 
include '../includes/header.php';

$rareItem = 1;
$popGenre = 2;
$popBook = 3;

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

$stmt_fetchRareItems = $pdo->prepare("SELECT book_title, feat_item_id, book_img, book_price FROM featured_items fi JOIN books b ON fi.book_fk = b.book_id WHERE feat_item_type_fk = :typeId");
$stmt_fetchRareItems->bindParam(":typeId", $rareItem, PDO::PARAM_INT);
$stmt_fetchRareItems->execute(); // Execute the prepared statement
$rareItems = $stmt_fetchRareItems->fetchAll(PDO::FETCH_ASSOC);

$stmt_fetchPopGenre = $pdo->prepare("SELECT genre_name, feat_item_id FROM featured_items fi JOIN genres g ON fi.genre_fk = g.genre_id WHERE feat_item_type_fk = :typeId");
$stmt_fetchPopGenre->bindParam(":typeId", $popGenre, PDO::PARAM_INT);
$stmt_fetchPopGenre->execute(); // Execute the prepared statement
$popGenres = $stmt_fetchPopGenre->fetchAll(PDO::FETCH_ASSOC);

$stmt_fetchPopBook = $pdo->prepare("SELECT book_title, feat_item_id FROM featured_items fi JOIN books b ON fi.book_fk = b.book_id WHERE feat_item_type_fk = :typeId");
$stmt_fetchPopBook->bindParam(":typeId", $popBook, PDO::PARAM_INT);
$stmt_fetchPopBook->execute(); // Execute the prepared statement
$popBooks = $stmt_fetchPopBook->fetchAll(PDO::FETCH_ASSOC);

$stmt_fetchFrontPageContent = $pdo->query("SELECT * FROM front_page_content");
$fpContent = $stmt_fetchFrontPageContent->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container">


<div class="search-area container mt-5">
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


<div class="rare-books-area">
<h4 class="search-label">
    <?php
        foreach ($fpContent as $cont) {
        if ($cont['cont_id'] == 2) {
            echo  htmlspecialchars($cont['cont_data']);
            break;
        }}
    ?>
</h4>

<div id="rareItemsCarousel" class="carousel slide" data-bs-ride="carousel">
    <button class="carousel-control-prev" type="button" data-bs-target="#featuredItemsCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <div class="carousel-inner">
        <?php
        // Set a flag for the first carousel item to add the "active" class to it
        $isFirstItem = true;

        // Loop through the data in chunks of 4 items (4 cards per slide)
        for ($i = 0; $i < count($rareItems); $i += 4) {
            // Start a new carousel item
            echo '<div class="carousel-item' . ($isFirstItem ? ' active' : '') . '">';
            echo '<div class="row">';

            // Loop through the 4 items (or fewer if it's the last chunk)
            for ($j = $i; $j < $i + 4 && $j < count($rareItems); $j++) {
                $item = $rareItems[$j];
                echo '
                <div class="col-12 col-md-6 col-lg-3 mb-4 d-flex">
                    <div class="card flex-fill">
                        <img src="../assets/img/' . htmlspecialchars($item['book_img']) . '" class="card-img-top" alt="Card image">
                        <div class="card-body">
                            <h5 class="card-title">' . htmlspecialchars($item['book_title']) . '</h5>
                            <p class="card-text">' . htmlspecialchars($item['book_price']) . '</p>
                            <a href="#" class="btn btn-primary">Learn More</a>
                        </div>
                    </div>
                </div>';
            }

            // Close the row and carousel item divs
            echo '</div></div>';

            // Set $isFirstItem to false after the first iteration
            $isFirstItem = false;
        }
        ?>
    </div>

    <!-- Controls for carousel navigation -->
    
    <button class="carousel-control-next" type="button" data-bs-target="#featuredItemsCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>

</div>

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