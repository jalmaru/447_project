<?php
session_start();
$error = '';
$success = '';

$conn = mysqli_connect('localhost', 'root', '', 'school_project');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $favorite_team = trim($_POST['favorite_team']);

    if ($username === '' || $password === '' || $favorite_team === '') {
        $error = "Username, password, and favorite team are required.";
    } else {
        $check_query = "SELECT Username FROM Users WHERE Username = '" . mysqli_real_escape_string($conn, $username) . "'";
        $check_result = mysqli_query($conn, $check_query);

        if ($check_result && mysqli_num_rows($check_result) > 0) {
            $error = "That username is already taken.";
        } else {
            $u = mysqli_real_escape_string($conn, $username);
            $p = mysqli_real_escape_string($conn, $password);
            $t = mysqli_real_escape_string($conn, $favorite_team);

            if ($favorite_team === 'None') {
                $insert_query = "INSERT INTO Users (Username, Password, FavoriteTeam) VALUES ('$u', '$p', NULL)";
            } else {
                $insert_query = "INSERT INTO Users (Username, Password, FavoriteTeam) VALUES ('$u', '$p', '$t')";
            }

            if (mysqli_query($conn, $insert_query)) {
                $success = "Account created successfully! You can now log in.";
            } else {
                $error = "Error creating account: " . mysqli_error($conn);
            }
        }
    }
}

$teams_result = mysqli_query($conn, "SELECT TeamName FROM Teams ORDER BY TeamName ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create New User</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="login-card">
        <h2>Create New User</h2>
        <?php if ($error) echo "<div class='error'>$error</div>"; ?>
        <?php if ($success) echo "<div class='success'>$success</div>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="favorite_team" required>
                <option value="" disabled selected>-- Select Favorite Team --</option>
                <option value="None">None</option>
                <?php
                if ($teams_result) {
                    while ($team = mysqli_fetch_assoc($teams_result)) {
                        $name = htmlspecialchars($team['TeamName']);
                        echo "<option value=\"$name\">$name</option>";
                    }
                }
                ?>
            </select>
            <button type="submit" class="btn btn-success">Create Account</button>
        </form>
        <a href="index.php" class="btn btn-secondary">&larr; Back to Login</a>
    </div>
</body>
</html>
