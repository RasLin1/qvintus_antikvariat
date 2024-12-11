<?php
include_once '../includes/emp-header.php';

checkUserRole(3, "book-editor.php");
?>

<div class="container" id="publisher_editor_area">
    <h3 class="my-4">Publisher Editor</h3>
    <div class="row justify-content-center" id="publisher_">
        <!-- Publishers will be dynamically injected here -->
    </div>
    <div class="row" id="add_area">
        <div class="container">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPublisherModal">
                Add New Publisher
            </button>
        </div>
        <?php include '../includes/modals.php'; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const publisherContainer = document.getElementById('publisher_');
    const addPublisherForm = document.getElementById('addPublisherForm');

    // Fetch and render publishers
    function fetchPublishers() {
        fetch('../includes/dynamicAJAX/ajax_handler.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ action: 'fetch', type: 'publishers' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.data) {
                publisherContainer.innerHTML = '';
                data.data.forEach(pub => {
                    publisherContainer.innerHTML += `
                        <div class="col-12 col-md-4 col-lg-3 mb-4 mx-2 d-flex justify-content-center">
                            <div class="card book-card flex-fill">
                                <h5 class="card-title my-2">${pub.pub_name}</h5>
                                <div class="card-footer d-flex justify-content-center align-items-center">
                                    <button class="btn btn-danger my-1" onclick="deletePublisher(${pub.pub_id})">Delete Publisher</button>
                                </div>
                            </div>
                        </div>`;
                });
            }
        })
        .catch(err => console.error('Error fetching publishers:', err));
    }

    // Delete publisher
    window.deletePublisher = function(publisherId) {
        fetch('../includes/dynamicAJAX/ajax_handler.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'delete',
                type: 'publishers',
                id: publisherId,
                depTab: 'books',
                depCol: 'publisher_fk',
                tab: 'publishers',
                col: 'pub_id'
            })
        })
        .then(response => response.json())
        .then(data => {
            fetchPublishers(); // Refresh the list after deletion
        })
        .catch(err => console.error('Error deleting publisher:', err));
    };

    // Add new publisher
    addPublisherForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(addPublisherForm);
        const publisherName = formData.get('publisherName');

        fetch('../includes/dynamicAJAX/ajax_handler.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'insert',
                type: 'publishers',
                'data[columns][]': 'pub_name',
                'data[values][]': publisherName
            })
        })
        .then(response => response.json())
        .then(data => {
            addPublisherForm.reset(); // Reset form fields
            fetchPublishers(); // Refresh the list after addition
            const addModal = new bootstrap.Modal(document.getElementById('addPublisherModal'));
            addModal.hide(); // Close the modal
        })
        .catch(err => console.error('Error adding publisher:', err));
    });

    // Initial fetch of publishers
    fetchPublishers();
});
</script>

<?php 
include_once '../includes/emp-footer.php';
?>