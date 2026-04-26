<?php
session_start();

if (!isset($_SESSION['logged_in'])) {
    header("Location: index.php");
    exit();
}

$conn = mysqli_connect('localhost', 'root', '', 'school_project');

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

$fav_team = $_SESSION['favorite_team'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Football Stats Dashboard</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; padding: 40px; color: #333; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .nav-links { display: flex; gap: 10px; align-items: center; }
        a.btn { padding: 8px 15px; background-color: #0056b3; color: white; text-decoration: none; border-radius: 4px; font-size: 14px; }
        a.btn-danger { background-color: #dc3545; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #0056b3; color: white; }
        tr:hover { background-color: #f1f1f1; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h2>Welcome, <?php echo $_SESSION['username']; ?></h2>
                <p>Favorite Team: <strong><?php echo $fav_team ? $fav_team : 'None selected'; ?></strong></p>
            </div>
            <div class="nav-links">
                <a href="search.php" class="btn">Search Database</a>
                <a href="?logout=true" class="btn btn-danger">Log Out</a>
            </div>
        </div>

        <h3>Game Schedule & Results</h3>
        <table>
            <tr>
                <th>Date</th>
                <th>Matchup</th>
                <th>Winner</th>
                <th>Stadium</th>
            </tr>
            <?php
            $query = "
                SELECT g.GameDate, g.HomeTeam, g.AwayTeam, g.Winner, s.Name AS StadiumName 
                FROM Game g
                LEFT JOIN Stadium s ON g.StadiumID = s.StadiumID
                ORDER BY g.GameDate DESC
            ";
            $result = mysqli_query($conn, $query);

            if ($result) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $matchup = $row['AwayTeam'] . " @ " . $row['HomeTeam'];
                    $isFav = ($row['HomeTeam'] == $fav_team || $row['AwayTeam'] == $fav_team) ? 'style="background-color: #e6f2ff;"' : '';
                    
                    echo "<tr $isFav>
                            <td>{$row['GameDate']}</td>
                            <td>{$matchup}</td>
                            <td>{$row['Winner']}</td>
                            <td>{$row['StadiumName']}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No games found or error in query.</td></tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>