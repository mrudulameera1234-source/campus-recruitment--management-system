<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Recruitment Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-navy: #001f3f;
            --secondary-navy: #003366;
            --accent-blue: #0074D9;
            --light-blue: #7FDBFF;
            --text-light: #f8f9fa;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
        }
        
        .sidebar {
            background-color: var(--primary-navy);
            color: var(--text-light);
            height: 100vh;
            position: fixed;
            padding-top: 20px;
            overflow-y: auto; 
            max-height:100vh;
            width:20%;
         }
        
        .sidebar .nav-link {
            color: var(--text-light);
            margin-bottom: 5px;
            border-radius: 5px;
            padding: 10px 15px;
        }
        
        .sidebar .nav-link:hover {
            background-color: var(--secondary-navy);
        }
        
        .sidebar .nav-link.active {
            background-color: var(--accent-blue);
            font-weight: 600;
        }
        
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
            overflow-y: auto;
        }
        
        .header {
            background-color: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .card-header {
            background-color: var(--primary-navy);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 15px 20px;
            font-weight: 600;
        }
        
        .btn-primary {
            background-color: var(--accent-blue);
            border-color: var(--accent-blue);
        }
        
        .stat-card {
            background: linear-gradient(135deg, var(--primary-navy), var(--secondary-navy));
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .stat-card i {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }
        
        .stat-card .count {
            font-size: 2rem;
            font-weight: 700;
        }
        
        .stat-card .label {
            opacity: 0.8;
            font-size: 0.9rem;
        }
        
        .table th {
            background-color: var(--primary-navy);
            color: white;
        }
        
        .badge-primary {
            background-color: var(--accent-blue);
        }
        
        .logo {
            padding: 10px 20px;
            margin-bottom: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .user-profile {
            display: flex;
            align-items: center;
        }
        
        .user-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <div class="logo">
                    <h4><i class="fas fa-graduation-cap"></i> Campus Recruitment</h4>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link"  href="admindashboard.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item has-submenu active">
                        <a class="nav-link" href="admin_manage_student.php">
                            <i class="fas fa-users"></i> Manage Students
                        </a>
                        <ul class="submenu show">
                            <li class="nav-item">
                                <a class="nav-link active" href="admin_manage_student.php?page=add" >
                                    <i class="fas fa-user-plus"></i> Student Credentials
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="admin_view_student.php">
                                    <i class="fas fa-user-edit"></i> Registered Students
                                </a>
                            </li>
                           
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_manage_company.php">
                            <i class="fas fa-building"></i> Manage Companies
                        </a>
                    </li>
                     <li class="nav-item">
                        <a class="nav-link" href="admin_courses.php">
                            <i class="fas fa-graduation-cap"></i>Courses
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="admin_manage_drives.php">
                            <i class="fas fa-briefcase"></i> Recruitment Drives
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#attendance">
                            <i class="fas fa-id-card"></i>Attendance
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_notifications.php">
                            <i class="fas fa-bell"></i>Notifications
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_feedback.php">
                            <i class="fas fa-book"></i> Feedbacks
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_reviews.php">
                            <i class="fas fa-clipboard"></i> Reviews
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#report">
                            <i class="fas fa-chart-bar"></i>Report
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_settings.php">
                            <i class="fas fa-cog"></i> Settings
                        </a>
                    </li>
                </ul>
            </div>

            
            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <div class="header">
                    <h4>Admin Dashboard</h4>
                    <div class="user-profile">
                        <img src="https://via.placeholder.com/40" alt="Admin">
                        <span>Admin User</span>
                    </div>
                </div>
                