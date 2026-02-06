<?php
require_once 'db_connect.php';

// Add parent_id to comments if it doesn't exist
$check_col = $conn->query("SHOW COLUMNS FROM comments LIKE 'parent_id'");
if ($check_col->num_rows == 0) {
    if ($conn->query("ALTER TABLE comments ADD COLUMN parent_id INT DEFAULT NULL, ADD FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE")) {
        echo "Column 'parent_id' added to 'comments' table.<br>";
    } else {
        echo "Error adding 'parent_id' column: " . $conn->error . "<br>";
    }
} else {
    echo "Column 'parent_id' already exists.<br>";
}

// Create ratings table
$sql_ratings = "CREATE TABLE IF NOT EXISTS ratings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recipe_id INT NOT NULL,
    user_id INT NOT NULL,
    rating TINYINT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_rating (recipe_id, user_id)
)";

if ($conn->query($sql_ratings)) {
    echo "Table 'ratings' created or already exists.<br>";
} else {
    echo "Error creating 'ratings' table: " . $conn->error . "<br>";
}

?>
