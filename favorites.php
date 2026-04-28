<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header("Location: index.php");
    exit();
}
$conn = mysqli_connect('localhost', 'root', '', 'school_project');
$username = $_SESSION['username'];

// Handle Remove Favorite
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_favorite'])) {
    $game_id = (int)$_POST['game_id'];
    $delete_fav = "DELETE FROM favorites WHERE Username = '$username' AND GameID = $game_id";
    mysqli_query($conn, $delete_fav);
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
        a.btn, button.btn-danger { padding: 8px 15px; background-color: #0056b3; color: white; text-decoration: none; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; }
        button.btn-danger { background-color: #dc3545; }
        button.btn-danger:hover { background-color: #c82333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #28a745; color: white; }
        tr:hover { background-color: #f1f1f1; }
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

        <table>
            <tr>
                <th>Date</th>
                <th>Matchup</th>
                <th>Winner</th>
                <th>Stadium</th>
                <th>Action</th>
            </tr>
            <?php
            // Join favorites table with game and stadium
            $query = "
                SELECT f.GameID, g.GameDate, g.HomeTeam, g.AwayTeam, g.Winner, s.StadiumName 
                FROM favorites f
                JOIN game g ON f.GameID = g.GameID
                LEFT JOIN stadium s ON g.StadiumID = s.StadiumID
                WHERE f.Username = '$username'
                ORDER BY g.GameDate DESC
            ";
            $result = mysqli_query($conn, $query);

            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $matchup = $row['AwayTeam'] . " @ " . $row['HomeTeam'];
                    echo "<tr>
                            <td>{$row['GameDate']}</td>
                            <td>{$matchup}</td>
                            <td>" . ($row['Winner'] ? $row['Winner'] : 'TBD') . "</td>
                            <td>" . ($row['StadiumName'] ? $row['StadiumName'] : 'TBD') . "</td>
                            <td>
                                <form method='POST' style='margin:0;'>
                                    <input type='hidden' name='game_id' value='{$row['GameID']}'>
                                    <button type='submit' name='remove_favorite' class='btn-danger'>Remove</button>
                                </form>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>You haven't favorited any games yet.</td></tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>