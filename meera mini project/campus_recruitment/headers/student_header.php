<?php
include("connection.php");
session_start();
$login_id=$_SESSION['login_id'];
$sql="select * from tbl_student where login_id='$login_id'";
$rslt=mysqli_query($conn,$sql);
$row=mysqli_fetch_assoc($rslt);
$student_name=$row['name'];
$_SESSION['student_id']=$row['student_id'];
$student_id = $_SESSION['student_id'];
$student_query = "SELECT s.*,l.*,c.course FROM tbl_student s join
tbl_login l on l.login_id=s.login_id 
join tbl_course c on c.course_id=s.course_id
WHERE s.student_id = '$student_id'";
$student_result = mysqli_query($conn, $student_query);
$student_data = mysqli_fetch_assoc($student_result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --navy-dark: #001f3f;
            --navy-medium: #1d3557;
            --navy-light: #457b9d;
            --accent: #e63946;
            --light-bg: #f8f9fa;
            --text-dark: #212529;
            --text-light: #6c757d;
            --white: #ffffff;
            --success: #2a9d8f;
            --warning: #f4a261;
            --danger: #e76f51;
            --info: #3a86ff;
        }
        
        body {
            background-color: var(--light-bg);
            color: var(--text-dark);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .sidebar {
            background-color: var(--navy-dark);
            color: var(--white);
            height: 100vh;
            position: fixed;
            padding-top: 60px;
            box-shadow: 3px 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            margin: 4px 0;
            border-radius: 4px;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: var(--navy-medium);
            color: var(--white);
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
            background-color: var(--light-bg);
        }
        
        .navbar {
            background-color: var(--white);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            width: calc(100% - 250px);
            margin-left: 250px;
            z-index: 1000;
        }
        
        .card {
            background-color: var(--white);
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }
        
        .stats-card {
            text-align: center;
            padding: 20px;
        }
        
        .stats-card i {
            font-size: 2rem;
            margin-bottom: 15px;
            color: var(--navy-medium);
        }
        
        .stats-card h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--navy-dark);
        }
        
        .stats-card p {
            color: var(--text-light);
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
            color: var(--white);
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        .table {
            background-color: var(--white);
            color: var(--text-dark);
        }
        
        .table th {
            background-color: var(--navy-medium);
            color: var(--white);
            border: none;
        }
        
        .table td {
            border-color: #e9ecef;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(10, 25, 47, 0.05);
        }
        
        .btn-primary {
            background-color: var(--navy-medium);
            border: none;
            font-weight: 600;
        }
        
        .btn-primary:hover {
            background-color: var(--navy-dark);
        }
        
        .card-header {
            background-color: var(--white);
            border-bottom: 1px solid #e9ecef;
            font-weight: 600;
            color: var(--navy-dark);
        }
        
        .badge {
            font-weight: 500;
            padding: 6px 10px;
        }
        
        .bg-warning {
            background-color: var(--warning) !important;
            color: #000;
        }
        
        .bg-success {
            background-color: var(--success) !important;
        }
        
        .bg-danger {
            background-color: var(--danger) !important;
        }
        
        .bg-info {
            background-color: var(--info) !important;
            color: #fff;
        }
        
        .text-muted {
            color: var(--text-light) !important;
        }
        
        .dropdown-menu {
            background-color: var(--white);
            border: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .dropdown-item {
            color: var(--text-dark);
        }
        
        .dropdown-item:hover {
            background-color: rgba(10, 25, 47, 0.1);
            color: var(--navy-dark);
        }
        
        .form-control {
            background-color: var(--white);
            border: 1px solid #ced4da;
            color: var(--text-dark);
        }
        
        .form-control:focus {
            background-color: var(--white);
            border-color: var(--navy-light);
            color: var(--text-dark);
            box-shadow: 0 0 0 0.25rem rgba(29, 53, 87, 0.25);
        }
        
        .job-card {
            cursor: pointer;
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }
        
        .job-card:hover {
            border-left-color: var(--navy-medium);
        }
        
        .company-logo {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            background-color: var(--navy-medium);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: var(--white);
        }
        
        .test-card {
            border-left: 4px solid var(--navy-medium);
        }
        
        .progress {
            background-color: #e9ecef;
            height: 8px;
        }
        
        .progress-bar {
            background-color: var(--navy-medium);
        }
        
        .review-star {
            color: var(--warning);
            font-size: 1.2rem;
        }
        
        .navbar-brand {
            color: var(--navy-dark);
            font-weight: 700;
        }
        
        .border-secondary {
            border-color: #e9ecef !important;
        }
                .student-profile {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }
        
        .profile-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .profile-title {
            font-size: 22px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .profile-details {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .profile-item {
            display: flex;
            align-items: center;
        }
        
        .profile-item i {
            margin-right: 10px;
            color: #001f3f;
            width: 20px;
        }
    </style>
</head>
<body>
    
    <!-- Sidebar -->
    <div class="sidebar col-md-3 col-lg-2 d-md-block">
        <div class="text-center mb-4">
            <h4><i class="fas fa-user-graduate me-2"></i>Student Portal</h4>
            <p class="text-muted" style="color: rgba(255,255,255,0.7) !important;">Welcome,<?php echo $student_name;?></p>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="studentdashboard.php" data-target="dashboard">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li >
                <a class="nav-link" href="student_vacancies.php">
                    <i class="fas fa-briefcase"></i> Job Openings
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="student_applications.php" >
                    <i class="fas fa-file-alt"></i> My Applications
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-target="aptitude">
                    <i class="fas fa-pencil-alt"></i> Aptitude Tests
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-target="results">
                    <i class="fas fa-chart-line"></i> Results
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-target="reviews">
                    <i class="fas fa-star"></i> Company Reviews
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-target="feedback">
                    <i class="fas fa-comment-dots"></i> Feedback
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#"  data-target="notifications">
                    <i class="fas fa-bell"></i>Notifications
                </a>
            </li>
        </ul>
    </div>
 <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
     
       

        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <i class="fas fa-briefcase"></i>
                    <span>Student Job Portal</span>
                </div>
                <div class="user-actions">
                    <button class="btn" id="profileBtn">My Profile</button>
                </div>
            </div>
        </div>
        </div>

    </nav>
    <!-- Navbar -->

    <!-- Main Content -->
  

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        
        // JavaScript to handle the application form and SweetAlert
        document.addEventListener('DOMContentLoaded', function() {
          
            const profileSection = document.getElementById('studentProfile');
            const profileBtn = document.getElementById('profileBtn');
            const hideProfileBtn = document.getElementById('hideProfileBtn');
           
            
            // Initially hide profile section
            profileSection.style.display = 'none';
            
            // Profile button click event
            profileBtn.addEventListener('click', function() {
                profileSection.style.display = 'block';
            });
            
            // Hide profile button click event
            hideProfileBtn.addEventListener('click', function() {
                profileSection.style.display = 'none';
            });
                   });
            </script>
 
</body>
</html>