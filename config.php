<?php
session_start();

// Database connection
$host = 'localhost';
$db   = 'quiz_game';
$user = 'your_username';
$pass = 'your_password';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// Database setup function
function setupDatabase($pdo) {
    // Create players table
    $pdo->exec("CREATE TABLE IF NOT EXISTS players (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL
    )");

    // Create game_sessions table
    $pdo->exec("CREATE TABLE IF NOT EXISTS game_sessions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        date DATETIME DEFAULT CURRENT_TIMESTAMP,
        is_completed BOOLEAN DEFAULT FALSE
    )");

    // Create player_scores table
    $pdo->exec("CREATE TABLE IF NOT EXISTS player_scores (
        id INT AUTO_INCREMENT PRIMARY KEY,
        game_session_id INT,
        player_id INT,
        score INT DEFAULT 0,
        FOREIGN KEY (game_session_id) REFERENCES game_sessions(id),
        FOREIGN KEY (player_id) REFERENCES players(id)
    )");

    // Create questions table
    $pdo->exec("CREATE TABLE IF NOT EXISTS questions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        question_text TEXT NOT NULL,
        max_selectable INT DEFAULT 1
    )");

    // Create question_options table
    $pdo->exec("CREATE TABLE IF NOT EXISTS question_options (
        id INT AUTO_INCREMENT PRIMARY KEY,
        question_id INT,
        option_text VARCHAR(255) NOT NULL,
        points INT DEFAULT 0,
        FOREIGN KEY (question_id) REFERENCES questions(id)
    )");
}

// Initialize game
function initializeGame($pdo, $playerNames) {
    // Start a new game session
    $stmt = $pdo->prepare("INSERT INTO game_sessions () VALUES ()");
    $stmt->execute();
    $gameSessionId = $pdo->lastInsertId();

    // Insert players and their scores
    $players = [];
    foreach ($playerNames as $name) {
        $stmt = $pdo->prepare("INSERT INTO players (name) VALUES (?)");
        $stmt->execute([$name]);
        $playerId = $pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO player_scores (game_session_id, player_id) VALUES (?, ?)");
        $stmt->execute([$gameSessionId, $playerId]);

        $players[] = [
            'id' => $playerId,
            'name' => $name
        ];
    }

    return [
        'game_session_id' => $gameSessionId,
        'players' => $players
    ];
}

// Get questions with options
function getQuestions($pdo) {
    $questions = [];
    $stmt = $pdo->query("SELECT * FROM questions");
    while ($question = $stmt->fetch()) {
        $optionStmt = $pdo->prepare("SELECT * FROM question_options WHERE question_id = ?");
        $optionStmt->execute([$question['id']]);
        $question['options'] = $optionStmt->fetchAll();
        $questions[] = $question;
    }
    return $questions;
}

// Update player score
function updatePlayerScore($pdo, $playerId, $gameSessionId, $points) {
    $stmt = $pdo->prepare("UPDATE player_scores SET score = score + ? 
                           WHERE player_id = ? AND game_session_id = ?");
    $stmt->execute([$points, $playerId, $gameSessionId]);
}

// Get final scores
function getFinalScores($pdo, $gameSessionId) {
    $stmt = $pdo->prepare("SELECT p.name, ps.score 
                           FROM player_scores ps 
                           JOIN players p ON ps.player_id = p.id 
                           WHERE ps.game_session_id = ? 
                           ORDER BY ps.score DESC");
    $stmt->execute([$gameSessionId]);
    return $stmt->fetchAll();
}

// Handle game logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'start_game':
            $playerNames = $_POST['players'] ?? [];
            $gameData = initializeGame($pdo, $playerNames);
            $_SESSION['game_session_id'] = $gameData['game_session_id'];
            $_SESSION['players'] = $gameData['players'];
            echo json_encode($gameData);
            break;

        case 'submit_answer':
            $playerId = $_POST['player_id'];
            $gameSessionId = $_SESSION['game_session_id'];
            $selectedOptions = $_POST['selected_options'] ?? [];
            $points = 0;

            foreach ($selectedOptions as $optionId) {
                $stmt = $pdo->prepare("SELECT points FROM question_options WHERE id = ?");
                $stmt->execute([$optionId]);
                $option = $stmt->fetch();
                $points += $option['points'];
            }

            updatePlayerScore($pdo, $playerId, $gameSessionId, $points);
            echo json_encode(['status' => 'success', 'points' => $points]);
            break;

        case 'end_game':
            $gameSessionId = $_SESSION['game_session_id'];
            $finalScores = getFinalScores($pdo, $gameSessionId);
            echo json_encode($finalScores);
            break;
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Quiz Game</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- (Keep existing HTML structure) -->
    <script src="game.js"></script>
</body>
</html>