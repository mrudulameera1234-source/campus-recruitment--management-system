<?php
session_start();
include('connection.php');
include("headers/admin_header.php");

// Handle feedback deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_feedback'])) {
    $feedback_id = mysqli_real_escape_string($conn, $_POST['feedback_id']);
    
    $delete_query = "DELETE FROM tbl_feedback WHERE feedback_id = $feedback_id";
    
    if (mysqli_query($conn, $delete_query)) {
        $_SESSION['success'] = "Feedback deleted successfully";
    } else {
        $_SESSION['error'] = "Error deleting feedback: " . mysqli_error($conn);
    }
    header("Location: admin_feedback.php");
    exit();
}

// Search functionality
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Pagination setup
$per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $per_page) - $per_page : 0;

// Base query for feedback
$query = "SELECT * FROM tbl_feedback 
          WHERE name LIKE '%$search%' OR 
                email LIKE '%$search%' OR
                comment LIKE '%$search%'
          ORDER BY date DESC";


// Count total records for pagination
$total_query = "SELECT COUNT(*) as total FROM tbl_feedback";
$total_result = mysqli_query($conn, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total = $total_row['total'];
$pages = ceil($total / $per_page);

// Add limit for pagination
$query .= " LIMIT $start, $per_page";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .feedback-card {
            border-left: 4px solid #0d6efd;
            margin-bottom: 20px;
            transition: all 0.3s;
        }
        .feedback-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .feedback-header {
            background-color: #f8f9fa;
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
        }
        .feedback-body {
            padding: 15px;
        }
        .feedback-meta {
            font-size: 0.9rem;
            color: #6c757d;
        }
        .badge-student {
            background-color: #17a2b8;
        }
        .badge-company {
            background-color: #28a745;
        }
        .action-btns .btn {
            margin-right: 5px;
        }
    </style>
</head>
<body>
           <!-- Main Content -->
            
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Feedback Management</h1>
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
                                               placeholder="Search by name, email or comment" 
                                               value="<?= htmlspecialchars($search) ?>">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-search"></i> Search
                                        </button>
                                        <a href="admin_feedback.php" class="btn btn-secondary">Reset</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Feedback List -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>
                            <i class="fas fa-comments"></i> Showing <?= number_format($total) ?> feedback entries
                        </span>
                    </div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while ($feedback = mysqli_fetch_assoc($result)): ?>
                                <div class="feedback-card">
                                    <div class="feedback-header d-flex justify-content-between">
                                        <div>
                                            <strong><?= htmlspecialchars($feedback['name']) ?></strong>
                                            <span class="feedback-meta ml-2">
                                                <?= htmlspecialchars($feedback['email']) ?>
                                            </span>
                                        </div>
                                        <div>
                                            <span class="badge <?= strpos($feedback['email'], 'ac.uk') ? 'badge-student' : 'badge-company' ?>">
                                                <?= strpos($feedback['email'], 'ac.uk') ? 'Student' : 'Company' ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="feedback-body">
                                        <p><?= nl2br(htmlspecialchars($feedback['message'])) ?></p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="far fa-clock"></i> <?= date('d M Y, h:i A', strtotime($feedback['date'])) ?>
                                            </small>
                                            <div class="action-btns">
                                                <form method="POST" action="" style="display:inline;">
                                                    <input type="hidden" name="feedback_id" value="<?= $feedback['feedback_id'] ?>">
                                                    <button type="submit" name="delete_feedback" class="btn btn-sm btn-danger" 
                                                            onclick="return confirm('Are you sure you want to delete this feedback?')">
                                                        <i class="fas fa-trash-alt"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="alert alert-info">No feedback records found</div>
                        <?php endif; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($pages > 1): ?>
                        <div class="card-footer">
                            <nav aria-label="Page navigation">
                                <ul class="pagination justify-content-center">
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
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Show success/error messages from session
        <?php if (isset($_SESSION['success'])): ?>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '<?= $_SESSION['success'] ?>',
                timer: 3000,
                toast: true,
                position: 'top-end'
            });
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?= $_SESSION['error'] ?>',
                timer: 3000,
                toast: true,
                position: 'top-end'
            });
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </script>
</body>
</html>
<?php ob_end_flush(); ?>