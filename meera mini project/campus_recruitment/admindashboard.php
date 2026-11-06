<?php 

include("headers/admin_header.php");
?>
                <!-- Dashboard Overview -->
                <div class="row" id="dashboard">
                    <div class="col-md-3">
                        <div class="stat-card">
                            <i class="fas fa-users"></i>
                            <div class="count">1,245</div>
                            <div class="label">Total Students</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <i class="fas fa-building"></i>
                            <div class="count">87</div>
                            <div class="label">Registered Companies</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <i class="fas fa-briefcase"></i>
                            <div class="count">32</div>
                            <div class="label">Active Drives</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <i class="fas fa-check-circle"></i>
                            <div class="count">563</div>
                            <div class="label">Placements</div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Activities -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                Recent Activities
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Activity</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>2023-06-15</td>
                                                <td>New student registration - John Doe</td>
                                                <td><span class="badge bg-warning">Pending</span></td>
                                                <td><button class="btn btn-sm btn-primary">Verify</button></td>
                                            </tr>
                                            <tr>
                                                <td>2023-06-14</td>
                                                <td>New company registration - Tech Solutions Inc.</td>
                                                <td><span class="badge bg-success">Approved</span></td>
                                                <td><button class="btn btn-sm btn-secondary">View</button></td>
                                            </tr>
                                            <tr>
                                                <td>2023-06-13</td>
                                                <td>Drive scheduled - Amazon on 2023-07-10</td>
                                                <td><span class="badge bg-info">Scheduled</span></td>
                                                <td><button class="btn btn-sm btn-secondary">Details</button></td>
                                            </tr>
                                            <tr>
                                                <td>2023-06-12</td>
                                                <td>Aptitude test results published</td>
                                                <td><span class="badge bg-success">Completed</span></td>
                                                <td><button class="btn btn-sm btn-secondary">View</button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                Quick Actions
                            </div>
                            <div class="card-body">
                                <button class="btn btn-primary btn-block mb-3">
                                    <i class="fas fa-user-plus"></i> Add New Student
                                </button>
                                <button class="btn btn-primary btn-block mb-3">
                                    <i class="fas fa-building"></i> Add New Company
                                </button>
                                <button class="btn btn-primary btn-block mb-3">
                                    <i class="fas fa-calendar-plus"></i> Schedule Drive
                                </button>
                                <button class="btn btn-primary btn-block mb-3">
                                    <i class="fas fa-file-upload"></i> Upload Study Material
                                </button>
                                <button class="btn btn-primary btn-block">
                                    <i class="fas fa-poll"></i> Generate Report
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Student Management Section -->
                <div class="card mt-4" id="students">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Student Management</span>
                            <button class="btn btn-sm btn-light">View All</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="studentTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">Pending Verification</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="verified-tab" data-bs-toggle="tab" data-bs-target="#verified" type="button" role="tab">Verified Students</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="placed-tab" data-bs-toggle="tab" data-bs-target="#placed" type="button" role="tab">Placed Students</button>
                            </li>
                        </ul>
                        <div class="tab-content p-3 border border-top-0 rounded-bottom">
                            <div class="tab-pane fade show active" id="pending" role="tabpanel">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Department</th>
                                            <th>Register Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>S2023001</td>
                                            <td>John Doe</td>
                                            <td>Computer Science</td>
                                            <td>2023-06-15</td>
                                            <td>
                                                <button class="btn btn-sm btn-success">Approve</button>
                                                <button class="btn btn-sm btn-danger">Reject</button>
                                            </td>
                                        </tr>
                                        <!-- More rows -->
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane fade" id="verified" role="tabpanel">
                                <!-- Verified students table -->
                            </div>
                            <div class="tab-pane fade" id="placed" role="tabpanel">
                                <!-- Placed students table -->
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Company Management Section -->
                <div class="card mt-4" id="admin_manage_company.php">
                    <div class="card-header">
                        Company Management
                    </div>
                    <div class="card-body">
                        <!-- Similar tab structure as students -->
                        <!-- With options to view, approve, revoke companies -->
                    </div>
                </div>
                
                <!-- Recruitment Drives Section -->
                <div class="card mt-4" id="drives">
                    <div class="card-header">
                        Recruitment Drives
                    </div>
                    <div class="card-body">
                        <!-- Drive scheduling, verification, tracking participation -->
                    </div>
                </div>
                
                <!-- Aptitude Tests Section -->
                <div class="card mt-4" id="aptitude">
                    <div class="card-header">
                        Aptitude Tests
                    </div>
                    <div class="card-body">
                        <!-- Create tests, publish results, view analytics -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // JavaScript for interactive functionality
    </script>
</body>
</html>