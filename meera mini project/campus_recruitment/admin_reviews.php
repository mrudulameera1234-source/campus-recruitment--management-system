<?php
ob_start();
session_start();
include("connection.php");
include("headers/admin_header.php");

// Handle review deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_review'])) {
    $review_id = mysqli_real_escape_string($conn, $_POST['review_id']);
    
    $delete_query = "DELETE FROM tbl_reviews WHERE review_id = ?";
    $stmt = mysqli_prepare($conn, $delete_query);
    mysqli_stmt_bind_param($stmt, "i", $review_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = "Review deleted successfully";
    } else {
        $_SESSION['errors'] = ["Error deleting review: " . mysqli_error($conn)];
    }
    mysqli_stmt_close($stmt);
    
    header("Location: admin_reviews.php");
    exit();
}

// Fetch all reviews with student and company information
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$company_filter = isset($_GET['company']) ? mysqli_real_escape_string($conn, $_GET['company']) : '';

$query = "SELECT r.*, s.name as student_name, s.admno, c.name 
          FROM tbl_reviews r
          JOIN tbl_student s ON r.student_id = s.student_id
          JOIN tbl_company c ON r.company_id = c.company_id
          WHERE (s.name LIKE '%$search%' OR 
                s.admno LIKE '%$search%' OR
                c.name LIKE '%$search%' OR
                r.comment LIKE '%$search%')";
                
if (!empty($company_filter)) {
    $query .= " AND r.company_id = '$company_filter'";
}

$query .= " ORDER BY r.date DESC";
$reviews_result = mysqli_query($conn, $query);

// Fetch all companies for filter dropdown
$companies_query = "SELECT * FROM tbl_company ORDER BY name ASC";
$companies_result = mysqli_query($conn, $companies_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Reviews Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .review-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border-left: 4px solid #0d6efd;
        }
        .rating {
            color: #ffc107;
            font-size: 1.2rem;
        }
        .student-info {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 10px;
        }
        .company-name {
            font-weight: 600;
            color: #0d6efd;
        }
        .empty-reviews {
            text-align: center;
            padding: 50px;
            color: #6c757d;
        }
        .search-container {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
         
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Company Reviews</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                        </div>
                    </div>
                </div>

                <!-- Search and Filter Section -->
                <div class="search-container">
                    <form method="GET" action="">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label for="search" class="form-label">Search Reviews</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="search" name="search" 
                                           placeholder="Search by student, company, or review text" 
                                           value="<?= htmlspecialchars($search) ?>">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <label for="company" class="form-label">Filter by Company</label>
                                <select class="form-select" id="company" name="company">
                                    <option value="">All Companies</option>
                                    <?php while ($company = mysqli_fetch_assoc($companies_result)): ?>
                                        <option value="<?= $company['company_id'] ?>" 
                                            <?= ($company_filter == $company['company_id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($company['name']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <a href="admin_reviews.php" class="btn btn-secondary w-100">
                                    <i class="fas fa-undo"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Reviews List -->
                <div class="row">
                    <?php if (mysqli_num_rows($reviews_result) > 0): ?>
                        <?php while ($review = mysqli_fetch_assoc($reviews_result)): ?>
                            <div class="col-md-6">
                                <div class="card review-card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div>
                                                <h5 class="company-name mb-1">
                                                    <?= htmlspecialchars($review['name']) ?>
                                                </h5>
                                                <div class="rating mb-2">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <i class="fas fa-star<?= ($i <= $review['rating']) ? '' : '-half-alt' ?>"></i>
                                                    <?php endfor; ?>
                                                    <span class="text-muted ms-2">(<?= $review['rating'] ?>/5)</span>
                                                </div>
                                            </div>
                                            <div class="text-muted small">
                                                <?= date('M d, Y', strtotime($review['date'])) ?>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <p class="card-text"><?= nl2br(htmlspecialchars($review[''])) ?></p>
                                        </div>
                                        
                                        <div class="student-info">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong><?= htmlspecialchars($review['student_name']) ?></strong>
                                                    <div class="text-muted small"><?= htmlspecialchars($review['admno']) ?></div>
                                                </div>
                                                <form method="POST" action="" class="delete-form">
                                                    <input type="hidden" name="review_id" value="<?= $review['review_id'] ?>">
                                                    <button type="submit" name="delete_review" class="btn btn-sm btn-danger" 
                                                            onclick="return confirm('Are you sure you want to delete this review?')">
                                                        <i class="fas fa-trash-alt"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="empty-reviews">
                            <i class="fas fa-comment-slash fa-4x mb-3"></i>
                            <h4>No reviews found</h4>
                            <p class="text-muted">There are no reviews matching your search criteria</p>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>comment
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show success/error messages
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

            <?php if (isset($_SESSION['errors'])): ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: '<?= implode("<br>", $_SESSION['errors']) ?>',
                    timer: 3000,
                    toast: true,
                    position: 'top-end'
                });
                <?php unset($_SESSION['errors']); ?>
            <?php endif; ?>

            // Confirm before deleting
            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>

<?php
ob_end_flush();
?>