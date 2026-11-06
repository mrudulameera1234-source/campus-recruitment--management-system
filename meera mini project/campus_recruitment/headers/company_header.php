
<?php
session_start();
include("connection.php");
$login_id=$_SESSION['login_id'];
$sql="select * from tbl_company where login_id='$login_id'";
$rslt=mysqli_query($conn,$sql);
$row=mysqli_fetch_assoc($rslt);
$_SESSION['company_id']=$row['company_id'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Recruitment Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #001f3f;
            --secondary: #003366;
            --light: #ecf0f1;
            --accent: #e74c3c;
            --success: #2ecc71;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .sidebar {
            background-color: var(--primary);
            color: white;
            height: 100vh;
            position: fixed;
            padding-top: 60px;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: #888 #f1f1f1;
}
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            margin: 4px 0;
            border-radius: 4px;
        }
        
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 24px;
            text-align: center;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
            padding-top: 80px;
        }
        
        .navbar {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            width: calc(100% - 250px);
            margin-left: 250px;
            z-index: 1000;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            transition: transform 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .stats-card {
            text-align: center;
            padding: 20px;
        }
        
        .stats-card i {
            font-size: 2rem;
            margin-bottom: 15px;
            color: var(--secondary);
        }
        
        .stats-card h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary);
        }
        
        .stats-card p {
            color: #7f8c8d;
            font-weight: 500;
        }
        
        .module-section {
            display: none;
        }
        
        .module-section.active {
            display: block;
        }
        
        .notification-badge {
            position: absolute;
            top: 5px;
            right: 5px;
            background-color: var(--accent);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .btn-primary {
            background-color: var(--secondary);
            border: none;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar col-md-3 col-lg-2 d-md-block">
        <div class="text-center mb-4">
            <h4>Company Dashboard</h4>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="companydashboard.php">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-target="applications">
                    <i class="fas fa-file-alt"></i> Student Applications
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="company_drive.php">
                    <i class="fas fa-calendar-alt"></i> Schedule Drives
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-target="attendance">
                    <i class="fas fa-user-check"></i> Attendance Tracking
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="company_aptitude.php" >
                    <i class="fas fa-pencil-alt"></i> Aptitude Tests
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-target="results">
                    <i class="fas fa-chart-line"></i> Interview Results
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-target="notifications">
                    <i class="fas fa-bell"></i> Notifications
                    <span class="notification-badge">3</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="company_feedback.php" >
                    <i class="fas fa-comment-dots"></i> Feedback
                </a>
            </li>
        </ul>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="d-flex align-items-center">
                <div class="dropdown ms-3">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://via.placeholder.com/40" alt="Company Logo" width="40" height="40" class="rounded-circle me-2">
                        <span>Admin User</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownUser">
                        <li><a class="dropdown-item" href="#">Profile</a></li>
                        <li><a class="dropdown-item" href="#">Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#">Sign out</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
       
        </div>

        <!-- Other sections would be implemented similarly -->
       

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
</body>
</html>