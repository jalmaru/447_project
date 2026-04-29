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
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; padding: 40px; color: #333; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .nav-links { display: flex; gap: 10px; align-items: center; }
        a.btn { padding: 8px 15px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 4px; font-size: 14px; }
        a.btn-primary { background-color: #0056b3; }
        a.btn-primary:hover { background-color: #004494; }
        .info { background-color: #f8f9fa; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #28a745; color: white; }
        tr:hover { background-color: #f1f1f1; }
        .empty { color: #888; font-style: italic; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2><?php echo $team ? htmlspecialchars($team['TeamName']) : 'Team Not Found'; ?></h2>
            <div class="nav-links">
                <a href="search.php" class="btn">&larr; Back to Search</a>
                <a href="dashboard.php" class="btn">Dashboard</a>
            </div>
        </div>

        <?php if ($team): ?>
            <div class="info">
                <p><strong>Division:</strong> <?php echo htmlspecialchars($team['Division']); ?></p>
                <p>
                    <a class="btn btn-primary" href="search.php?q=<?php echo urlencode($team['TeamName']); ?>&filter=games">Games</a>
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
