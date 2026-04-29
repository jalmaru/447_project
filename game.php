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
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>
                <?php if ($game): ?>
                    <a href="team.php?name=<?php echo urlencode($game['AwayTeam']); ?>"><?php echo htmlspecialchars($game['AwayTeam']); ?></a>
                    @
                    <a href="team.php?name=<?php echo urlencode($game['HomeTeam']); ?>"><?php echo htmlspecialchars($game['HomeTeam']); ?></a>
                <?php else: ?>
                    Game Not Found
                <?php endif; ?>
            </h2>
            <div class="nav-links">
                <a href="search.php" class="btn btn-secondary">&larr; Back to Search</a>
                <a href="dashboard.php" class="btn btn-secondary">Dashboard</a>
            </div>
        </div>

        <?php if ($game): ?>
            <div class="info">
                <p><strong>Date:</strong> <?php echo htmlspecialchars($game['GameDate']); ?></p>
                <p><strong>Stadium:</strong>
                    <?php if ($game['StadiumName']): ?>
                        <a href="search.php?q=<?php echo urlencode($game['StadiumName']); ?>&filter=stadiums"><?php echo htmlspecialchars($game['StadiumName']); ?></a>
                    <?php else: ?>
                        TBD
                    <?php endif; ?>
                </p>
                <p><strong>Winner:</strong> <?php echo $game['Winner'] ? htmlspecialchars($game['Winner']) : 'TBD'; ?></p>
            </div>

            <h3>Box Score</h3>
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
                            <a href="team.php?name=<?php echo urlencode($bs['TeamName']); ?>">
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
        <?php endif; ?>
    </div>
</body>
</html>
