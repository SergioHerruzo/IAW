<?php
require 'db_connect.php';

// Read the SQL file
$sqlFile = 'db.sql';
if (!file_exists($sqlFile)) {
    die("Error: db.sql file not found.");
}

$sql = file_get_contents($sqlFile);

// Remove specific "CREATE DATABASE" and "USE" commands if they are in the file to avoid conflicts with db_connect.php logic
// or just execute multiple queries.
// mysqli::multi_query is needed.

if ($conn->multi_query($sql)) {
    do {
        /* store first result set */
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->next_result());
    echo "<div style='color: green; padding: 20px; font-family: sans-serif;'>Database tables created successfully! <a href='index.html'>Go to Home</a></div>";
} else {
    echo "Error creating tables: " . $conn->error;
}

$conn->close();
?>
