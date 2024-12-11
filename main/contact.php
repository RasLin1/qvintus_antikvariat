<?php 
include '../includes/header.php';
?>

<div class="container">

<div class="row" id="site-title">
    <h3>Kontakta oss</h3>
</div>

<div id="contact-form" class="row">
    <div class="col-6">
        <form method="POST">
            <label for="cus_name">Namn:</label>
            <input name="cus_name" id="cus_name" type="text" /><br><br>
            <label for="cus_phone">Telefon:</label>
            <input name="cus_phone" id="cus_phone" type="tel" pattern="[0-9]{10}"/><br><br>
            <label for="cus_name">E-post:</label>
            <input name="cus_name" id="cus_name" type="email" /><br><br>
            <label for="cus_name">Ärende:</label>
            <input name="cus_name" id="cus_name" type="text" /><br><br>
        </form>
    </div>
    <div class="col-6" id="store-location">

    </div>
</div>

</div>

<style>
    .container {
        color: white;
    }
</style>

<?php 
include '../includes/footer.php';
?>