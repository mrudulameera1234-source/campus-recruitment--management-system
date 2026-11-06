    <?php
ob_start();
// Database connection and processing
session_start();
include("connection.php");
include("headers/admin_header.php");

// Search functionality
$search = '';
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
}

// Pagination setup
$per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $per_page) - $per_page : 0;

// Base query
$query = "SELECT s.*, c.course
          FROM tbl_student s
          LEFT JOIN tbl_course c ON s.course_id = c.course_id
          WHERE s.name LIKE '%$search%' OR 
                s.admno LIKE '%$search%' OR
                s.phonenumber LIKE '%$search%' OR
                c.course LIKE '%$search%'
          ORDER BY s.year DESC, s.name ASC";

// Count total records for pagination
$total_query = "SELECT COUNT(*) as total FROM tbl_student";
$total_result = mysqli_query($conn, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total = $total_row['total'];
$pages = ceil($total / $per_page);

// Add limit for pagination
$query .= " LIMIT $start, $per_page";
$result = mysqli_query($conn, $query);
?>
    <div class="container-fluid">
        <div class="row">
            
            <!-- Main Content -->
            
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Student Records</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                    </div>
                </div>

                <!-- Search and Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" name="search" 
                                               placeholder="Search by name, admission no, phone or course" 
                                               value="<?= htmlspecialchars($search) ?>">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-search"></i> Search
                                        </button>
                                        <a href="students.php" class="btn btn-secondary">Reset</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Students Table -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>
                            <i class="fas fa-users"></i> Showing <?= number_format($total) ?> student(s)
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Photo</th>
                                        <th>Adm No</th>
                                        <th>Name</th>
                                        <th>DOB</th>
                                        <th>Course</th>
                                        <th>Resume</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (mysqli_num_rows($result) > 0): ?>
                                        <?php while ($student = mysqli_fetch_assoc($result)): ?>
                                            <tr>
                                                <td class="text-center">
                                                    <?php if (!empty($student['photo'])): ?>
                                                        <img src="uploads/<?= htmlspecialchars($student['photo']) ?>" 
                                                             class="student-photo" width='100' height='100'
                                                             alt="<?= htmlspecialchars($student['name']) ?>">
                                                    <?php else: ?>
                                                        <i class="fas fa-user-circle fa-2x text-secondary"></i>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= htmlspecialchars($student['admno']) ?></td>
                                                <td><?= htmlspecialchars($student['name']) ?></td>
                                                <td><?= date('d/m/Y', strtotime($student['dob'])) ?></td>
                                                <td><?= htmlspecialchars($student['course'] ?? 'N/A') ?></td>
                                                <td class="action-btns">
                                                    <a href="view_student.php?id=<?= $student['student_id'] ?>" 
                                                       class="btn btn-sm btn-primary" 
                                                       title="View Full Details">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                    <?php if (!empty($student['resume'])): ?>
                                                        <a href="uploads/<?= htmlspecialchars($student['resume']) ?>" 
                                                           class="btn btn-sm btn-info mt-1" 
                                                           title="Download Resume" 
                                                           download>
                                                            <i class="fas fa-file-download"></i> Resume
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="11" class="text-center py-4">No student records found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($pages > 1): ?>
                            <nav aria-label="Page navigation">
                                <ul class="pagination justify-content-center mt-4">
                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>" aria-label="Previous">
                                                <span aria-hidden="true">&laquo;</span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = 1; $i <= $pages; $i++): ?>
                                        <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                            <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <?php if ($page < $pages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>" aria-label="Next">
                                                <span aria-hidden="true">&raquo;</span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>