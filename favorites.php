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
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Favorited Games</h2>
            <div class="nav-links">
                <a href="dashboard.php" class="btn btn-secondary">&larr; Back to Dashboard</a>
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
                                    <button type='submit' name='remove_favorite' class='btn btn-danger'>Remove</button>
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