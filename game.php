<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header("Location: index.php");
    exit();
}
$conn = mysqli_connect('localhost', 'root', '', 'school_project');

$game_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$game = null;
$box_scores = [];

if ($game_id > 0) {
    $game_sql = "
        SELECT g.GameID, g.GameDate, g.HomeTeam, g.AwayTeam, g.Winner, s.StadiumName
        FROM game g
        LEFT JOIN stadium s ON g.StadiumID = s.StadiumID
        WHERE g.GameID = $game_id
        LIMIT 1
    ";
    $game_res = mysqli_query($conn, $game_sql);
    if ($game_res && mysqli_num_rows($game_res) > 0) {
        $game = mysqli_fetch_assoc($game_res);

        $stats_sql = "
            SELECT TeamName, TotalPoints, PassingYards, RushingYards, TurnoversForced
            FROM gamestats
            WHERE GameID = $game_id
        ";
        $stats_res = mysqli_query($conn, $stats_sql);
        if ($stats_res) {
            while ($row = mysqli_fetch_assoc($stats_res)) {
                $box_scores[] = $row;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $game ? htmlspecialchars($game['AwayTeam'] . ' @ ' . $game['HomeTeam']) : 'Game Not Found'; ?></title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; padding: 40px; color: #333; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .nav-links { display: flex; gap: 10px; align-items: center; }
        a.btn { padding: 8px 15px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 4px; font-size: 14px; }
        .info { background-color: #f8f9fa; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .info p { margin: 6px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #fd7e14; color: white; }
        tr:hover { background-color: #f1f1f1; }
        .empty { color: #888; font-style: italic; }
        a.team-link { color: #0056b3; text-decoration: none; }
        a.team-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>
                <?php if ($game): ?>
                    <a class="team-link" href="team.php?name=<?php echo urlencode($game['AwayTeam']); ?>"><?php echo htmlspecialchars($game['AwayTeam']); ?></a>
                    @
                    <a class="team-link" href="team.php?name=<?php echo urlencode($game['HomeTeam']); ?>"><?php echo htmlspecialchars($game['HomeTeam']); ?></a>
                <?php else: ?>
                    Game Not Found
                <?php endif; ?>
            </h2>
            <div class="nav-links">
                <a href="search.php" class="btn">&larr; Back to Search</a>
                <a href="dashboard.php" class="btn">Dashboard</a>
            </div>
        </div>

        <?php if ($game): ?>
            <div class="info">
                <p><strong>Date:</strong> <?php echo htmlspecialchars($game['GameDate']); ?></p>
                <p><strong>Stadium:</strong> <?php echo $game['StadiumName'] ? htmlspecialchars($game['StadiumName']) : 'TBD'; ?></p>
                <p><strong>Winner:</strong> <?php echo $game['Winner'] ? htmlspecialchars($game['Winner']) : 'TBD'; ?></p>
            </div>

            <h3>Box Score</h3>
            <?php if (count($box_scores) > 0): ?>
                <table>
                    <tr>
                        <th>Team</th>
                        <th>Points</th>
                        <th>Passing Yards</th>
                        <th>Rushing Yards</th>
                        <th>Turnovers Forced</th>
                    </tr>
                    <?php foreach ($box_scores as $bs): ?>
                        <tr>
                            <td>
                                <a class="team-link" href="team.php?name=<?php echo urlencode($bs['TeamName']); ?>">
                                    <?php echo htmlspecialchars($bs['TeamName']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($bs['TotalPoints']); ?></td>
                            <td><?php echo htmlspecialchars($bs['PassingYards']); ?></td>
                            <td><?php echo htmlspecialchars($bs['RushingYards']); ?></td>
                            <td><?php echo htmlspecialchars($bs['TurnoversForced']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p class="empty">No box score stats recorded for this game yet.</p>
            <?php endif; ?>
        <?php else: ?>
            <p class="empty">No game found with that ID.</p>
        <?php endif; ?>
    </div>
</body>
</html>
