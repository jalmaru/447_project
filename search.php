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
                $teamName = htmlspecialchars($row['TeamName']);
                $teamLink = "team.php?name=" . urlencode($row['TeamName']);
                $results[] = [
                    'Type' => 'Team',
                    'Primary' => "<a href=\"$teamLink\"><strong>$teamName</strong></a>",
                    'Secondary' => "Division: " . htmlspecialchars($row['Division'])
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
                $playerName = htmlspecialchars($row['PlayerName']);
                $position = htmlspecialchars($row['Position']);
                $teamName = htmlspecialchars($row['TeamName']);
                $teamLink = "team.php?name=" . urlencode($row['TeamName']);
                $results[] = [
                    'Type' => 'Player',
                    'Primary' => "<strong>$playerName</strong> ($position)",
                    'Secondary' => "Team: <a href=\"$teamLink\">$teamName</a>"
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
            SELECT g.GameID, g.GameDate, g.HomeTeam, g.AwayTeam, s.StadiumName
            FROM game g
            LEFT JOIN stadium s ON g.StadiumID = s.StadiumID
            WHERE g.HomeTeam LIKE '%$search_query%' OR g.AwayTeam LIKE '%$search_query%'
        ";
        $res = mysqli_query($conn, $sql);
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $stadium = $row['StadiumName'] ? htmlspecialchars($row['StadiumName']) : 'TBD';
                $home = htmlspecialchars($row['HomeTeam']);
                $away = htmlspecialchars($row['AwayTeam']);
                $date = htmlspecialchars($row['GameDate']);
                $gameLink = "game.php?id=" . urlencode($row['GameID']);
                $results[] = [
                    'Type' => 'Game',
                    'Primary' => "<a href=\"$gameLink\"><strong>$away @ $home</strong></a>",
                    'Secondary' => "Date: $date | Stadium: $stadium"
                ];
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Database</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Search Database</h2>
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>

        <form class="search-box" method="GET" action="search.php">
            <select name="filter">
                <option value="all" <?php if($filter == 'all') echo 'selected'; ?>>All Categories</option>
                <option value="teams" <?php if($filter == 'teams') echo 'selected'; ?>>Teams</option>
                <option value="players" <?php if($filter == 'players') echo 'selected'; ?>>Players</option>
                <option value="stadiums" <?php if($filter == 'stadiums') echo 'selected'; ?>>Stadiums</option>
                <option value="games" <?php if($filter == 'games') echo 'selected'; ?>>Games</option>
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
                <p class="empty">No results found matching your criteria.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>