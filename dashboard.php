<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header("Location: index.php");
    exit();
}
$conn = mysqli_connect('localhost', 'root', '', 'school_project');

// Handle Log Out
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

$fav_team = $_SESSION['favorite_team'];
$username = $_SESSION['username'];

// Handle Add to Favorites
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['favorite_game'])) {
    $game_id = (int)$_POST['game_id'];
    
    // Check if it's already favorited to prevent duplicate errors
    $check_query = "SELECT * FROM favorites WHERE Username = '$username' AND GameID = $game_id";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) == 0) {
        $insert_fav = "INSERT INTO favorites (Username, GameID) VALUES ('$username', $game_id)";
        mysqli_query($conn, $insert_fav);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Football Stats Dashboard</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; padding: 40px; color: #333; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .nav-links { display: flex; gap: 10px; align-items: center; }
        a.btn, button.btn-fav { padding: 8px 15px; background-color: #0056b3; color: white; text-decoration: none; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; }
        a.btn-danger { background-color: #dc3545; }
        a.btn-success { background-color: #28a745; }
        button.btn-fav:hover { background-color: #004494; }
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
                <h2>Welcome, <?php echo htmlspecialchars($username); ?></h2>
                <p>Favorite Team: <strong><?php echo $fav_team ? htmlspecialchars($fav_team) : 'None selected'; ?></strong></p>
            </div>
            <div class="nav-links">
                <a href="favorites.php" class="btn btn-success">My Favorites</a>
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
                <th>Action</th>
            </tr>
            <?php
            // Updated to use s.StadiumName based on new schema
            $query = "
                SELECT g.GameID, g.GameDate, g.HomeTeam, g.AwayTeam, g.Winner, s.StadiumName 
                FROM game g
                LEFT JOIN stadium s ON g.StadiumID = s.StadiumID
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
                            <td>" . ($row['Winner'] ? $row['Winner'] : 'TBD') . "</td>
                            <td>" . ($row['StadiumName'] ? $row['StadiumName'] : 'TBD') . "</td>
                            <td>
                                <form method='POST' style='margin:0;'>
                                    <input type='hidden' name='game_id' value='{$row['GameID']}'>
                                    <button type='submit' name='favorite_game' class='btn-fav'>Favorite</button>
                                </form>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No games found or error in query.</td></tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>