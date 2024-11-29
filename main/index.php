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

$stmt_fetchPopBook = $pdo->prepare("SELECT book_title, feat_item_id, book_img, book_price FROM featured_items fi JOIN books b ON fi.book_fk = b.book_id WHERE feat_item_type_fk = :typeId");
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
    <div class="d-flex justify-content-between align-items-center mb-2">
        <!-- Title/Label -->
        <h4 class="rare-books-label mb-0">
            <?php
                foreach ($fpContent as $cont) {
                    if ($cont['cont_id'] == 2) {
                        echo htmlspecialchars($cont['cont_data']);
                        break;
                    }
                }
            ?>
        </h4>

        <!-- Navigation Buttons -->
        <div>
            <button class="btn btn-outline-secondary me-1" type="button" data-bs-target="#rareItemsCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="btn btn-outline-secondary" type="button" data-bs-target="#rareItemsCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>

    <!-- Carousel -->
    <div id="rareItemsCarousel" class="carousel slide position-relative" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php
            $isFirstItem = true;

            for ($i = 0; $i < count($rareItems); $i += 4) {
                echo '<div class="carousel-item' . ($isFirstItem ? ' active' : '') . '">';
                echo '<div class="row justify-content-center gx-4">';

                for ($j = $i; $j < $i + 4 && $j < count($rareItems); $j++) {
                    $item = $rareItems[$j];
                    echo '
                    <div class="col-12 col-md-6 col-lg-2 mb-4 mx-4 d-flex justify-content-center">
                        <div class="card book-card flex-fill" style="height: 400px; overflow: hidden;">
                            <!-- Background Image Section -->
                            <div class="card-image" style="background-image: url(\'../assets/img/' . htmlspecialchars($item['book_img']) . '\'); background-size: cover; background-position: center; height: 80%; position: relative;">
                                <div class="card-overlay" style="position: absolute; bottom: 0; left: 0; width: 100%; background: rgba(0, 0, 0, 0.5); color: #fff; text-align: center; padding: 10px;">
                                    <h5 class="card-title mb-0">' . htmlspecialchars($item['book_title']) . '</h5>
                                    <p class="card-text mb-0">' . number_format($item['book_price'], 2) . '€</p>
                                </div>
                            </div>
                            <!-- Button Section -->
                            <div class="card-footer d-flex justify-content-center align-items-center" style="height: 20%; background: #f8f9fa;">
                                <a href="#" class="btn btn-primary">Learn More</a>
                            </div>
                        </div>
                    </div>';
                }

                echo '</div></div>';
                $isFirstItem = false;
            }
            ?>
        </div>
    </div>
</div>

<div class="row" name="featured-genres-area">
    <h4 class="search-label">
    <?php
        foreach ($fpContent as $cont) {
        if ($cont['cont_id'] == 3) {
            echo  htmlspecialchars($cont['cont_data']);
            break;
        }}?>
    </h4><br><br>
    <?php
        foreach ($popGenres as $genre) {
            // Generate card HTML for each book
            echo "
                <div class='col-md-6 col-lg-3 col-12'>
                    <div class='card' style=';'>
                        <div class='card-body'>
                            <h5 class='card-title'>{$genre['genre_name']}</h5>
                            <div class='d-flex justify-content-center'>
                                <a href='#' class='btn btn-primary col-12'>View books in this genre</a>
                            </div>
                        </div>
                    </div>
                </div>
            ";
        }
    ?>



</div><br><br>

<div class="container" id="popular-books-area">
<div class="d-flex justify-content-between align-items-center mb-2">
        <!-- Title/Label -->
        <h4 class="popular-books-label mb-0">
            <?php
                foreach ($fpContent as $cont) {
                    if ($cont['cont_id'] == 4) {
                        echo htmlspecialchars($cont['cont_data']);
                        break;
                    }
                }
            ?>
        </h4>

        <!-- Navigation Buttons -->
        <div>
            <button class="btn btn-outline-secondary me-1" type="button" data-bs-target="#popularBooksCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="btn btn-outline-secondary" type="button" data-bs-target="#popularBooksCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>

<!-- Carousel -->
<div id="popularBooksCarousel" class="carousel slide position-relative" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php
            $isFirstItem = true;

            for ($i = 0; $i < count($popBooks); $i += 6) {
                echo '<div class="carousel-item' . ($isFirstItem ? ' active' : '') . '">';
                echo '<div class="row justify-content-center gx-4">';

                for ($j = $i; $j < $i + 6 && $j < count($popBooks); $j++) {
                    $item = $popBooks[$j];
                    echo '
                    <div class="col-12 col-md-6 col-lg-2 mb-4 mx-4 d-flex justify-content-center">
                        <div class="card book-card flex-fill" style="height: 400px; overflow: hidden;">
                            <!-- Background Image Section -->
                            <div class="card-image" style="background-image: url(\'../assets/img/' . htmlspecialchars($item['book_img']) . '\'); background-size: cover; background-position: center; height: 80%; position: relative;">
                                <div class="card-overlay" style="position: absolute; bottom: 0; left: 0; width: 100%; background: rgba(0, 0, 0, 0.5); color: #fff; text-align: center; padding: 10px;">
                                    <h5 class="card-title mb-0">' . htmlspecialchars($item['book_title']) . '</h5>
                                    <p class="card-text mb-0">' . number_format($item['book_price'], 2) . '€</p>
                                </div>
                            </div>
                            <!-- Button Section -->
                            <div class="card-footer d-flex justify-content-center align-items-center" style="height: 20%; background: #f8f9fa;">
                                <a href="#" class="btn btn-primary">Learn More</a>
                            </div>
                        </div>
                    </div>';
                }

                echo '</div></div>';
                $isFirstItem = false;
            }
            ?>
        </div>
    </div>
</div>

<div class="req-area-container container" id="request-area" style="min-height: 30vh;">
    <div class="row d-flex flex-column justify-content-between align-items-center h-100" style="min-height: 30vh;">
        <!-- Heading at the top -->
        <h4 class="mb-4" id="request-area-label">
            <?php
                foreach ($fpContent as $cont) {
                    if ($cont['cont_id'] == 5) {
                        echo htmlspecialchars($cont['cont_data']);
                        break;
                    }
                }
            ?>
        </h4>

        <!-- Paragraph in the middle -->
        <p class="text-center my-auto">
            <?php
                foreach ($fpContent as $cont) {
                    if ($cont['cont_id'] == 6) {
                        echo htmlspecialchars($cont['cont_data']);
                        break;
                    }
                }
            ?>
        </p>

        <!-- Button at the bottom -->
        <a href="#" class="btn btn-primary mt-4" style="width: 10%;">
            <?php
                foreach ($fpContent as $cont) {
                    if ($cont['cont_id'] == 7) {
                        echo htmlspecialchars($cont['cont_data']);
                        break;
                    }
                }
            ?>
        </a>
    </div>
</div>

<div class="row mt-4" id="qvintus-greeting-area" style="min-height: 30vh;">
    <div class="col-lg-6 col-12 d-flex flex-column justify-content-between align-items-center h-100" id="greeting-text-area" style="min-height: 30vh;">
    <h4 class="mb-4" id="request-area-label">
            <?php
                foreach ($fpContent as $cont) {
                    if ($cont['cont_id'] == 8) {
                        echo htmlspecialchars($cont['cont_data']);
                        break;
                    }
                }
            ?>
        </h4>

        <!-- Paragraph in the middle -->
        <p class="text-center my-auto">
            <?php
                foreach ($fpContent as $cont) {
                    if ($cont['cont_id'] == 9) {
                        echo htmlspecialchars($cont['cont_data']);
                        break;
                    }
                }
            ?>
        </p>
    </div>
    <div class="col-lg-6 col-12" id="greeting-pic-area" style="min-height: 30vh;">
        <img src="../assets/img/anders2.jpg" alt="Anders den mäktige" width="" height="380">
    </div>
</div>

<div class="customer-stories-area">

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
    #rareItemsCarousel {
        position: relative;
    }
    .carousel-control-prev, .carousel-control-next {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        z-index: 10;
    }
    .carousel-inner .row {
        margin: 0;
    }
    .carousel-item {
        overflow: hidden;
    }
    .book-card {
        height: 500px; /* Set a maximum width for consistent sizing */
    }

    .req-area-container {
        height: 70%;
    }

</style>
<?php 
include '../includes/footer.php';
?>