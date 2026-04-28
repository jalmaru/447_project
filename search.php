<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header("Location: index.php");
    exit();
}
$conn = mysqli_connect('localhost', 'root', '', 'school_project');

$results = [];
$search_query = "";
$filter = "all";

if (isset($_GET['q']) && !empty($_GET['q'])) {
    $search_query = mysqli_real_escape_string($conn, $_GET['q']);
    $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

    // 1. Search Teams
    if ($filter == 'all' || $filter == 'teams') {
        $sql = "SELECT TeamName, Division FROM teams WHERE TeamName LIKE '%$search_query%'";
        $res = mysqli_query($conn, $sql);
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $results[] = [
                    'Type' => 'Team',
                    'Primary' => "<strong>{$row['TeamName']}</strong>",
                    'Secondary' => "Division: {$row['Division']}"
                ];
            }
        }
    }

    // 2. Search Players (Updated to remove Coach table join)
    if ($filter == 'all' || $filter == 'players') {
        $sql = "
            SELECT PlayerName, Position, TeamName 
            FROM players
            WHERE PlayerName LIKE '%$search_query%'
        ";
        $res = mysqli_query($conn, $sql);
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $results[] = [
                    'Type' => 'Player',
                    'Primary' => "<strong>{$row['PlayerName']}</strong> ({$row['Position']})",
                    'Secondary' => "Team: {$row['TeamName']}"
                ];
            }
        }
    }

    // 3. Search Stadiums (Updated for StadiumName column)
    if ($filter == 'all' || $filter == 'stadiums') {
        $sql = "SELECT StadiumName, City, Capacity FROM stadium WHERE StadiumName LIKE '%$search_query%' OR City LIKE '%$search_query%'";
        $res = mysqli_query($conn, $sql);
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $results[] = [
                    'Type' => 'Stadium',
                    'Primary' => "<strong>{$row['StadiumName']}</strong>",
                    'Secondary' => "Location: {$row['City']} | Capacity: " . number_format($row['Capacity'])
                ];
            }
        }
    }

    // 4. Search Games
    if ($filter == 'all' || $filter == 'games') {
        $sql = "
            SELECT g.GameDate, g.HomeTeam, g.AwayTeam, s.StadiumName
            FROM game g
            LEFT JOIN stadium s ON g.StadiumID = s.StadiumID
            WHERE g.HomeTeam LIKE '%$search_query%' OR g.AwayTeam LIKE '%$search_query%'
        ";
        $res = mysqli_query($conn, $sql);
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $stadium = $row['StadiumName'] ? $row['StadiumName'] : 'TBD';
                $results[] = [
                    'Type' => 'Game',
                    'Primary' => "<strong>{$row['AwayTeam']} @ {$row['HomeTeam']}</strong>",
                    'Secondary' => "Date: {$row['GameDate']} | Stadium: {$stadium}"
                ];
            }
        }
    }

    // 5. Search Box Scores
    if ($filter == 'all' || $filter == 'stats') {
        $sql = "
            SELECT gs.TeamName, gs.TotalPoints, gs.PassingYards, gs.RushingYards, gs.TurnoversForced, g.GameDate
            FROM gamestats gs
            JOIN game g ON gs.GameID = g.GameID
            WHERE gs.TeamName LIKE '%$search_query%'
        ";
        $res = mysqli_query($conn, $sql);
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $results[] = [
                    'Type' => 'Box Score',
                    'Primary' => "<strong>{$row['TeamName']}</strong> (Game: {$row['GameDate']})",
                    'Secondary' => "Points: {$row['TotalPoints']} | Pass Yds: {$row['PassingYards']} | Rush Yds: {$row['RushingYards']} | TO Forced: {$row['TurnoversForced']}"
                ];
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Omni-Search Database</title>
    <style>
        /* (Keep your existing search.php CSS here) */
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; padding: 40px; color: #333; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        a.btn { padding: 8px 15px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 4px; font-size: 14px; }
        .search-box { display: flex; margin-bottom: 30px; }
        .search-box select { padding: 12px; border: 1px solid #ccc; border-radius: 4px 0 0 4px; font-size: 16px; background-color: #f8f9fa; cursor: pointer; }
        .search-box input[type="text"] { flex-grow: 1; padding: 12px; border: 1px solid #ccc; border-left: none; font-size: 16px; }
        .search-box button { padding: 12px 24px; background-color: #0056b3; color: white; border: none; border-radius: 0 4px 4px 0; cursor: pointer; font-size: 16px; font-weight: bold; }
        .search-box button:hover { background-color: #004494; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 30px; }
        th, td { padding: 14px 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #0056b3; color: white; }
        tr:hover { background-color: #f1f1f1; }
        .badge { padding: 5px 10px; border-radius: 12px; font-size: 12px; color: white; font-weight: bold; display: inline-block; width: 70px; text-align: center;}
        .badge-team { background-color: #28a745; }
        .badge-player { background-color: #17a2b8; }
        .badge-stadium { background-color: #e83e8c; }
        .badge-game { background-color: #ffc107; color: #333; }
        .badge-box { background-color: #fd7e14; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Omni-Search Database</h2>
            <a href="dashboard.php" class="btn">Back to Dashboard</a>
        </div>

        <form class="search-box" method="GET" action="search.php">
            <select name="filter">
                <option value="all" <?php if($filter == 'all') echo 'selected'; ?>>All Categories</option>
                <option value="teams" <?php if($filter == 'teams') echo 'selected'; ?>>Teams</option>
                <option value="players" <?php if($filter == 'players') echo 'selected'; ?>>Players</option>
                <option value="stadiums" <?php if($filter == 'stadiums') echo 'selected'; ?>>Stadiums</option>
                <option value="games" <?php if($filter == 'games') echo 'selected'; ?>>Games</option>
                <option value="stats" <?php if($filter == 'stats') echo 'selected'; ?>>Box Scores</option>
            </select>
            <input type="text" name="q" placeholder="Search parameters..." value="<?php echo htmlspecialchars($search_query); ?>" required>
            <button type="submit">Search</button>
        </form>

        <?php if (!empty($search_query)): ?>
            <h3>Results for: "<?php echo htmlspecialchars($search_query); ?>"</h3>
            
            <?php if (count($results) > 0): ?>
                <table>
                    <tr>
                        <th style="width: 15%;">Category</th>
                        <th style="width: 35%;">Result</th>
                        <th style="width: 50%;">Details</th>
                    </tr>
                    <?php foreach ($results as $row): ?>
                        <tr>
                            <td>
                                <?php $badgeClass = strtolower(explode(' ', $row['Type'])[0]); ?>
                                <span class="badge badge-<?php echo $badgeClass; ?>">
                                    <?php echo $row['Type']; ?>
                                </span>
                            </td>
                            <td><?php echo $row['Primary']; ?></td>
                            <td style="font-size: 14px; color: #555;"><?php echo $row['Secondary']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p style="color: #888; font-style: italic;">No results found matching your criteria.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>