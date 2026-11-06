<?php 

include("headers/company_header.php");
?>
<body>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Dashboard Section -->
        <div class="module-section active" id="dashboard">
            <h2 class="mb-4">Dashboard Overview</h2>
            
            <div class="row">
                <div class="col-md-3">
                    <div class="card stats-card">
                        <i class="fas fa-file-alt"></i>
                        <h2>142</h2>
                        <p>New Applications</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card">
                        <i class="fas fa-calendar-check"></i>
                        <h2>7</h2>
                        <p>Upcoming Drives</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card">
                        <i class="fas fa-users"></i>
                        <h2>83%</h2>
                        <p>Attendance Rate</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card">
                        <i class="fas fa-bell"></i>
                        <h2>3</h2>
                        <p>Notifications</p>
                    </div>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">Recent Applications</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Position</th>
                                            <th>Applied On</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>John Smith</td>
                                            <td>Software Engineer</td>
                                            <td>Jun 12, 2023</td>
                                            <td><span class="badge bg-warning">Pending</span></td>
                                        </tr>
                                        <tr>
                                            <td>Emma Johnson</td>
                                            <td>Data Analyst</td>
                                            <td>Jun 11, 2023</td>
                                            <td><span class="badge bg-success">Approved</span></td>
                                        </tr>
                                        <tr>
                                            <td>Michael Brown</td>
                                            <td>UX Designer</td>
                                            <td>Jun 10, 2023</td>
                                            <td><span class="badge bg-danger">Rejected</span></td>
                                        </tr>
                                        <tr>
                                            <td>Sarah Williams</td>
                                            <td>Product Manager</td>
                                            <td>Jun 9, 2023</td>
                                            <td><span class="badge bg-info">Interview</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">Upcoming Drives</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-start mb-3">
                                <div class="me-3 text-center">
                                    <h5 class="mb-0">15</h5>
                                    <small>JUN</small>
                                </div>
                                <div>
                                    <h6 class="mb-0">Campus Drive - Tech University</h6>
                                    <small class="text-muted">10:00 AM - 4:00 PM</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-start mb-3">
                                <div class="me-3 text-center">
                                    <h5 class="mb-0">18</h5>
                                    <small>JUN</small>
                                </div>
                                <div>
                                    <h6 class="mb-0">Virtual Career Fair</h6>
                                    <small class="text-muted">9:00 AM - 5:00 PM</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-start">
                                <div class="me-3 text-center">
                                    <h5 class="mb-0">22</h5>
                                    <small>JUN</small>
                                </div>
                                <div>
                                    <h6 class="mb-0">On-site Interviews</h6>
                                    <small class="text-muted">9:00 AM - 3:00 PM</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Other sections would be implemented similarly -->
        <div class="module-section" id="applications">
            <h2 class="mb-4">Student Applications</h2>
            <div class="card">
                <div class="card-body">
                    <p>This section would display data from tbl_applications with filtering, sorting, and application review capabilities.</p>
                </div>
            </div>
        </div>

        <div class="module-section" id="jobs">
            <h2 class="mb-4">Job Postings</h2>
            <div class="card">
                <div class="card-body">
                    <p>This section would allow creating, editing, and managing job postings stored in tbl_job.</p>
                </div>
            </div>
        </div>

        <div class="module-section" id="drives">
            <h2 class="mb-4">Schedule Drives</h2>
            <div class="card">
                <div class="card-body">
                    <p>This section would allow scheduling and managing drives stored in tbl_drives.</p>
                </div>
            </div>
        </div>

        <div class="module-section" id="attendance">
            <h2 class="mb-4">Attendance Tracking</h2>
            <div class="card">
                <div class="card-body">
                    <p>This section would track student attendance for interviews (both online and offline) stored in tbl_attendance.</p>
                </div>
            </div>
        </div>

        <div class="module-section" id="aptitude">
            <h2 class="mb-4">Aptitude Tests</h2>
            <div class="card">
                <div class="card-body">
                    <p>This section would manage online aptitude tests and view results stored in tbl_aptitude.</p>
                </div>
            </div>
        </div>

        <div class="module-section" id="results">
            <h2 class="mb-4">Interview Results</h2>
            <div class="card">
                <div class="card-body">
                    <p>This section would publish and manage interview results stored in tbl_results.</p>
                </div>
            </div>
        </div>

        <div class="module-section" id="notifications">
            <h2 class="mb-4">Notifications</h2>
            <div class="card">
                <div class="card-body">
                    <p>This section would display and manage notifications for admin stored in tbl_notifications.</p>
                </div>
            </div>
        </div>

        <div class="module-section" id="feedback">
            <h2 class="mb-4">Website Feedback</h2>
            <div class="card">
                <div class="card-body">
                    <p>This section would view and manage feedback about the website stored in tbl_feedback.</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
   
</body>
</html>