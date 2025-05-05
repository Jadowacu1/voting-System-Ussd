<?php
require_once "db.php";
require_once "functions.php";


$sessionId   = $_POST['sessionId']   ?? '123456';  // Default for local testing
$serviceCode = $_POST['serviceCode'] ?? '*384*53750#';
$phoneNumber = $_POST['phoneNumber'] ?? '250780123456';
$text        = $_POST['text']        ?? '';

// $text = $_POST['text'];

$textArray = explode("*", $text);
$userResponseCount = count($textArray);

$response = "";

$regNumber = getRegNumberByPhone($phoneNumber);

if (!$regNumber) {
    if ($userResponseCount == 1 && empty($textArray[0])) {
        // Initial request - prompt for registration
        $response = "CON Welcome to the Student Union Voting System.\nPlease enter your registration number:";
    } elseif ($userResponseCount == 1) {
        // User entered reg number
        $enteredRegNumber = trim($textArray[0]);
        $regResult = registerStudent($enteredRegNumber, $phoneNumber);
        if ($regResult === true) {
            $response = "CON Registration successful.\n" . mainMenu();
        } else {
            $response = "END $regResult";
        }
    } else {
        $response = "END Invalid input. Please start over.";
    }
} else {
    // Already registered - show main menu or process voting
    if ($userResponseCount == 1 && empty($textArray[0])) {
        $response = "CON " . mainMenu();
    } elseif ($userResponseCount == 1) {
        switch ($textArray[0]) {
            case "1": $response = voteForPosition($phoneNumber, "President"); break;
            case "2": $response = voteForPosition($phoneNumber, "Vice President"); break;
            case "3": $response = voteForPosition($phoneNumber, "Secretary"); break;
            case "4": $response = voteForPosition($phoneNumber, "Treasurer"); break;
            case "5": $response = viewCandidates(); break;
            case "6": $response = "END Thank you for using our service."; break;
            default: $response = "END Invalid choice.";
        }
    } elseif ($userResponseCount == 2) {
        // User voting: [1, candidate_id] etc.
        $menuChoice = $textArray[0];
        $candidateId = intval($textArray[1]);

        $positionMap = [
            "1" => "President",
            "2" => "Vice President",
            "3" => "Secretary",
            "4" => "Treasurer"
        ];

        if (isset($positionMap[$menuChoice])) {
            $positionName = $positionMap[$menuChoice];
            $response = saveVote($phoneNumber, $positionName, $candidateId);
        } else {
            $response = "END Invalid vote.";
        }
    } else {
        $response = "END Invalid request.";
    }
}

header('Content-type: text/plain');
echo $response;
