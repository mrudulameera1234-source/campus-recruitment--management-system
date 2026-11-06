<?php

include("connection.php");

include("headers/company_header.php");

$error = '';
$success = '';

// Get courses for eligibility using MySQLi (to match your header)
$courses = [];
$sql = "SELECT * FROM tbl_course ORDER BY course";
$result = mysqli_query($conn, $sql);
if ($result) {
    $courses = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    $error = "Error loading courses: " . mysqli_error($conn);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $prefered_date = $_POST['prefered_date'];
    $drive_description = $_POST['drive_description'];
    $post = $_POST['post'];
    $vacancy_description = $_POST['vacancy_description'];
    $arrears_allowed = $_POST['arrears_allowed'];
    $cutoff_cgpa = $_POST['cutoff_cgpa'];
    $last_date = $_POST['last_date'];
    $eligible_courses = $_POST['eligible_courses'] ?? [];

    // Validation
    if (empty($prefered_date) || empty($post) || empty($arrears_allowed) || empty($cutoff_cgpa) || empty($last_date)) {
        $error = "Please fill all required fields";
    } elseif (count($eligible_courses) == 0) {
        $error = "Please select at least one eligible course";
    } else {
        // Start transaction
        mysqli_begin_transaction($conn);
        
        try {
            // Insert drive details
            $stmt = mysqli_prepare($conn, "INSERT INTO tbl_drive (company_id, prefered_date, description, created_at) VALUES (?, ?, ?, NOW())");
            mysqli_stmt_bind_param($stmt, "iss", $_SESSION['company_id'], $prefered_date, $drive_description);
            mysqli_stmt_execute($stmt);
            $drive_id = mysqli_insert_id($conn);
            
            if ($drive_id) {
                // Insert vacancy details
                $stmt2 = mysqli_prepare($conn, "INSERT INTO tbl_vacancies (drive_id, post, description, arrears_allowed, cutoff_cgpa, last_date, posted_on) VALUES (?, ?, ?, ?, ?, ?, NOW())");
                mysqli_stmt_bind_param($stmt2, "issdds", $drive_id, $post, $vacancy_description, $arrears_allowed, $cutoff_cgpa, $last_date);
                mysqli_stmt_execute($stmt2);
                $vacancy_id = mysqli_insert_id($conn);
                
                if ($vacancy_id) {
                    // Insert eligible courses
                    $stmt3 = mysqli_prepare($conn, "INSERT INTO tbl_vacancy_course (vacancy_id, course_id) VALUES (?, ?)");
                    foreach ($eligible_courses as $course_id) {
                        mysqli_stmt_bind_param($stmt3, "ii", $vacancy_id, $course_id);
                        mysqli_stmt_execute($stmt3);
                    }
                    
                    mysqli_commit($conn);
                    $success = "Drive scheduled successfully! Waiting for admin approval.";
                    
                    // Clear form
                    $_POST = array();
                } else {
                    throw new Exception("Failed to insert vacancy details");
                }
            } else {
                throw new Exception("Failed to insert drive details");
            }
            
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error = "Error scheduling drive: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Drive - Company Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }
        .card {
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            border: none;
            border-radius: 10px;
        }
        .form-section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .required:after {
            content: " *";
            color: red;
        }
        .course-checkbox {
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 10px;
            margin: 5px 0;
            transition: all 0.3s;
        }
        .course-checkbox:hover {
            background-color: #f8f9fa;
            border-color: #007bff;
        }
        .course-checkbox.selected {
            background-color: #e3f2fd;
            border-color: #2196f3;
        }
    </style>
</head>
<body>
  
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Header -->
                <div class="text-center mb-4">
                    <h1 class="text-primary">Schedule Campus Drive</h1>
                    <p class="text-muted">Fill in the drive details for admin approval</p>
                </div>

                <!-- Alerts -->
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Drive Scheduling Form -->
                <div class="main-content">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="">
                            <!-- Drive Information -->
                            <div class="form-section">
                                <h4 class="mb-3 text-primary">📅 Drive Information</h4>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Preferred Drive Date</label>
                                        <input type="date" class="form-control" name="prefered_date" 
                                               value="<?php echo isset($_POST['prefered_date']) ? htmlspecialchars($_POST['prefered_date']) : ''; ?>" 
                                               min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                                        <small class="text-muted">Select the preferred date for conducting the drive</small>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Drive Description</label>
                                        <textarea class="form-control" name="drive_description" rows="2" 
                                                  placeholder="Brief description about the drive..."><?php echo isset($_POST['drive_description']) ? htmlspecialchars($_POST['drive_description']) : ''; ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Vacancy Details -->
                            <div class="form-section">
                                <h4 class="mb-3 text-primary">💼 Vacancy Details</h4>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Job Post/Position</label>
                                        <input type="text" class="form-control" name="post" 
                                               value="<?php echo isset($_POST['post']) ? htmlspecialchars($_POST['post']) : ''; ?>" 
                                               placeholder="e.g., Software Engineer, Data Analyst" required>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Last Date to Apply</label>
                                        <input type="date" class="form-control" name="last_date" 
                                               value="<?php echo isset($_POST['last_date']) ? htmlspecialchars($_POST['last_date']) : ''; ?>" 
                                               min="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label required">Cutoff CGPA</label>
                                        <input type="number" class="form-control" name="cutoff_cgpa" 
                                               value="<?php echo isset($_POST['cutoff_cgpa']) ? htmlspecialchars($_POST['cutoff_cgpa']) : ''; ?>" 
                                               step="0.01" min="0" max="10" placeholder="e.g., 7.5" required>
                                        <small class="text-muted">Minimum CGPA required</small>
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label required">Arrears Allowed</label>
                                        <input type="number" class="form-control" name="arrears_allowed" 
                                               value="<?php echo isset($_POST['arrears_allowed']) ? htmlspecialchars($_POST['arrears_allowed']) : ''; ?>" 
                                               min="0" max="10" placeholder="e.g., 2" required>
                                        <small class="text-muted">Maximum number of arrears allowed</small>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Job Description</label>
                                    <textarea class="form-control" name="vacancy_description" rows="4" 
                                              placeholder="Detailed job description, roles and responsibilities, skills required..."><?php echo isset($_POST['vacancy_description']) ? htmlspecialchars($_POST['vacancy_description']) : ''; ?></textarea>
                                </div>
                            </div>

                            <!-- Eligibility Criteria -->
                            <div class="form-section">
                                <h4 class="mb-3 text-primary">🎓 Eligibility Criteria</h4>
                                <p class="text-muted mb-3">Select the courses that are eligible for this drive:</p>
                                
                                <div class="row">
                                    <?php foreach ($courses as $course): ?>
                                        <div class="col-md-4 mb-2">
                                            <div class="course-checkbox">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" 
                                                           name="eligible_courses[]" 
                                                           value="<?php echo $course['course_id']; ?>"
                                                           id="course_<?php echo $course['course_id']; ?>"
                                                           <?php 
                                                           if (isset($_POST['eligible_courses']) && in_array($course['course_id'], $_POST['eligible_courses'])) {
                                                               echo 'checked';
                                                           }
                                                           ?>>
                                                    <label class="form-check-label w-100" for="course_<?php echo $course['course_id']; ?>">
                                                        <?php echo htmlspecialchars($course['course']); ?>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <?php if (empty($courses)): ?>
                                    <div class="alert alert-warning">
                                        No courses available. Please contact administrator.
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <button type="reset" class="btn btn-secondary me-md-2">
                                    🔄 Clear Form
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    ✅ Schedule Drive
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Information Card -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title">ℹ️ Drive Submission Process</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">1. Fill all the required details accurately</li>
                            <li class="list-group-item">2. Submit the form for admin approval</li>
                            <li class="list-group-item">3. Admin will review and approve/reject the drive</li>
                            <li class="list-group-item">4. You will be notified once approved</li>
                            <li class="list-group-item">5. Approved drives will be visible to students</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Enhance checkbox selection
        document.querySelectorAll('.form-check-input').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const parentDiv = this.closest('.course-checkbox');
                if (this.checked) {
                    parentDiv.classList.add('selected');
                } else {
                    parentDiv.classList.remove('selected');
                }
            });
            
            // Initialize selected state
            if (checkbox.checked) {
                checkbox.closest('.course-checkbox').classList.add('selected');
            }
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const checkboxes = document.querySelectorAll('input[name="eligible_courses[]"]:checked');
            if (checkboxes.length === 0) {
                e.preventDefault();
                alert('Please select at least one eligible course.');
                return false;
            }
        });

        // Set minimum dates
        const today = new Date().toISOString().split('T')[0];
        document.querySelector('input[name="prefered_date"]').min = today;
        document.querySelector('input[name="last_date"]').min = today;
    </script>
</body>
</html>