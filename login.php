<?php
session_start();
include("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $_SESSION["user_id"] = $row["id"];
        $_SESSION["role"] = $row["role"];

        if ($row["role"] == "admin") {
            header("location: admin_dashboard.php");
        } elseif ($row["role"] == "user") {
            header("location: user_dashboard.php");
        }
    } else {
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Log In Page</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    
    <style>
        body {
            background: linear-gradient(to right, #283048, #859398); /* Darker Gradient */
            color: #fff; /* Set text color to white for better readability */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <form method="post">
            <h2>LOGIN</h2>
            <?php if (isset($error)) { ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php } ?>
            <div class="form-group">
                <label for="username">USERNAME</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">PASSWORD</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-secondary">LOGIN</button>
        </form>
    </div>
</body>
</html>
