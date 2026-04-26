<?php
session_start();

if (!isset($_SESSION['logged_in'])) {
    header("Location: index.php");
    exit();
}

$conn = mysqli_connect('localhost', 'root', '', 'school_project');

$team_results = [];
$player_results = [];
$search_query = "";

if (isset($_GET['q']) && !empty($_GET['q'])) {
    $search_query = mysqli_real_escape_string($conn, $_GET['q']);

    $team_sql = "SELECT TeamName, Division FROM Teams WHERE TeamName LIKE '%$search_query%'";
    $team_res = mysqli_query($conn, $team_sql);
    if ($team_res) {
        while ($row = mysqli_fetch_assoc($team_res)) {
            $team_results[] = $row;
        }
    }

    $player_sql = "SELECT PlayerName, Position, TeamName FROM Players WHERE PlayerName LIKE '%$search_query%'";
    $player_res = mysqli_query($conn, $player_sql);
    if ($player_res) {
        while ($row = mysqli_fetch_assoc($player_res)) {
            $player_results[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Database</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; padding: 40px; color: #333; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        a.btn { padding: 8px 15px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 4px; font-size: 14px; }
        .search-box { display: flex; margin-bottom: 30px; }
        .search-box input[type="text"] { flex-grow: 1; padding: 12px; border: 1px solid #ccc; border-radius: 4px 0 0 4px; font-size: 16px; }
        .search-box button { padding: 12px 24px; background-color: #0056b3; color: white; border: none; border-radius: 0 4px 4px 0; cursor: pointer; font-size: 16px; }
        .search-box button:hover { background-color: #004494; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 30px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #0056b3; color: white; }
        tr:hover { background-color: #f1f1f1; }
        .no-results { color: #888; font-style: italic; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Database Search</h2>
            <a href="dashboard.php" class="btn">Back to Dashboard</a>
        </div>

        <form class="search-box" method="GET" action="search.php">
            <input type="text" name="q" placeholder="Search for a team or player..." value="<?php echo htmlspecialchars($search_query); ?>" required>
            <button type="submit">Search</button>
        </form>

        <?php if (!empty($search_query)): ?>
            <h3>Search Results for: "<?php echo htmlspecialchars($search_query); ?>"</h3>

            <h4>Teams</h4>
            <?php if (count($team_results) > 0): ?>
                <table>
                    <tr>
                        <th>Team Name</th>
                        <th>Division</th>
                    </tr>
                    <?php foreach ($team_results as $team): ?>
                        <tr>
                            <td><strong><?php echo $team['TeamName']; ?></strong></td>
                            <td><?php echo $team['Division']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p class="no-results">No teams found matching your search.</p>
            <?php endif; ?>

            <h4>Players</h4>
            <?php if (count($player_results) > 0): ?>
                <table>
                    <tr>
                        <th>Player Name</th>
                        <th>Position</th>
                        <th>Team</th>
                    </tr>
                    <?php foreach ($player_results as $player): ?>
                        <tr>
                            <td><strong><?php echo $player['PlayerName']; ?></strong></td>
                            <td><?php echo $player['Position']; ?></td>
                            <td><?php echo $player['TeamName']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p class="no-results">No players found matching your search.</p>
            <?php endif; ?>

        <?php endif; ?>
    </div>
</body>
</html>