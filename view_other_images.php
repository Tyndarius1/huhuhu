<?php
session_start();
include("config.php");

if (!isset($_SESSION["user_id"])) {
    header("location: login.php");
    exit();
}

$userID = $_SESSION["user_id"];
$sqlUser = "SELECT * FROM users WHERE id = '$userID'";
$resultUser = $conn->query($sqlUser);

if ($resultUser->num_rows == 1) {
    $rowUser = $resultUser->fetch_assoc();
    $userName = $rowUser["username"];
} else {
    $userName = "User";
}

// Fetch all profile pictures for other users
$sqlOtherProfilePictures = "SELECT * FROM user_profile WHERE user_id != '$userID'";
$resultOtherProfilePictures = $conn->query($sqlOtherProfilePictures);
$otherProfilePictures = array();

while ($rowOtherProfilePicture = $resultOtherProfilePictures->fetch_assoc()) {
    $otherProfilePictures[] = $rowOtherProfilePicture;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Other Users' Images</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Images Uploaded by Other Users</h2>
        <p><a href="user_dashboard.php">Back to Dashboard</a></p>

        <?php if (count($otherProfilePictures) > 0) { ?>
            <div class="row">
                <?php foreach ($otherProfilePictures as $otherPicture) { ?>
                    <div class="col-md-4 mb-4">
                        <img src="<?php echo $otherPicture['profile_picture']; ?>" alt="Profile Picture" class="img-thumbnail">
                        <p>Uploaded by: <?php echo $otherPicture['name']; ?></p>
                    </div>
                <?php } ?>
            </div>
        <?php } else { ?>
            <p>No images uploaded by other users yet.</p>
        <?php } ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
