<?php
session_start();
$error = '';
$success = '';

$conn = mysqli_connect('localhost', 'root', '', 'school_project');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $favorite_team = trim($_POST['favorite_team']);

    if ($username === '' || $password === '') {
        $error = "Username and password are required.";
    } else {
        $check_query = "SELECT Username FROM Users WHERE Username = '" . mysqli_real_escape_string($conn, $username) . "'";
        $check_result = mysqli_query($conn, $check_query);

        if ($check_result && mysqli_num_rows($check_result) > 0) {
            $error = "That username is already taken.";
        } else {
            $u = mysqli_real_escape_string($conn, $username);
            $p = mysqli_real_escape_string($conn, $password);
            $t = mysqli_real_escape_string($conn, $favorite_team);

            if ($t === '') {
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
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-card { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 320px; text-align: center; }
        input, select { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background-color: #1e7e34; }
        .error { color: red; font-size: 14px; margin-bottom: 10px; }
        .success { color: #155724; background-color: #d4edda; padding: 8px; border-radius: 4px; font-size: 14px; margin-bottom: 10px; }
        .back-link { display: block; margin-top: 15px; color: #0056b3; text-decoration: none; font-size: 14px; }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>Create New User</h2>
        <?php if ($error) echo "<div class='error'>$error</div>"; ?>
        <?php if ($success) echo "<div class='success'>$success</div>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="favorite_team">
                <option value="">-- Select Favorite Team (optional) --</option>
                <?php
                if ($teams_result) {
                    while ($team = mysqli_fetch_assoc($teams_result)) {
                        $name = htmlspecialchars($team['TeamName']);
                        echo "<option value=\"$name\">$name</option>";
                    }
                }
                ?>
            </select>
            <button type="submit">Create Account</button>
        </form>
        <a href="index.php" class="back-link">&larr; Back to Login</a>
    </div>
</body>
</html>
