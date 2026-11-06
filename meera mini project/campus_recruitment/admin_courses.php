<?php
ob_start();
include("connection.php");
include("headers/admin_header.php");

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Add Course
    if (isset($_POST['add_course'])) {
        $course = trim($_POST['course']);
        $duration = trim($_POST['duration']);
        
        // Server-side validation
        $errors = [];
        
        if (empty($course)) {
            $errors[] = "Course name is required";
        }
        if (empty($duration)) {
            $errors[] = "Duration is required";
        }

        if (empty($errors)) {
            $insert_course = "INSERT INTO tbl_course (course, duration) 
                             VALUES (?, ?)";
            
            $stmt = mysqli_prepare($conn, $insert_course);
            mysqli_stmt_bind_param($stmt, "ss", $course, $duration);
            
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['success'] = "Course added successfully";
                header("Location: admin_courses.php");
                exit();
            } else {
                $errors[] = "Error: ".mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header("Location: admin_courses.php");
            exit();
        }
    }
    
    // Edit Course
    if (isset($_POST['edit_course'])) {
        $course_id = $_POST['course_id'];
        $course = trim($_POST['course']);
        $duration = trim($_POST['duration']);
        
        // Server-side validation
        $errors = [];
        
        if (empty($course)) {
            $errors[] = "Course name is required";
        }
        if (empty($duration)) {
            $errors[] = "Duration is required";
        }

        if (empty($errors)) {
            $update_course = "UPDATE tbl_course 
                            SET course=?, duration=?
                            WHERE course_id=?";
            
            $stmt = mysqli_prepare($conn, $update_course);
            mysqli_stmt_bind_param($stmt, "ssi", $course, $duration, $course_id);
            
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['success'] = "Course updated successfully";
                header("Location: admin_courses.php");
                exit();
            } else {
                $errors[] = "Error: ".mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header("Location: admin_courses.php");
            exit();
        }
    }
    
    // Delete Course
    if (isset($_POST['delete_course'])) {
        $course_id = mysqli_real_escape_string($conn, $_POST['course_id']);
        
        $delete_course = "DELETE FROM tbl_course WHERE course_id=?";
        
        $stmt = mysqli_prepare($conn, $delete_course);
        mysqli_stmt_bind_param($stmt, "i", $course_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = "Course deleted successfully";
        } else {
            $_SESSION['errors'] = ["Error deleting course: " . mysqli_error($conn)];
        }
        mysqli_stmt_close($stmt);
        
        header("Location: admin_courses.php");
        exit();
    }
}

// Fetch all courses
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$courses_query = "SELECT * FROM tbl_course 
                 WHERE course LIKE '%$search%' OR duration LIKE '%$search%' 
                 ORDER BY course ASC";
$courses_result = mysqli_query($conn, $courses_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .action-icons a {
            margin: 0 5px;
            color: #6c757d;
            text-decoration: none;
        }
        .action-icons a:hover {
            color: #0d6efd;
        }
        .table-responsive {
            overflow-x: auto;
        }
        .error-message {
            color: #dc3545;
            font-size: 0.875em;
        }
    </style>
</head>
<body>
    <div class="container-fluid" >
        <div class="row">
            <!-- Main Content -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Course Management</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                            <i class="fas fa-plus"></i> Add Course
                        </button>
                    </div>
                </div>
                
                <!-- Search and Filter -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" name="search" placeholder="Search by Course or Duration" value="<?= htmlspecialchars($search) ?>">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-search"></i> Search
                                        </button>
                                        <a href="admin_courses.php" class="btn btn-secondary">Clear</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Courses Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Course Name</th>
                                        <th>Duration</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (mysqli_num_rows($courses_result) > 0): ?>
                                        <?php $counter = 1; ?>
                                        <?php while ($course = mysqli_fetch_assoc($courses_result)): ?>
                                            <tr>
                                                <td><?= $counter++ ?></td>
                                                <td><?= htmlspecialchars($course['course']) ?></td>
                                                <td><?= htmlspecialchars($course['duration']) ?></td>
                                                <td class="action-icons">
                                                    <a href="#" class="edit-course" title="Edit" 
                                                       data-courseid="<?= $course['course_id'] ?>"
                                                       data-course="<?= htmlspecialchars($course['course']) ?>"
                                                       data-duration="<?= htmlspecialchars($course['duration']) ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="#" class="delete-course" title="Delete" 
                                                       data-courseid="<?= $course['course_id'] ?>"
                                                       data-course="<?= htmlspecialchars($course['course']) ?>">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No courses found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Add Course Modal -->
    <div class="modal fade" id="addCourseModal" tabindex="-1" aria-labelledby="addCourseModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="addCourseForm" method="POST" action="">
                    <input type="hidden" name="add_course" value="1">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addCourseModalLabel">Add New Course</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                       <div class="mb-3">
    <label for="course" class="form-label">Course Name</label>
    <input type="text" class="form-control" id="course" name="course" required>
    <div id="courseError" class="error-message">
        <?php 
        if (isset($_SESSION['errors']['course'])) {
            echo $_SESSION['errors']['course'];
            unset($_SESSION['errors']['course']);
        }
        ?>
    </div>
</div>
<div class="mb-3">
    <label for="duration" class="form-label">Duration</label>
    <input type="text" class="form-control" id="duration" name="duration" required placeholder="e.g., 3 years, 6 months, etc.">
    <div id="durationError" class="error-message">
        <?php 
        if (isset($_SESSION['errors']['duration'])) {
            echo $_SESSION['errors']['duration'];
            unset($_SESSION['errors']['duration']);
        }
        ?>
    </div>
</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Course</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Course Modal -->
    <div class="modal fade" id="editCourseModal" tabindex="-1" aria-labelledby="editCourseModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editCourseForm" method="POST" action="">
                    <input type="hidden" name="edit_course" value="1">
                    <input type="hidden" name="course_id" id="editCourseId">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editCourseModalLabel">Edit Course</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="editCourse" class="form-label">Course Name</label>
                            <input type="text" class="form-control" id="editCourse" name="course" required>
                            <div id="editCourseError" class="error-message"></div>
                        </div>
                        <div class="mb-3">
                            <label for="editDuration" class="form-label">Duration</label>
                            <input type="text" class="form-control" id="editDuration" name="duration" required>
                            <div id="editDurationError" class="error-message"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="">
                    <input type="hidden" name="delete_course" value="1">
                    <input type="hidden" name="course_id" id="deleteCourseId">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirm Deletion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete the course: <strong id="courseNameToDelete"></strong>?</p>
                        <p class="text-danger">This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
     <script src="mainstyle/js/admin_coursevalidation.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show success/error messages
            <?php if (isset($_SESSION['success'])): ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: '<?= $_SESSION['success'] ?>',
                    confirmButtonText: 'OK'
                });
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['errors'])): ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: '<?= implode("<br>", $_SESSION['errors']) ?>',
                    confirmButtonText: 'OK'
                });
                <?php unset($_SESSION['errors']); ?>
            <?php endif; ?>
            
            // Edit Course Modal
            const editCourseModal = new bootstrap.Modal(document.getElementById('editCourseModal'));
            document.querySelectorAll('.edit-course').forEach(button => {
                button.addEventListener('click', function() {
                    const courseId = this.getAttribute('data-courseid');
                    const course = this.getAttribute('data-course');
                    const duration = this.getAttribute('data-duration');
                    
                    document.getElementById('editCourseId').value = courseId;
                    document.getElementById('editCourse').value = course;
                    document.getElementById('editDuration').value = duration;
                    
                    editCourseModal.show();
                });
            });
            
            // Delete Confirmation Modal
            const deleteConfirmationModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
            document.querySelectorAll('.delete-course').forEach(button => {
                button.addEventListener('click', function() {
                    const courseId = this.getAttribute('data-courseid');
                    const courseName = this.getAttribute('data-course');
                    
                    document.getElementById('deleteCourseId').value = courseId;
                    document.getElementById('courseNameToDelete').textContent = courseName;
                    
                    deleteConfirmationModal.show();
                });
            });
            
            // Form validation
            const addForm = document.getElementById('addCourseForm');
            if (addForm) {
                addForm.addEventListener('submit', function(e) {
                    let valid = true;
                    
                    // Validate course name
                    const course = document.getElementById('course').value.trim();
                    if (course === '') {
                        document.getElementById('courseError').textContent = 'Course name is required';
                        valid = false;
                    } else {
                        document.getElementById('courseError').textContent = '';
                    }
                    
                    // Validate duration
                    const duration = document.getElementById('duration').value.trim();
                    if (duration === '') {
                        document.getElementById('durationError').textContent = 'Duration is required';
                        valid = false;
                    } else {
                        document.getElementById('durationError').textContent = '';
                    }
                    
                    if (!valid) {
                        e.preventDefault();
                    }
                });
            }
            
            const editForm = document.getElementById('editCourseForm');
            if (editForm) {
                editForm.addEventListener('submit', function(e) {
                    let valid = true;
                    
                    // Validate course name
                    const course = document.getElementById('editCourse').value.trim();
                    if (course === '') {
                        document.getElementById('editCourseError').textContent = 'Course name is required';
                        valid = false;
                    } else {
                        document.getElementById('editCourseError').textContent = '';
                    }
                    
                    // Validate duration
                    const duration = document.getElementById('editDuration').value.trim();
                    if (duration === '') {
                        document.getElementById('editDurationError').textContent = 'Duration is required';
                        valid = false;
                    } else {
                        document.getElementById('editDurationError').textContent = '';
                    }
                    
                    if (!valid) {
                        e.preventDefault();
                    }
                });
            }
        });
    </script>

    <?php
    ob_end_flush();
    ?>
</body>
</html>