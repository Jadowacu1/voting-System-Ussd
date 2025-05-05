<?php
include('db.php');

$sessionId = $_POST['sessionId'] ?? '';
$serviceCode = $_POST['serviceCode'] ?? '';
$phoneNumber = $_POST['phoneNumber'] ?? '';
$text = $_POST['text'] ?? '';

$textArray = explode('*', $text);
$level = count($textArray);

$adminPhones = [];
$query = $conn->query("SELECT phoneNumber FROM admins");
while ($row = $query->fetch_assoc()) {
    $adminPhones[] = $row['phoneNumber'];
}
if (!in_array($phoneNumber, $adminPhones)) {
    echo "END Access Denied.";
    exit;
}

$response = '';

// Admin Main Menu
if ($text == '') {
    $response = "CON Admin Menu:\n";
    $response .= "1. Register Position\n";
    $response .= "2. Register Candidate\n";
    $response .= "3. View Results\n";
    $response .= "4. Exit";
}

// Level 1 Choices
elseif ($level == 1) {
    switch ($textArray[0]) {
        case "1":
            $response = "CON Enter Position Name:";
            break;
        case "2":
            $response = "CON Enter Candidate Name:";
            break;
        case "3":
            $response = "CON View Results:\n";
            $response .= "1. View All Results\n";
            $response .= "2. View President Votes\n";
            $response .= "3. View Vice President Votes\n";
            $response .= "4. View Secretary Votes\n";
            $response .= "5. View Treasurer Votes\n";
            $response .= "6. Exit";
            break;
        case "4":
            $response = "END Goodbye.";
            break;
        default:
            $response = "END Invalid option.";
    }
}

// Level 2 Input
elseif ($level == 2) {
    switch ($textArray[0]) {
        case "1":
            $positionName = $textArray[1];
            $stmt = $conn->prepare("INSERT INTO positions (name) VALUES (?)");
            $stmt->bind_param("s", $positionName);
            $stmt->execute();
            $response = "END Position '$positionName' registered successfully.";
            break;

        case "2":
            $response = "CON Select Position ID for candidate:\n";
            $query = $conn->query("SELECT id, name FROM positions ORDER BY id ASC");
            while ($row = $query->fetch_assoc()) {
            $response .= $row['id'] . ". " . $row['name'] . "\n";
            }
            break;

        case "3":
            $choice = $textArray[1];
            switch ($choice) {
                case "1":
                    $response = viewResultsByPosition();
                    break;
                case "2":
                    $response = viewResultsByPosition("President");
                    break;
                case "3":
                    $response = viewResultsByPosition("Vice President");
                    break;
                case "4":
                    $response = viewResultsByPosition("Secretary");
                    break;
                case "5":
                    $response = viewResultsByPosition("Treasurer");
                    break;
                case "6":
                    $response = "END Goodbye.";
                    break;
                default:
                    $response = "END Invalid option.";
            }
            break;

        default:
            $response = "END Invalid input.";
    }
}

// Level 3 Input for candidate registration
elseif ($level == 3 && $textArray[0] == "2") {
    $candidateName = $textArray[1];
    $positionId = intval($textArray[2]);

    // Insert candidate
    $stmt = $conn->prepare("INSERT INTO candidates (name, position_id) VALUES (?, ?)");
    $stmt->bind_param("si", $candidateName, $positionId);
    $stmt->execute();
    $response = "END Candidate '$candidateName' registered successfully.";
}

else {
    $response = "END Invalid request.";
}

header('Content-type: text/plain');
echo $response;

// View results function
function viewResultsByPosition($positionFilter = null) {
    global $conn;

    if ($positionFilter) {
        $stmt = $conn->prepare("
            SELECT candidates.name AS candidate, COUNT(votes.id) AS vote_count 
            FROM candidates 
            LEFT JOIN votes ON candidates.id = votes.candidate_id 
            INNER JOIN positions ON candidates.position_id = positions.id 
            WHERE positions.name = ? 
            GROUP BY candidates.id
        ");
        $stmt->bind_param("s", $positionFilter);
        $stmt->execute();
        $result = $stmt->get_result();

        $output = "END $positionFilter Votes:\n";
        while ($row = $result->fetch_assoc()) {
            $output .= "- " . $row['candidate'] . ": " . $row['vote_count'] . " vote(s)\n";
        }

        return $output;
    } else {
        $stmt = $conn->query("
            SELECT positions.name AS position, candidates.name AS candidate, COUNT(votes.id) AS vote_count 
            FROM candidates 
            LEFT JOIN votes ON candidates.id = votes.candidate_id 
            INNER JOIN positions ON candidates.position_id = positions.id 
            GROUP BY candidates.id 
            ORDER BY positions.id
        ");

        $currentPosition = null;
        $output = "END Election Results:\n";

        while ($row = $stmt->fetch_assoc()) {
            if ($row['position'] !== $currentPosition) {
                $currentPosition = $row['position'];
                $output .= "\n$currentPosition:\n";
            }
            $output .= "- " . $row['candidate'] . ": " . $row['vote_count'] . " vote(s)\n";
        }

        return $output;
    }
}
?>
