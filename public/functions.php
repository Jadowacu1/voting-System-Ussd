<?php

function getRegNumberByPhone($phoneNumber) {
    global $conn;
    $stmt = $conn->prepare("SELECT reg_number FROM students WHERE phone_number = ?");
    $stmt->bind_param("s", $phoneNumber);
    $stmt->execute();
    $res = $stmt->get_result();
    return $res->num_rows > 0 ? $res->fetch_assoc()['reg_number'] : null;
}

function registerStudent($regNumber, $phoneNumber) {
    global $conn;
    $stmt = $conn->prepare("SELECT id FROM students WHERE reg_number = ? OR phone_number = ?");
    $stmt->bind_param("ss", $regNumber, $phoneNumber);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        return "This registration number or phone number is already used.";
    }

    $stmt = $conn->prepare("INSERT INTO students (reg_number, phone_number) VALUES (?, ?)");
    return $stmt->bind_param("ss", $regNumber, $phoneNumber) && $stmt->execute()
        ? true
        : "Registration failed.";
}

function mainMenu() {
    return "Welcome to the Student Union Election Voting System\n" .
           "1. Vote for President\n" .
           "2. Vote for Vice President\n" .
           "3. Vote for Secretary\n" .
           "4. Vote for Treasurer\n" .
           "5. View Candidates' Information\n" .
           "6. Exit";
}

function voteForPosition($phoneNumber, $positionName) {
    global $conn;

    // Get position ID
    $stmt = $conn->prepare("SELECT id FROM positions WHERE name = ?");
    $stmt->bind_param("s", $positionName);
    $stmt->execute();
    $posResult = $stmt->get_result();

    if ($posResult->num_rows == 0) {
        return "END Invalid position.";
    }

    $positionId = $posResult->fetch_assoc()['id'];

    // Get student reg number
    $regNumber = getRegNumberByPhone($phoneNumber);
    if (!$regNumber) {
        return "END You are not registered. Please register before voting.";
    }

    // Check if already voted
    $check = $conn->prepare("SELECT id FROM votes WHERE reg_number = ? AND position_id = ?");
    $check->bind_param("si", $regNumber, $positionId);
    $check->execute();
    $voteRes = $check->get_result();

    if ($voteRes->num_rows > 0) {
        return "END You have already voted for $positionName.";
    }

    // Show candidates
    $response = "CON Select a candidate for $positionName:\n";
    $query = $conn->prepare("SELECT id, name FROM candidates WHERE position_id = ?");
    $query->bind_param("i", $positionId);
    $query->execute();
    $candidates = $query->get_result();

    while ($row = $candidates->fetch_assoc()) {
        $response .= $row['id'] . ". " . $row['name'] . "\n";
    }

    return $response;
}

function viewCandidates() {
    global $conn;

    $stmt = $conn->prepare("
        SELECT p.name AS position, c.name AS candidate
        FROM candidates c
        INNER JOIN positions p ON c.position_id = p.id
    ");
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        return "END No candidates available.";
    }

    $response = "CON Candidates Information:\n";
    while ($row = $result->fetch_assoc()) {
        $response .= $row['position'] . ": " . $row['candidate'] . "\n";
    }

    $response .= "0. Go Back";
    return $response;
}

function saveVote($phoneNumber, $positionName, $candidateId) {
    global $conn;

    // Get reg_number
    $regNumber = getRegNumberByPhone($phoneNumber);
    if (!$regNumber) {
        return "END You are not registered.";
    }

    // Get position ID
    $stmt = $conn->prepare("SELECT id FROM positions WHERE name = ?");
    $stmt->bind_param("s", $positionName);
    $stmt->execute();
    $posRes = $stmt->get_result();

    if ($posRes->num_rows == 0) {
        return "END Invalid position.";
    }

    $positionId = $posRes->fetch_assoc()['id'];

    // Check for duplicate vote
    $check = $conn->prepare("SELECT id FROM votes WHERE reg_number = ? AND position_id = ?");
    $check->bind_param("si", $regNumber, $positionId);
    $check->execute();
    $voteRes = $check->get_result();

    if ($voteRes->num_rows > 0) {
        return "END You have already voted for $positionName.";
    }

    // Save vote
    $stmt = $conn->prepare("INSERT INTO votes (reg_number, position_id, candidate_id) VALUES (?, ?, ?)");
    $stmt->bind_param("sii", $regNumber, $positionId, $candidateId);
    return $stmt->execute()
        ? "END Your vote for $positionName has been recorded."
        : "END Failed to record your vote.";
}
