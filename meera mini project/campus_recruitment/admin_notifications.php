<?php
include("connection.php");
include("headers/admin_header.php");

// Handle delete notification
if (isset($_GET['delete_id'])) {
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    
    $delete_sql = "DELETE FROM tbl_notification WHERE notification_id = '$delete_id'";
    if (mysqli_query($conn, $delete_sql)) {
        echo "<script>alert('Notification deleted successfully'); window.location.href='admin_notifications.php';</script>";
    } else {
        echo "<script>alert('Error deleting notification: " . mysqli_error($conn) . "');</script>";
    }
}

// Fetch all notifications with company names from the database
$sql = "SELECT n.notification_id, n.drive_id, n.posted_on, n.description, 
               c.name 
        FROM tbl_notification n
        JOIN tbl_drive d ON n.drive_id = d.drive_id
        JOIN tbl_company c ON d.company_id = c.company_id
        ORDER BY n.posted_on DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Notifications</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .notification {
            background: #f9f9f9;
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            position: relative;
        }
        .notification.unread {
            background: #e7f4ff;
            border-left: 4px solid #2196F3;
        }
        .notification-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .notification-company {
            font-weight: bold;
            color: #2196F3;
            font-size: 1.1em;
        }
        .notification-date {
            color: #666;
            font-size: 0.9em;
        }
        .notification-message {
            margin-top: 10px;
            padding: 10px;
            background: white;
            border-radius: 4px;
        }
        .actions {
            margin-top: 10px;
            text-align: right;
        }
        .actions a {
            color: #2196F3;
            text-decoration: none;
            margin-right: 10px;
            padding: 5px 10px;
            border-radius: 3px;
        }
        .actions a:hover {
            background-color: #e7f4ff;
        }
        .actions a.delete {
            color: #f44336;
        }
        .actions a.delete:hover {
            background-color: #ffebee;
        }
        .no-notifications {
            text-align: center;
            padding: 30px;
            color: #666;
            font-style: italic;
        }
    </style>
    <script>
        function confirmDelete(notificationId) {
            if (confirm('Are you sure you want to delete this notification?')) {
                window.location.href = 'admin_notifications.php?delete_id=' + notificationId;
            }
            return false;
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Company Notifications</h1>
        
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="notification">
                    <div class="notification-header">
                        <span class="notification-company"><?php echo htmlspecialchars($row['name']); ?></span>
                        <span class="notification-date"><?php echo date('M j, Y g:i a', strtotime($row['posted_on'])); ?></span>
                    </div>
                    <div class="notification-message"><?php echo nl2br(htmlspecialchars($row['description'])); ?></div>
                    <div class="actions">
                        <a href="#" class="delete" onclick="return confirmDelete(<?php echo $row['notification_id']; ?>)">
                            Delete
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-notifications">
                <p>No notifications found.</p>
            </div>
        <?php endif; ?>
    </div>

    <?php $conn->close(); ?>
</body>
</html>