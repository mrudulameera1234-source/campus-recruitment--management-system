<?php
ob_start();
// Database connection and processing

include("connection.php");
include("headers/company_header.php");

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $comment=$_POST['comment'];
        
        $insert_student = "INSERT INTO tbl_feedback(name,email,comment) 
                               VALUES ('$name', '$email', '$comment')";
            
            if (mysqli_query($conn, $insert_student)) {
                $_SESSION['success'] = "Feedback given successfully";
                header("Location: company_feedback.php");
                exit();
            } else {
                $errors[] = "Error: ".mysqli_error($conn);
            }
        }

        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header("Location:company_feedback.php");
            exit();
        }
    
   ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback - Our Company</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            text-align: center;
            padding: 30px 0;
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        
        .feedback-form {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        input, textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border 0.3s;
        }
        
        input:focus, textarea:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }
        
        textarea {
            min-height: 150px;
            resize: vertical;
        }
        
        .submit-btn {
            background: #3498db;
            color: white;
            border: none;
            padding: 14px 20px;
            font-size: 18px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            transition: background 0.3s;
        }
        
        .submit-btn:hover {
            background: #2980b9;
        }
        
        .error {
            color: #e74c3c;
            font-size: 14px;
            margin-top: 5px;
            display: none;
        }
        
        .thank-you {
            display: none;
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        .thank-you h2 {
            color: #27ae60;
            margin-bottom: 15px;
        }
        
        .thank-you p {
            margin-bottom: 20px;
            font-size: 18px;
        }
        
        .back-btn {
            display: inline-block;
            background: #3498db;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            transition: background 0.3s;
        }
        
        .back-btn:hover {
            background: #2980b9;
        }
        
        footer {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            color: #7f8c8d;
            font-size: 14px;
        }
        
        @media (max-width: 600px) {
            .container {
                padding: 10px;
            }
            
            h1 {
                font-size: 2rem;
            }
            
            .feedback-form {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Share Your Feedback</h1>
            <p class="subtitle">We value your opinion and strive to improve based on your suggestions</p>
    </header>
          <form id="feedbackForm" class="feedback-form" action="" method="POST">
            <div class="form-group">
                <label for="name">Your Name *</label>
                <input type="text" id="name" name="name" required>
                <div class="error" id="nameError">Please enter your name</div>
            </div>

            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" required>
                <div class="error" id="emailError">Please enter a valid email address</div>
            </div>
            
            <div class="form-group">
                <label for="comment">Your Feedback *</label>
                <textarea id="comment" name="comment" placeholder="Please share your thoughts, suggestions, or concerns about our company..." required></textarea>
                <div class="error" id="commentError">Please enter your feedback</div>
            </div>
            
            <button type="submit" class="submit-btn">Submit Feedback</button>
        </form>
        
        <div id="thankYouMessage" class="thank-you">
            <h2>Thank You for Your Feedback!</h2>
            <p>We appreciate you taking the time to share your thoughts with us. Your feedback is valuable in helping us improve.</p>
            <a href="#" class="back-btn" id="newFeedback">Submit Another Feedback</a>
        </div>
        
        <footer>
            <p>&copy; 2023 Our Company. All rights reserved.</p>
        </footer>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="mainstyle/js/company_feedback_validation.js"></script>

</body>
</html>