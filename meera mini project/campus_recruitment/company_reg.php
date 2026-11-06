<?php
// include your database connection
session_start();
include("connection.php");
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $regno= $_POST['regno'];
    $description = $_POST['description'];
    $phone = $_POST['phone'];
    $link = $_POST['link'];
    $location = $_POST['location'];
    $email= $_POST['email'];
    $pwd= $_POST['password'];

    // Server-side validation
    $errors = [];

    if (empty($errors)) 
{
        // 1. Insert into tbl_login
        $role = "company";
        $login_sql = "INSERT INTO tbl_login (email, password, role) VALUES ('$email', '$pwd', '$role')";
        if (mysqli_query($conn, $login_sql)) {
            $login_id = mysqli_insert_id($conn);
            // 2. Insert into tbl_company
            $insert_company = "INSERT INTO tbl_company (login_id, name, regno, description, phNO ,link ,location)
                           VALUES ('$login_id', '$name',' $regno', '$description', '$phone','$link','$location')";
                    if (mysqli_query($conn, $insert_company)) {
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
            echo "<script>alert('Registration failed. Please try again.');</script>";
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
                                    <p>Connect with candidates</p>
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
                                            <label for="name">Enter  company name *</label>
                                            <input type="text" name="name" id="name" class="form-control" 
                                                   placeholder="Enter your full name" required
                                                   onblur="validateName(this)">
                                            <div id="nameError" class="error">
                                                <?php echo isset($errors['name']) ? $errors['name'] : ''; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Registration number -->
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="regno">Registration number *</label>
                                            <input type="text" name="regno" id="regno" class="form-control" 
                                                   placeholder="Enter company registration number" required
                                                   onblur="validateRegno(this)">
                                            <div id="regnoError" class="error">
                                                <?php echo isset($errors['regno']) ? $errors['regno'] : ''; ?>
                                            </div>
                                        </div>
                                    </div>
                                
                                    <!-- Description -->
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="description">Description *</label>
                                            <input type="text" name="description" id="description" class="form-control" 
                                                   placeholder="Enter company desription" required
                                                   onblur="validateDescription(this)">
                                            <div id="descriptionError" class="error">
                                                <?php echo isset($errors['description']) ? $errors['description'] : ''; ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                     <!-- Site link -->
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="link">Website Address *</label>
                                            <input type="text" name="link" id="link" class="form-control" 
                                                   placeholder="Enter company link" required
                                                   onblur="validateLink(this)">
                                            <div id="linkError" class="error">
                                                <?php echo isset($errors['link']) ? $errors['link'] : ''; ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Location -->
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="location">Location *</label>
                                            <input type="text" name="location" id="location" class="form-control" 
                                                   placeholder="Enter company location" required
                                                   onblur="validateLocation(this)">
                                            <div id="locationError" class="error">
                                                <?php echo isset($errors['location']) ? $errors['location'] : ''; ?>
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
    <script src="mainstyle/js/company_validation.js"></script>
    
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