<?php
include("connection.php");
include("headers/student_header.php");

// Get student ID from session
$student_id = $_SESSION['student_id'];

// Handle application withdrawal
if ($_SERVER['REQUEST_METHOD'] == 'POST' ) {
    echo "<script>alert('inside');</script>";
    $application_id = $_POST['application_id'];
    
    $delete_sql = "DELETE FROM tbl_applications WHERE application_id = ? AND student_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("ii", $application_id, $student_id);
    
    if ($delete_stmt->execute()) {
        $success_message = "Application withdrawn successfully!";
    } else {
        $error_message = "Error withdrawing application. Please try again.";
    }
}

// Fetch student's applications with details
$applications_sql = "SELECT 
                        a.application_id, 
                        a.applied_on, 
                        v.post, 
                        v.description,
                        v.cutoff_cgpa,
                        v.arrears_allowed,
                        d.prefered_date,c.name as company_name,c.location
                     FROM tbl_applications a
                     JOIN tbl_vacancies v ON a.vacancy_id = v.vacancy_id
                     JOIN tbl_drive d ON v.drive_id = d.drive_id
                     JOIN tbl_company c ON c.company_id=d.company_id
                     WHERE a.student_id = ?
                     ORDER BY a.applied_on DESC";
$applications_stmt = $conn->prepare($applications_sql);
$applications_stmt->bind_param("i", $student_id);
$applications_stmt->execute();
$applications_result = $applications_stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Applications - Student Job Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
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
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background: linear-gradient(135deg, #001f3f 0%, #1e47a3 100%);
            color: white;
            padding: 20px 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 0 0 10px 10px;
            margin-bottom: 30px;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
            display: flex;
            align-items: center;
        }
        
        .logo i {
            margin-right: 10px;
            font-size: 28px;
        }
        
        .nav-links {
            display: flex;
            gap: 20px;
        }
        
        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            transition: background 0.3s;
        }
        
        .nav-links a:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .nav-links a.active {
            background: rgba(255, 255, 255, 0.2);
        }
        
        .page-title {
            font-size: 28px;
            margin-bottom: 20px;
            color: #001f3f;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .page-title i {
            color: #1e47a3;
        }
        
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            text-align: center;
        }
        
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: #001f3f;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #666;
            font-size: 14px;
        }
        
        .applications-list {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        
        .application-header {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 1fr;
            padding: 15px 20px;
            background: #f8f9fa;
            font-weight: 600;
            border-bottom: 1px solid #eee;
        }
        
        .application-item {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 1fr;
            padding: 20px;
            border-bottom: 1px solid #eee;
            align-items: center;
        }
        
        .application-item:last-child {
            border-bottom: none;
        }
        
        .application-item:hover {
            background: #f8f9fa;
        }
        
        .job-title {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .company-name {
            color: #666;
            font-size: 14px;
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-align: center;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-approved {
            background: #d4edda;
            color: #155724;
        }
        
        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status-withdrawn {
            background: #e2e3e5;
            color: #383d41;
        }
        
        .action-btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .withdraw-btn {
            background: #ff6b6b;
            color: white;
        }
        
        .withdraw-btn:hover {
            background: #ff5252;
        }
        
        .view-btn {
            background: #001f3f;
            color: white;
        }
        
        .view-btn:hover {
            background: #003366;
        }
        
        .btn-disabled {
            background: #ccc;
            color: #666;
            cursor: not-allowed;
        }
        
        .no-applications {
            text-align: center;
            padding: 50px 20px;
            color: #666;
        }
        
        .no-applications i {
            font-size: 60px;
            margin-bottom: 20px;
            color: #ddd;
        }
        
        .application-details {
            display: none;
            margin-top: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
            grid-column: 1 / -1;
        }
        
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .detail-item {
            margin-bottom: 10px;
        }
        
        .detail-label {
            font-weight: 600;
            color: #555;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: 500;
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
        
        @media (max-width: 768px) {
            .application-header, .application-item {
                grid-template-columns: 1fr;
                gap: 10px;
            }
            
            .application-header {
                display: none;
            }
            
            .application-item {
                padding: 15px;
                border: 1px solid #eee;
                margin-bottom: 10px;
                border-radius: 5px;
            }
            
            .stats-cards {
                grid-template-columns: 1fr;
            }
            
            .header-content {
                flex-direction: column;
                text-align: center;
            }
            
            .nav-links {
                margin-top: 15px;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    
      <div class="main-content">
        
  

    <div class="container">
         <div class="student-profile" id="studentProfile">
            <div class="profile-header">
                <h3 class="profile-title">My Profile</h3>
                <button class="btn" id="hideProfileBtn">Hide Profile</button>
            </div>
            <div class="profile-details">
                <div class="profile-item">
                    <i class="fas fa-user"></i>
                    <span><strong>Name:</strong> <?php echo $student_data['name'] ?? 'N/A'; ?></span>
                </div>
                <div class="profile-item">
                    <i class="fas fa-envelope"></i>
                    <span><strong>Email:</strong> <?php echo $student_data['email'] ?? 'N/A'; ?></span>
                </div>
                <div class="profile-item">
                    <i class="fas fa-graduation-cap"></i>
                    <span><strong>Course:</strong> <?php echo $student_data['course'] ?? 'N/A'; ?></span>
                </div>
                <div class="profile-item">
                    <i class="fas fa-star"></i>
                    <span><strong>CGPA:</strong> <?php echo $student_data['cgpa'] ?? 'N/A'; ?></span>
                </div>
                <div class="profile-item">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span><strong>Arrears:</strong> <?php echo $student_data['arrears'] ?? 'N/A'; ?></span>
                </div>
            </div>
        </div>
        <!-- Display success/error messages -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
    <!-- Applications List -->
        <div class="applications-list">
            <?php if ($applications_result->num_rows > 0): ?>
                <div class="application-header">
                    <div>Job Position</div>
                    <div>Company</div>
                    <div>Applied On</div>
                    <div>Actions</div>
                </div>
                
                <?php while($application = $applications_result->fetch_assoc()): ?>
                    <div class="application-item">
                        <div>
                            <div class="job-title"><?php echo htmlspecialchars($application['post']); ?></div>
                            <div class="company-name"><?php echo htmlspecialchars($application['company_name']); ?></div>
                        </div>
                        <div><?php echo htmlspecialchars($application['location']); ?></div>
                        <div><?php echo date('M j, Y', strtotime($application['applied_on'])); ?></div>
                        <div>
                            <button class="action-btn view-btn" onclick="toggleDetails(<?php echo $application['application_id']; ?>)">
                                <i class="fas fa-eye"></i> Details
                            </button>
                                <form method="POST" style="display: inline;"  action="">
                                    <input type="hidden" name="application_id" value="<?php echo $application['application_id']; ?>">
                                    <button type="submit" name="withdraw" class="action-btn withdraw-btn" onclick="return confirmWithdraw()">
                                        <i class="fas fa-times"></i> Withdraw
                                    </button>
                                </form>
                        </div>
                        
                        <!-- Application Details -->
                        <div class="application-details" id="details-<?php echo $application['application_id']; ?>">
                            <div class="details-grid">
                                <div class="detail-item">
                                    <span class="detail-label">Job Description:</span><br>
                                    <?php echo htmlspecialchars($application['description']); ?>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Drive Date:</span><br>
                                    <?php echo date('F j, Y', strtotime($application['prefered_date'])); ?>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">CGPA Requirement:</span><br>
                                    <?php echo htmlspecialchars($application['cutoff_cgpa']); ?>+
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Arrears Allowed:</span><br>
                                    <?php echo htmlspecialchars($application['arrears_allowed']); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-applications">
                    <i class="fas fa-file-alt"></i>
                    <h3>No Applications Yet</h3>
                    <p>You haven't applied to any jobs yet. Browse available positions and apply!</p>
                    <a href="student_dashboard.php" class="action-btn view-btn" style="margin-top: 15px; display: inline-block; text-decoration: none;">
                        <i class="fas fa-briefcase"></i> Browse Jobs
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // JavaScript to handle application details and withdrawals
        function toggleDetails(applicationId) {
            const detailsElement = document.getElementById('details-' + applicationId);
            if (detailsElement.style.display === 'block') {
                detailsElement.style.display = 'none';
            } else {
                detailsElement.style.display = 'block';
            }
        }
        
        function confirmWithdraw() {
            return confirm("Are you sure you want to withdraw this application? This action cannot be undone.");
        }
        
        // Add SweetAlert for withdrawal confirmation
        document.addEventListener('DOMContentLoaded', function() {
            const withdrawButtons = document.querySelectorAll('.withdraw-btn');
            
            withdrawButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const form = this.closest('form');
                    const jobTitle = this.closest('.application-item').querySelector('.job-title').textContent;
                    
                    Swal.fire({
                        title: 'Withdraw Application?',
                        text: 'Are you sure you want to withdraw your application for "' + jobTitle + '"?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ff6b6b',
                        cancelButtonColor: '#001f3f',
                        confirmButtonText: 'Yes, withdraw it!',
                        cancelButtonText: 'No, keep it'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
            
            // Show status-specific messages
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('withdrawn') === 'success') {
                Swal.fire({
                    title: 'Application Withdrawn!',
                    text: 'Your application has been successfully withdrawn.',
                    icon: 'info',
                    confirmButtonColor: '#001f3f'
                });
            }
        });
    </script>
</body>
</html>