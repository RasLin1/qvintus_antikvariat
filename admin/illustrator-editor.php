<?php
include_once '../includes/emp-header.php';

checkUserRole(3, "book-editor.php");
?>

<div class="container" id="illustrator_editor_area">
<h3 class="my-4">Illustrator Editor</h3>
    <div class="row" id="illustrator_">
    <?php 
    foreach ($illustrators as $ill) {
        // Generate card HTML for each book
        echo '
        <div class="col-12 col-md-6 col-lg-2 mb-4 mx-4 d-flex justify-content-center">
            <div class="card book-card flex-fill">
                <h5 class="card-title my-2">' . htmlspecialchars($ill['illustrator_name']) . '</h5>
                <div class="card-footer d-flex justify-content-center align-items-center">
                    <form method="POST">
                        <input type="hidden" name="deleteIllustratorId" id="deleteIllustratorId" value="' . htmlspecialchars($ill['illustrator_id']) . '"/>
                        <input type="submit" id="deleteIllustrator" name="deleteIllustrator" class="btn btn-danger my-1" value="Delete illustrator"/>
                    </form>
                </div>
            </div>
        </div>';
    }
    ?>
    </div>
    <div class="row" id="add_area">
        <div class="container">
            
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addIllustratorModal">
                Add New Illustrator 
            </button>
        </div>
        <?php include '../includes/modals.php'; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const illustratorContainer = document.getElementById('illustrator_');
    const addIllustratorForm = document.getElementById('addIllustratorForm');

    // Fetch and render illustrators
    function fetchIllustrators() {
        fetch('../includes/dynamicAJAX/ajax_handler.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ action: 'fetch', type: 'illustrators' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.data) {
                illustratorContainer.innerHTML = '';
                data.data.forEach(ill => {
                    illustratorContainer.innerHTML += `
                        <div class="col-12 col-md-4 col-lg-3 mb-4 mx-2 d-flex justify-content-center">
                            <div class="card book-card flex-fill">
                                <h5 class="card-title my-2">${ill.illustrator_name}</h5>
                                <div class="card-footer d-flex justify-content-center align-items-center">
                                    <button class="btn btn-danger my-1" onclick="deleteIllustrator(${ill.illustrator_id})">Delete Illustrator</button>
                                </div>
                            </div>
                        </div>`;
                });
            }
        })
        .catch(err => console.error('Error fetching illustrators:', err));
    }

    // Delete illustrator
    window.deleteIllustrator = function(illustratorId) {
        fetch('../includes/dynamicAJAX/ajax_handler.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'delete',
                type: 'illustrators',
                id: illustratorId,
                depTab: 'book_illustrators',
                depCol: 'illustrator_fk',
                tab: 'illustrators',
                col: 'illustrator_id'
            })
        })
        .then(response => response.json())
        .then(data => {
            fetchIllustrators(); // Refresh the list after deletion
        })
        .catch(err => console.error('Error deleting illustrator:', err));
    };

    // Add new illustrator
    addIllustratorForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(addIllustratorForm);
        const illustratorName = formData.get('illustratorName');

        fetch('../includes/dynamicAJAX/ajax_handler.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'insert',
                type: 'illustrators',
                'data[columns][]': 'illustrator_name',
                'data[values][]': illustratorName
            })
        })
        .then(response => response.json())
        .then(data => {
            addIllustratorForm.reset(); // Reset form fields
            fetchIllustrators(); // Refresh the list after addition
            const addModal = new bootstrap.Modal(document.getElementById('addIllustratorModal'));
            addModal.hide(); // Close the modal
        })
        .catch(err => console.error('Error adding illustrator:', err));
    });

    // Initial fetch of illustrators
    fetchIllustrators();
});
</script>

<?php 
include_once '../includes/emp-footer.php';
?>
