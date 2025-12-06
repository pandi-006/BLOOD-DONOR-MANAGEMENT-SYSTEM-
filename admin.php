<?php
require_once 'config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    // Handle login
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
        $username = sanitize_input($_POST['username']);
        $password = $_POST['password'];
        
        $sql = "SELECT * FROM admin_users WHERE username = '$username'";
        $result = mysqli_query($conn, $sql);
        
        if ($result && mysqli_num_rows($result) == 1) {
            $admin = mysqli_fetch_assoc($result);
            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $username;
                header("Location: admin.php");
                exit();
            } else {
                $login_error = "Invalid username or password!";
            }
        } else {
            $login_error = "Invalid username or password!";
        }
    }
    
    // Show login form
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Login - Blood Donor System</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 20px;
            }
            .login-container {
                background: white;
                padding: 40px;
                border-radius: 15px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.3);
                width: 100%;
                max-width: 400px;
            }
            h1 {
                color: #e74c3c;
                text-align: center;
                margin-bottom: 30px;
            }
            .form-group {
                margin-bottom: 20px;
            }
            label {
                display: block;
                margin-bottom: 8px;
                color: #333;
                font-weight: 600;
            }
            input {
                width: 100%;
                padding: 12px;
                border: 2px solid #ddd;
                border-radius: 8px;
                font-size: 1em;
            }
            input:focus {
                outline: none;
                border-color: #e74c3c;
            }
            button {
                background: #e74c3c;
                color: white;
                padding: 12px 30px;
                border: none;
                border-radius: 8px;
                font-size: 1.1em;
                cursor: pointer;
                width: 100%;
                transition: background 0.3s;
            }
            button:hover {
                background: #c0392b;
            }
            .error-message {
                background: #e74c3c;
                color: white;
                padding: 12px;
                border-radius: 8px;
                margin-bottom: 20px;
                text-align: center;
            }
            .back-link {
                text-align: center;
                margin-top: 20px;
            }
            .back-link a {
                color: #e74c3c;
                text-decoration: none;
            }
        </style>
    </head>
    <body>
        <div class="login-container">
            <h1>🔐 Admin Login</h1>
            <?php if(isset($login_error)): ?>
                <div class="error-message"><?php echo $login_error; ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" name="login">Login</button>
            </form>
            <div class="back-link">
                <a href="index.php">← Back to Home</a>
            </div>
            <p style="text-align: center; color: #666; margin-top: 20px; font-size: 0.9em;">
                Default Login: admin / admin123
            </p>
        </div>
    </body>
    </html>
    <?php
    exit();
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit();
}

// Handle delete donor
if (isset($_GET['delete_donor'])) {
    $donor_id = sanitize_input($_GET['delete_donor']);
    $sql = "DELETE FROM donors WHERE donor_id = '$donor_id'";
    if (mysqli_query($conn, $sql)) {
        $success_message = "Donor deleted successfully!";
    }
}

// Handle delete request
if (isset($_GET['delete_request'])) {
    $request_id = sanitize_input($_GET['delete_request']);
    $sql = "DELETE FROM blood_requests WHERE id = '$request_id'";
    if (mysqli_query($conn, $sql)) {
        $success_message = "Request deleted successfully!";
    }
}

// Handle update request status
if (isset($_GET['update_status'])) {
    $request_id = sanitize_input($_GET['update_status']);
    $status = sanitize_input($_GET['status']);
    $sql = "UPDATE blood_requests SET status = '$status' WHERE id = '$request_id'";
    if (mysqli_query($conn, $sql)) {
        $success_message = "Request status updated!";
    }
}
// Handle delete organ donor
if (isset($_GET['delete_organ_donor'])) {
    $donation_id = sanitize_input($_GET['delete_organ_donor']);
    $sql = "DELETE FROM organs_donation WHERE Donation_ID = '$donation_id'";
    if (mysqli_query($conn, $sql)) {
        $success_message = "Organ donor deleted successfully!";
    }
}

// Handle update organ donation status
if (isset($_GET['update_organ_status'])) {
    $donation_id = sanitize_input($_GET['update_organ_status']);
    $status = sanitize_input($_GET['organ_status']);
    $sql = "UPDATE organs_donation SET Donation_Status = '$status' WHERE Donation_ID = '$donation_id'";
    if (mysqli_query($conn, $sql)) {
        $success_message = "Organ donation status updated!";
    }
}

// Get statistics with error checking
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM donors");
$total_donors = ($result && mysqli_num_rows($result) > 0) ? mysqli_fetch_assoc($result)['count'] : 0;

$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM blood_requests");
$total_requests = ($result && mysqli_num_rows($result) > 0) ? mysqli_fetch_assoc($result)['count'] : 0;

$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM blood_requests WHERE status='Pending'");
$pending_requests = ($result && mysqli_num_rows($result) > 0) ? mysqli_fetch_assoc($result)['count'] : 0;

$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM donors WHERE is_available=1");
$available_donors = ($result && mysqli_num_rows($result) > 0) ? mysqli_fetch_assoc($result)['count'] : 0;
// Organ donation statistics
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM organs_donation");
$total_organ_donors = ($result && mysqli_num_rows($result) > 0) ? mysqli_fetch_assoc($result)['count'] : 0;

$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM organs_donation WHERE Donation_Status='Pending'");
$pending_organ_donations = ($result && mysqli_num_rows($result) > 0) ? mysqli_fetch_assoc($result)['count'] : 0;

// Get all donors with calculated age
$donors_result = mysqli_query($conn, "SELECT *, TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) AS age FROM donors ORDER BY created_at DESC");

// Get all requests
$requests_result = mysqli_query($conn, "SELECT * FROM blood_requests ORDER BY requested_date DESC");
// Get all organ donors
$organ_donors_result = mysqli_query($conn, "SELECT * FROM organs_donation ORDER BY registration_date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Blood Donor System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        header {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-left h1 {
            color: #e74c3c;
            font-size: 2em;
            margin-bottom: 5px;
        }

        .header-left p {
            color: #666;
        }

        .logout-btn {
            background: #e74c3c;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: background 0.3s;
        }

        .logout-btn:hover {
            background: #c0392b;
        }

        .nav-menu {
            background: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .nav-menu a {
            padding: 10px 20px;
            background: #e74c3c;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .nav-menu a:hover {
            background: #c0392b;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .stat-number {
            font-size: 2.5em;
            color: #e74c3c;
            font-weight: bold;
        }

        .stat-label {
            color: #666;
            margin-top: 5px;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin-bottom: 30px;
        }

        h2 {
            color: #e74c3c;
            margin-bottom: 20px;
            border-bottom: 3px solid #e74c3c;
            padding-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background: #f8f9fa;
            color: #333;
            font-weight: 600;
        }

        .blood-badge {
            display: inline-block;
            background: #e74c3c;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.9em;
            font-weight: bold;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.85em;
            font-weight: bold;
        }

        .status-pending {
            background: #f39c12;
            color: white;
        }

        .status-fulfilled {
            background: #2ecc71;
            color: white;
        }

        .status-cancelled {
            background: #95a5a6;
            color: white;
        }

        .action-btn {
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.9em;
            margin-right: 5px;
            display: inline-block;
        }

        .btn-delete {
            background: #e74c3c;
            color: white;
        }

        .btn-fulfill {
            background: #2ecc71;
            color: white;
        }

        .btn-cancel {
            background: #95a5a6;
            color: white;
        }

        .success-message {
            background: #2ecc71;
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        .table-container {
            overflow-x: auto;
        }

        @media (max-width: 768px) {
            header {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="header-left">
                <h1>⚙️ Admin Dashboard</h1>
                <p>Welcome, <?php echo $_SESSION['admin_username']; ?>!</p>
            </div>
            <a href="admin.php?logout=1" class="logout-btn">🚪 Logout</a>
        </header>

        <div class="nav-menu">
            <a href="index.php">🏠 Home</a>
            <a href="blood_request.php">🆘 Request Blood</a>
            <a href="admin.php">⚙️ Admin Panel</a>
             <a href="my_donations.php">📋 My Donations</a>
             <a href="emergency_alerts.php">🚨 Emergency Alerts</a>
        </div>

        <?php if(isset($success_message)): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_donors; ?></div>
                <div class="stat-label">Total Donors</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $available_donors; ?></div>
                <div class="stat-label">Available Donors</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_requests; ?></div>
                <div class="stat-label">Total Requests</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $pending_requests; ?></div>
                <div class="stat-label">Pending Requests</div>
            </div>
        </div>
<div class="stat-card">
    <div class="stat-number"><?php echo $total_organ_donors; ?></div>
    <div class="stat-label">Total Organ Donors</div>
</div>
<div class="stat-card">
    <div class="stat-number"><?php echo $pending_organ_donations; ?></div>
    <div class="stat-label">Pending Organ Donations</div>
</div>

        <div class="card">
            <h2>Manage Donors</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Blood Group</th>
                            <th>Phone</th>
                            <th>City</th>
                            <th>Age</th>
                            <th>Registered</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($donors_result && mysqli_num_rows($donors_result) > 0): ?>
                            <?php while($donor = mysqli_fetch_assoc($donors_result)): ?>
                                <tr>
                                    <td><?php echo $donor['donor_id']; ?></td>
                                    <td><?php echo htmlspecialchars($donor['full_name']); ?></td>
                                    <td><span class="blood-badge"><?php echo $donor['blood_group']; ?></span></td>
                                    <td><?php echo htmlspecialchars($donor['phone']); ?></td>
                                    <td><?php echo htmlspecialchars($donor['city']); ?></td>
                                    <td><?php echo $donor['age']; ?></td>
                                    <td><?php echo format_date($donor['created_at']); ?></td>
                                    <td>
                                        <a href="admin.php?delete_donor=<?php echo $donor['donor_id']; ?>" 
                                           class="action-btn btn-delete" 
                                           onclick="return confirm('Are you sure you want to delete this donor?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" style="text-align: center; color: #999;">No donors found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <h2>Manage Blood Requests</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Patient</th>
                            <th>Blood Group</th>
                            <th>Units</th>
                            <th>Hospital</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($requests_result && mysqli_num_rows($requests_result) > 0): ?>
                            <?php while($request = mysqli_fetch_assoc($requests_result)): ?>
                                <tr>
                                 <td><?php echo$request['request_id']; ?></td>
                                    <td><?php echo htmlspecialchars($request['patient_name']); ?></td>
                                    <td><span class="blood-badge"><?php echo $request['blood_group']; ?></span></td>
                                    <td><?php echo $request['units_needed']; ?></td>
                                    <td><?php echo htmlspecialchars($request['hospital_name']); ?></td>
                                    <td><?php echo htmlspecialchars($request['phone']); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($request['status']); ?>">
                                            <?php echo $request['status']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if($request['status'] == 'Pending'): ?>
                                            <a href="&status=Fulfilled" 
                                               class="action-btn btn-fulfill">Fulfill</a>
                                            <a href="&status=Cancelled" 
                                               class="action-btn btn-cancel">Cancel</a>
                                        <?php endif; ?>
                                        <a href="admin.php?delete_request=<?php echo $request['request_id']; ?>" 
                                           class="action-btn btn-delete" 
                                           onclick="return confirm('Are you sure you want to delete this request?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" style="text-align: center; color: #999;">No blood requests found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
       <div class="card">
    <h2>Manage Organ Donors</h2>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Donor Name</th>
                    <th>Organ Type</th>
                    <th>Age</th>
                    <th>Blood Group</th>
                    <th>City</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Registered</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if($organ_donors_result && mysqli_num_rows($organ_donors_result) > 0): ?>
                    <?php while($organ_donor = mysqli_fetch_assoc($organ_donors_result)): ?>
                        <tr>
                            <td><?php echo $organ_donor['Donation_ID']; ?></td>
                            <td><?php echo htmlspecialchars($organ_donor['donor_name']); ?></td>
                            <td><span class="blood-badge" style="background: #4caf50;"><?php echo $organ_donor['Organ_Type']; ?></span></td>
                            <td><?php echo $organ_donor['donor_age']; ?></td>
                            <td><?php echo $organ_donor['donor_blood_group']; ?></td>
                            <td><?php echo htmlspecialchars($organ_donor['donor_city']); ?></td>
                            <td><?php echo htmlspecialchars($organ_donor['donor_phone']); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo strtolower($organ_donor['Donation_Status']); ?>">
                                    <?php echo $organ_donor['Donation_Status']; ?>
                                </span>
                            </td>
                            <td><?php echo format_date($organ_donor['registration_date']); ?></td>
                            <td>
                                <?php if($organ_donor['Donation_Status'] == 'Pending'): ?>
                                    <a href="admin.php?update_organ_status=<?php echo $organ_donor['Donation_ID']; ?>&organ_status=Approved" 
                                       class="action-btn btn-fulfill">Approve</a>
                                    <a href="admin.php?update_organ_status=<?php echo $organ_donor['Donation_ID']; ?>&organ_status=Rejected" 
                                       class="action-btn btn-cancel">Reject</a>
                                <?php endif; ?>
                                <a href="admin.php?delete_organ_donor=<?php echo $organ_donor['Donation_ID']; ?>" 
                                   class="action-btn btn-delete" 
                                   onclick="return confirm('Are you sure you want to delete this organ donor?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" style="text-align: center; color: #999;">No organ donors found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    </div>
    </div>
</body>
</html>