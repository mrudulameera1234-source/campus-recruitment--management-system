<?php
include("connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = $_POST['email'];
    
    // Check if email exists in database
    $query = "SELECT email FROM tbl_login WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    
    if (mysqli_stmt_num_rows($stmt) > 0) {
        echo "exists";
    } else {
        echo "available";
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>