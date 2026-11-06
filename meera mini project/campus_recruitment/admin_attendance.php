<?php
include("connection.php");
include("headers/admin_header.php");

// Initialize variables
$success = '';
$error = '';

// Fetch aptitude test attendance data
$aptitude_query = "SELECT a.*, s.name as student_name, c.name as company_name 
                   FROM tbl_aptitude a 
                   JOIN tbl_student s ON a.student_id = s.id 
                   JOIN tbl_company c ON a.company_id = c.id ";
$aptitude_result = mysqli_query($conn, $aptitude_query);

// Fetch interview attendance data
$interview_query = "SELECT att.*, s.name as student_name, c.name as company_name, app.id as application_id
                    FROM tbl_attendance att 
                    JOIN tbl_student s ON att.student_id = s.id 
                    JOIN tbl_company c ON att.company_id = c.id 
                    JOIN tbl_applications app ON att.application_id = app.id
                    WHERE att.type IN ('online', 'offline')
                    ORDER BY att.updated_at DESC";
$interview_result = mysqli_query($conn, $interview_query);

// Handle marking attendance
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mark_attendance'])) {
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $application_id = mysqli_real_escape_string($conn, $_POST['application_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $type = mysqli_real_escape_string($conn, $_POST['type']);
    
    // Check if attendance record already exists
    $check_query = "SELECT * FROM tbl_attendance WHERE student_id = '$student_id' AND application_id = '$application_id'";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        // Update existing record
        $update_query = "UPDATE tbl_attendance SET status = '$status', updated_at = NOW() 
                         WHERE student_id = '$student_id' AND application_id = '$application_id'";
        if (mysqli_query($conn, $update_query)) {
            $success = "Attendance updated successfully!";
        } else {
            $error = "Error updating attendance: " . mysqli_error($conn);
        }
    } else {
        // Insert new record
        $insert_query = "INSERT INTO tbl_attendance (student_id, application_id, status, type, updated_at) 
                         VALUES ('$student_id', '$application_id', '$status', '$type', NOW())";
        if (mysqli_query($conn, $insert_query)) {
            $success = "Attendance marked successfully!";
        } else {
            $error = "Error marking attendance: " . mysqli_error($conn);
        }
    }
    
    // Refresh the page to show updated data
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Tracking - Campus Recruitment System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #3498db;
            --secondary: #2c3e50;
            --success: #2ecc71;
            --danger: #e74c3c;
            --warning: #f39c12;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --gray: #7f8c8d;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background: linear-gradient(135deg, var(--secondary), #1a2530);
            color: #fff;
            padding-top: 20px;
            transition: all 0.3s ease;
        }
        
        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .sidebar-header img {
            width: 40px;
            height: 40px;
            object-fit: cover;
        }
        
        .sidebar-header h2 {
            font-size: 20px;
        }
        
        .sidebar-menu {
            list-style: none;
        }
        
        .sidebar-menu li {
            padding: 12px 20px;
            border-left: 4px solid transparent;
            transition: all 0.3s;
        }
        
        .sidebar-menu li:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-left: 4px solid var(--primary);
        }
        
        .sidebar-menu li.active {
            background-color: rgba(255, 255, 255, 0.15);
            border-left: 4px solid var(--primary);
        }
        
        .sidebar-menu a {
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        /* Main Content Styles */
        .main-content {
            flex: 1;
            padding: 20px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .header h1 {
            color: var(--secondary);
            font-size: 28px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        /* Tabs */
        .tabs {
            display: flex;
            margin-bottom: 20px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .tab {
            padding: 15px 20px;
            flex: 1;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            border-bottom: 3px solid transparent;
        }
        
        .tab.active {
            border-bottom: 3px solid var(--primary);
            font-weight: bold;
            color: var(--primary);
        }
        
        .tab:hover:not(.active) {
            background-color: #f8f9fa;
        }
        
        /* Filters */
        .filters {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .filter-group {
            flex: 1;
            min-width: 200px;
        }
        
        .filter-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--secondary);
        }
        
        .filter-group select, .filter-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        /* Table Styles */
        .table-container {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: var(--secondary);
        }
        
        tr:hover {
            background-color: #f8f9fa;
        }
        
        .attendance-status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .present {
            background-color: #e7f6e9;
            color: #2ecc71;
        }
        
        .absent {
            background-color: #fbebeb;
            color: #e74c3c;
        }
        
        .pending {
            background-color: #fef5e6;
            color: #f39c12;
        }
        
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
        }
        
        .btn-success {
            background-color: var(--success);
            color: white;
        }
        
        .btn-success:hover {
            background-color: #27ae60;
        }
        
        .btn-danger {
            background-color: var(--danger);
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #c0392b;
        }
        
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 20px;
        }
        
        .aptitude-icon {
            background-color: #e3f2fd;
            color: var(--primary);
        }
        
        .interview-icon {
            background-color: #e8f5e9;
            color: var(--success);
        }
        
        .stat-info h3 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .stat-info p {
            color: var(--gray);
            font-size: 14px;
        }
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background: white;
            border-radius: 8px;
            width: 400px;
            max-width: 90%;
            padding: 20px;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .modal-header h2 {
            color: var(--secondary);
        }
        
        .close {
            font-size: 24px;
            cursor: pointer;
        }
        
        /* Alert Messages */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            display: flex;
            align-items: center;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* Responsive Styles */
        @media (max-width: 992px) {
            .admin-container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                padding: 10px;
            }
            
            .sidebar-menu {
                display: flex;
                overflow-x: auto;
            }
            
            .sidebar-menu li {
                border-left: none;
                border-bottom: 4px solid transparent;
                padding: 10px 15px;
            }
            
            .sidebar-menu li:hover,
            .sidebar-menu li.active {
                border-left: none;
                border-bottom: 4px solid var(--primary);
            }
            
            .filters {
                flex-direction: column;
            }
        }
        
        @media (max-width: 768px) {
            .stats-cards {
                grid-template-columns: 1fr;
            }
            
            table {
                display: block;
                overflow-x: auto;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="https://cdn-icons-png.flaticon.com/512/3719/3719076.png" alt="Logo">
                <h2>Campus Recruitment</h2>
            </div>
            <ul class="sidebar-menu">
                <li><a href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="#"><i class="fas fa-users"></i> Students</a></li>
                <li><a href="#"><i class="fas fa-building"></i> Companies</a></li>
                <li><a href="#"><i class="fas fa-book"></i> Courses</a></li>
                <li class="active"><a href="#"><i class="fas fa-clipboard-check"></i> Attendance</a></li>
                <li><a href="#"><i class="fas fa-bell"></i> Notifications</a></li>
                <li><a href="#"><i class="fas fa-comment"></i> Feedback</a></li>
                <li><a href="#"><i class="fas fa-cog"></i> Settings</a></li>
                <li><a href="#"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="header">
                <h1><i class="fas fa-clipboard-check"></i> Attendance Tracking</h1>
                <div class="user-info">
                    <img src="https://ui-avatars.com/api/?name=Admin+User&background=3498db&color=fff" alt="Admin User">
                    <span>Admin User</span>
                </div>
            </div>

            <!-- Display success/error messages -->
            <?php if(!empty($success)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <?php if(!empty($error)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <!-- Stats Cards -->
            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-icon aptitude-icon">
                        <i class="fas fa-brain"></i>
                    </div>
                    <div class="stat-info">
                        <h3>85%</h3>
                        <p>Aptitude Test Attendance</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon interview-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <div class="stat-info">
                        <h3>72%</h3>
                        <p>Interview Attendance</p>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="tabs">
                <div class="tab active" data-tab="aptitude">Aptitude Test Attendance</div>
                <div class="tab" data-tab="interview">Interview Attendance</div>
            </div>

            <!-- Filters -->
            <div class="filters">
                <div class="filter-group">
                    <label for="date-filter">Date</label>
                    <input type="date" id="date-filter">
                </div>
                <div class="filter-group">
                    <label for="company-filter">Company</label>
                    <select id="company-filter">
                        <option value="">All Companies</option>
                        <option value="google">Google</option>
                        <option value="microsoft">Microsoft</option>
                        <option value="amazon">Amazon</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="status-filter">Status</label>
                    <select id="status-filter">
                        <option value="">All Status</option>
                        <option value="present">Present</option>
                        <option value="absent">Absent</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
            </div>

            <!-- Aptitude Test Attendance Table -->
            <div class="table-container" id="aptitude-table">
                <table>
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Company</th>
                            <th>Test Date</th>
                            <th>Status</th>
                            <th>Score</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($aptitude_result)): ?>
                        <tr>
                            <td><?php echo $row['student_id']; ?></td>
                            <td><?php echo $row['student_name']; ?></td>
                            <td><?php echo $row['company_name']; ?></td>
                            <td><?php echo date('Y-m-d', strtotime($row['test_date'])); ?></td>
                            <td>
                                <span class="attendance-status <?php echo strtolower($row['status']); ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>
                            <td><?php echo $row['score'] ? $row['score'] . '%' : '-'; ?></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-primary btn-sm">Details</button>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Interview Attendance Table -->
            <div class="table-container" id="interview-table" style="display: none;">
                <table>
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Company</th>
                            <th>Interview Date</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Reset pointer and fetch interview data again
                        mysqli_data_seek($interview_result, 0);
                        while($row = mysqli_fetch_assoc($interview_result)): 
                        ?>
                        <tr>
                            <td><?php echo $row['student_id']; ?></td>
                            <td><?php echo $row['student_name']; ?></td>
                            <td><?php echo $row['company_name']; ?></td>
                            <td><?php echo date('Y-m-d', strtotime($row['updated_at'])); ?></td>
                            <td><?php echo ucfirst($row['type']); ?></td>
                            <td>
                                <span class="attendance-status <?php echo strtolower($row['status']); ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <?php if($row['status'] == 'pending'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="student_id" value="<?php echo $row['student_id']; ?>">
                                        <input type="hidden" name="application_id" value="<?php echo $row['application_id']; ?>">
                                        <input type="hidden" name="type" value="<?php echo $row['type']; ?>">
                                        <input type="hidden" name="status" value="present">
                                        <button type="submit" name="mark_attendance" class="btn btn-success btn-sm">Present</button>
                                    </form>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="student_id" value="<?php echo $row['student_id']; ?>">
                                        <input type="hidden" name="application_id" value="<?php echo $row['application_id']; ?>">
                                        <input type="hidden" name="type" value="<?php echo $row['type']; ?>">
                                        <input type="hidden" name="status" value="absent">
                                        <button type="submit" name="mark_attendance" class="btn btn-danger btn-sm">Absent</button>
                                    </form>
                                    <?php else: ?>
                                    <button class="btn btn-primary btn-sm">Details</button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script>
        // Tab functionality
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                
                document.getElementById('aptitude-table').style.display = 'none';
                document.getElementById('interview-table').style.display = 'none';
                
                document.getElementById(tab.dataset.tab + '-table').style.display = 'block';
            });
        });

        // Filter functionality (basic implementation)
        document.getElementById('date-filter').addEventListener('change', filterTable);
        document.getElementById('company-filter').addEventListener('change', filterTable);
        document.getElementById('status-filter').addEventListener('change', filterTable);

        function filterTable() {
            const dateFilter = document.getElementById('date-filter').value;
            const companyFilter = document.getElementById('company-filter').value;
            const statusFilter = document.getElementById('status-filter').value;
            
            // This is a simplified implementation
            // In a real application, you would likely make an AJAX request to the server with filter parameters
            
            console.log('Filtering by:', {dateFilter, companyFilter, statusFilter});
            alert('In a real application, this would filter the table based on your selection. For now, check console for filter values.');
        }
    </script>
</body>
</html>