<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "film_recommendation";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

$userId = isset($_GET['user_id']) ? intval($_GET['user_id']) : 1;

// Le stesse funzioni di sopra possono essere riutilizzate qui

function getUserRatings($conn, $userId) {
    $sql = "SELECT film_id, valutazione FROM valutazioni WHERE utente_id = $userId";
    $result = $conn->query($sql);

    $ratings = [];
    while ($row = $result->fetch_assoc()) {
        $ratings[$row['film_id']] = $row['valutazione'];
    }
    return $ratings;
}

function cosineSimilarity($ratings1, $ratings2) {
    $dotProduct = 0;
    $normA = 0;
    $normB = 0;

    foreach ($ratings1 as $filmId => $rating) {
        if (isset($ratings2[$filmId])) {
            $dotProduct += $rating * $ratings2[$filmId];
            $normA += pow($rating, 2);
            $normB += pow($ratings2[$filmId], 2);
        }
    }

    if ($normA == 0 || $normB == 0) {
        return 0;
    }

    return $dotProduct / (sqrt($normA) * sqrt($normB));
}

function getRecommendations($conn, $userId) {
    $userRatings = getUserRatings($conn, $userId);
    $users = $conn->query("SELECT id FROM utenti WHERE id != $userId");

    $similarities = [];
    while ($row = $users->fetch_assoc()) {
        $otherUserId = $row['id'];
        $otherUserRatings = getUserRatings($conn, $otherUserId);
        $similarity = cosineSimilarity($userRatings, $otherUserRatings);
        $similarities[$otherUserId] = $similarity;
    }

    arsort($similarities);

    $recommendations = [];
    foreach ($similarities as $otherUserId => $similarity) {
        if ($similarity > 0) {
            $otherUserRatings = getUserRatings($conn, $otherUserId);
            foreach ($otherUserRatings as $filmId => $rating) {
                if (!isset($userRatings[$filmId])) {
                    if (!isset($recommendations[$filmId])) {
                        $recommendations[$filmId] = 0;
                    }
                    $recommendations[$filmId] += $similarity * $rating;
                }
            }
        }
    }

    arsort($recommendations);
    return $recommendations;
}

$recommendations = getRecommendations($conn, $userId);

$response = [];
foreach ($recommendations as $filmId => $score) {
    $filmTitle = $conn->query("SELECT titolo FROM film WHERE id = $filmId")->fetch_assoc()['titolo'];
    $response[] = ["titolo" => $filmTitle, "punteggio" => $score];
}

echo json_encode($response);

$conn->close();
?>
