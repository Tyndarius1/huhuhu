<?php
session_start();
include("config.php");

if (!isset($_SESSION["user_id"]) || !isset($_SESSION["role"])) {
    header("location: login.php");
    exit();
}

$userID = $_SESSION["user_id"];
$sql = "SELECT * FROM users WHERE id = '$userID'";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $userName = $row["username"];
} else {
    $userName = "User";
}

// Handle file upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profilePictures"])) {
    $targetDir = "uploads/";
    $uploadedFiles = $_FILES["profilePictures"];

    foreach ($uploadedFiles["tmp_name"] as $key => $tmp_name) {
        $targetFile = $targetDir . basename($uploadedFiles["name"][$key]);
        if (move_uploaded_file($tmp_name, $targetFile)) {
            $insertProfilePicture = "INSERT INTO user_profile (user_id, name, profile_picture) VALUES ('$userID', '$userName', '$targetFile')";
            $conn->query($insertProfilePicture);
        } else {
            echo "Error uploading file.";
        }
    }
}

// Delete uploaded photo
if (isset($_GET["delete"]) && isset($_GET["photo_id"])) {
    $photoID = $_GET["photo_id"];
    $deletePhotoQuery = "DELETE FROM user_profile WHERE id = '$photoID' AND user_id = '$userID'";
    $conn->query($deletePhotoQuery);
    header("Location: user_dashboard.php"); // Redirect to refresh the page
}

// Fetch all profile pictures for the current user
$sqlUserProfilePictures = "SELECT * FROM user_profile WHERE user_id = '$userID'";
$resultUserPictures = $conn->query($sqlUserProfilePictures);
$userProfilePictures = array();

while ($rowUserPicture = $resultUserPictures->fetch_assoc()) {
    $userProfilePictures[] = $rowUserPicture;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>USER DASHBOARD</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">

    <style>
        body {
            background: linear-gradient(to right, #283048, #859398); /* Darker Gradient */
            color: #fff; /* Set text color to white for better readability */
        }
    </style>
</head>
<body>
    <div class="container mt-2">
        <h2>Welcome, <?php echo $userName; ?>!</h2>
        <p><a href="logout.php">LOGOUT</a></p>

        <!-- File Upload Form for Multiple Pictures -->
        <form method="post" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="profilePictures">CHOOSE FILE</label>
                <input type="file" class="form-control-file" id="profilePictures" name="profilePictures[]" accept="image/*" multiple required>
            </div>
            <button type="submit" class="btn btn-primary">UPLOAD</button>
        </form>

        <hr>

        <!--logic to view my uploaded image-->
        <h3>PHOTO YOU UPLOADED</h3>
        <?php if (count($userProfilePictures) > 0) { ?>
            <div class="row">
                <?php foreach ($userProfilePictures as $userPicture) { ?>
                    <div class="col-md-4 mb-4">
                        <img src="<?php echo $userPicture['profile_picture']; ?>" alt="Profile Picture" class="img-thumbnail">
                        <p>
                            UPLOADED BY: <?php echo $userPicture['name']; ?>
                            <br>
                        </p>
                    </div>
                <?php } ?>
            </div>
        <?php } else { ?>
            <p>No images uploaded by you yet.</p>
        <?php } ?>

        <hr>

        <!-- Link to View Other Users' Images -->
        <h3>View Other Users' Images</h3>
        <p><a href="view_other_images.php">View images uploaded by other users</a></p>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
