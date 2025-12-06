<?php
require_once 'config.php';

// Handle blood request submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_request'])) {
    $patient_name = sanitize_input($_POST['patient_name']);
    $blood_group = sanitize_input($_POST['blood_group']);
    $units_needed = sanitize_input($_POST['units_needed']);
    $hospital_name = sanitize_input($_POST['hospital_name']);
    $city = sanitize_input($_POST['city']);
    $contact_person = sanitize_input($_POST['contact_person']);
    $phone = sanitize_input($_POST['phone']);
    $urgency = sanitize_input($_POST['urgency']);
    $notes = sanitize_input($_POST['notes']);
    
    $sql = "INSERT INTO blood_requests (patient_name, blood_group, units_needed, hospital_name, city, contact_person, phone, urgency, notes) 
            VALUES ('$patient_name', '$blood_group', '$units_needed', '$hospital_name', '$city', '$contact_person', '$phone', '$urgency', '$notes')";
    
    if (mysqli_query($conn, $sql)) {
        $success_message = "Blood request submitted successfully! We will contact you soon.";
    } else {
        $error_message = "Error: " . mysqli_error($conn);
    }
}

// Fetch recent blood requests
$requests_sql = "SELECT * FROM blood_requests WHERE status = 'Pending' ORDER BY requested_date DESC LIMIT 10";
$requests_result = mysqli_query($conn, $requests_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Request - Blood Donor System</title>
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

        .main-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
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
            min-height: 100px;
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

        .request-card {
            background: #f8f9fa;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 8px;
            border-left: 5px solid #e74c3c;
        }

        .request-card h3 {
            color: #333;
            margin-bottom: 10px;
        }

        .request-info {
            color: #666;
            line-height: 1.8;
        }

        .blood-badge {
            display: inline-block;
            background: #e74c3c;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            margin-right: 10px;
        }

        .urgency-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: bold;
        }

        .urgency-critical {
            background: #e74c3c;
            color: white;
        }

        .urgency-urgent {
            background: #f39c12;
            color: white;
        }

        .urgency-normal {
            background: #3498db;
            color: white;
        }

        .request-list {
            max-height: 600px;
            overflow-y: auto;
        }

        .no-requests {
            text-align: center;
            color: #999;
            padding: 40px;
            font-style: italic;
        }

        .info-box {
            background: #fff3cd;
            border-left: 5px solid #ffc107;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .info-box h3 {
            color: #856404;
            margin-bottom: 10px;
        }

        .info-box p {
            color: #856404;
            line-height: 1.6;
        }

        @media (max-width: 1024px) {
            .main-content {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>🆘 Blood Request</h1>
            <p class="tagline">Request Blood for Emergency</p>
        </header>

        <div class="nav-menu">
            <a href="index.php">🏠 Home</a>
            <a href="blood_request.php">🆘 Request Blood</a>
            <a href="admin.php">⚙️ Admin Panel</a>
             <a href="my_donations.php">📋 My Donations</a>
            <a href="emergency_alerts.php">🚨 Emergency Alerts</a>
        </div>

        <div class="main-content">
            <!-- Request Form -->
            <div class="card">
                <h2>Submit Blood Request</h2>

                <div class="info-box">
                    <h3>ℹ️ Important Information</h3>
                    <p>Fill out this form to request blood. Our team will contact available donors and get back to you as soon as possible.</p>
                </div>
                
                <?php if(isset($success_message)): ?>
                    <div class="success-message"><?php echo $success_message; ?></div>
                <?php endif; ?>
                
                <?php if(isset($error_message)): ?>
                    <div class="error-message"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="patient_name">Patient Name *</label>
                        <input type="text" id="patient_name" name="patient_name" required>
                    </div>

                    <div class="form-group">
                        <label for="blood_group">Blood Group Required *</label>
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
                        <label for="units_needed">Units Needed *</label>
                        <input type="number" id="units_needed" name="units_needed" min="1" required>
                    </div>

                    <div class="form-group">
                        <label for="hospital_name">Hospital Name *</label>
                        <input type="text" id="hospital_name" name="hospital_name" required>
                    </div>

                    <div class="form-group">
                        <label for="city">City *</label>
                        <input type="text" id="city" name="city" required>
                    </div>

                    <div class="form-group">
                        <label for="contact_person">Contact Person *</label>
                        <input type="text" id="contact_person" name="contact_person" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number *</label>
                        <input type="tel" id="phone" name="phone" required>
                    </div>

                    <div class="form-group">
                        <label for="urgency">Urgency Level *</label>
                        <select id="urgency" name="urgency" required>
                            <option value="Normal">Normal</option>
                            <option value="Urgent">Urgent</option>
                            <option value="Critical">Critical</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="notes">Additional Notes</label>
                        <textarea id="notes" name="notes" placeholder="Any additional information..."></textarea>
                    </div>

                    <button type="submit" name="submit_request">Submit Request</button>
                </form>
            </div>

            <!-- Recent Requests -->
            <div class="card">
                <h2>Recent Blood Requests</h2>

                <div class="request-list">
                    <?php if($requests_result && mysqli_num_rows($requests_result) > 0): ?>
                        <?php while($request = mysqli_fetch_assoc($requests_result)): ?>
                            <div class="request-card">
                                <h3><?php echo htmlspecialchars($request['patient_name']); ?></h3>
                                <div class="request-info">
                                    <span class="blood-badge"><?php echo $request['blood_group']; ?></span>
                                    <span class="urgency-badge urgency-<?php echo strtolower($request['urgency']); ?>">
                                        <?php echo $request['urgency']; ?>
                                    </span>
                                    <br><br>
                                    <strong>Units Needed:</strong> <?php echo $request['units_needed']; ?><br>
                                    <strong>Hospital:</strong> <?php echo htmlspecialchars($request['hospital_name']); ?><br>
                                    <strong>City:</strong> <?php echo htmlspecialchars($request['city']); ?><br>
                                    <strong>Contact:</strong> <?php echo htmlspecialchars($request['contact_person']); ?> - <?php echo htmlspecialchars($request['phone']); ?><br>
                                    <?php if(!empty($request['notes'])): ?>
                                        <strong>Notes:</strong> <?php echo htmlspecialchars($request['notes']); ?><br>
                                    <?php endif; ?>
                                    <strong>Requested:</strong> <?php echo format_date($request['requested_date']); ?>

                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="no-requests">No pending blood requests.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>