<?php
include 'db.php';

$admins = [
    ['username' => 'admin1', 'phoneNumber' => '+250783339580'],
    ['username' => 'admin2', 'phoneNumber' => '+250788658293']
];

foreach ($admins as $admin) {
    $username = $conn->real_escape_string($admin['username']);
    $phone = $conn->real_escape_string($admin['phoneNumber']);
    
    $conn->query("INSERT INTO admins (username, phoneNumber) VALUES ('$username', '$phone')");
}

echo "Seeding completed.";
?>
