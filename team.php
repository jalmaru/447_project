<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header("Location: index.php");
    exit();
}
$conn = mysqli_connect('localhost', 'root', '', 'school_project');

$team_name = isset($_GET['name']) ? $_GET['name'] : '';
$team = null;
$players = [];

if ($team_name !== '') {
    $safe_name = mysqli_real_escape_string($conn, $team_name);

    $team_sql = "SELECT TeamName, Division FROM teams WHERE TeamName = '$safe_name' LIMIT 1";
    $team_res = mysqli_query($conn, $team_sql);
    if ($team_res && mysqli_num_rows($team_res) > 0) {
        $team = mysqli_fetch_assoc($team_res);

        $players_sql = "SELECT PlayerName, Position FROM players WHERE TeamName = '$safe_name' ORDER BY PlayerName ASC";
        $players_res = mysqli_query($conn, $players_sql);
        if ($players_res) {
            while ($row = mysqli_fetch_assoc($players_res)) {
                $players[] = $row;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $team ? htmlspecialchars($team['TeamName']) : 'Team Not Found'; ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h2><?php echo $team ? htmlspecialchars($team['TeamName']) : 'Team Not Found'; ?></h2>
            <div class="nav-links">
                <a href="search.php" class="btn btn-secondary">&larr; Back to Search</a>
                <a href="dashboard.php" class="btn btn-secondary">Dashboard</a>
            </div>
        </div>

        <?php if ($team): ?>
            <div class="info">
                <p><strong>Division:</strong> <?php echo htmlspecialchars($team['Division']); ?></p>
                <p>
                    <a class="btn" href="search.php?q=<?php echo urlencode($team['TeamName']); ?>&filter=games">Games</a>
                </p>
            </div>

            <h3>Roster</h3>
            <table>
                <tr>
                    <th>Player Name</th>
                    <th>Position</th>
                </tr>
                <?php foreach ($players as $p): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($p['PlayerName']); ?></td>
                        <td><?php echo htmlspecialchars($p['Position']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
