<?php
ob_start();
session_start();
include("headers/admin_header.php");
include("connection.php");

// Handle approval/rejection
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['approve'])) {
        $company_id = $_POST['company_id'];
        $stmt = $conn->prepare("UPDATE tbl_company SET status='approved' WHERE company_id=?");
        $stmt->bind_param("i", $company_id);
        $stmt->execute();
        $_SESSION['message'] = "Company approved successfully!";
    } 
    elseif (isset($_POST['reject'])) {
        $company_id = $_POST['company_id'];
        $stmt = $conn->prepare("UPDATE tbl_company SET status='rejected' WHERE company_id=?");
        $stmt->bind_param("i", $company_id);
        $stmt->execute();
        $_SESSION['message'] = "Company rejected successfully!";
    }
    header('Location: admin_manage_company.php');
    exit;
}

// Fetch pending companies
$pending_query = "SELECT * FROM tbl_company WHERE status='pending' ORDER BY created_at DESC";
$pending_result = $conn->query($pending_query);

// Fetch approved companies
$approved_query = "SELECT company_id, name, regno, link, description, location, phno, created_at FROM tbl_company WHERE status='approved' ORDER BY created_at DESC";
$approved_result = $conn->query($approved_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Companies</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .nav-tabs .nav-link.active {
            font-weight: bold;
            background-color: #f8f9fa;
            border-bottom: 3px solid #0d6efd;
        }
        .nav-tabs .nav-link {
            color: #495057;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2 class="mb-4"><i class="fas fa-building"></i> Manage Companies</h2>
        
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-info"><?= $_SESSION['message'] ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
        
        <ul class="nav nav-tabs" id="companyTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                    <i class="fas fa-clock"></i> Pending Approval
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="approved-tab" data-bs-toggle="tab" data-bs-target="#approved" type="button" role="tab">
                    <i class="fas fa-check-circle"></i> Approved Companies
                </button>
            </li>
        </ul>
        
        <div class="tab-content p-3 border border-top-0 rounded-bottom">
            <!-- Pending Companies Tab -->
            <div class="tab-pane fade show active" id="pending" role="tabpanel">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <i class="fas fa-clock"></i> Pending Company Registrations
                    </div>
                    <div class="card-body">
                        <?php if ($pending_result->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Company Name</th>
                                            <th>Registration No</th>
                                            <th>Website</th>
                                            <th>Description</th>
                                            <th>Location</th>
                                            <th>Phone</th>
                                            <th>Registration Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($company = $pending_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($company['company_id']) ?></td>
                                            <td><?= htmlspecialchars($company['name']) ?></td>
                                            <td><?= htmlspecialchars($company['regno']) ?></td>
                                            <td><a href="<?= htmlspecialchars($company['link']) ?>" target="_blank">Visit</a></td>
                                            <td><?= htmlspecialchars(substr($company['description'], 0, 50)) ?>...</td>
                                            <td><?= htmlspecialchars($company['location']) ?></td>
                                            <td><?= htmlspecialchars($company['phno']) ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($company['created_at'])) ?></td>
                                            <td>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="company_id" value="<?= $company['company_id'] ?>">
                                                    <button type="submit" name="approve" class="btn btn-success btn-sm">
                                                        <i class="fas fa-check"></i> Approve
                                                    </button>
                                                </form>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="company_id" value="<?= $company['company_id'] ?>">
                                                    <button type="submit" name="reject" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-times"></i> Reject
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">No pending company registrations.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Approved Companies Tab -->
            <div class="tab-pane fade" id="approved" role="tabpanel">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-check-circle"></i> Approved Companies
                    </div>
                    <div class="card-body">
                        <?php if ($approved_result->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Company Name</th>
                                            <th>Registration No</th>
                                            <th>Website</th>
                                            <th>Description</th>
                                            <th>Location</th>
                                            <th>Phone</th>
                                            <th>Registration Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($company = $approved_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($company['company_id']) ?></td>
                                            <td><?= htmlspecialchars($company['name']) ?></td>
                                            <td><?= htmlspecialchars($company['regno']) ?></td>
                                            <td><a href="<?= htmlspecialchars($company['link']) ?>" target="_blank">Visit</a></td>
                                            <td><?= htmlspecialchars(substr($company['description'], 0, 50)) ?>...</td>
                                            <td><?= htmlspecialchars($company['location']) ?></td>
                                            <td><?= htmlspecialchars($company['phno']) ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($company['created_at'])) ?></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">No approved companies yet.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Activate the tab from URL hash if present
        document.addEventListener('DOMContentLoaded', function() {
            if (window.location.hash) {
                const tabTrigger = new bootstrap.Tab(document.querySelector(window.location.hash + '-tab'));
                tabTrigger.show();
            }
        });
    </script>
</body>
<?php
ob_end_flush();
?>
</html>