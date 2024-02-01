<?php
session_start();
include("config.php");

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("location: login.php");
    exit();
}

// this is the function to get all usermin the database
function getAllUsers() {
    global $conn;
    $sql = "SELECT * FROM users";
    $result = $conn->query($sql);

    $users = array();
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    return $users;
}

// this will get the admin's name base on the database using ID
$userID = $_SESSION["user_id"];
$sql = "SELECT * FROM users WHERE id = '$userID'";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $adminName = $row["username"];
} else {
    // Handle error if user is not found
    $adminName = "Admin";
}

// soo this is the logic for the admin to add new user, update user, and delete user
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["addUser"])) {
        $newUsername = $_POST["newUsername"];
        $newPassword = $_POST["newPassword"];
        $newRole = $_POST["newRole"];

        //this is to determine if the username is unique and error if username is already in use
        $checkUsername = "SELECT * FROM users WHERE username = '$newUsername'";
        $result = $conn->query($checkUsername);

        if ($result->num_rows > 0) {
            $addUserError = "Username already exists. Please choose a different username.";
        } else {
            // Insert new user into the database
            $insertUser = "INSERT INTO users (username, password, role) VALUES ('$newUsername', '$newPassword', '$newRole')";
            if ($conn->query($insertUser) === TRUE) {
                header("location: admin_dashboard.php");
                exit();
            } else {
                $addUserError = "Error adding user. Please try again.";
            }
        }
    } elseif (isset($_POST["updateUser"])) {
        $userIDToUpdate = $_POST["userIDToUpdate"];
        $updatedUsername = $_POST["updatedUsername"];
        $updatedPassword = $_POST["updatedPassword"];
        $updatedRole = $_POST["updatedRole"];

        // admin can update user information such as password and username and role in the admin_dashboard 
        $updateUser = "UPDATE users SET username = '$updatedUsername', password = '$updatedPassword', role = '$updatedRole' WHERE id = '$userIDToUpdate'";
        if ($conn->query($updateUser) === TRUE) {
            header("location: admin_dashboard.php");
            exit();
        } else {
            $updateUserError = "Error updating user. Please try again.";
        }
    } elseif (isset($_POST["deleteUser"])) {
        $userIDToDelete = $_POST["userIDToDelete"];

        // Delete user from the database
        $deleteUser = "DELETE FROM users WHERE id = '$userIDToDelete'";
        if ($conn->query($deleteUser) === TRUE) {
            header("location: admin_dashboard.php");
            exit();
        } else {
            $deleteUserError = "Error deleting user. Please try again.";
        }
    }
}

// Fetch all users
$allUsers = getAllUsers();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WELCOME ADMIN</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Welcome, <?php echo $adminName; ?>!</h2>
        <p><a href="logout.php">Logout</a></p>
        <h2>ADMIN PANEL</h2>
        <h3>ALL USERS</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>USERNAME</th>
                    <th>ROLE</th>
                    <th>ACTION</th>
                </tr>
            </thead>
            <tbody>
    <?php foreach ($allUsers as $user) { ?>
        <tr class="<?php echo ($user["role"] == 'admin') ? 'table-danger' : 'table-success'; ?>">
            <td><?php echo $user["id"]; ?></td>
            <td><?php echo $user["username"]; ?></td>
            <td><?php echo $user["role"]; ?></td>
            <td>
                <a href="#" data-toggle="modal" data-target="#updateModal<?php echo $user["id"]; ?>">UPDATE</a>  ||
                <a href="#" data-toggle="modal" data-target="#deleteModal<?php echo $user["id"]; ?>">DELETE</a>
            </td>
        </tr>

                    <!-- Update Modal -->
                    <div class="modal fade" id="updateModal<?php echo $user["id"]; ?>" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel<?php echo $user["id"]; ?>" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="updateModalLabel<?php echo $user["id"]; ?>">UPDATE USER</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form method="post" action="">
                                        <input type="hidden" name="userIDToUpdate" value="<?php echo $user["id"]; ?>">
                                        <div class="form-group">
                                            <label for="updatedUsername">USERNAME</label>
                                            <input type="text" class="form-control" id="updatedUsername" name="updatedUsername" value="<?php echo $user["username"]; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="updatedPassword">PASSWORD</label>
                                            <input type="password" class="form-control" id="updatedPassword" name="updatedPassword" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="updatedRole">ROLE</label>
                                            <select class="form-control" id="updatedRole" name="updatedRole" required>
                                                <option value="user" <?php echo ($user["role"] == 'user') ? 'selected' : ''; ?>>User</option>
                                                <option value="admin" <?php echo ($user["role"] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary" name="updateUser">UPDATE USER</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Delete Modal -->
                    <div class="modal fade" id="deleteModal<?php echo $user["id"]; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel<?php echo $user["id"]; ?>" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteModalLabel<?php echo $user["id"]; ?>">DELETE THIS USER?</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p>ARE YOU SURE YOU WANT TO DELETE THIS USER?</p>
                                    <form method="post" action="">
                                        <input type="hidden" name="userIDToDelete" value="<?php echo $user["id"]; ?>">
                                        <button type="submit" class="btn btn-danger" name="deleteUser"> DELETE USER </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </tbody>
        </table>

        <h3>ADD NEW</h3>
        <?php if (isset($addUserError)) { ?>
            <div class="alert alert-danger"><?php echo $addUserError; ?></div>
        <?php } ?>
        <form method="post" action="">
            <div class="form-group">
                <label for="newUsername">USERNAME</label>
                <input type="text" class="form-control" id="newUsername" name="newUsername" required>
            </div>
            <div class="form-group">
                <label for="newPassword">PASSWORD</label>
                <input type="password" class="form-control" id="newPassword" name="newPassword" required>
            </div>
            <div class="form-group">
                <label for="newRole">ROLE</label>
                <select class="form-control" id="newRole" name="newRole" required>
                    <option value="user">USER</option>
                    <option value="admin">ADMIN</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success" name="addUser">ADD USER</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
