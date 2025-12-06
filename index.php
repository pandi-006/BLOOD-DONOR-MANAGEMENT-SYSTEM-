<?php
require_once 'config.php';

// Handle donor registration
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register_donor'])) {
    $name = sanitize_input($_POST['full_name']);
    $blood_group = sanitize_input($_POST['blood_group']);
    $phone = sanitize_input($_POST['phone']);
    $email = sanitize_input($_POST['email']);
    $city = sanitize_input($_POST['city']);
    $state = sanitize_input($_POST['state']);
    $pincode = sanitize_input($_POST['pincode']);
    $dob = sanitize_input($_POST['date_of_birth']);
    $gender = sanitize_input($_POST['gender']);
    $address = sanitize_input($_POST['address']);
    
    $sql = "INSERT INTO donors (full_name, blood_group, phone, email, city, state, pincode, date_of_birth, gender, address) 
            VALUES ('$name', '$blood_group', '$phone', '$email', '$city', '$state', '$pincode', '$dob', '$gender', '$address')";
    
    if (mysqli_query($conn, $sql)) {
        $success_message = "Donor registered successfully!";
    } else {
        $error_message = "Error: " . mysqli_error($conn);
    }
}

// Get search parameters
$search_blood = isset($_GET['search_blood']) ? sanitize_input($_GET['search_blood']) : '';
$search_city = isset($_GET['search_city']) ? sanitize_input($_GET['search_city']) : '';

// Fetch donors
$sql = "SELECT *, TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) AS age FROM donors WHERE 1=1";
if (!empty($search_blood)) {
    $sql .= " AND blood_group = '$search_blood'";
}
if (!empty($search_city)) {
    $sql .= " AND city LIKE '%$search_city%'";
}
$sql .= " ORDER BY created_at DESC";
$donors_result = mysqli_query($conn, $sql);

// Check if query failed
if (!$donors_result) {
    $donors_result = false;
}

// Get statistics
$stats_sql = "SELECT 
    COUNT(*) as total_donors,
    COUNT(CASE WHEN is_available = 1 THEN 1 END) as available_donors
    FROM donors";
$stats_result = mysqli_query($conn, $stats_sql);

// Check if query was successful
if ($stats_result) {
    $stats = mysqli_fetch_assoc($stats_result);
} else {
    // Default values if query fails
    $stats = array('total_donors' => 0, 'available_donors' => 0);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Donor Management System</title>
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
            text-align: center;
            margin-bottom: 30px;
        }

        h1 {
            color: #e74c3c;
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .tagline {
            color: #666;
            font-size: 1.1em;
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

        .main-content {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        h2 {
            color: #e74c3c;
            margin-bottom: 20px;
            border-bottom: 3px solid #e74c3c;
            padding-bottom: 10px;
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

        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1em;
            transition: border-color 0.3s;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        input:focus, select:focus, textarea:focus {
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
            transition: background 0.3s;
            width: 100%;
        }

        button:hover {
            background: #c0392b;
        }

        .success-message {
            background: #2ecc71;
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        .error-message {
            background: #e74c3c;
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        .search-bar {
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 15px;
            margin-bottom: 20px;
        }

        .donor-list {
            max-height: 600px;
            overflow-y: auto;
        }

        .donor-card {
            background: #f8f9fa;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 8px;
            border-left: 5px solid #e74c3c;
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 20px;
        }

        .blood-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 80px;
            height: 80px;
            background: #e74c3c;
            color: white;
            border-radius: 50%;
            font-size: 1.5em;
            font-weight: bold;
        }

        .donor-details h3 {
            color: #333;
            margin-bottom: 10px;
        }

        .donor-info {
            color: #666;
            line-height: 1.8;
        }

        .donor-info span {
            display: inline-block;
            margin-right: 15px;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: bold;
        }

        .status-available {
            background: #2ecc71;
            color: white;
        }

        .status-unavailable {
            background: #95a5a6;
            color: white;
        }

        .no-donors {
            text-align: center;
            color: #999;
            padding: 40px;
            font-style: italic;
        }

        @media (max-width: 1024px) {
            .main-content {
                grid-template-columns: 1fr;
            }
            .search-bar {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>🩸 Blood Donor Management System</h1>
            <p class="tagline">Save Lives by Donating Blood</p>
        </header>

        <div class="nav-menu">
            <a href="index.php">🩸 Blood Donation</a>
            <a href="organ_registration.php">💚 Organ Donation</a>
            <a href="blood_request.php">🆘 Request Blood</a>
            <a href="emergency_alerts.php">🚨 Emergency Alerts</a>
            <a href="my_donations.php">📋 My Donations</a>   
            <a href="admin.php">⚙️ Admin Panel</a>
        </div>

        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total_donors']; ?></div>
                <div class="stat-label">Total Donors</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['available_donors']; ?></div>
                <div class="stat-label">Available Donors</div>
            </div>
        </div>

        <div class="main-content">
            <!-- Registration Form -->
            <div class="card">
                <h2>Register as Donor</h2>
                
                <?php if(isset($success_message)): ?>
                    <div class="success-message"><?php echo $success_message; ?></div>
                <?php endif; ?>
                
                <?php if(isset($error_message)): ?>
                    <div class="error-message"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="full_name">Full Name *</label>
                        <input type="text" id="full_name" name="full_name" required>
                    </div>

                    <div class="form-group">
                        <label for="blood_group">Blood Group *</label>
                        <select id="blood_group" name="blood_group" required>
                            <option value="">Select Blood Group</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="date_of_birth">Date of Birth *</label>
                        <input type="date" id="date_of_birth" name="date_of_birth" max="<?php echo date('Y-m-d', strtotime('-18 years')); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="gender">Gender *</label>
                        <select id="gender" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number *</label>
                        <input type="tel" id="phone" name="phone" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email">
                    </div>

                    <div class="form-group">
                        <label for="city">City *</label>
                        <input type="text" id="city" name="city" required>
                    </div>

                    <div class="form-group">
                        <label for="state">State *</label>
                        <input type="text" id="state" name="state" required>
                    </div>

                    <div class="form-group">
                        <label for="pincode">Pincode *</label>
                        <input type="text" id="pincode" name="pincode" pattern="[0-9]{6}" maxlength="6" required>
                    </div>

                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea id="address" name="address"></textarea>
                    </div>

                    <button type="submit" name="register_donor">Register Donor</button>
                </form>
            </div>

            <!-- Donor List -->
            <div class="card">
                <h2>Registered Donors</h2>
                
                <form method="GET" action="" class="search-bar">
                    <div class="form-group" style="margin-bottom: 0;">
                        <select name="search_blood" onchange="this.form.submit()">
                            <option value="">All Blood Groups</option>
                            <option value="A+" <?php echo $search_blood == 'A+' ? 'selected' : ''; ?>>A+</option>
                            <option value="A-" <?php echo $search_blood == 'A-' ? 'selected' : ''; ?>>A-</option>
                            <option value="B+" <?php echo $search_blood == 'B+' ? 'selected' : ''; ?>>B+</option>
                            <option value="B-" <?php echo $search_blood == 'B-' ? 'selected' : ''; ?>>B-</option>
                            <option value="AB+" <?php echo $search_blood == 'AB+' ? 'selected' : ''; ?>>AB+</option>
                            <option value="AB-" <?php echo $search_blood == 'AB-' ? 'selected' : ''; ?>>AB-</option>
                            <option value="O+" <?php echo $search_blood == 'O+' ? 'selected' : ''; ?>>O+</option>
                            <option value="O-" <?php echo $search_blood == 'O-' ? 'selected' : ''; ?>>O-</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <input type="text" name="search_city" placeholder="Search by City" value="<?php echo $search_city; ?>">
                    </div>
                    <button type="submit">🔍 Search</button>
                </form>

                <div class="donor-list">
                    <?php if($donors_result && mysqli_num_rows($donors_result) > 0): ?>
                        <?php while($donor = mysqli_fetch_assoc($donors_result)): ?>
                            <div class="donor-card">
                                <div class="blood-badge"><?php echo $donor['blood_group']; ?></div>
                                <div class="donor-details">
                                    <h3><?php echo htmlspecialchars($donor['full_name']); ?></h3>
                                    <div class="donor-info">
                                        <span>📞 <?php echo htmlspecialchars($donor['phone']); ?></span>
                                        <?php if(!empty($donor['email'])): ?>
                                            <span>📧 <?php echo htmlspecialchars($donor['email']); ?></span>
                                        <?php endif; ?>
                                        <br>
                                        <span>📍 <?php echo htmlspecialchars($donor['city']); ?>, <?php echo htmlspecialchars($donor['state']); ?></span>
                                        <span>👤 Age: <?php echo $donor['age']; ?></span>
                                        <span>🚻 <?php echo htmlspecialchars($donor['gender']); ?></span>
                                        <span class="status-badge <?php echo $donor['is_available'] == 1 ? 'status-available' : 'status-unavailable'; ?>">
                                            <?php echo $donor['is_available'] == 1 ? '✓ Available' : '✗ Not Available'; ?>
                                        </span>
                                        <br>
                                        <span>📅 Registered: <?php echo format_date($donor['created_at']); ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="no-donors">No donors found. Be the first to register!</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>