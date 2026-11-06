
        // Validation functions
        function validateName(field) {
            const error = document.getElementById("nameError");
            const namePattern = /^[A-Za-z\s]+$/;
            
            if (field.value.trim() === "") {
                showError(field, error, "Name is required.");
            } else if (!namePattern.test(field.value.trim())) {
                showError(field, error, "Name can contain only letters and spaces.");
            } else {
                clearError(field, error);
            }
        }
        
        function validateEmail(field) {
            const error = document.getElementById("emailError");
            const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const email = field.value.trim();
            
            if (email === "") {
                showError(field, error, "Email is required.");
                return;
            }
            
            if (!pattern.test(email)) {
                showError(field, error, "Enter a valid email.");
                return;
            }
            
            // Check email availability via AJAX
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "check_email.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onload = function() {
                if (xhr.status === 200) {
                    if (xhr.responseText.trim() === "exists") {
                        showError(field, error, "Email already exists.");
                    } else {
                        clearError(field, error);
                    }
                }
            };
            xhr.send("email=" + encodeURIComponent(email));
        }
       
 
function validateAdmno($conn, $admission_no) {
    // Clean input
    $admission_no = trim($admno);
    
    // Check if empty
    if (empty($admission_no)) {
        showError(field, error, "Admission number is required.");
                return;
    }
    
    // Check length (example: 8-12 chars)
    if (strlen($admission_no) < 8 || strlen($admission_no) > 12) {
        return "Must be 8-12 characters";
    }
     // 3. Check for special symbols (only allow letters, numbers, and hyphens)
    if (!preg_match('/^[A-Za-z0-9\-]+$/', $admission_no)) {
        return "Cannot contain special characters (except hyphens)";
    }
    // Check if exists in database (procedural query)
    $query = "SELECT id FROM students WHERE admission_no = '" . mysqli_real_escape_string($conn, $admission_no) ;
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        mysqli_free_result($result);
        return "Admission number already exists";
    }
    
    return ""; // No error
}


        function validatePhone(field) {
            const phone = field.value.trim();
            const error = document.getElementById("phoneError");
            const phonePattern = /^\d{10}$/;
            
            if (phone === "") {
                showError(field, error, "Phone number is required.");
            } else if (!phonePattern.test(phone)) {
                showError(field, error, "Please enter a valid 10-digit phone number.");
            } else {
                clearError(field, error);
            }
        }
        
        function validatePassword(field) {
            const error = document.getElementById("passwordError");
            
            if (field.value === "") {
                showError(field, error, "Password is required.");
            } else if (field.value.length < 6) {
                showError(field, error, "Password must be at least 6 characters.");
            } else {
                clearError(field, error);
            }
        }
        
        function validateConfirmPassword(field) {
            const error = document.getElementById("confirmPasswordError");
            const pwd = document.getElementById("password").value;
            
            if (field.value === "") {
                showError(field, error, "Please confirm your password.");
            } else if (pwd !== field.value) {
                showError(field, error, "Passwords do not match.");
            } else {
                clearError(field, error);
            }
        }

function validatePhoto($file) {
    // 1. Check if file was uploaded
    if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return "Please select a photo";
    }

    // 2. Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return "Upload error code: " . $file['error'];
    }

    // 3. Validate file size (2MB max)
    $max_size = 2 * 1024 * 1024;
    if ($file['size'] > $max_size) {
        return "Photo must be smaller than 2MB";
    }

    // 4. Validate image type
    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime_type, $allowed_types)) {
        return "Only JPG, JPEG, or PNG images allowed";
    }

    // 5. Verify actual image content
    $image_info = getimagesize($file['tmp_name']);
    if ($image_info === false) {
        return "Invalid image file";
    }

    return ""; // No errors
}

function validateArrears($arrears) {
    // Check if NULL or empty string
    if ($arrears === NULL || $arrears === '') {
        return "Number of arrears cannot be empty";
    }
    
    // Check if numeric
    if (!is_numeric($arrears)) {
        return "Arrears must be a number";
    }
    
    // Check if non-negative integer
    if ($arrears < 0 || $arrears != round($arrears)) {
        return "Must be a whole number (0 or higher)";
    }
    
    return ""; // No error
}


function validateCGPA($cgpa) {
    // Check if empty
    if (empty($cgpa)) {
        return "CGPA is required";
    }

    // Check if numeric
    if (!is_numeric($cgpa)) {
        return "CGPA must be a number";
    }

    // Check range (typically 0.0 to 4.0 or 10.0)
    $cgpa = floatval($cgpa);
    if ($cgpa < 0 || $cgpa > 4.0) {
        return "CGPA must be between 0.0 and 4.0";
    }

    // Check decimal places (optional)
    if (strlen(substr(strrchr($cgpa, "."), 1) > 2)) {
        return "Maximum 2 decimal places allowed";
    }

    return ""; // No error
}


function validateDob($dob) {
    // Check if empty
    if (empty($dob)) {
        return "Date of birth is required";
    }

    // Validate format (YYYY-MM-DD)
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dob)) {
        return "Use YYYY-MM-DD format";
    }

    // Check if valid date
    $dateParts = explode('-', $dob);
    if (!checkdate($dateParts[1], $dateParts[2], $dateParts[0])) {
        return "Invalid date";
    }

    // Check if not in future
    if (strtotime($dob) > time()) {
        return "Date cannot be in future";
    }

    return ""; // No error
}


function validateYear($conn, $student_id, $current_year) {
    // 1. Get student's joining year and course duration
    $query = "SELECT s.year, c.duration FROM students s JOIN courses c ON s.course_id = c.course_id WHERE s.student_id = " . mysqli_real_escape_string($conn, $student_id) ;
    
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) == 0) {
        return "Student not found";
    }
    
    $row = mysqli_fetch_assoc($result);
    $year = $row['year'];
    $duration = $row['duration'];
    
    // 2. Calculate expected graduation year
    $graduation_year = $year + $duration;
    
    // 3. Compare with current year
    if ($graduation_year != $current_year) {
        return "Only final year students can register (Expected graduation: $graduation_year)";
    }
    
    return ""; // Validation passed
}

function validateCourse($data) {
    $errors = [];
    
    // Validate main course selection
    if (empty($data['course'])) {
        $errors['course'] = 'Please select your current course';
    } 
    return $errors;
}

function validateResume($file) {
    // 1. Check if file was uploaded
    if (!isset($file['error'])) {
        return "No file uploaded";
    }

    // 2. Check for upload errors
    if ($file['error'] != UPLOAD_ERR_OK) {
        return "File upload error";
    }

    // 3. Validate file size (5MB max)
    if ($file['size'] > 5242880) { // 5MB in bytes
        return "File too large (max 5MB)";
    }

    // 4. Validate file type
    $allowed_types = array('application/pdf', 
                         'application/msword',
                         'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    $file_info = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($file_info, $file['tmp_name']);
    finfo_close($file_info);

    if (!in_array($mime_type, $allowed_types)) {
        return "Invalid file type (only PDF/DOC/DOCX allowed)";
    }

    // 5. Validate file extension
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_exts = array('pdf', 'doc', 'docx');
    if (!in_array($ext, $allowed_exts)) {
        return "Invalid file extension";
    }

    return NULL; // No errors
}

        // Helper functions
        function showError(field, errorElement, message) {
            errorElement.textContent = message;
            field.classList.add("error-border");
        }
        
        function clearError(field, errorElement) {
            errorElement.textContent = "";
            field.classList.remove("error-border");
        }
      

        // Form validation
        function validateForm() {
            // Validate all fields
            validateName(document.getElementById("name"));
            validateAdmno(document.getElementById("admno"));
            validateDob(document.getElementById("dob"));
            validateEmail(document.getElementById("email"));
            validateYear(document.getElementById("year"));
            validateCourse(document.getElementById("course"));
            validateCgpa(document.getElementById("cgpa"));
            validateArrear(document.getElementById("arrear"));
            validateResume(document.getElementById("resume"));
            validatePhoto(document.getElementById("photo"));
            validatePhone(document.getElementById("phone"));
            validatePassword(document.getElementById("password"));
            validateConfirmPassword(document.getElementById("confirm_password"));
            
            // Check if any errors exist
            const errors = document.querySelectorAll(".error");
            for (let error of errors) {
                if (error.textContent !== "") {
                    return false;
                }
            }
            return true;
        }
        
        // Attach form validation
        document.getElementById("registrationForm").onsubmit = function() {
            return validateForm();
        };
