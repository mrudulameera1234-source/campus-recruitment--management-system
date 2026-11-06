<?php
include("connection.php");
include("headers/company_header.php");
// Check if company is logged in
if (!isset($_SESSION['company_id'])) {
    header("Location: company_login.php");
    exit();
}

$company_id = $_SESSION['company_id'];

// Get company details
$company_query = "SELECT * FROM tbl_company WHERE company_id = '$company_id'";
$company_result = mysqli_query($conn, $company_query);
$company_data = mysqli_fetch_assoc($company_result);

// Get vacancies posted by this company
$vacancies_query = "
    SELECT v.*, COUNT(a.application_id) as application_count
    FROM tbl_vacancies v
    JOIN tbl_drive d ON v.drive_id = d.drive_id
    LEFT JOIN tbl_applications a ON v.vacancy_id = a.vacancy_id
    WHERE d.company_id = '$company_id'
    GROUP BY v.vacancy_id
    ORDER BY v.posted_on DESC
";
$vacancies_result = mysqli_query($conn, $vacancies_query);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create_test'])) {
        // Create new aptitude test
        $vacancy_id = mysqli_real_escape_string($conn, $_POST['vacancy_id']);
        $time = mysqli_real_escape_string($conn, $_POST['time']);
        
        // Check if test already exists for this vacancy
        $check_query = "SELECT * FROM tbl_aptitude WHERE vacancy_id = '$vacancy_id'";
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            $error_message = "An aptitude test already exists for this vacancy.";
        } else {
            $insert_query = "INSERT INTO tbl_aptitude (vacancy_id, time) VALUES ('$vacancy_id', '$time')";
            if (mysqli_query($conn, $insert_query)) {
                $aptitude_id = mysqli_insert_id($conn);
                $success_message = "Aptitude test created successfully! Test ID: " . $aptitude_id;
            } else {
                $error_message = "Error creating test: " . mysqli_error($conn);
            }
        }
    }
    
    if (isset($_POST['add_question'])) {
        // Add question to test
        $aptitude_id = mysqli_real_escape_string($conn, $_POST['aptitude_id']);
        $question = mysqli_real_escape_string($conn, $_POST['question']);
        $option1 = mysqli_real_escape_string($conn, $_POST['option1']);
        $option2 = mysqli_real_escape_string($conn, $_POST['option2']);
        $option3 = mysqli_real_escape_string($conn, $_POST['option3']);
        $option4 = mysqli_real_escape_string($conn, $_POST['option4']);
        $correct = mysqli_real_escape_string($conn, $_POST['correct']);
        
        $insert_query = "INSERT INTO tbl_questions (aptitude_id, question, option1, option2, option3, option4, correct) 
                         VALUES ('$aptitude_id', '$question', '$option1', '$option2', '$option3', '$option4', '$correct')";
        if (mysqli_query($conn, $insert_query)) {
            $success_message = "Question added successfully!";
        } else {
            $error_message = "Error adding question: " . mysqli_error($conn);
        }
    }
    
    if (isset($_POST['delete_question'])) {
        // Delete question
        $question_id = mysqli_real_escape_string($conn, $_POST['question_id']);
        $delete_query = "DELETE FROM tbl_questions WHERE question_id = '$question_id'";
        if (mysqli_query($conn, $delete_query)) {
            $success_message = "Question deleted successfully!";
        } else {
            $error_message = "Error deleting question: " . mysqli_error($conn);
        }
    }
}

// Get existing aptitude tests with question counts
$aptitude_tests_query = "
    SELECT a.*, v.post, v.vacancy_id, COUNT(q.question_id) as question_count
    FROM tbl_aptitude a
    JOIN tbl_vacancies v ON a.vacancy_id = v.vacancy_id
    JOIN tbl_drive d ON v.drive_id = d.drive_id
    LEFT JOIN tbl_questions q ON a.aptitude_id = q.aptitude_id
    WHERE d.company_id = '$company_id'
    GROUP BY a.aptitude_id
    ORDER BY a.aptitude_id DESC
";
$aptitude_tests_result = mysqli_query($conn, $aptitude_tests_query);

// Get applicants for selected vacancy
$selected_vacancy_id = isset($_GET['vacancy_id']) ? $_GET['vacancy_id'] : '';
$applicants_result = null;

if ($selected_vacancy_id) {
    $applicants_query = "
        SELECT a.*, s.name, s.email, s.course, s.cgpa
        FROM tbl_applications a
        JOIN tbl_students s ON a.student_id = s.student_id
        WHERE a.vacancy_id = '$selected_vacancy_id'
        ORDER BY a.applied_on DESC
    ";
    $applicants_result = mysqli_query($conn, $applicants_query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Dashboard - Aptitude Tests</title>
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
            margin:0;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background: linear-gradient(135deg, #2c3e50 0%, #0d5484ff 100%);
            color: white;
            padding: 20px 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            margin-top: 0;
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
        
        .company-info {
            text-align: right;
        }
        
        .dashboard {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 30px;
        }
      
        .main-content {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }
        
        .card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .card-title {
            font-size: 22px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .btn {
            padding: 8px 15px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s ease;
            margin: 2px;
            font-size: 14px;
        }
        
        .btn:hover {
            background: #2980b9;
        }
        
        .btn-success {
            background: #27ae60;
        }
        
        .btn-success:hover {
            background: #219a52;
        }
        
        .btn-danger {
            background: #e74c3c;
        }
        
        .btn-danger:hover {
            background: #c0392b;
        }
        
        .btn-info {
            background: #17a2b8;
        }
        
        .btn-info:hover {
            background: #138496;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        .table th, .table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .table tr:hover {
            background: #f8f9fa;
        }
        
        .badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-success {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }
        
        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #444;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background: white;
            border-radius: 10px;
            padding: 30px;
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .modal-title {
            font-size: 22px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .close-modal {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #777;
        }
        
        .applicants-list {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #eee;
            border-radius: 5px;
            padding: 10px;
        }
        
        .applicant-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .applicant-item:last-child {
            border-bottom: none;
        }
        
        .question-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
            position: relative;
        }
        
        .question-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
        }
        
        .question-text {
            font-weight: 500;
            margin-bottom: 10px;
            flex: 1;
        }
        
        .options-list {
            list-style: none;
            margin-left: 20px;
        }
        
        .options-list li {
            margin-bottom: 5px;
            padding: 5px;
        }
        
        .correct-option {
            background: #d4edda;
            border-left: 3px solid #28a745;
            padding-left: 10px;
        }
        
        .test-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .vacancy-selector {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .vacancy-selector select {
            flex: 1;
        }
        
        @media (max-width: 768px) {
            .dashboard {
                grid-template-columns: 1fr;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .header-content {
                flex-direction: column;
                text-align: center;
            }
            
            .company-info {
                text-align: center;
                margin-top: 15px;
            }
            
            .test-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
  <div class="main-content">

    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <i class="fas fa-brain"></i>
                    <span>Aptitude Test Manager</span>
                </div>
                <div class="company-info">
                    <h3><?php echo $company_data['name']; ?></h3>
                    <p>Welcome to your dashboard</p>
                </div>
            </div>
        </div>
    </header>
                <!-- Create New Test Section -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Create New Aptitude Test</h3>
                    </div>
                    <form method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="vacancy_id">Select Vacancy</label>
                                <select id="vacancy_id" name="vacancy_id" required>
                                    <option value="">Choose a vacancy</option>
                                    <?php
                                    if(mysqli_num_rows($vacancies_result) > 0) {
                                        mysqli_data_seek($vacancies_result, 0);
                                        while($vacancy = mysqli_fetch_assoc($vacancies_result)) {
                                            $selected = isset($_POST['vacancy_id']) && $_POST['vacancy_id'] == $vacancy['vacancy_id'] ? 'selected' : '';
                                            echo '<option value="'.$vacancy['vacancy_id'].'" '.$selected.'>'
                                                .$vacancy['post'].' ('.$vacancy['application_count'].' applicants)'
                                                .'</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="time">Test Duration (minutes)</label>
                                <input type="number" id="time" name="time" min="5" max="180" value="<?php echo isset($_POST['time']) ? $_POST['time'] : '30'; ?>" required>
                            </div>
                        </div>
                        <button type="submit" name="create_test" class="btn btn-success">Create Test</button>
                    </form>
                </div>

                <!-- Existing Tests Section -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Existing Aptitude Tests</h3>
                    </div>
                    
                    <?php if(mysqli_num_rows($aptitude_tests_result) > 0): ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Test ID</th>
                                    <th>Job Post</th>
                                    <th>Vacancy ID</th>
                                    <th>Duration</th>
                                    <th>Questions</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($test = mysqli_fetch_assoc($aptitude_tests_result)): ?>
                                <tr>
                                    <td>#<?php echo $test['aptitude_id']; ?></td>
                                    <td><?php echo $test['post']; ?></td>
                                    <td>#<?php echo $test['vacancy_id']; ?></td>
                                    <td><?php echo $test['time']; ?> minutes</td>
                                    <td>
                                        <span class="badge <?php echo $test['question_count'] > 0 ? 'badge-success' : 'badge-warning'; ?>">
                                            <?php echo $test['question_count']; ?> questions
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-info" onclick="viewTest(<?php echo $test['aptitude_id']; ?>, '<?php echo $test['post']; ?>')">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                        <button class="btn btn-success" onclick="addQuestion(<?php echo $test['aptitude_id']; ?>, '<?php echo $test['post']; ?>')">
                                            <i class="fas fa-plus"></i> Add Question
                                        </button>
                                        <button class="btn" onclick="viewApplicants(<?php echo $test['vacancy_id']; ?>, '<?php echo $test['post']; ?>')">
                                            <i class="fas fa-users"></i> Applicants
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No aptitude tests created yet. Create your first test above.</p>
                    <?php endif; ?>
                </div>

                <!-- Applicants Section -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Applicants</h3>
                    </div>
                    
                    <div class="vacancy-selector">
                        <label for="filter_vacancy">Select Vacancy:</label>
                        <select id="filter_vacancy" onchange="window.location.href='aptitude_tests.php?vacancy_id=' + this.value">
                            <option value="">Select a vacancy</option>
                            <?php
                            mysqli_data_seek($vacancies_result, 0);
                            while($vacancy = mysqli_fetch_assoc($vacancies_result)) {
                                $selected = $selected_vacancy_id == $vacancy['vacancy_id'] ? 'selected' : '';
                                echo '<option value="'.$vacancy['vacancy_id'].'" '.$selected.'>'.$vacancy['post'].'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    
                    <?php if($selected_vacancy_id && $applicants_result): ?>
                        <?php if(mysqli_num_rows($applicants_result) > 0): ?>
                            <div class="applicants-list">
                                <?php while($applicant = mysqli_fetch_assoc($applicants_result)): ?>
                                <div class="applicant-item">
                                    <div>
                                        <strong><?php echo $applicant['name']; ?></strong><br>
                                        <small>Email: <?php echo $applicant['email']; ?></small><br>
                                        <small>Course: <?php echo $applicant['course']; ?> | CGPA: <?php echo $applicant['cgpa']; ?></small>
                                    </div>
                                    <div>
                                        <span class="badge badge-info">Applied: <?php echo date('M j, Y', strtotime($applicant['applied_on'])); ?></span>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <p>No applicants found for this vacancy.</p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p>Please select a vacancy to view applicants.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Question Modal -->
    <div class="modal" id="questionModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="questionModalTitle">Add Question to Test</h3>
                <button class="close-modal" onclick="closeModal('questionModal')">&times;</button>
            </div>
            <form method="POST" id="questionForm">
                <input type="hidden" id="modal_aptitude_id" name="aptitude_id">
                <div class="form-group">
                    <label for="question">Question</label>
                    <textarea id="question" name="question" placeholder="Enter your question here..." required></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="option1">Option 1</label>
                        <input type="text" id="option1" name="option1" placeholder="First option" required>
                    </div>
                    <div class="form-group">
                        <label for="option2">Option 2</label>
                        <input type="text" id="option2" name="option2" placeholder="Second option" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="option3">Option 3</label>
                        <input type="text" id="option3" name="option3" placeholder="Third option" required>
                    </div>
                    <div class="form-group">
                        <label for="option4">Option 4</label>
                        <input type="text" id="option4" name="option4" placeholder="Fourth option" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="correct">Correct Option</label>
                    <select id="correct" name="correct" required>
                        <option value="">Select correct option</option>
                        <option value="1">Option 1</option>
                        <option value="2">Option 2</option>
                        <option value="3">Option 3</option>
                        <option value="4">Option 4</option>
                    </select>
                </div>
                <div class="test-actions">
                    <button type="submit" name="add_question" class="btn btn-success">Add Question</button>
                    <button type="button" class="btn" onclick="closeModal('questionModal')">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Test Modal -->
    <div class="modal" id="viewTestModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="viewTestModalTitle">Test Details</h3>
                <button class="close-modal" onclick="closeModal('viewTestModal')">&times;</button>
            </div>
            <div id="testDetails">
                <!-- Test details will be loaded here by JavaScript -->
            </div>
        </div>
    </div>

    <!-- View Applicants Modal -->
    <div class="modal" id="viewApplicantsModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="viewApplicantsModalTitle">Applicants</h3>
                <button class="close-modal" onclick="closeModal('viewApplicantsModal')">&times;</button>
            </div>
            <div id="applicantsDetails">
                <!-- Applicants details will be loaded here by JavaScript -->
            </div>
        </div>
    </div>

    <script>
        // JavaScript functions
        function addQuestion(aptitudeId, postTitle) {
            document.getElementById('modal_aptitude_id').value = aptitudeId;
            document.getElementById('questionModalTitle').textContent = 'Add Question - ' + postTitle;
            document.getElementById('questionModal').style.display = 'flex';
            document.getElementById('questionForm').reset();
        }
        
        function viewTest(aptitudeId, postTitle) {
            document.getElementById('viewTestModalTitle').textContent = 'Test Details - ' + postTitle;
            
            // Fetch test questions via AJAX
            fetch('get_test_questions.php?aptitude_id=' + aptitudeId)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('testDetails').innerHTML = data;
                })
                .catch(error => {
                    document.getElementById('testDetails').innerHTML = '<p>Error loading test details.</p>';
                });
            
            document.getElementById('viewTestModal').style.display = 'flex';
        }
        
        function viewApplicants(vacancyId, postTitle) {
            document.getElementById('viewApplicantsModalTitle').textContent = 'Applicants - ' + postTitle;
            
            // Fetch applicants via AJAX
            fetch('get_applicants.php?vacancy_id=' + vacancyId)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('applicantsDetails').innerHTML = data;
                })
                .catch(error => {
                    document.getElementById('applicantsDetails').innerHTML = '<p>Error loading applicants.</p>';
                });
            
            document.getElementById('viewApplicantsModal').style.display = 'flex';
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        function deleteQuestion(questionId, aptitudeId) {
            if (confirm('Are you sure you want to delete this question?')) {
                // Submit delete form
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="question_id" value="${questionId}">
                    <input type="hidden" name="delete_question" value="1">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            // Show success/error messages
            <?php if(isset($success_message)): ?>
                Swal.fire({
                    title: 'Success!',
                    text: '<?php echo $success_message; ?>',
                    icon: 'success',
                    confirmButtonColor: '#3498db'
                });
            <?php endif; ?>
            
            <?php if(isset($error_message)): ?>
                Swal.fire({
                    title: 'Error!',
                    text: '<?php echo $error_message; ?>',
                    icon: 'error',
                    confirmButtonColor: '#3498db'
                });
            <?php endif; ?>
        });
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modals = document.getElementsByClassName('modal');
            for (let modal of modals) {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            }
        }
    </script>
</body>
</html>