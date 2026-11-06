<?php
// include your database connection
session_start();
include("connection.php");
$sql="select*from tbl_course";
$result=mysqli_query($conn,$sql);
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $admno=$_POST['admno'];
    $dob=$_POST['dob'];
    $year = $_POST['year'];
    $course=$_POST['course'];
    $arrear=$_POST['arrear'];
    $cgpa=$_POST['cgpa'];
    $phone = $_POST['phone'];
    $resume= $_FILES['resume']['name'];
    $photo= $_FILES['photo']['name'];
    $email= $_POST['email'];
    $pwd= $_POST['password'];
    // Server-side validation
    $errors = [];

    //Handling file uploads
    $photo= $_FILES['photo']['name'];
    $photo_tmp=$_FILES['photo']['tmp_name'];
    $upload_dir="uploads/";
    move_uploaded_file($photo_tmp, $upload_dir . $photo);
    
    $resume= $_FILES['resume']['name'];
    $resume_tmp=$_FILES['resume']['tmp_name'];
    $upload_dir="uploads/";
    move_uploaded_file($resume_tmp, $upload_dir . $resume);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Simple query to check if student exists in both tables
$query = "SELECT * from  
           tbl_student_credentials
          WHERE admno = '$admno' AND name='$name'and dob='$dob'";

$result = $conn->query($query);

if ($result->num_rows > 0) {
    $rowcredential=mysqli_fetch_assoc($result);
    // Student exists in both tables - ACCEPT registration
    echo "Registration accepted - Valid student";
        $credential_id=$rowcredential['credential_id'];
    // Proceed with registration process
} else {
    // Student doesn't exist in both tables - REJECT registration
  echo '
            <!DOCTYPE html>
            <html>
            <head>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            </head>
            <body>
                <script>
                    window.onload = function() {
                        Swal.fire({
                            icon: "error",
                            title: "Failed!",
                            text: "Registration failed",
                            confirmButtonText: "Please try later"
                        }).then(() => {
                            window.location.href = "login.php";
                        });
                    };
                </script>
            </body>
            </html>';

            exit();
    // Handle rejection
}

    
if (empty($errors)) 
{
        // 1. Insert into tbl_login
        $role = "student";
        $login_sql = "INSERT INTO tbl_login (email, password, role) VALUES ('$email', '$pwd', '$role')";
        if (mysqli_query($conn, $login_sql)) {
            $login_id = mysqli_insert_id($conn);
            // 2. Insert into tbl_student
            $insert_student = "INSERT INTO tbl_student (credential_id,name, admno, dob, course_id, year, arrears, cgpa, phonenumber, login_id ,resume, photo)
                           VALUES ('$credential_id','$name', '$admno', '$dob', '$course', '$year','$arrear', '$cgpa','$phone', '$login_id','$resume', '$photo')";
                    if (mysqli_query($conn, $insert_student)) {
                      echo '
            <!DOCTYPE html>
            <html>
            <head>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            </head>
            <body>
                <script>
                    window.onload = function() {
                        Swal.fire({
                            icon: "success",
                            title: "Registered!",
                            text: "Registration successful",
                            confirmButtonText: "Wait for approval"
                        }).then(() => {
                            window.location.href = "login.php";
                        });
                    };
                </script>
            </body>
            </html>';

            exit();
                    }
        } else {
            echo '
            <!DOCTYPE html>
            <html>
            <head>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            </head>
            <body>
                <script>
                    window.onload = function() {
                        Swal.fire({
                            icon: "error",
                            title: "Failed!",
                            text: "Registration failed",
                            confirmButtonText: "Please try later"
                        }).then(() => {
                            window.location.href = "login.php";
                        });
                    };
                </script>
            </body>
            </html>';

            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Board - Registration</title>
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .error {
            color: #dc3545;
            font-size: 14px;
            margin-top: 5px;
        }
        .form-control.error-border {
            border-color: #dc3545;
        }
    </style>
</head>
<body>
    <?php include('headers/index_header.php'); ?>
    
    <!-- Hero Area Start-->
    <div class="slider-area ">
        <div class="single-slider section-overly slider-height2 d-flex align-items-center" style="background-image: url(../mainstyle/img/hero/about.jpg);">
            <div class="container">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="hero-cap text-center">
                            <h2>Create Your Account</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Hero Area End -->
    
    <!-- Registration Form Start -->
    <div class="job-listing-area pt-120 pb-120">
        <div class="container">
            <div class="row">
                <!-- Left content -->
                <div class="col-xl-3 col-lg-3 col-md-4"></div>
                
                <!-- Right content -->
                <div class="col-xl-6 col-lg-6 col-md-8">
                    <div class="featured-jobs-wrapper">
                        <div class="single-job-items mb-30">
                            <div class="job-items">
                                <div class="job-tittle">
                                    <h4>Register Now</h4>
                                    <p>Join our community to find your dream job</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Form -->
                        <div class="apply-form2">
                            <form action="" method="post" enctype="multipart/form-data" id="registrationForm">
                                <div class="row">
                                    <!-- Name -->
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="name">Full Name *</label>
                                            <input type="text" name="name" id="name" class="form-control" 
                                                   placeholder="Enter your full name" required
                                                   onblur="validateName(this)">
                                            <div id="nameError" class="error">
                                                <?php echo isset($errors['name']) ? $errors['name'] : ''; ?>
                                            </div>
                                        </div>
                                    </div>
                                     <!-- Admission number -->
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="admno">Admission number*</label>
                                            <input type="text" name="admno" id="admno" class="form-control" 
                                                   placeholder="Enter your admission number" required
                                                   onblur="validateAdmno(this)">
                                            <div id="admnoError" class="error">
                                                <?php echo isset($errors['admno']) ? $errors['admno'] : ''; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Date of birth -->
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="dob">Date of birth*</label>
                                            <input type="text" name="dob" id="dob" class="form-control" 
                                                   placeholder="YYYY-MM-DD" required
                                                   onblur="validateDob(this)">
                                            <div id="dobError" class="error">
                                                <?php echo isset($errors['dob']) ? $errors['dob'] : ''; ?>
                                            </div>
                                        </div>
                                    </div>

                                <!-- Course -->
                                  <div class="form-group">
                                        <label for="course">Current Pursuing Course *</label>
                                        <select name="course" id="course" class="form-control" required onblur="validateCourse(this)">
                                                <option value="">-- Select Course --</option>
                                                <?php
                                                while($row=mysqli_fetch_assoc($result))
                                                {
                                                    ?>

                                                <option value="<?php echo $row['course_id'];?>"><?php echo $row['course'];?></option>
                                               <?php
                                                }
                                                ?>
                                        </select>
                                        <div id="courseError" class="error">
                                            <?php echo isset($errors['course']) ? $errors['course'] : ''; ?>
                                        </div>
                                    </div>

                                     <!-- Year -->
                                         <div class="form-group">
                                             <label for="year">Year of join*</label>
                                            <input type="text" name="year" id="year" class="form-control" 
                                                   placeholder="Enter your year of join" required
                                                   onblur="validateYear(this)">
                                            <div id="yearError" class="error">
                                                <?php echo isset($errors['year']) ? $errors['year'] : ''; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Arrears -->
                                         <div class="form-group">
                                             <label for="arrear">Enter the number of arrears*</label>
                                            <input type="arrear" name="arrear" id="arrear" class="form-control" 
                                                   placeholder="Enter the number of arrears you have" required
                                                   onblur="validateArrear(this)">
                                            <div id="arrearError" class="error">
                                                <?php echo isset($errors['arrear']) ? $errors['arrear'] : ''; ?>
                                            </div>
                                        </div>
                                    </div>

                                     <!-- cgpa -->
                                         <div class="form-group">
                                             <label for="cgpa">CGPA*</label>
                                            <input type="text" name="cgpa" id="cgpa" class="form-control" 
                                                   placeholder="Enter your cgpa" required
                                                   onblur="validateCgpa(this)">
                                            <div id="cgpaError" class="error">
                                                <?php echo isset($errors['cgpa']) ? $errors['cgpa'] : ''; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Phone -->
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="phone">Contact Number *</label>
                                            <input type="text" name="phone" id="phone" class="form-control" 
                                                   placeholder="Enter your phone number" required
                                                   onblur="validatePhone(this)">
                                            <div id="phoneError" class="error">
                                                <?php echo isset($errors['phone']) ? $errors['phone'] : ''; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Email -->
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="email">Email *</label>
                                            <input type="email" name="email" id="email" class="form-control" 
                                                   placeholder="Enter your email address" required
                                                   onblur="validateEmail(this)">
                                            <div id="emailError" class="error">
                                                <?php echo isset($errors['email']) ? $errors['email'] : ''; ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                  
                                    
                                    <!-- Password -->
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="password">Password *</label>
                                            <input type="password" id="password" name="password" class="form-control" 
                                                   placeholder="Enter your password (min 6 characters)" required
                                                   onblur="validatePassword(this)">
                                            <div id="passwordError" class="error">
                                                <?php echo isset($errors['password']) ? $errors['password'] : ''; ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Confirm Password -->
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="confirm_password">Confirm Password *</label>
                                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" 
                                                   placeholder="Confirm your password" required
                                                   onblur="validateConfirmPassword(this)">
                                            <div id="confirmPasswordError" class="error">
                                                <?php echo isset($errors['confirm_password']) ? $errors['confirm_password'] : ''; ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                     <!-- Resume -->
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="resume">Resume *</label>
                                            <input type="file" name="resume" id="resume" class="form-control" 
                                                   placeholder="Upload your resume" required
                                                   onblur="validateResume(this)">
                                            <div id="resumeError" class="error">
                                                <?php echo isset($errors['resume']) ? $errors['resume'] : ''; ?>
                                            </div>
                                        </div>
                                    </div>

                                      <!-- Photo -->
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="photo">Photo *</label>
                                            <input type="file" name="photo" id="photo" class="form-control" 
                                                   placeholder="Upload your photo" required
                                                   onblur="validatePhoto(this)">
                                            <div id="photoError" class="error">
                                                <?php echo isset($errors['photo']) ? $errors['photo'] : ''; ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Submit Button -->
                                    <div class="col-lg-12">
                                        <div class="submit-section">
                                            <button type="submit" class="btn head-btn1 btn-block">Register Now</button>
                                        </div>
                                    </div>
                                    
                                    <!-- Login Link -->
                                    <div class="col-lg-12 mt-4 text-center">
                                        <p>Already have an account? <a href="login.php" class="text-primary">Login here</a></p>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-lg-3 col-md-4"></div>
            </div>
        </div>
    </div>
    <!-- Registration Form End -->
    
    <!-- Add your footer here -->
    
    <script src="mainstyle/js/user_validation.js"></script>
     <!-- Add SweetAlert JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="mainstyle/js/user_validation.js"></script>
    
    <?php if (isset($_SESSION['registration_success']) && $_SESSION['registration_success']): ?>
    <script>
        Swal.fire({
            title: 'Success!',
            text: 'You have successfully registered.',
            icon: 'success',
            confirmButtonText: 'OK',
            timer: 3000,
            timerProgressBar: true,
            willClose: () => {
                window.location.href = 'login.php';
            }
        });
        <?php unset($_SESSION['registration_success']); ?>
    </script>
    <?php endif; ?>
</body>
</html>