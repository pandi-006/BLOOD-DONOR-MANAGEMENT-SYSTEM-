<?php
require_once 'config.php';

// Handle organ donor registration
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register_organ_donor'])) {
    $donor_name = sanitize_input($_POST['donor_name']);
    $donor_phone = sanitize_input($_POST['donor_phone']);
    $donor_email = sanitize_input($_POST['donor_email']);
    $donor_age = sanitize_input($_POST['donor_age']);
    $donor_blood_group = sanitize_input($_POST['donor_blood_group']);
    $donor_city = sanitize_input($_POST['donor_city']);
    $organ_type = sanitize_input($_POST['organ_type']);
    $medical_eligibility = sanitize_input($_POST['medical_eligibility']);
    $next_of_kin_consent = sanitize_input($_POST['next_of_kin_consent']);
    $notes = sanitize_input($_POST['notes']);
    
    $sql = "INSERT INTO organs_donation (donor_name, donor_phone, donor_email, donor_age, donor_blood_group, 
            donor_city, Organ_Type, Donation_Status, Medical_Eligibility, Next_of_Kin_Consent, 
            Organ_Availability, Notes) 
            VALUES ('$donor_name', '$donor_phone', '$donor_email', '$donor_age', '$donor_blood_group', 
            '$donor_city', '$organ_type', 'Pending', '$medical_eligibility', '$next_of_kin_consent', 
            'Available', '$notes')";
    
    if (mysqli_query($conn, $sql)) {
        $success_message = "Thank you for registering as an organ donor! Your registration has been submitted successfully.";
    } else {
        $error_message = "Error: " . mysqli_error($conn);
    }
}

// Fetch recent organ donors
$donors_sql = "SELECT * FROM organs_donation ORDER BY registration_date DESC LIMIT 10";
$donors_result = mysqli_query($conn, $donors_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organ Donation Registration - Blood Donor System</title>
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

        .info-box {
            background: #e8f5e9;
            border-left: 5px solid #4caf50;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .info-box h3 {
            color: #2e7d32;
            margin-bottom: 10px;
        }

        .info-box p {
            color: #2e7d32;
            line-height: 1.6;
        }

        .donor-card {
            background: #f8f9fa;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            border-left: 5px solid #4caf50;
        }

        .donor-card h3 {
            color: #333;
            margin-bottom: 8px;
        }

        .donor-info {
            color: #666;
            line-height: 1.6;
            font-size: 0.95em;
        }

        .organ-badge {
            display: inline-block;
            background: #4caf50;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            margin-right: 10px;
            font-size: 0.9em;
        }

        .donor-list {
            max-height: 600px;
            overflow-y: auto;
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
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>💚 Organ Donation Registration</h1>
            <p class="tagline">Give the Gift of Life - Register as an Organ Donor</p>
        </header>

        <div class="nav-menu">
            <a href="index.php">🏠 Home</a>
            <a href="blood_request.php">🆘 Blood Request</a>
            <a href="organ_registration.php">💚 Organ Donation</a>
            <a href="admin.php">⚙️ Admin Panel</a>
             <a href="my_donations.php">📋 My Donations</a>
            <a href="emergency_alerts.php">🚨 Emergency Alerts</a>
        </div>

        <div class="main-content">
            <!-- Registration Form -->
            <div class="card">
                <h2>Register as Organ Donor</h2>

                <div class="info-box">
                    <h3>ℹ️ Important Information</h3>
                    <p>By registering as an organ donor, you can save multiple lives. Your decision to donate organs can give hope to those waiting for transplants.</p>
                </div>
                
                <?php if(isset($success_message)): ?>
                    <div class="success-message"><?php echo $success_message; ?></div>
                <?php endif; ?>
                
                <?php if(isset($error_message)): ?>
                    <div class="error-message"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="donor_name">Full Name *</label>
                        <input type="text" id="donor_name" name="donor_name" required>
                    </div>

                    <div class="form-group">
                        <label for="donor_phone">Phone Number *</label>
                        <input type="tel" id="donor_phone" name="donor_phone" required>
                    </div>

                    <div class="form-group">
                        <label for="donor_email">Email Address *</label>
                        <input type="email" id="donor_email" name="donor_email" required>
                    </div>

                    <div class="form-group">
                        <label for="donor_age">Age *</label>
                        <input type="number" id="donor_age" name="donor_age" min="18" max="65" required>
                    </div>

                    <div class="form-group">
                        <label for="donor_blood_group">Blood Group *</label>
                        <select id="donor_blood_group" name="donor_blood_group" required>
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
                        <label for="donor_city">City *</label>
                        <input type="text" id="donor_city" name="donor_city" required>
                    </div>

                    <div class="form-group">
                        <label for="organ_type">Organ to Donate *</label>
                        <select id="organ_type" name="organ_type" required>
                            <option value="">Select Organ</option>
                            <option value="Kidney">Kidney</option>
                            <option value="Liver">Liver</option>
                            <option value="Heart">Heart</option>
                            <option value="Lungs">Lungs</option>
                            <option value="Pancreas">Pancreas</option>
                            <option value="Cornea">Cornea</option>
                            <option value="Multiple">Multiple Organs</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="medical_eligibility">Medical Eligibility Status *</label>
                        <select id="medical_eligibility" name="medical_eligibility" required>
                            <option value="">Select Status</option>
                            <option value="Eligible">Medically Eligible</option>
                            <option value="Pending Review">Pending Medical Review</option>
                            <option value="Under Evaluation">Under Evaluation</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="next_of_kin_consent">Next of Kin Consent *</label>
                        <select id="next_of_kin_consent" name="next_of_kin_consent" required>
                            <option value="">Select</option>
                            <option value="Yes">Yes - Family Informed & Agreed</option>
                            <option value="Pending">Pending - Will Inform Family</option>
                            <option value="No">No - Not Yet Discussed</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="notes">Additional Notes</label>
                        <textarea id="notes" name="notes" placeholder="Any medical conditions, allergies, or additional information..."></textarea>
                    </div>

                    <button type="submit" name="register_organ_donor">Register as Donor</button>
                </form>
            </div>

            <!-- Recent Donors -->
            <div class="card">
                <h2>Recent Organ Donor Registrations</h2>

                <div class="donor-list">
                    <?php if($donors_result && mysqli_num_rows($donors_result) > 0): ?>
                        <?php while($donor = mysqli_fetch_assoc($donors_result)): ?>
                            <div class="donor-card">
                                <h3><?php echo htmlspecialchars($donor['donor_name']); ?></h3>
                                <div class="donor-info">
                                    <span class="organ-badge"><?php echo $donor['Organ_Type']; ?></span>
                                    <br><br>
                                    <strong>Age:</strong> <?php echo $donor['donor_age']; ?> years<br>
                                    <strong>Blood Group:</strong> <?php echo $donor['donor_blood_group']; ?><br>
                                    <strong>City:</strong> <?php echo htmlspecialchars($donor['donor_city']); ?><br>
                                    <strong>Status:</strong> <?php echo $donor['Donation_Status']; ?><br>
                                    <strong>Registered:</strong> <?php echo format_date($donor['registration_date']); ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="no-donors">No organ donor registrations yet.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>