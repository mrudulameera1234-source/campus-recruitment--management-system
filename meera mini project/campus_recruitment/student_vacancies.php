<?php

include("connection.php");
include("headers/student_header.php");


// Get student information


// Get job vacancies with related data (only accepted drives)
$vacancies_query = "
    SELECT 
        v.vacancy_id,
        v.drive_id,
        v.post,
        v.description,
        v.posted_on,
        v.arrears_allowed,
        v.cutoff_cgpa,
        v.last_date,
        d.drive_id,
        d.prefered_date,
        d.status,
        c.name as company_name,
        GROUP_CONCAT(DISTINCT vc.course_id) as courses
    FROM tbl_vacancies v
    JOIN tbl_drive d ON v.drive_id = d.drive_id
    JOIN tbl_company c ON d.company_id = c.company_id
    LEFT JOIN tbl_vacancy_course vc ON v.vacancy_id = vc.vacancy_id
    WHERE d.status = 'approved'
    GROUP BY v.vacancy_id
    ORDER BY v.posted_on DESC
";

$vacancies_result = mysqli_query($conn, $vacancies_query);

// Handle application submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apply'])) {
    $vacancy_id = mysqli_real_escape_string($conn, $_POST['vacancy_id']);
    
    // Check if student already applied
    $check_query = "SELECT * FROM tbl_applications WHERE student_id = '$student_id' AND vacancy_id = '$vacancy_id'";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) == 0) {
        // Insert application
        $insert_query = "INSERT INTO tbl_applications (student_id, vacancy_id, applied_on) 
                         VALUES ('$student_id', '$vacancy_id', NOW())";
        
        if (mysqli_query($conn, $insert_query)) {
            $success_message = "Application submitted successfully!";
        } else {
            $error_message = "Error submitting application: " . mysqli_error($conn);
        }
    } else {
        $error_message = "You have already applied for this position.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Job Portal</title>
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
        }
        
      
        
        header {
            background: linear-gradient(135deg, #001f3f 0%, #1e47a3 100%);
            color: white;
            padding: 20px 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 0 0 10px 10px;
            margin-bottom: 30px;
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
        
        .search-bar {
            display: flex;
            margin: 20px 0;
        }
        
        .search-bar input {
            flex: 1;
            padding: 12px 15px;
            border: none;
            border-radius: 5px 0 0 5px;
            font-size: 16px;
        }
        
        .search-bar button {
            padding: 12px 20px;
            background: #ff6b6b;
            color: white;
            border: none;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
            font-weight: bold;
        }
        
        .filters {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .filter {
            padding: 8px 15px;
            background: white;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .filter:hover {
            background: #001f3f;
            color: white;
        }
        
        .filter.active {
            background: #001f3f;
            color: white;
        }
        
        .job-listings {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
        }
        
        .job-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .job-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }
        
        .job-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        
        .job-title {
            font-size: 20px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .drive-id {
            background: #eaf4ff;
            color: #3a8ffe;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 500;
        }
        
        .job-details {
            margin-bottom: 20px;
        }
        
        .job-info {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            font-size: 15px;
        }
        
        .job-info i {
            margin-right: 10px;
            color: #001f3f;
            width: 20px;
        }
        
        .description {
            margin: 15px 0;
            color: #555;
            line-height: 1.5;
            max-height: 100px;
            overflow-y: auto;
        }
        
        .requirements {
            display: flex;
            gap: 15px;
            margin: 15px 0;
            flex-wrap: wrap;
        }
        
        .req-item {
            background: #f8f9fa;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 13px;
            display: flex;
            align-items: center;
        }
        
        .req-item i {
            margin-right: 5px;
            color: #001f3f;
        }
        
        .eligibility-error {
            background: #ffe6e6;
            color: #d9534f;
            padding: 10px;
            border-radius: 5px;
            margin: 15px 0;
            font-size: 14px;
        }
        
        .eligibility-error i {
            margin-right: 5px;
        }
        
        .posted-date, .last-date {
            color: #777;
            font-size: 14px;
            margin-top: 5px;
        }
        
        .last-date {
            color: #e74c3c;
            font-weight: 500;
        }
        
        .apply-btn {
            width: 100%;
            padding: 12px;
            background: #001f3f;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
            margin-top: 15px;
        }
        
        .apply-btn:hover {
            background: #003366;
        }
        
        .apply-btn:disabled {
            background: #cccccc;
            cursor: not-allowed;
        }
        
        .applied-btn {
            width: 100%;
            padding: 12px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            margin-top: 15px;
            cursor: default;
        }
        
        .cancel-btn {
            width: 100%;
            padding: 12px;
            background: #f8f9fa;
            color: #555;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
            margin-top: 10px;
        }
        
        .cancel-btn:hover {
            background: #e9ecef;
        }
        
        .application-form {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 500px;
            z-index: 1000;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .form-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .form-title {
            font-size: 22px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .close-form {
            background: none;
            border: none;
            font-size: 22px;
            cursor: pointer;
            color: #777;
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
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .submit-btn {
            width: 100%;
            padding: 12px;
            background: #001f3f;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        
        .submit-btn:hover {
            background: #003366;
        }
        
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
        
        .btn {
            padding: 10px 20px;
            background: #001f3f;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            margin-left: 10px;
        }
        
        .btn:hover {
            background: #003366;
        }
        

        
        .no-jobs {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            grid-column: 1 / -1;
        }
        
        .no-jobs i {
            font-size: 50px;
            color: #ddd;
            margin-bottom: 15px;
        }
        
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                text-align: center;
            }
            
            .logo {
                margin-bottom: 15px;
            }
            
            .job-listings {
                grid-template-columns: 1fr;
            }
            
            .filters {
                justify-content: center;
            }
            
            .user-actions {
                margin-top: 15px;
            }
            
            .btn {
                margin: 5px;
            }
        }
    </style>
</head>
<body>
     

    <div class="main-content">
   

    <div class="container">
        <!-- Student Profile Section -->
        <div class="student-profile" id="studentProfile">
            <div class="profile-header">
                <h3 class="profile-title">My Profile</h3>
                <button class="btn" id="hideProfileBtn">Hide Profile</button>
            </div>
            <div class="profile-details">
                <div class="profile-item">
                    <i class="fas fa-user"></i>
                    <span><strong>Name:</strong> <?php echo $student_data['name'] ?? 'N/A'; ?></span>
                </div>
                <div class="profile-item">
                    <i class="fas fa-envelope"></i>
                    <span><strong>Email:</strong> <?php echo $student_data['email'] ?? 'N/A'; ?></span>
                </div>
                <div class="profile-item">
                    <i class="fas fa-graduation-cap"></i>
                    <span><strong>Course:</strong> <?php echo $student_data['course'] ?? 'N/A'; ?></span>
                </div>
                <div class="profile-item">
                    <i class="fas fa-star"></i>
                    <span><strong>CGPA:</strong> <?php echo $student_data['cgpa'] ?? 'N/A'; ?></span>
                </div>
                <div class="profile-item">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span><strong>Arrears:</strong> <?php echo $student_data['arrears'] ?? 'N/A'; ?></span>
                </div>
            </div>
        </div>

        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search for job titles, companies, or keywords...">
            <button id="searchBtn">Search</button>
        </div>
        
        <div class="filters">
            <div class="filter active" data-filter="all">All Jobs</div>
            <div class="filter" data-filter="it">IT & Software</div>
            <div class="filter" data-filter="engineering">Engineering</div>
            <div class="filter" data-filter="business">Business</div>
            <div class="filter" data-filter="design">Design</div>
            <div class="filter" data-filter="marketing">Marketing</div>
        </div>
        
        <div class="job-listings" id="jobListings">
            <?php
            // Display job vacancies from database
            if(mysqli_num_rows($vacancies_result) > 0) {
                while($vacancy = mysqli_fetch_assoc($vacancies_result)) {
                    // Check if student is eligible for this vacancy
                    $is_eligible = true;
                    $eligibility_errors = array();
                    
                    // Check if student has already applied
                    $applied_query = "SELECT * FROM tbl_applications WHERE student_id = '$student_id' AND vacancy_id = '{$vacancy['vacancy_id']}'";
                    $applied_result = mysqli_query($conn, $applied_query);
                    $has_applied = mysqli_num_rows($applied_result) > 0;
                    
                    // Check course eligibility
                    if(!empty($vacancy['courses'])) {
                        $course_array = explode(',', $vacancy['courses']);
                        if(!in_array($student_data['course_id'], $course_array)) {
                            $is_eligible = false;
                            $eligibility_errors[] = "Your course (" . $student_data['course'] . ") doesn't match the required courses (" . implode(', ', $course_array) . ")";
                        }
                    }
                    
                    // Check CGPA eligibility
                    if($student_data['cgpa'] < $vacancy['cutoff_cgpa']) {
                        $is_eligible = false;
                        $eligibility_errors[] = "Your CGPA (" . $student_data['cgpa'] . ") is below the required CGPA (" . $vacancy['cutoff_cgpa'] . ")";
                    }
                    
                    // Check arrears eligibility
                    if($student_data['arrears'] > $vacancy['arrears_allowed']) {
                        $is_eligible = false;
                        $eligibility_errors[] = "You have " . $student_data['arrears'] . " arrears, but only " . $vacancy['arrears_allowed'] . " are allowed";
                    }
                    
                    // Format the dates
                    $posted_date = date("F j, Y", strtotime($vacancy['posted_on']));
                    $last_date = date("F j, Y", strtotime($vacancy['last_date']));
                    $prefered_date = date("F j, Y", strtotime($vacancy['prefered_date']));
                    
                    echo '
                    <div class="job-card" id="job-'.$vacancy['vacancy_id'].'" data-courses="'.$vacancy['courses'].'">
                        <div class="job-header">
                            <h3 class="job-title">'.$vacancy['post'].'</h3>
                            <span class="drive-id">'.$vacancy['drive_id'].'</span>
                        </div>
                        <div class="job-details">
                            <div class="job-info">
                                <i class="fas fa-building"></i>
                                <span>'.$vacancy['company_name'].'</span>
                            </div>
                            <div class="job-info">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Preferred Date: '.$prefered_date.'</span>
                            </div>
                            <div class="description">
                                '.$vacancy['description'].'
                            </div>
                            <div class="requirements">
                                <div class="req-item">
                                    <i class="fas fa-graduation-cap"></i>
                                    <span>Courses: '.str_replace(',', ', ', $vacancy['courses']).'</span>
                                </div>
                                <div class="req-item">
                                    <i class="fas fa-bolt"></i>
                                    <span>CGPA: '.$vacancy['cutoff_cgpa'].'+</span>
                                </div>
                                <div class="req-item">
                                    <i class="fas fa-times-circle"></i>
                                    <span>Arrears: '.$vacancy['arrears_allowed'].' allowed</span>
                                </div>
                            </div>';
                            
                    // Show eligibility errors if not eligible
                    if(!$is_eligible) {
                        echo '<div class="eligibility-error">';
                        echo '<i class="fas fa-exclamation-circle"></i>';
                        echo 'You are not eligible for this position:';
                        echo '<ul>';
                        foreach($eligibility_errors as $error) {
                            echo '<li>'.$error.'</li>';
                        }
                        echo '</ul>';
                        echo '</div>';
                    }
                            
                    echo '
                            <div class="posted-date">
                                Posted on: '.$posted_date.'
                            </div>
                            <div class="last-date">
                                Last date to apply: '.$last_date.'
                            </div>
                        </div>';
                        
                    if($has_applied) {
                        echo '<button class="applied-btn">Already Applied</button>';
                    } else if($is_eligible) {
                        echo '
                        <button class="apply-btn" data-id="'.$vacancy['vacancy_id'].'" data-title="'.$vacancy['post'].'">Apply Now</button>';
                    } else {
                        echo '
                        <button class="apply-btn" disabled>Not Eligible</button>';
                    }
                        
                    echo '
                        <button class="cancel-btn" data-id="'.$vacancy['vacancy_id'].'" data-title="'.$vacancy['post'].'">Remove Listing</button>
                    </div>';
                }
            } else {
                echo '
                <div class="no-jobs">
                    <i class="fas fa-briefcase"></i>
                    <h3>No Job Openings Available</h3>
                    <p>There are currently no job openings. Please check back later.</p>
                </div>';
            }
            ?>
        </div>
    </div>

    <!-- Application Form -->
    <div class="overlay" id="overlay"></div>
    <div class="application-form" id="applicationForm">
        <div class="form-header">
            <h3 class="form-title">Apply for <span id="jobTitleForm">Position</span></h3>
            <button class="close-form" id="closeForm">&times;</button>
        </div>
        <form id="jobApplicationForm" method="POST">
            <input type="hidden" id="vacancyId" name="vacancy_id">
            <input type="hidden" name="apply" value="1">
            <div class="form-group">
                <label for="fullName">Full Name</label>
                <input type="text" id="fullName" value="<?php echo $student_data['name'] ?? ''; ?>" required readonly>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" value="<?php echo $student_data['email'] ?? ''; ?>" required readonly>
            </div>
            <div class="form-group">
                <label for="course">Course</label>
                <input type="text" id="courseDisplay" value="<?php echo $student_data['course'] ?? ''; ?>" readonly>
            </div>
            <div class="form-group">
                <label for="cgpa">Current CGPA</label>
                <input type="text" id="cgpaDisplay" value="<?php echo $student_data['cgpa'] ?? ''; ?>" readonly>
            </div>
            <div class="form-group">
                <label for="arrears">Number of Arrears</label>
                <input type="text" id="arrearsDisplay" value="<?php echo $student_data['arrears'] ?? ''; ?>" readonly>
            </div>
            <button type="submit" class="submit-btn">Submit Application</button>
        </form>
    </div>

    <script>
        // JavaScript to handle the application form and SweetAlert
        document.addEventListener('DOMContentLoaded', function() {
            const applyButtons = document.querySelectorAll('.apply-btn');
            const cancelButtons = document.querySelectorAll('.cancel-btn');
            const closeForm = document.getElementById('closeForm');
            const overlay = document.getElementById('overlay');
            const applicationForm = document.getElementById('applicationForm');
            const jobApplicationForm = document.getElementById('jobApplicationForm');
            const jobTitleForm = document.getElementById('jobTitleForm');
            const vacancyIdInput = document.getElementById('vacancyId');
            const profileSection = document.getElementById('studentProfile');
            const profileBtn = document.getElementById('profileBtn');
            const hideProfileBtn = document.getElementById('hideProfileBtn');
            const applicationsBtn = document.getElementById('applicationsBtn');
            const searchBtn = document.getElementById('searchBtn');
            const searchInput = document.getElementById('searchInput');
            const filters = document.querySelectorAll('.filter');
            
            // Initially hide profile section
            profileSection.style.display = 'none';
            
            // Profile button click event
            profileBtn.addEventListener('click', function() {
                profileSection.style.display = 'block';
            });
            
            // Hide profile button click event
            hideProfileBtn.addEventListener('click', function() {
                profileSection.style.display = 'none';
            });
            
            
            // Apply button click event
            applyButtons.forEach(button => {
                button.addEventListener('click', function() {
                    if(this.disabled) return;
                    
                    const jobId = this.getAttribute('data-id');
                    const jobTitle = this.getAttribute('data-title');
                    
                    // Set form values
                    jobTitleForm.textContent = jobTitle;
                    vacancyIdInput.value = jobId;
                    
                    // Show the form
                    applicationForm.style.display = 'block';
                    overlay.style.display = 'block';
                    document.body.style.overflow = 'hidden';
                });
            });
            
            // Cancel button click event - Remove the job card
            cancelButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const jobId = this.getAttribute('data-id');
                    const jobTitle = this.getAttribute('data-title');
                    const jobCard = document.getElementById('job-' + jobId);
                    
                    // Show SweetAlert confirmation
                    Swal.fire({
                        title: 'Remove ' + jobTitle + '?',
                        text: 'Are you sure you want to remove this job listing from your view?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#001f3f',
                        cancelButtonColor: '#ff6b6b',
                        confirmButtonText: 'Yes, remove it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Remove the job card from the page
                            if(jobCard) {
                                jobCard.style.opacity = '0';
                                jobCard.style.transform = 'translateX(-100px)';
                                setTimeout(function() {
                                    jobCard.remove();
                                    
                                    // Check if no jobs left
                                    if(document.querySelectorAll('.job-card').length === 0) {
                                        document.getElementById('jobListings').innerHTML = 
                                            '<div class="no-jobs"><i class="fas fa-briefcase"></i><h3>No Job Openings Available</h3><p>There are currently no job openings. Please check back later.</p></div>';
                                    }
                                }, 300);
                            }
                            
                            // Show success message
                            Swal.fire({
                                title: 'Listing Removed!',
                                text: jobTitle + ' has been removed from your view.',
                                icon: 'success',
                                confirmButtonColor: '#001f3f'
                            });
                        }
                    });
                });
            });
            
            // Close form event
            closeForm.addEventListener('click', function() {
                applicationForm.style.display = 'none';
                overlay.style.display = 'none';
                document.body.style.overflow = 'auto';
            });
            
            // Overlay click event
            overlay.addEventListener('click', function() {
                applicationForm.style.display = 'none';
                this.style.display = 'none';
                document.body.style.overflow = 'auto';
            });
            
            // Form submission event
            jobApplicationForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Show success message
                Swal.fire({
                    title: 'Application Submitted!',
                    text: 'Your application has been successfully submitted.',
                    icon: 'success',
                    confirmButtonColor: '#001f3f'
                }).then(() => {
                    // Submit the form
                    this.submit();
                });
            });
            
            // Filter functionality
            filters.forEach(filter => {
                filter.addEventListener('click', function() {
                    filters.forEach(f => f.classList.remove('active'));
                    this.classList.add('active');
                    
                    const filterValue = this.getAttribute('data-filter');
                    filterJobs(filterValue);
                });
            });
            
            // Search functionality
            searchBtn.addEventListener('click', function() {
                const searchTerm = searchInput.value.trim().toLowerCase();
                filterJobs('all', searchTerm);
            });
            
            // Enter key search
            searchInput.addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    const searchTerm = searchInput.value.trim().toLowerCase();
                    filterJobs('all', searchTerm);
                }
            });
            
            // Filter and search function
            function filterJobs(filter, searchTerm = '') {
                const jobCards = document.querySelectorAll('.job-card');
                let visibleCount = 0;
                
                jobCards.forEach(card => {
                    const title = card.querySelector('.job-title').textContent.toLowerCase();
                    const company = card.querySelector('.job-info:nth-child(1) span').textContent.toLowerCase();
                    const courses = card.getAttribute('data-courses').toLowerCase();
                    
                    let matchesFilter = true;
                    let matchesSearch = true;
                    
                    // Apply filter
                    if (filter !== 'all') {
                        matchesFilter = courses.includes(filter);
                    }
                    
                    // Apply search
                    if (searchTerm) {
                        matchesSearch = title.includes(searchTerm) || 
                                       company.includes(searchTerm) || 
                                       courses.includes(searchTerm);
                    }
                    
                    if (matchesFilter && matchesSearch) {
                        card.style.display = 'block';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });
                
                // Show message if no jobs match
                const noJobsDiv = document.querySelector('.no-jobs');
                if (visibleCount === 0) {
                    if (!noJobsDiv) {
                        document.getElementById('jobListings').innerHTML = 
                            '<div class="no-jobs"><i class="fas fa-search"></i><h3>No Matching Jobs</h3><p>No job openings match your search criteria.</p></div>';
                    }
                } else if (noJobsDiv && noJobsDiv.parentNode) {
                    noJobsDiv.parentNode.removeChild(noJobsDiv);
                }
            }
            
            // Show success/error messages from PHP
            <?php if(isset($success_message)): ?>
                Swal.fire({
                    title: 'Success!',
                    text: '<?php echo $success_message; ?>',
                    icon: 'success',
                    confirmButtonColor: '#001f3f'
                });
            <?php endif; ?>
            
            <?php if(isset($error_message)): ?>
                Swal.fire({
                    title: 'Error!',
                    text: '<?php echo $error_message; ?>',
                    icon: 'error',
                    confirmButtonColor: '#001f3f'
                });
            <?php endif; ?>
        });
    </script>
</body>
</html>