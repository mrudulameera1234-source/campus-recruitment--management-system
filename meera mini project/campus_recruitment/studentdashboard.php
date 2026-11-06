<?php 

include("headers/student_header.php");
?>
<body>
 <!-- Main Content -->
    <div class="main-content">
        <!-- Dashboard Section -->
        <div class="module-section active" id="dashboard">
            <h2 class="mb-4" style="color: var(--navy-dark);">Student Dashboard</h2>
            
            <div class="row">
                <div class="col-md-3">
                    <div class="card stats-card">
                        <i class="fas fa-briefcase"></i>
                        <h2>24</h2>
                        <p>Job Openings</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card">
                        <i class="fas fa-file-alt"></i>
                        <h2>7</h2>
                        <p>My Applications</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card">
                        <i class="fas fa-calendar-check"></i>
                        <h2>3</h2>
                        <p>Upcoming Interviews</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card">
                        <i class="fas fa-chart-line"></i>
                        <h2>82%</h2>
                        <p>Profile Completion</p>
                    </div>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Upcoming Interviews</h5>
                            <a href="#" class="btn btn-sm btn-primary">View All</a>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-start mb-3 p-3 border-bottom border-secondary">
                                <div class="company-logo me-3">TC</div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">TechCorp - Software Engineer</h6>
                                    <small class="text-muted">June 15, 2023 • 10:00 AM</small>
                                    <div class="mt-2">
                                        <span class="badge bg-info">Online</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-start mb-3 p-3 border-bottom border-secondary">
                                <div class="company-logo me-3">IC</div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">InnovateCo - Data Analyst</h6>
                                    <small class="text-muted">June 18, 2023 • 2:30 PM</small>
                                    <div class="mt-2">
                                        <span class="badge bg-warning text-dark">On-site</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-start p-3">
                                <div class="company-logo me-3">GC</div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">GlobalTech - UX Designer</h6>
                                    <small class="text-muted">June 22, 2023 • 11:15 AM</small>
                                    <div class="mt-2">
                                        <span class="badge bg-info">Online</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Recommended Jobs</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-start mb-3 p-3 job-card">
                                <div class="company-logo me-3">FC</div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">Frontend Developer</h6>
                                    <small class="text-muted">FutureTech • New York, NY</small>
                                    <div class="mt-2">
                                        <span class="badge bg-success">Full-time</span>
                                        <span class="badge bg-secondary">Remote</span>
                                    </div>
                                </div>
                                <button class="btn btn-sm btn-primary">Apply</button>
                            </div>
                            
                            <div class="d-flex align-items-start p-3 job-card">
                                <div class="company-logo me-3">DC</div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">Data Scientist</h6>
                                    <small class="text-muted">DataWorks • San Francisco, CA</small>
                                    <div class="mt-2">
                                        <span class="badge bg-success">Full-time</span>
                                        <span class="badge bg-secondary">Hybrid</span>
                                    </div>
                                </div>
                                <button class="btn btn-sm btn-primary">Apply</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Aptitude Tests</h5>
                            <a href="#" class="btn btn-sm btn-primary">View All</a>
                        </div>
                        <div class="card-body">
                            <div class="card test-card mb-3">
                                <div class="card-body">
                                    <h6>Technical Skills Assessment</h6>
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <span class="text-muted">Status: <span class="text-success">Completed</span></span>
                                        <span class="badge bg-success">Score: 85%</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card test-card mb-3">
                                <div class="card-body">
                                    <h6>Logical Reasoning Test</h6>
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <span class="text-muted">Status: <span class="text-warning">Pending</span></span>
                                        <button class="btn btn-sm btn-primary">Start Test</button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card test-card">
                                <div class="card-body">
                                    <h6>Personality Assessment</h6>
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <span class="text-muted">Status: <span class="text-info">In Progress</span></span>
                                        <div class="d-flex align-items-center">
                                            <div class="progress me-2" style="width: 100px;">
                                                <div class="progress-bar" role="progressbar" style="width: 60%;" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <span>60%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Application Status</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="mb-0">TechCorp - Software Engineer</h6>
                                    <small class="text-muted">Applied: June 5, 2023</small>
                                </div>
                                <span class="badge bg-info">Interview Scheduled</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="mb-0">InnovateCo - Data Analyst</h6>
                                    <small class="text-muted">Applied: June 3, 2023</small>
                                </div>
                                <span class="badge bg-warning text-dark">Under Review</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">WebSolutions - Frontend Dev</h6>
                                    <small class="text-muted">Applied: May 28, 2023</small>
                                </div>
                                <span class="badge bg-success">Offer Received</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Other sections would be implemented similarly -->
        <div class="module-section" id="jobs">
            <h2 class="mb-4" style="color: var(--navy-dark);">Job Openings</h2>
            <div class="card">
                <div class="card-body">
                    <p>This section would display job openings from tbl_job with search and filter capabilities.</p>
                </div>
            </div>
        </div>

        <div class="module-section" id="applications">
            <h2 class="mb-4" style="color: var(--navy-dark);">My Applications</h2>
            <div class="card">
                <div class="card-body">
                    <p>This section would display the student's applications from tbl_applications with status tracking.</p>
                </div>
            </div>
        </div>

        <div class="module-section" id="interviews">
            <h2 class="mb-4" style="color: var(--navy-dark);">Scheduled Interviews</h2>
            <div class="card">
                <div class="card-body">
                    <p>This section would display the student's scheduled interviews from tbl_drives.</p>
                </div>
            </div>
        </div>

        <div class="module-section" id="companies">
            <h2 class="mb-4" style="color: var(--navy-dark);">Company Directory</h2>
            <div class="card">
                <div class="card-body">
                    <p>This section would display company details from tbl_company.</p>
                </div>
            </div>
        </div>

        <div class="module-section" id="aptitude">
            <h2 class="mb-4" style="color: var(--navy-dark);">Aptitude Tests</h2>
            <div class="card">
                <div class="card-body">
                    <p>This section would allow students to take aptitude tests with results stored in tbl_aptitude.</p>
                </div>
            </div>
        </div>

        <div class="module-section" id="results">
            <h2 class="mb-4" style="color: var(--navy-dark);">Interview Results</h2>
            <div class="card">
                <div class="card-body">
                    <p>This section would display the student's interview results from tbl_results.</p>
                </div>
            </div>
        </div>

        <div class="module-section" id="reviews">
            <h2 class="mb-4" style="color: var(--navy-dark);">Company Reviews</h2>
            <div class="card">
                <div class="card-body">
                    <p>This section would allow students to review companies with data stored in tbl_review.</p>
                </div>
            </div>
        </div>

        <div class="module-section" id="feedback">
            <h2 class="mb-4" style="color: var(--navy-dark);">Website Feedback</h2>
            <div class="card">
                <div class="card-body">
                    <p>This section would allow students to provide feedback about the website stored in tbl_feedback.</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
   </body>
</html>