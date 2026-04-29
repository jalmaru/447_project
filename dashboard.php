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

// Handle Change Favorite Team
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_favorite_team'])) {
    $new_team = trim($_POST['favorite_team']);
    $safe_user = mysqli_real_escape_string($conn, $username);

    if ($new_team === 'None' || $new_team === '') {
        $update_sql = "UPDATE Users SET FavoriteTeam = NULL WHERE Username = '$safe_user'";
        mysqli_query($conn, $update_sql);
        $_SESSION['favorite_team'] = null;
        $fav_team = null;
    } else {
        $safe_team = mysqli_real_escape_string($conn, $new_team);
        $update_sql = "UPDATE Users SET FavoriteTeam = '$safe_team' WHERE Username = '$safe_user'";
        mysqli_query($conn, $update_sql);
        $_SESSION['favorite_team'] = $new_team;
        $fav_team = $new_team;
    }
}

$teams_for_dropdown = mysqli_query($conn, "SELECT TeamName FROM teams ORDER BY TeamName ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Football Stats Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h2>Welcome, <?php echo htmlspecialchars($username); ?></h2>
                <p>
                    Favorite Team: <strong><?php echo $fav_team ? htmlspecialchars($fav_team) : 'None selected'; ?></strong>
                    <details class="fav-team-toggle">
                        <summary>Change Favorite Team</summary>
                        <form method="POST" class="fav-team-form">
                            <select name="favorite_team">
                                <option value="None" <?php if (!$fav_team) echo 'selected'; ?>>None</option>
                                <?php
                                if ($teams_for_dropdown) {
                                    while ($t = mysqli_fetch_assoc($teams_for_dropdown)) {
                                        $name = htmlspecialchars($t['TeamName']);
                                        $sel = ($fav_team === $t['TeamName']) ? 'selected' : '';
                                        echo "<option value=\"$name\" $sel>$name</option>";
                                    }
                                }
                                ?>
                            </select>
                            <button type="submit" name="change_favorite_team" class="btn">Save</button>
                        </form>
                    </details>
                </p>
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
                    $matchup = htmlspecialchars($row['AwayTeam']) . " @ " . htmlspecialchars($row['HomeTeam']);
                    $gameLink = "game.php?id=" . urlencode($row['GameID']);
                    $isFav = ($row['HomeTeam'] == $fav_team || $row['AwayTeam'] == $fav_team) ? 'style="background-color: #e6f2ff;"' : '';
                    
                    echo "<tr $isFav>
                            <td>{$row['GameDate']}</td>
                            <td><a href='{$gameLink}'>{$matchup}</a></td>
                            <td>" . ($row['Winner'] ? $row['Winner'] : 'TBD') . "</td>
                            <td>" . ($row['StadiumName'] ? $row['StadiumName'] : 'TBD') . "</td>
                            <td>
                                <form method='POST' style='margin:0;'>
                                    <input type='hidden' name='game_id' value='{$row['GameID']}'>
                                    <button type='submit' name='favorite_game' class='btn'>Favorite</button>
                                </form>
                            </td>
                          </tr>";
                }
            }
            ?>
        </table>
    </div>
</body>
</html>