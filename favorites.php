<?php
session_start();

if (!isset($_SESSION['logged_in'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Favorited Games</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; padding: 40px; color: #333; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .nav-links { display: flex; gap: 10px; align-items: center; }
        a.btn { padding: 8px 15px; background-color: #0056b3; color: white; text-decoration: none; border-radius: 4px; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Favorited Games</h2>
            <div class="nav-links">
                <a href="dashboard.php" class="btn">&larr; Back to Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>
