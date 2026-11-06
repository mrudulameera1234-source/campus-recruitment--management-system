<?php
session_start();
include("connection.php");
include("headers/admin_header.php");

// Initialize variables
$error = '';
$success = '';
$adminData = [];

// Fetch current admin data
$query = "SELECT * FROM tbl_login WHERE role = 'admin'";
$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) > 0) {
    $adminData = mysqli_fetch_assoc($result);
}

// Handle form submissions
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Update Profile Information
    if(isset($_POST['update_profile'])) {
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        
        $updateQuery = "UPDATE tbl_login SET email = '$email' WHERE role = 'admin'";
        
        if(mysqli_query($conn, $updateQuery)) {
            $success = "Profile updated successfully!";
            // Refresh admin data
            $result = mysqli_query($conn, $query);
            if(mysqli_num_rows($result) > 0) {
                $adminData = mysqli_fetch_assoc($result);
            }
        } else {
            $error = "Error updating profile: " . mysqli_error($conn);
        }
    }
    
    // Update Password
    if(isset($_POST['update_password'])) {
        $currentPassword = $_POST['currentPassword'];
        $newPassword = $_POST['newPassword'];
        $confirmPassword = $_POST['confirmPassword'];
        
        // Verify current password
        if(password_verify($currentPassword, $adminData['password'])) {
            if($newPassword === $confirmPassword) {
                $updateQuery = "UPDATE tbl_login SET password = '$newPassword' WHERE role = 'admin'";
                
                if(mysqli_query($conn, $updateQuery)) {
                    $success = "Password updated successfully!";
                } else {
                    $error = "Error updating password: " . mysqli_error($conn);
                }
            } else {
                $error = "New passwords do not match!";
            }
        } else {
            $error = "Current password is incorrect!";
        }
    }
    
    // Update System Settings (pseudo-code - you'll need to implement your settings storage method)
    if(isset($_POST['save_settings'])) {
        // This would typically update settings in a settings table
        $emailNotifications = isset($_POST['email_notifications']) ? 1 : 0;
        $studentApproval = isset($_POST['student_approval']) ? 1 : 0;
        $companyApproval = isset($_POST['company_approval']) ? 1 : 0;
        $resultsPerPage = (int)$_POST['resultsPerPage'];
        
        // Add your update logic for system settings here
        $success = "System settings updated successfully!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings - Campus Recruitment System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --success: #2ecc71;
            --danger: #e74c3c;
            --warning: #f39c12;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --gray: #7f8c8d;
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
        
        /* Settings Panel Styles */
        .settings-panel {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 25px;
            margin-bottom: 30px;
        }
        
        .panel-header {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .panel-header h2 {
            color: var(--secondary);
            font-size: 22px;
        }
        
        .panel-header i {
            color: var(--primary);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--secondary);
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: border 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            outline: none;
        }
        
        .form-hint {
            font-size: 13px;
            color: var(--gray);
            margin-top: 5px;
        }
        
        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
        }
        
        .btn-secondary {
            background-color: #f1f1f1ff;
            color: #333;
            margin-right: 10px;
        }
        
        .btn-secondary:hover {
            background-color: #e0e0e0;
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }
        
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 30px;
        }
        
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }
        
        .slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .slider {
            background-color: var(--success);
        }
        
        input:checked + .slider:before {
            transform: translateX(30px);
        }
        
        .toggle-label {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .password-strength {
            height: 5px;
            margin-top: 8px;
            border-radius: 3px;
            transition: all 0.3s;
        }
        
        .strength-weak {
            background-color: var(--danger);
            width: 33%;
        }
        
        .strength-medium {
            background-color: var(--warning);
            width: 66%;
        }
        
        .strength-strong {
            background-color: var(--success);
            width: 100%;
        }
        
        .security-notification {
            background-color: #fff8e1;
            border-left: 4px solid var(--warning);
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        
        .alert-success {
            color: #3c763d;
            background-color: #dff0d8;
            border-color: #d6e9c6;
        }
        
        .alert-error {
            color: #a94442;
            background-color: #f2dede;
            border-color: #ebccd1;
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
        }
        
        @media (max-width: 576px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .form-actions {
                flex-direction: column;
                gap: 10px;
            }
            
            .btn {
                width: 100%;
            }
            
            .btn-secondary {
                margin-right: 0;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">

        <!-- Main Content -->
            <div class="header">
                <h1><i class="fas fa-cog"></i> Admin Settings</h1>
                <div class="user-info">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($adminData['name'] ?? 'Admin'); ?>&background=3498db&color=fff" alt="Admin User">
                    <span><?php echo $adminData['name'] ?? 'Admin'; ?></span>
                </div>
            </div>

            <!-- Display error/success messages -->
            <?php if(!empty($error)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if(!empty($success)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <!-- Security Settings -->
            <div class="settings-panel">
                <div class="panel-header">
                    <i class="fas fa-shield-alt"></i>
                    <h2>Security Settings</h2>
                </div>
                
                <div class="security-notification">
                    <i class="fas fa-exclamation-circle"></i> For security reasons, please change your password regularly and ensure it's strong.
                </div>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="currentPassword">Current Password</label>
                        <input type="password" id="currentPassword" name="currentPassword" class="form-control" placeholder="Enter current password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="newPassword">New Password</label>
                        <input type="password" id="newPassword" name="newPassword" class="form-control" placeholder="Enter new password" required>
                        <div class="password-strength strength-weak"></div>
                        <p class="form-hint">Use at least 8 characters with a mix of letters, numbers and symbols.</p>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirmPassword">Confirm New Password</label>
                        <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" placeholder="Confirm new password" required>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary">Cancel</button>
                        <button type="submit" name="update_password" class="btn btn-primary">Update Password</button>
                    </div>
                </form>
            </div>

            <!-- System Settings -->
            <div class="settings-panel">
                <div class="panel-header">
                    <i class="fas fa-sliders-h"></i>
                    <h2>System Settings</h2>
                </div>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label class="toggle-label">
                            <span class="toggle-switch">
                                <input type="checkbox" name="email_notifications" checked>
                                <span class="slider"></span>
                            </span>
                            Enable Email Notifications
                        </label>
                        <p class="form-hint">Receive email alerts for new company registrations and important activities.</p>
                    </div>
                    
                    <div class="form-group">
                        <label class="toggle-label">
                            <span class="toggle-switch">
                                <input type="checkbox" name="student_approval" checked>
                                <span class="slider"></span>
                            </span>
                            Student Registration Approval
                        </label>
                        <p class="form-hint">Require admin approval for new student registrations.</p>
                    </div>
                    
                    <div class="form-group">
                        <label class="toggle-label">
                            <span class="toggle-switch">
                                <input type="checkbox" name="company_approval" checked>
                                <span class="slider"></span>
                            </span>
                            Company Registration Approval
                        </label>
                        <p class="form-hint">Require admin approval for new company registrations.</p>
                    </div>
                    
                    <div class="form-group">
                        <label for="resultsPerPage">Results Per Page</label>
                        <select id="resultsPerPage" name="resultsPerPage" class="form-control">
                            <option value="10">10</option>
                            <option value="25" selected>25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <p class="form-hint">Number of items to display on management pages.</p>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary">Cancel</button>
                        <button type="submit" name="save_settings" class="btn btn-primary">Save Settings</button>
                    </div>
                </form>
            </div>

            <!-- Account Settings -->
            <div class="settings-panel">
                <div class="panel-header">
                    <i class="fas fa-user-cog"></i>
                    <h2>Account Settings</h2>
                </div>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="adminName">Admin Name</label>
                        <input type="text" id="adminName" name="adminName" class="form-control" value="<?php echo $adminData['name'] ?? 'Administrator'; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="adminEmail">Email Address</label>
                        <input type="email" id="adminEmail" name="adminEmail" class="form-control" value="<?php echo $adminData['email'] ?? 'admin@campusrecruitment.edu'; ?>" required>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary">Cancel</button>
                        <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                    </div>
                </form>
            </div>
            
            <!-- Danger Zone -->
            <div class="settings-panel" style="border-left: 4px solid var(--danger);">
                <div class="panel-header">
                    <i class="fas fa-exclamation-triangle" style="color: var(--danger);"></i>
                    <h2 style="color: var(--danger);">Danger Zone</h2>
                </div>
                
                <div class="form-group">
                    <p>Once you delete your account, there is no going back. Please be certain.</p>
                </div>
                
                <div class="form-actions">
                    <button class="btn btn-secondary">Export Data</button>
                    <button class="btn" style="background-color: var(--danger); color: white;">Delete Account</button>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Password strength indicator
        const passwordInput = document.getElementById('newPassword');
        const strengthBar = document.querySelector('.password-strength');
        
        passwordInput.addEventListener('input', function() {
            const password = passwordInput.value;
            let strength = 0;
            
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
            if (password.match(/\d/)) strength++;
            if (password.match(/[^a-zA-Z\d]/)) strength++;
            
            strengthBar.className = 'password-strength';
            
            if (password.length === 0) {
                strengthBar.className = 'password-strength';
            } else if (strength < 2) {
                strengthBar.classList.add('strength-weak');
            } else if (strength < 4) {
                strengthBar.classList.add('strength-medium');
            } else {
                strengthBar.classList.add('strength-strong');
            }
        });
        
        // Form submission handling
        document.addEventListener('DOMContentLoaded', function() {
            const saveButtons = document.querySelectorAll('.btn-primary');
            
            saveButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    // For demo purposes, we'll let the form submit normally
                    // In a real application, you might want to add AJAX here
                    
                    const form = this.closest('form');
                    if(form.checkValidity()) {
                        // Show saving indication
                        const originalText = this.textContent;
                        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
                        this.disabled = true;
                        
                        // The form will submit and refresh the page
                        // In a real application, you might use AJAX instead
                    }
                });
            });
            
            // Cancel buttons
            const cancelButtons = document.querySelectorAll('.btn-secondary');
            cancelButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Reset form values
                    const form = this.closest('form');
                    form.reset();
                });
            });
        });
    </script>
</body>
</html>