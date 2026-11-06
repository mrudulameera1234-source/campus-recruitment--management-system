<?php
ob_start();
include("connection.php");
include("headers/admin_header.php");

// Handle drive status updates
if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
   
         echo "<script>alert('inside post');</script>";
        $drive_id = mysqli_real_escape_string($conn, $_POST['drive_id']);
        $status = mysqli_real_escape_string($conn, $_POST['status']);
        $admin_note = mysqli_real_escape_string($conn, $_POST['admin_note'] ?? '');

        $update_query = "UPDATE tbl_drive SET status = '$status', admin_note = '$admin_note' 
                     WHERE drive_id = '$drive_id'";
        
        if (mysqli_query($conn, $update_query)) {
            $_SESSION['success'] = "Drive status updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating drive status: " . mysqli_error($conn);
        }
        
        header("Location: admin_manage_drives.php");
        exit();
    }


// Fetch all drives with company names
$query = "SELECT d.*, c.name as company_name 
          FROM tbl_drive d
          JOIN tbl_company c ON d.company_id = c.company_id
          ORDER BY d.prefered_date DESC, d.created_at DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Drive Schedules</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .status-pending { color: #ffc107; font-weight: bold; }
        .status-approved { color: #28a745; font-weight: bold; }
        .status-rejected { color: #dc3545; font-weight: bold; }
        .card { margin-bottom: 20px; }
        .drive-details { margin-bottom: 15px; }
        .action-btns { margin-top: 10px; }
        .notes-section { margin-top: 15px; }
        .badge { font-size: 0.9em; }
        .form-group { margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <main class="col-md-15 ms-sm-auto col-lg-16 px-md-9">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Drive Schedules Management</h1>
                </div>

                <!-- Status Messages -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= $_SESSION['success']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $_SESSION['error']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <!-- Drives List -->
                <div class="row">
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($drive = mysqli_fetch_assoc($result)): ?>
                            <div class="col-md-9">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0"><?= htmlspecialchars($drive['company_name']) ?></h5>
                                        <span class="badge bg-<?= 
                                            $drive['status'] == 'approved' ? 'success' : 
                                            ($drive['status'] == 'rejected' ? 'danger' : 'warning') ?>">
                                            <?= ucfirst($drive['status']) ?>
                                        </span>
                                    </div>
                                    <div class="card-body">
                                        <div class="drive-details">
                                            <p><strong>Preferred Date:</strong> <?= date('F j, Y', strtotime($drive['prefered_date'])) ?></p>
                                            <p><strong>Description:</strong> <?= htmlspecialchars($drive['description']) ?></p>
                                            <p><strong>Submitted On:</strong> <?= date('F j, Y H:i', strtotime($drive['created_at'])) ?></p>
                                            <?php if (!empty($drive['admin_note'])): ?>
                                                <div class="notes-section">
                                                    <p><strong>Admin Note:</strong> <?= htmlspecialchars($drive['admin_note']) ?></p>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Status Update Form -->
                                        <form method="post" id="form" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                                            <input type="hidden" name="drive_id" value="<?= $drive['drive_id'] ?>">
                                            
                                            <div class="form-group">
                                                <label for="admin_note_<?= $drive['drive_id'] ?>">Admin Note:</label>
                                                <textarea class="form-control" id="admin_note_<?= $drive['drive_id'] ?>" 
                                                          name="admin_note" rows="2"></textarea>
                                            </div>
                                            
                                            <div class="action-btns">
                                              
                                                  <?php if($drive['status']=='pending'){
                                                    ?>
                                                 
                                                    <button type="submit" name="update_status" value="approve" 
                                                            class="btn btn-success me-2" onclick="setStatus('approved')">
                                                        <i class="fas fa-check"></i> Approve
                                            </button>
                                           <?php
                                                  }
                                                  ?>
                                                
                                             
                                                     <?php if($drive['status']=='pending'){
                                                    ?>
                                                    <button type="submit" name="update_status" value="reject" 
                                                            class="btn btn-danger me-2" onclick="setStatus('rejected')">
                                                        <i class="fas fa-times"></i> Reject
                                                    </button>
                                         
                                                 <?php
                                                  }
                                                  ?>
                                                <input type="hidden" name="status" id="status_<?= $drive['drive_id'] ?>" value="">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="alert alert-info">No drive schedules found.</div>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function setStatus(status) {
            // Find the closest form and set the status value
            event.preventDefault();
            const form = event.target.closest('form');
            const statusInput = form.querySelector('input[name="status"]');
            statusInput.value = status;
            
            // Submit the form
            form.submit();
        }
    </script>
</body>
</html>