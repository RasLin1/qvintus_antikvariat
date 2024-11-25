<?php
include_once '../includes/emp-header.php';

checkUserRole(1, "../main/index.php");

$searchTerm = '%'; // Default to match all books

if (isset($_GET['title'])) {
    $searchTerm = '%' . $_GET['title'] . '%'; // Adjust search term based on user input
}

$bookQuery = "
    SELECT 
        b.book_id, 
        b.book_title, 
        b.book_img, 
        b.book_price,
        a.author_name
    FROM 
        books b
    JOIN 
        book_author ba ON b.book_id = ba.book_fk
    JOIN 
        authors a ON ba.author_fk = a.author_id
    WHERE 
        b.book_title LIKE :searchTerm;
";

$stmt_retrieveBooks = $pdo->prepare($bookQuery);
$stmt_retrieveBooks->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR);
$stmt_retrieveBooks->execute();
$books = $stmt_retrieveBooks->fetchAll(PDO::FETCH_ASSOC);

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

?>

<div class="container" id="book_editor_area">
<h3 class="my-4">Book Editor</h3>
    <div class="row" id="search_area">
        <h4>Search for books</h4>
        <input type="text" id="searchInput" placeholder="Search by title, author, or genre..." style="width: 100%; padding: 10px; margin-bottom: 20px;">
        <div id="searchResults"></div>
    </div>
    <div class="row" id="book_area">
    <?php 
    if ($books) {
        // Start the foreach loop if there are books
        foreach ($books as $book) {
            echo "
            <div class='col-md-4'>
                <div class='card' style='width: 18rem;'>
                    <img src='../assets/img/" . htmlspecialchars($book['book_img']) . "' class='card-img-top' alt='" . htmlspecialchars($book['book_title']) . "'>
                    <div class='card-body'>
                        <h5 class='card-title'>" . htmlspecialchars($book['book_title']) . "</h5>
                        <p class='card-text'>Author: " . htmlspecialchars($book['author_name']) . "</p>
                        <p class='card-text'>Price: " . number_format($book['book_price'], 2) . "€</p>
                        <div class='d-flex justify-content-between'>
                            <!-- Edit Button -->
                            <form method='POST'>
                                <input type='hidden' name='currBookId' value='" . htmlspecialchars($book['book_id']) . "'/>
                                <input type='submit' value='Edit' name='editBook'/>
                            </form>
                            <a href='delete_book.php?id=" . htmlspecialchars($book['book_id']) . "' class='btn btn-danger'>Delete</a>
                        </div>
                    </div>
                </div>
            </div>
            ";
        }
    } else {
        echo "<p>No books found.</p>";
    }    
    ?>

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

<?php 
include_once '../includes/emp-footer.php';
?>


<script>
        // Debounce function to minimize API calls
        function debounce(func, delay) {
            let timeoutId;
            return (...args) => {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => func(...args), delay);
            };
        }

        // Fetch search results from the server
        async function fetchSearchResults(query) {
            try {
                const response = await fetch(`search.php?query=${encodeURIComponent(query)}`);
                if (!response.ok) {
                    throw new Error('Failed to fetch search results');
                }
                return await response.json();
            } catch (error) {
                console.error('Error fetching search results:', error);
                return [];
            }
        }

        // Update the search results in the DOM
        function updateSearchResults(books) {
            const resultsContainer = document.getElementById('searchResults');
            resultsContainer.innerHTML = ''; // Clear existing results

            if (books.length === 0) {
                resultsContainer.innerHTML = '<p>No results found.</p>';
                return;
            }

            books.forEach((book) => {
                const bookDiv = document.createElement('div');
                bookDiv.className = 'book-result';
                bookDiv.innerHTML = `
                    <h3>${book.book_title}</h3>
                    <p><strong>Authors:</strong> ${book.authors || 'N/A'}</p>
                    <p><strong>Genres:</strong> ${book.genres || 'N/A'}</p>
                    <p>${book.book_summary || 'No summary available.'}</p>
                `;
                resultsContainer.appendChild(bookDiv);
            });
        }

        // Add event listener for live search
        document.getElementById('searchInput').addEventListener(
            'input',
            debounce(async (e) => {
                const query = e.target.value.trim();
                if (query === '') {
                    updateSearchResults([]); // Clear results if input is empty
                    return;
                }

                const results = await fetchSearchResults(query);
                updateSearchResults(results);
            }, 300) // 300ms debounce delay
        );
</script>