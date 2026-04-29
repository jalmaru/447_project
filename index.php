<?php
session_start();
$error = '';

// Database connection
$conn = mysqli_connect('localhost', 'root', '', 'school_project');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM Users WHERE Username = '$username' AND Password = '$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $row['Username'];
        $_SESSION['favorite_team'] = $row['FavoriteTeam'];
        
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid credentials!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>NFL Database Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="brand">
        <img src="https://upload.wikimedia.org/wikipedia/en/a/a2/National_Football_League_logo.svg" alt="NFL Logo">
        <h1>NFL Database</h1>
    </div>
    <div class="login-card">
        <h2>User Login</h2>
        <?php if ($error) echo "<div class='error'>$error</div>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="btn">Log In</button>
        </form>
        <a href="create_user.php" class="btn btn-success">Create New User</a>
    </div>
</body>
</html>