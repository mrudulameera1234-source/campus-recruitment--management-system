<?php
ob_start();
// Database connection and processing
session_start();
include("connection.php");
include("headers/admin_header.php");

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Add Student
    if (isset($_POST['add_student'])) {
        $name = $_POST['name'];
        $admno = $_POST['admno'];
        $dob = $_POST['dob'];
        $year = $_POST['year'];
        
        // Server-side validation
        $errors = [];
        
        if (empty($name)) {
            $errors[] = "Name is required";
        } else if (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
            $errors[] = "Name should contain only alphabets and spaces";
        }
        
        if (empty($admno)) {
            $errors[] = "Admission number is required";
        } else {
            // Check if admission number already exists
            $check_admno = "SELECT * FROM tbl_student_credentials WHERE admno = '$admno'";
            $result = mysqli_query($conn, $check_admno);
            if (mysqli_num_rows($result) > 0) {
                $errors[] = "Admission number already exists";
            }
        }
        
        if (empty($dob)) {
            $errors[] = "Date of birth is required";
        } else {
            // Check if student is at least 17 years old
            $today = new DateTime();
            $birthdate = new DateTime($dob);
            $age = $today->diff($birthdate)->y;
            if ($age < 17) {
                $errors[] = "Student must be at least 17 years old";
            }
            
            // Check if student with same name and DOB already exists
            $check_student = "SELECT * FROM tbl_student_credentials WHERE name = '$name' AND dob = '$dob'";
            $result = mysqli_query($conn, $check_student);
            if (mysqli_num_rows($result) > 0) {
                $errors[] = "Student with this name and date of birth already exists";
            }
        }
        
        if (empty($year)) {
            $errors[] = "Year of joining is required";
        } else if ($year < 2000 || $year > 2030) {
            $errors[] = "Year must be between 2000 and 2030";
        }

        if (empty($errors)) {
            $insert_student = "INSERT INTO tbl_student_credentials (admno, year, dob, name) 
                               VALUES ('$admno', '$year', '$dob', '$name')";
            
            if (mysqli_query($conn, $insert_student)) {
                $_SESSION['success'] = "Student added successfully";
                header("Location: admin_manage_student.php");
                exit();
            } else {
                $errors[] = "Error: ".mysqli_error($conn);
            }
        }
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header("Location: admin_manage_student.php");
            exit();
        }
    }
    
    // Edit Student
    if (isset($_POST['edit_student'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $admno = $_POST['admno'];
        $dob = $_POST['dob'];
        $year = $_POST['year'];
        
        // Server-side validation
        $errors = [];
        
        if (empty($name)) {
            $errors[] = "Name is required";
        } else if (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
            $errors[] = "Name should contain only alphabets and spaces";
        }
        
        if (empty($admno)) {
            $errors[] = "Admission number is required";
        } else {
            // Check if admission number already exists (excluding current student)
            $check_admno = "SELECT * FROM tbl_student_credentials WHERE admno = '$admno' AND credential_id != $id";
            $result = mysqli_query($conn, $check_admno);
            if (mysqli_num_rows($result) > 0) {
                $errors[] = "Admission number already exists";
            }
        }
        
        if (empty($dob)) {
            $errors[] = "Date of birth is required";
        } else {
            // Check if student is at least 17 years old
            $today = new DateTime();
            $birthdate = new DateTime($dob);
            $age = $today->diff($birthdate)->y;
            if ($age < 17) {
                $errors[] = "Student must be at least 17 years old";
            }
            
            // Check if student with same name and DOB already exists (excluding current student)
            $check_student = "SELECT * FROM tbl_student_credentials WHERE name = '$name' AND dob = '$dob' AND credential_id != $id";
            $result = mysqli_query($conn, $check_student);
            if (mysqli_num_rows($result) > 0) {
                $errors[] = "Student with this name and date of birth already exists";
            }
        }
        
        if (empty($year)) {
            $errors[] = "Year of joining is required";
        } else if ($year < 2000 || $year > 2030) {
            $errors[] = "Year must be between 2000 and 2030";
        }

        if (empty($errors)) {
            $update_student = "UPDATE tbl_student_credentials 
                              SET name='$name', admno='$admno', dob='$dob', year='$year' 
                              WHERE credential_id=$id";
            
            if (mysqli_query($conn, $update_student)) {
                $_SESSION['success'] = "Student updated successfully";
                header("Location: admin_manage_student.php");
                exit();
            } else {
                $errors[] = "Error: ".mysqli_error($conn);
            }
        }
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header("Location: admin_manage_student.php");
            exit();
        }
    }
    
    // Delete Student
    if (isset($_POST['delete_student'])) {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        
        $delete_student = "DELETE FROM tbl_student_credentials WHERE credential_id=$id";
        
        if (mysqli_query($conn, $delete_student)) {
            $_SESSION['success'] = "Student deleted successfully";
            header("Location: admin_manage_student.php");
            exit();
        } else {
            $_SESSION['errors'] = ["Error deleting student: " . mysqli_error($conn)];
            header("Location: admin_manage_student.php");
            exit();
        }
    }
}

// Fetch all students
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$students_query = "SELECT * FROM tbl_student_credentials 
                  WHERE name LIKE '%$search%' OR admno LIKE '%$search%' 
                  ORDER BY year DESC, name ASC";
$students_result = mysqli_query($conn, $students_query);

// Get existing students for JavaScript validation
$existing_students = [];
while ($student = mysqli_fetch_assoc($students_result)) {
    $existing_students[] = [
        'id' => $student['credential_id'],
        'name' => $student['name'],
        'admno' => $student['admno'],
        'dob' => $student['dob']
    ];
}
// Reset pointer for later use
mysqli_data_seek($students_result, 0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management</title>
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
        .is-invalid {
            border-color: #dc3545;
        }
    </style>
</head>
<body>
    <!-- Main Content -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Student Credential Management</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                <i class="fas fa-plus"></i> Add Student Credentials
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
                            <input type="text" class="form-control" name="search" placeholder="Search by Name or Admission No" value="<?= htmlspecialchars($search) ?>">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i> Search
                            </button>
                            <a href="admin_manage_student.php" class="btn btn-secondary">Clear</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Students Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Admission No</th>
                            <th>Name</th>
                            <th>Date of Birth</th>
                            <th>Year</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($students_result) > 0): ?>
                            <?php while ($student = mysqli_fetch_assoc($students_result)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($student['admno']) ?></td>
                                    <td><?= htmlspecialchars($student['name']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($student['dob'])) ?></td>
                                    <td><?= htmlspecialchars($student['year']) ?></td>
                                    <td class="action-icons">
                                        <a href="#" class="edit-student" title="Edit" 
                                           data-id="<?= $student['credential_id'] ?>"
                                           data-name="<?= htmlspecialchars($student['name']) ?>"
                                           data-admno="<?= htmlspecialchars($student['admno']) ?>"
                                           data-dob="<?= date('Y-m-d', strtotime($student['dob'])) ?>"
                                           data-year="<?= htmlspecialchars($student['year']) ?>">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" class="delete-student" title="Delete" 
                                           data-id="<?= $student['credential_id'] ?>"
                                           data-name="<?= htmlspecialchars($student['name']) ?>">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">No students found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Student Modal -->
    <div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="addStudentForm" method="POST" action="">
                    <input type="hidden" name="add_student" value="1">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addStudentModalLabel">Add New Student</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <div id="nameError" class="error-message"></div>
                        </div>
                        <div class="mb-3">
                            <label for="admno" class="form-label">Admission Number</label>
                            <input type="text" class="form-control" id="admno" name="admno" required>
                            <div id="admnoError" class="error-message"></div>
                        </div>
                        <div class="mb-3">
                            <label for="dob" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="dob" name="dob" required>
                            <div id="dobError" class="error-message"></div>
                        </div>
                        <div class="mb-3">
                            <label for="year" class="form-label">Year of Joining</label>
                            <input type="number" class="form-control" id="year" name="year" min="2000" max="2030" required>
                            <div id="yearError" class="error-message"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Student</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Student Modal -->
    <div class="modal fade" id="editStudentModal" tabindex="-1" aria-labelledby="editStudentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editStudentForm" method="POST" action="">
                    <input type="hidden" name="edit_student" value="1">
                    <input type="hidden" name="id" id="editId">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editStudentModalLabel">Edit Student</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="editName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="editName" name="name" required>
                            <div id="editNameError" class="error-message"></div>
                        </div>
                        <div class="mb-3">
                            <label for="editAdmno" class="form-label">Admission Number</label>
                            <input type="text" class="form-control" id="editAdmno" name="admno" required>
                            <div id="editAdmnoError" class="error-message"></div>
                        </div>
                        <div class="mb-3">
                            <label for="editDob" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="editDob" name="dob" required>
                            <div id="editDobError" class="error-message"></div>
                        </div>
                        <div class="mb-3">
                            <label for="editYear" class="form-label">Year of Joining</label>
                            <input type="number" class="form-control" id="editYear" name="year" min="2000" max="2030" required>
                            <div id="editYearError" class="error-message"></div>
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
                    <input type="hidden" name="delete_student" value="1">
                    <input type="hidden" name="id" id="deleteId">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirm Deletion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete the student: <strong id="studentNameToDelete"></strong>?</p>
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
   
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Store existing students data for validation
        const existingStudents = <?= json_encode($existing_students) ?>;
        
        // Name validation (only alphabets and spaces)
        function validateName(name) {
            const regex = /^[a-zA-Z\s]+$/;
            return regex.test(name);
        }

        // DOB validation (age > 17)
        function validateDOB(dob) {
            const today = new Date();
            const birthDate = new Date(dob);
            const age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();
            
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                return age - 1 >= 17;
            }
            return age >= 17;
        }

        // Year validation (>= 2000)
        function validateYear(year) {
            return year >= 2000 && year <= 2030;
        }

        // Check if admission number already exists
        function isAdmnoUnique(admno, excludeId = null) {
            return !existingStudents.some(student => {
                if (excludeId && student.id == excludeId) return false;
                return student.admno === admno;
            });
        }

        // Check if student already exists (same name and DOB)
        function isStudentUnique(name, dob, excludeId = null) {
            return !existingStudents.some(student => {
                if (excludeId && student.id == excludeId) return false;
                return student.name.toLowerCase() === name.toLowerCase() && student.dob === dob;
            });
        }

        // Validate add student form
        function validateAddStudentForm() {
            let isValid = true;
            const name = document.getElementById('name').value.trim();
            const admno = document.getElementById('admno').value.trim();
            const dob = document.getElementById('dob').value;
            const year = document.getElementById('year').value;

            // Reset error messages
            document.getElementById('nameError').textContent = '';
            document.getElementById('admnoError').textContent = '';
            document.getElementById('dobError').textContent = '';
            document.getElementById('yearError').textContent = '';

            // Validate name
            if (!name) {
                document.getElementById('nameError').textContent = 'Name is required';
                document.getElementById('name').classList.add('is-invalid');
                isValid = false;
            } else if (!validateName(name)) {
                document.getElementById('nameError').textContent = 'Name should contain only alphabets and spaces';
                document.getElementById('name').classList.add('is-invalid');
                isValid = false;
            } else {
                document.getElementById('name').classList.remove('is-invalid');
            }

            // Validate admission number
            if (!admno) {
                document.getElementById('admnoError').textContent = 'Admission number is required';
                document.getElementById('admno').classList.add('is-invalid');
                isValid = false;
            } else if (!isAdmnoUnique(admno)) {
                document.getElementById('admnoError').textContent = 'Admission number already exists';
                document.getElementById('admno').classList.add('is-invalid');
                isValid = false;
            } else {
                document.getElementById('admno').classList.remove('is-invalid');
            }

            // Validate date of birth
            if (!dob) {
                document.getElementById('dobError').textContent = 'Date of birth is required';
                document.getElementById('dob').classList.add('is-invalid');
                isValid = false;
            } else if (!validateDOB(dob)) {
                document.getElementById('dobError').textContent = 'Student must be at least 17 years old';
                document.getElementById('dob').classList.add('is-invalid');
                isValid = false;
            } else if (name && !isStudentUnique(name, dob)) {
                document.getElementById('dobError').textContent = 'Student with this name and date of birth already exists';
                document.getElementById('dob').classList.add('is-invalid');
                isValid = false;
            } else {
                document.getElementById('dob').classList.remove('is-invalid');
            }

            // Validate year
            if (!year) {
                document.getElementById('yearError').textContent = 'Year is required';
                document.getElementById('year').classList.add('is-invalid');
                isValid = false;
            } else if (!validateYear(year)) {
                document.getElementById('yearError').textContent = 'Year must be between 2000 and 2030';
                document.getElementById('year').classList.add('is-invalid');
                isValid = false;
            } else {
                document.getElementById('year').classList.remove('is-invalid');
            }

            return isValid;
        }

        // Validate edit student form
        function validateEditStudentForm() {
            let isValid = true;
            const id = document.getElementById('editId').value;
            const name = document.getElementById('editName').value.trim();
            const admno = document.getElementById('editAdmno').value.trim();
            const dob = document.getElementById('editDob').value;
            const year = document.getElementById('editYear').value;

            // Reset error messages
            document.getElementById('editNameError').textContent = '';
            document.getElementById('editAdmnoError').textContent = '';
            document.getElementById('editDobError').textContent = '';
            document.getElementById('editYearError').textContent = '';

            // Validate name
            if (!name) {
                document.getElementById('editNameError').textContent = 'Name is required';
                document.getElementById('editName').classList.add('is-invalid');
                isValid = false;
            } else if (!validateName(name)) {
                document.getElementById('editNameError').textContent = 'Name should contain only alphabets and spaces';
                document.getElementById('editName').classList.add('is-invalid');
                isValid = false;
            } else {
                document.getElementById('editName').classList.remove('is-invalid');
            }

            // Validate admission number
            if (!admno) {
                document.getElementById('editAdmnoError').textContent = 'Admission number is required';
                document.getElementById('editAdmno').classList.add('is-invalid');
                isValid = false;
            } else if (!isAdmnoUnique(admno, id)) {
                document.getElementById('editAdmnoError').textContent = 'Admission number already exists';
                document.getElementById('editAdmno').classList.add('is-invalid');
                isValid = false;
            } else {
                document.getElementById('editAdmno').classList.remove('is-invalid');
            }

            // Validate date of birth
            if (!dob) {
                document.getElementById('editDobError').textContent = 'Date of birth is required';
                document.getElementById('editDob').classList.add('is-invalid');
                isValid = false;
            } else if (!validateDOB(dob)) {
                document.getElementById('editDobError').textContent = 'Student must be at least 17 years old';
                document.getElementById('editDob').classList.add('is-invalid');
                isValid = false;
            } else if (name && !isStudentUnique(name, dob, id)) {
                document.getElementById('editDobError').textContent = 'Student with this name and date of birth already exists';
                document.getElementById('editDob').classList.add('is-invalid');
                isValid = false;
            } else {
                document.getElementById('editDob').classList.remove('is-invalid');
            }

            // Validate year
            if (!year) {
                document.getElementById('editYearError').textContent = 'Year is required';
                document.getElementById('editYear').classList.add('is-invalid');
                isValid = false;
            } else if (!validateYear(year)) {
                document.getElementById('editYearError').textContent = 'Year must be between 2000 and 2030';
                document.getElementById('editYear').classList.add('is-invalid');
                isValid = false;
            } else {
                document.getElementById('editYear').classList.remove('is-invalid');
            }

            return isValid;
        }

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
            
            // Edit Student Modal
            const editStudentModal = new bootstrap.Modal(document.getElementById('editStudentModal'));
            document.querySelectorAll('.edit-student').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    const admno = this.getAttribute('data-admno');
                    const dob = this.getAttribute('data-dob');
                    const year = this.getAttribute('data-year');
                    
                    document.getElementById('editId').value = id;
                    document.getElementById('editName').value = name;
                    document.getElementById('editAdmno').value = admno;
                    document.getElementById('editDob').value = dob;
                    document.getElementById('editYear').value = year;
                    
                    // Clear validation errors
                    document.getElementById('editNameError').textContent = '';
                    document.getElementById('editAdmnoError').textContent = '';
                    document.getElementById('editDobError').textContent = '';
                    document.getElementById('editYearError').textContent = '';
                    document.getElementById('editName').classList.remove('is-invalid');
                    document.getElementById('editAdmno').classList.remove('is-invalid');
                    document.getElementById('editDob').classList.remove('is-invalid');
                    document.getElementById('editYear').classList.remove('is-invalid');
                    
                    editStudentModal.show();
                });
            });
            
            // Delete Confirmation Modal
            const deleteConfirmationModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
            document.querySelectorAll('.delete-student').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    
                    document.getElementById('deleteId').value = id;
                    document.getElementById('studentNameToDelete').textContent = name;
                    
                    deleteConfirmationModal.show();
                });
            });
            
            // Add form validation
            const addStudentForm = document.getElementById('addStudentForm');
            if (addStudentForm) {
                addStudentForm.addEventListener('submit', function(e) {
                    if (!validateAddStudentForm()) {
                        e.preventDefault();
                    }
                });
                
                // Real-time validation for add form
                document.getElementById('name').addEventListener('input', function() {
                    const name = this.value.trim();
                    const errorElement = document.getElementById('nameError');
                    
                    if (!name) {
                        errorElement.textContent = 'Name is required';
                        this.classList.add('is-invalid');
                    } else if (!validateName(name)) {
                        errorElement.textContent = 'Name should contain only alphabets and spaces';
                        this.classList.add('is-invalid');
                    } else {
                        errorElement.textContent = '';
                        this.classList.remove('is-invalid');
                    }
                });

                document.getElementById('admno').addEventListener('input', function() {
                    const admno = this.value.trim();
                    const errorElement = document.getElementById('admnoError');
                    
                    if (!admno) {
                        errorElement.textContent = 'Admission number is required';
                        this.classList.add('is-invalid');
                    } else if (!isAdmnoUnique(admno)) {
                        errorElement.textContent = 'Admission number already exists';
                        this.classList.add('is-invalid');
                    } else {
                        errorElement.textContent = '';
                        this.classList.remove('is-invalid');
                    }
                });

                document.getElementById('dob').addEventListener('change', function() {
                    const dob = this.value;
                    const name = document.getElementById('name').value.trim();
                    const errorElement = document.getElementById('dobError');
                    
                    if (!dob) {
                        errorElement.textContent = 'Date of birth is required';
                        this.classList.add('is-invalid');
                    } else if (!validateDOB(dob)) {
                        errorElement.textContent = 'Student must be at least 17 years old';
                        this.classList.add('is-invalid');
                    } else if (name && !isStudentUnique(name, dob)) {
                        errorElement.textContent = 'Student with this name and date of birth already exists';
                        this.classList.add('is-invalid');
                    } else {
                        errorElement.textContent = '';
                        this.classList.remove('is-invalid');
                    }
                });

                document.getElementById('year').addEventListener('input', function() {
                    const year = this.value;
                    const errorElement = document.getElementById('yearError');
                    
                    if (!year) {
                        errorElement.textContent = 'Year is required';
                        this.classList.add('is-invalid');
                    } else if (!validateYear(year)) {
                        errorElement.textContent = 'Year must be between 2000 and 2030';
                        this.classList.add('is-invalid');
                    } else {
                        errorElement.textContent = '';
                        this.classList.remove('is-invalid');
                    }
                });
            }
            
            // Edit form validation
            const editStudentForm = document.getElementById('editStudentForm');
            if (editStudentForm) {
                editStudentForm.addEventListener('submit', function(e) {
                    if (!validateEditStudentForm()) {
                        e.preventDefault();
                    }
                });
                
                // Real-time validation for edit form
                document.getElementById('editName').addEventListener('input', function() {
                    const name = this.value.trim();
                    const errorElement = document.getElementById('editNameError');
                    
                    if (!name) {
                        errorElement.textContent = 'Name is required';
                        this.classList.add('is-invalid');
                    } else if (!validateName(name)) {
                        errorElement.textContent = 'Name should contain only alphabets and spaces';
                        this.classList.add('is-invalid');
                    } else {
                        errorElement.textContent = '';
                        this.classList.remove('is-invalid');
                    }
                });

                document.getElementById('editAdmno').addEventListener('input', function() {
                    const admno = this.value.trim();
                    const id = document.getElementById('editId').value;
                    const errorElement = document.getElementById('editAdmnoError');
                    
                    if (!admno) {
                        errorElement.textContent = 'Admission number is required';
                        this.classList.add('is-invalid');
                    } else if (!isAdmnoUnique(admno, id)) {
                        errorElement.textContent = 'Admission number already exists';
                        this.classList.add('is-invalid');
                    } else {
                        errorElement.textContent = '';
                        this.classList.remove('is-invalid');
                    }
                });

                document.getElementById('editDob').addEventListener('change', function() {
                    const dob = this.value;
                    const name = document.getElementById('editName').value.trim();
                    const id = document.getElementById('editId').value;
                    const errorElement = document.getElementById('editDobError');
                    
                    if (!dob) {
                        errorElement.textContent = 'Date of birth is required';
                        this.classList.add('is-invalid');
                    } else if (!validateDOB(dob)) {
                        errorElement.textContent = 'Student must be at least 17 years old';
                        this.classList.add('is-invalid');
                    } else if (name && !isStudentUnique(name, dob, id)) {
                        errorElement.textContent = 'Student with this name and date of birth already exists';
                        this.classList.add('is-invalid');
                    } else {
                        errorElement.textContent = '';
                        this.classList.remove('is-invalid');
                    }
                });

                document.getElementById('editYear').addEventListener('input', function() {
                    const year = this.value;
                    const errorElement = document.getElementById('editYearError');
                    
                    if (!year) {
                        errorElement.textContent = 'Year is required';
                        this.classList.add('is-invalid');
                    } else if (!validateYear(year)) {
                        errorElement.textContent = 'Year must be between 2000 and 2030';
                        this.classList.add('is-invalid');
                    } else {
                        errorElement.textContent = '';
                        this.classList.remove('is-invalid');
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