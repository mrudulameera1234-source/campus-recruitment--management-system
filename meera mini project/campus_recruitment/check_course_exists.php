<?php
// Database configuration
include('connection.php');

// Set content type to JSON
header('Content-Type: application/json');

// Check if course parameter is provided
if (isset($_POST['course']) && !empty(trim($_POST['course']))) {
    $courseName = trim($_POST['course']);
    
    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT course_id FROM tbl_course WHERE course = ?");
    $stmt->bind_param("s", $courseName);
    $stmt->execute();
    $stmt->store_result();
    
    // Check if course exists
    if ($stmt->num_rows > 0) {
        // Course exists
        echo json_encode(['exists' => true]);
    } else {
        // Course does not exist
        echo json_encode(['exists' => false]);
    }
    
    $stmt->close();
} else {
    // Invalid request - no course parameter provided
    echo json_encode(['exists' => false, 'error' => 'No course name provided']);
}

$conn->close();
?>