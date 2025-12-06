<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "Lakshmi@123";
$dbname = "blood_donor_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $age = $_POST['age'];
    $blood_group = $_POST['blood_group'];
    $city = $_POST['city'];
    $organs = isset($_POST['organs']) ? implode(", ", $_POST['organs']) : "";
    $status = "Pending";
    $registered = date("Y-m-d"); // MySQL date format
    
    $sql = "INSERT INTO organs_donation (donor_name, donor_phone, donor_email, donor_age, donor_blood_group, donor_city, Organ_Type, Donation_Status, Consent_Date) 
            VALUES ('$full_name', '$phone', '$email', '$age', '$blood_group', '$city', '$organs', '$status', '$registered')";
    
    if ($conn->query($sql) === TRUE) {
        $message = "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0;'>Registration successful! Thank you for registering as an organ donor.</div>";
    } else {
        $message = "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0;'>Error: " . $conn->error . "</div>";
    }
}

// Fetch recent registrations
$recent_sql = "SELECT * FROM organs_donation ORDER BY Donation_ID DESC LIMIT 5";
$recent_result = $conn->query($recent_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organ Donation Registration</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .header {
            background: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            color: #e74c3c;
            font-size: 48px;
            margin-bottom: 10px;
        }
        
        .header h1::before {
            content: "💚 ";
        }
        
        .header p {
            color: #7f8c8d;
            font-size: 18px;
        }
        
        .nav {
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            text-align: center;
        }
        
        .nav a {
            display: inline-block;
            background: #e74c3c;
            color: white;
            padding: 12px 30px;
            margin: 0 10px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background 0.3s;
        }
        
        .nav a:hover {
            background: #c0392b;
        }
        
        .container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        
        .card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .card h2 {
            color: #e74c3c;
            font-size: 28px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #e74c3c;
        }
        
        .info-box {
            background: #d5f4e6;
            border-left: 4px solid #27ae60;
            padding: 20px;
            margin-bottom: 25px;
            border-radius: 5px;
        }
        
        .info-box h3 {
            color: #27ae60;
            margin-bottom: 10px;
        }
        
        .info-box p {
            color: #2c3e50;
            line-height: 1.6;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            color: #2c3e50;
            font-weight: bold;
            margin-bottom: 8px;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .checkbox-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 10px;
        }
        
        .checkbox-group label {
            display: flex;
            align-items: center;
            font-weight: normal;
            cursor: pointer;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: auto;
            margin-right: 8px;
        }
        
        .submit-btn {
            width: 100%;
            background: #e74c3c;
            color: white;
            padding: 15px;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .submit-btn:hover {
            background: #c0392b;
        }
        
        .donor-card {
            background: #f8f9fa;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            border-left: 5px solid #27ae60;
        }
        
        .donor-card h3 {
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .organ-badge {
            display: inline-block;
            background: #27ae60;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            margin: 5px 5px 5px 0;
        }
        
        .donor-info {
            color: #7f8c8d;
            margin: 5px 0;
        }
        
        .status-pending {
            color: #f39c12;
            font-weight: bold;
     }
        
        .status-approved {
            color: #27ae60;
            font-weight: bold;
        }
        
        @media (max-width: 968px) {
            .container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Organ Donation Registration</h1>
        <p>Give the Gift of Life - Register as an Organ Donor</p>
    </div>
    
    <div class="nav">
        <a href="index.php">🏠 Home</a>
        <a href="blood_request.php">🆘 Blood Request</a>
        <a href="organ_registration.php">💚 Organ Donation</a>
        <a href="admin.php">⚙️ Admin Panel</a>
         <a href="my_donations.php">📋 My Donations</a>
        <a href="emergency_alerts.php">🚨 Emergency Alerts</a>
    </div>
    
    <div class="container">
        <div class="card">
            <h2>Register as Organ Donor</h2>
            
            <div class="info-box">
                <h3>ℹ️ Important Information</h3>
                <p>By registering as an organ donor, you can save multiple lives. Your decision to donate organs can give hope to those waiting for transplants.</p>
            </div>
            
            <?php echo $message; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="full_name" required>
                </div>
                
                <div class="form-group">
                    <label>Phone Number *</label>
                    <input type="tel" name="phone" required>
                </div>
                
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label>Age *</label>
                    <input type="number" name="age" min="18" max="65" required>
                </div>
                
                <div class="form-group">
                    <label>Blood Group *</label>
                    <select name="blood_group" required>
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
                    <label>City *</label>
                    <input type="text" name="city" required>
                </div>
                
                <div class="form-group">
                    <label>Organs to Donate *</label>
                    <div class="checkbox-group">
                        <label><input type="checkbox" name="organs[]" value="Heart"> Heart</label>
                        <label><input type="checkbox" name="organs[]" value="Liver"> Liver</label>
                        <label><input type="checkbox" name="organs[]" value="Kidney"> Kidney</label>
                        <label><input type="checkbox" name="organs[]" value="Lungs"> Lungs</label>
                        <label><input type="checkbox" name="organs[]" value="Pancreas"> Pancreas</label>
                        <label><input type="checkbox" name="organs[]" value="Corneas"> Corneas</label>
                    </div>
                </div>
                
                <button type="submit" class="submit-btn">Register as Donor</button>
            </form>
        </div>
        
        <div class="card">
            <h2>Recent Organ Donor Registrations</h2>
            
            <?php
            if ($recent_result->num_rows > 0) {
                while($row = $recent_result->fetch_assoc()) {
                    $organs_array = explode(", ", $row['Organ_Type']);
                    echo "<div class='donor-card'>";
                    echo "<h3>" . htmlspecialchars($row['donor_name']) . "</h3>";
                    
                    foreach($organs_array as $organ) {
                        echo "<span class='organ-badge'>" . htmlspecialchars($organ) . "</span>";
                    }
                    
                    echo "<p class='donor-info'><strong>Age:</strong> " . $row['donor_age'] . " years</p>";
                    echo "<p class='donor-info'><strong>Blood Group:</strong> " . $row['donor_blood_group'] . "</p>";
                    echo "<p class='donor-info'><strong>City:</strong> " . htmlspecialchars($row['donor_city']) . "</p>";
                    
                    $status_class = $row['Donation_Status'] == 'Pending' ? 'status-pending' : 'status-approved';
                    echo "<p class='donor-info'><strong>Status:</strong> <span class='$status_class'>" . $row['Donation_Status'] . "</span></p>";
                    echo "<p class='donor-info'><strong>Registered:</strong> " . date("d-M-Y", strtotime($row['Consent_Date'])) . "</p>";
                    echo "</div>";
                }
            } else {
                echo "<p style='text-align: center; color: #7f8c8d; padding: 40px;'>No registrations yet. Be the first to register!</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>