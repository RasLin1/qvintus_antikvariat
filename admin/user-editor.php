<?php
ob_start();
include_once '../includes/emp-header.php';

checkUserRole(3, "book-editor.php");

$stmt_fetchAllUsers = $pdo->query("SELECT * FROM employee e JOIN employee_roles er ON e.emp_role_fk = er.role_id");
$users = $stmt_fetchAllUsers->fetchAll(PDO::FETCH_ASSOC);

$stmt_fetchAllUserRoles = $pdo->query("SELECT * FROM employee");
$roles = $stmt_fetchAllUserRoles->fetchAll(PDO::FETCH_ASSOC);

if(isset($_POST['deleteUser'])){
    $deleteResult = deleteUser($pdo, $_POST['currUserId'], "user-editor.php");
}
?>

<div class="container" id="user_editor_area">
    <div class="row justify-content-center" id="allUserDisplayArea">
    <?php 
        foreach ($users as $user) {
            // Generate card HTML for each genre
            echo "
                <div class='col-md-3 col-12 d-flex my-2'>
                    <div class='card flex-fill' style='width: ;'>
                        <div class='card-body'>
                            <h5 class='card-title'>{$user['emp_fname']} {$user['emp_lname']} </h5>
                            <p class='card-text'>{$user['role_name']}</p>
                            <div class='d-flex justify-content-center'>
                                <button type='button' class='btn btn-warning edit-user-btn' data-bs-toggle='modal' data-bs-target='#editUserModal'
                                data-user-id='".$user['emp_id']."'
                                data-username='".$user['emp_uname']."'
                                data-first-name='".$user['emp_fname']."'
                                data-last-name='".$user['emp_lname']."'
                                data-role='".$user['emp_role_fk']."'>
                                
                                    Edit User
                                </button>
                                <span>|</span>
                                <form method='POST'>
                                    <input type='hidden' name='currUserId' id='currUserId' value='{$user['emp_id']}'/>
                                    <input class='btn btn-danger' type='submit' value='Delete User' name='deleteUser' id='deleteUser'/>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            ";
        }
    ?>
    </div>
    <div class="row justify-content-center my-2" id="newUserButton">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal" style="width: 10vw;">
            Create New User
        </button>
    </div>
</div>

<?php include '../includes/modals.php'; ?>

<script>

</script>

<?php 
include_once '../includes/emp-footer.php';
?>
