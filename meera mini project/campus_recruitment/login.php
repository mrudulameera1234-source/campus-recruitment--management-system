<?php
session_start();
include("connection.php");
$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $pwd = trim($_POST["password"]);
    $sql = "SELECT * FROM tbl_login WHERE email='$email' AND password='$pwd'";
   
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
         $_SESSION['login_id']=$row['login_id'];
        switch ($row['role']) {
            case "admin":
                header("Location: admindashboard.php");
                break;
            case "student":
                header("Location: studentdashboard.php");
                break;
            case "company":
                header("Location: companydashboard.php");
                break;
            default:
                $error = "Unknown role.";
        }
        exit;
    } else {
        $error = "Invalid email or password.";
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
                            <h2>Login into your account</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Hero Area End -->
    
    <!-- Login Form Start -->
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
                                    <h4>Login</h4>
                                    <p>Join our community to find your dream job</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Form -->
                        <div class="apply-form2">
                            <form action="" method="post" enctype="multipart/form-data" id="registrationForm">
                                <div class="row">
                                    
                                    
                                    <!-- Email -->
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="email">Email *</label>
                                            <input type="email" name="email" id="email" class="form-control" 
                                                   placeholder="Enter your email address" required
                                                   oninput="validateEmail(this)">
                                            <div id="emailError" class="error">
                                                
                                            </div>
                                        </div>
                                    </div>
                                    
                                    
                                    <!-- Password -->
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="password">Password *</label>
                                            <input type="password" id="password" name="password" class="form-control" 
                                                   placeholder="Enter your password (min 6 characters)" required>
                                                  
                                
                                        </div>
                                    </div>
                                    
                                   
                                    <!-- Submit Button -->
                                    <div class="col-lg-12">
                                        <div class="submit-section">
                                            <button type="submit" class="btn head-btn1 btn-block">Login</button>
                                        </div>
                                    </div>
                                    
                                    <!-- Login Link -->
                                    <div class="col-lg-12 mt-4 text-center">
                                        <p>Don't have an account? <a href="student_reg.php" class="text-primary">Register here</a></p>
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
    
   
     <!-- Add SweetAlert JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- <script src="mainstyle/js/user_validation.js"></script> -->
    
    <script>
function validateEmail(field) {
    const error = document.getElementById("emailError");
    const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const email = field.value.trim();
    
    if (email === "") {
        showError(field, error, "Email is required.");
        return false;
    }
    
    if (!pattern.test(email)) {
        showError(field, error, "Please enter a valid email address.");
        return false;
    }
    
    clearError(field, error);
    return true;
}

function showError(field, errorElement, message) {
    errorElement.textContent = message;
    field.classList.add("error-border");
}

function clearError(field, errorElement) {
    errorElement.textContent = "";
    field.classList.remove("error-border");
}

// Form submission validation
document.querySelector("form").addEventListener("submit", function(e) {
    const emailValid = validateEmail(document.getElementById("email"));
    
    if (!emailValid) {
        e.preventDefault(); // Prevent form submission if validation fails
    }
});
</script>
</body>
</html>

