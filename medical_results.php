<?php
session_start();
include('config.php');
require 'vendor/autoload.php'; // Assuming PHPMailer is installed via Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Redirect to login page if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("location: admin_login.php");
    exit;
}

// Validate appointment_id from URL
if (!isset($_GET['appointment_id']) || !is_numeric($_GET['appointment_id'])) {
    echo "<div class='alert alert-danger'>Invalid or missing appointment ID.</div>";
    exit;
}

$appointment_id = intval($_GET['appointment_id']);

// Fetch appointment details from the database
$query = "SELECT * FROM appointment_archive WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $appointment_id);
$stmt->execute();
$appointment = $stmt->get_result()->fetch_assoc();

if (!$appointment) {
    echo "<div class='alert alert-danger'>Appointment not found.</div>";
    exit;
}

// Fetch existing medical results if available
$query = "SELECT * FROM medical_results WHERE appointment_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $appointment_id);
$stmt->execute();
$medical_result = $stmt->get_result()->fetch_assoc();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Helper function to safely get POST data
    function getPostData($key) {
        return isset($_POST[$key]) ? htmlspecialchars(trim($_POST[$key])) : 'N/A';
    }

    $category = getPostData('form_category');
    $HBsAg = getPostData('HBsAg');
    $HAV_IgM = getPostData('HAV_IgM');
    $RPR_VDRL = getPostData('RPR_VDRL');
    $HIV_Test = getPostData('HIV_Test');
    $Blood_Type = getPostData('Blood_Type');
    $Pregnancy_Test = getPostData('Pregnancy_Test');
    $Others = getPostData('Others');
    $Physician = getPostData('Physician');
    $Age_Sex = getPostData('Age_Sex');
    $Date = getPostData('Date');
    // Additional fields for other tests
    $Glucose_FBS = getPostData('Glucose_FBS');
    $Glucose_RBS = getPostData('Glucose_RBS');
    $Total_Cholesterol = getPostData('Total_Cholesterol');
    $Triglycerides = getPostData('Triglycerides');
    $HDL = getPostData('HDL');
    $LDL = getPostData('LDL');
    $Uric_Acid = getPostData('Uric_Acid');
    $Urea = getPostData('Urea');
    $Creatinine = getPostData('Creatinine');
    $SGOT_AST = getPostData('SGOT_AST');
    $SGPT_ALT = getPostData('SGPT_ALT');
    $Sodium = getPostData('Sodium');
    $Potassium = getPostData('Potassium');
    $HBA1C = getPostData('HBA1C');
    $Phosphorus = getPostData('Phosphorus');
    $Chloride = getPostData('Chloride');
    $Calcium = getPostData('Calcium');
    $Albumin = getPostData('Albumin');
    $Magnesium = getPostData('Magnesium');

    // For Hematology
    $RBC = getPostData('RBC');
    $Hemoglobin = getPostData('Hemoglobin');
    $Hematocrit = getPostData('Hematocrit');
    $WBC = getPostData('WBC');
    $Lymphocytes = getPostData('Lymphocytes');
    $Segments = getPostData('Segments');
    $Monocytes = getPostData('Monocytes');
    $Eosinophils = getPostData('Eosinophils');
    $Basophils = getPostData('Basophils');
    $Platelet_Count = getPostData('Platelet_Count');
    $Clotting_Time = getPostData('Clotting_Time');
    $Bleeding_Time = getPostData('Bleeding_Time');
    $Others_Hematology = getPostData('Others_Hematology');

    // For Urinalysis
    $Urinalysis_Color = getPostData('Color');
    $Urinalysis_Character = getPostData('Character');
    $Urinalysis_Specific_Gravity = getPostData('Specific_Gravity');
    $Urinalysis_Reaction = getPostData('Reaction');
    $Urinalysis_Ketone = getPostData('Ketone');
    $Urinalysis_Blood = getPostData('Blood');
    $Urinalysis_Protein = getPostData('Protein');
    $Urinalysis_Glucose = getPostData('Glucose');
    $Urinalysis_Urobilinogen = getPostData('Urobilinogen');
    $Urinalysis_Bilirubin = getPostData('Bilirubin');
    $Urinalysis_Nitrite = getPostData('Nitrite');
    $Urinalysis_Leukocyte_Esterase = getPostData('Leukocyte_Esterase');

    // For Microscopic
    $Microscopic_PUS = getPostData('PUS');
    $Microscopic_RBC = getPostData('RBC_microscopic');
    $Microscopic_Epithelial_Cells = getPostData('Epithelial_Cells');
    $Microscopic_Mucous_Threads = getPostData('Mucous_Threads');
    $Microscopic_Amorphous_Urates = getPostData('Amorphous_Urates');
    $Microscopic_Amorphous_PO4 = getPostData('Amorphous_PO4');
    $Microscopic_Crystals = getPostData('Crystals');
    $Microscopic_Albumin = getPostData('Albumin');
    $Microscopic_Bacteria = getPostData('Bacteria');
    $Microscopic_Coarse_Granular = getPostData('Coarse_Granular');
    $Microscopic_Hyaline_Cast = getPostData('Hyaline_Cast');
    $Microscopic_Others = getPostData('Others');

    // Check if updating or inserting new results
    if ($medical_result) {
        // Update existing results
        $query = "UPDATE medical_results SET category = ?, HBsAg = ?, HAV_IgM = ?, RPR_VDRL = ?, HIV_Test = ?, Blood_Type = ?, Pregnancy_Test = ?, Others = ?, 
                  Physician = ?, Age_Sex = ?, Date = ?, Glucose_FBS = ?, Glucose_RBS = ?, Total_Cholesterol = ?, Triglycerides = ?, HDL = ?, LDL = ?, Uric_Acid = ?, 
                  Urea = ?, Creatinine = ?, SGOT_AST = ?, SGPT_ALT = ?, Sodium = ?, Potassium = ?, HBA1C = ?, Phosphorus = ?, Chloride = ?, Calcium = ?, Albumin = ?, 
                  Magnesium = ?, RBC = ?, Hemoglobin = ?, Hematocrit = ?, WBC = ?, Lymphocytes = ?, Segments = ?, Monocytes = ?, Eosinophils = ?, Basophils = ?, 
                  Platelet_Count = ?, Clotting_Time = ?, Bleeding_Time = ?, Others_Hematology = ?, Urinalysis_Color = ?, Urinalysis_Character = ?, 
                  Urinalysis_Specific_Gravity = ?, Urinalysis_Reaction = ?, Urinalysis_Ketone = ?, Urinalysis_Blood = ?, Urinalysis_Protein = ?, Urinalysis_Glucose = ?, 
                  Urinalysis_Urobilinogen = ?, Urinalysis_Bilirubin = ?, Urinalysis_Nitrite = ?, Urinalysis_Leukocyte_Esterase = ?, 
                  Microscopic_PUS = ?, Microscopic_RBC = ?, Microscopic_Epithelial_Cells = ?, Microscopic_Mucous_Threads = ?, Microscopic_Amorphous_Urates = ?, 
                  Microscopic_Amorphous_PO4 = ?, Microscopic_Crystals = ?, Microscopic_Albumin = ?, Microscopic_Bacteria = ?, Microscopic_Coarse_Granular = ?, 
                  Microscopic_Hyaline_Cast = ?, Microscopic_Others = ? WHERE appointment_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssssssssssssssssssssssssssssssssssssssssssssssssssssi", 
            $category, $HBsAg, $HAV_IgM, $RPR_VDRL, $HIV_Test, $Blood_Type, $Pregnancy_Test, $Others, $Physician, 
            $Age_Sex, $Date, $Glucose_FBS, $Glucose_RBS, $Total_Cholesterol, $Triglycerides, $HDL, $LDL, $Uric_Acid, 
            $Urea, $Creatinine, $SGOT_AST, $SGPT_ALT, $Sodium, $Potassium, $HBA1C, $Phosphorus, $Chloride, $Calcium, 
            $Albumin, $Magnesium, $RBC, $Hemoglobin, $Hematocrit, $WBC, $Lymphocytes, $Segments, $Monocytes, 
            $Eosinophils, $Basophils, $Platelet_Count, $Clotting_Time, $Bleeding_Time, $Others_Hematology, 
            $Urinalysis_Color, $Urinalysis_Character, $Urinalysis_Specific_Gravity, $Urinalysis_Reaction, 
            $Urinalysis_Ketone, $Urinalysis_Blood, $Urinalysis_Protein, $Urinalysis_Glucose, $Urinalysis_Urobilinogen, 
            $Urinalysis_Bilirubin, $Urinalysis_Nitrite, $Urinalysis_Leukocyte_Esterase, $Microscopic_PUS, 
            $Microscopic_RBC, $Microscopic_Epithelial_Cells, $Microscopic_Mucous_Threads, 
            $Microscopic_Amorphous_Urates, $Microscopic_Amorphous_PO4, $Microscopic_Crystals, $Microscopic_Albumin, 
            $Microscopic_Bacteria, $Microscopic_Coarse_Granular, $Microscopic_Hyaline_Cast, $Microscopic_Others, 
            $appointment_id);
        $stmt->execute();
        $message = "Medical results updated successfully!";
    } else {
        // Insert new medical results
        $query = "INSERT INTO medical_results (appointment_id, category, HBsAg, HAV_IgM, RPR_VDRL, HIV_Test, Blood_Type, Pregnancy_Test, Others, 
                  Physician, Age_Sex, Date, Glucose_FBS, Glucose_RBS, Total_Cholesterol, Triglycerides, HDL, LDL, Uric_Acid, Urea, Creatinine, 
                  SGOT_AST, SGPT_ALT, Sodium, Potassium, HBA1C, Phosphorus, Chloride, Calcium, Albumin, Magnesium, RBC, Hemoglobin, Hematocrit, 
                  WBC, Lymphocytes, Segments, Monocytes, Eosinophils, Basophils, Platelet_Count, Clotting_Time, Bleeding_Time, 
                  Others_Hematology, Urinalysis_Color, Urinalysis_Character, Urinalysis_Specific_Gravity, Urinalysis_Reaction, Urinalysis_Ketone, 
                  Urinalysis_Blood, Urinalysis_Protein, Urinalysis_Glucose, Urinalysis_Urobilinogen, Urinalysis_Bilirubin, Urinalysis_Nitrite, 
                  Urinalysis_Leukocyte_Esterase, Microscopic_PUS, Microscopic_RBC, Microscopic_Epithelial_Cells, Microscopic_Mucous_Threads, 
                  Microscopic_Amorphous_Urates, Microscopic_Amorphous_PO4, Microscopic_Crystals, Microscopic_Albumin, Microscopic_Bacteria, 
                  Microscopic_Coarse_Granular, Microscopic_Hyaline_Cast, Microscopic_Others) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
                  ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("issssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssss", 
            $appointment_id, $category, $HBsAg, $HAV_IgM, $RPR_VDRL, $HIV_Test, $Blood_Type, $Pregnancy_Test, $Others, 
            $Physician, $Age_Sex, $Date, $Glucose_FBS, $Glucose_RBS, $Total_Cholesterol, $Triglycerides, $HDL, $LDL, $Uric_Acid, $Urea, 
            $Creatinine, $SGOT_AST, $SGPT_ALT, $Sodium, $Potassium, $HBA1C, $Phosphorus, $Chloride, $Calcium, $Albumin, 
            $Magnesium, $RBC, $Hemoglobin, $Hematocrit, $WBC, $Lymphocytes, $Segments, $Monocytes, $Eosinophils, $Basophils, 
            $Platelet_Count, $Clotting_Time, $Bleeding_Time, $Others_Hematology, $Urinalysis_Color, $Urinalysis_Character, 
            $Urinalysis_Specific_Gravity, $Urinalysis_Reaction, $Urinalysis_Ketone, $Urinalysis_Blood, $Urinalysis_Protein, 
            $Urinalysis_Glucose, $Urinalysis_Urobilinogen, $Urinalysis_Bilirubin, $Urinalysis_Nitrite, $Urinalysis_Leukocyte_Esterase, 
            $Microscopic_PUS, $Microscopic_RBC, $Microscopic_Epithelial_Cells, $Microscopic_Mucous_Threads, 
            $Microscopic_Amorphous_Urates, $Microscopic_Amorphous_PO4, $Microscopic_Crystals, $Microscopic_Albumin, 
            $Microscopic_Bacteria, $Microscopic_Coarse_Granular, $Microscopic_Hyaline_Cast, $Microscopic_Others);
        $stmt->execute();
        $message = "Medical results created successfully!";
    }

    // Send email with PHPMailer
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'jnabubos@gmail.com'; // Replace with your email
        $mail->Password   = 'alxhljezdphvawic'; // Replace with your email app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('jnabubos@gmail.com', 'Medical Clinic');
        $mail->addAddress($appointment['email'], $appointment['name']);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Medical Results for Your Appointment';
        $mail->Body    = '
            <h3>Medical Results for Appointment #' . htmlspecialchars($appointment_id) . '</h3>
            <p><strong>Category:</strong> ' . htmlspecialchars($category) . '</p>
            <p><strong>Physician:</strong> ' . htmlspecialchars($Physician) . '</p>
            <p><strong>Age/Sex:</strong> ' . htmlspecialchars($Age_Sex) . '</p>
            <p><strong>Date:</strong> ' . htmlspecialchars($Date) . '</p>
            <p><strong>HBsAg:</strong> ' . htmlspecialchars($HBsAg) . '</p>
            <p><strong>HAV IgM:</strong> ' . htmlspecialchars($HAV_IgM) . '</p>
            <p><strong>RPR/VDRL:</strong> ' . htmlspecialchars($RPR_VDRL) . '</p>
            <p><strong>HIV Test:</strong> ' . htmlspecialchars($HIV_Test) . '</p>
            <p><strong>Blood Type:</strong> ' . htmlspecialchars($Blood_Type) . '</p>
            <p><strong>Pregnancy Test:</strong> ' . htmlspecialchars($Pregnancy_Test) . '</p>
            <p><strong>Others:</strong> ' . htmlspecialchars($Others) . '</p>
            <p><strong>Glucose (FBS):</strong> ' . htmlspecialchars($Glucose_FBS) . '</p>
            <p><strong>Glucose (RBS):</strong> ' . htmlspecialchars($Glucose_RBS) . '</p>
            <p><strong>Total Cholesterol:</strong> ' . htmlspecialchars($Total_Cholesterol) . '</p>
            <p><strong>Triglycerides:</strong> ' . htmlspecialchars($Triglycerides) . '</p>
            <p><strong>HDL:</strong> ' . htmlspecialchars($HDL) . '</p>
            <p><strong>LDL:</strong> ' . htmlspecialchars($LDL) . '</p>
            <p><strong>Uric Acid:</strong> ' . htmlspecialchars($Uric_Acid) . '</p>
            <p><strong>Urea:</strong> ' . htmlspecialchars($Urea) . '</p>
            <p><strong>Creatinine:</strong> ' . htmlspecialchars($Creatinine) . '</p>
            <p><strong>SGOT/AST:</strong> ' . htmlspecialchars($SGOT_AST) . '</p>
            <p><strong>SGPT/ALT:</strong> ' . htmlspecialchars($SGPT_ALT) . '</p>
            <p><strong>Sodium:</strong> ' . htmlspecialchars($Sodium) . '</p>
            <p><strong>Potassium:</strong> ' . htmlspecialchars($Potassium) . '</p>
            <p><strong>HBA1C:</strong> ' . htmlspecialchars($HBA1C) . '</p>
            <p><strong>Phosphorus:</strong> ' . htmlspecialchars($Phosphorus) . '</p>
            <p><strong>Chloride:</strong> ' . htmlspecialchars($Chloride) . '</p>
            <p><strong>Calcium:</strong> ' . htmlspecialchars($Calcium) . '</p>
            <p><strong>Albumin:</strong> ' . htmlspecialchars($Albumin) . '</p>
            <p><strong>Magnesium:</strong> ' . htmlspecialchars($Magnesium) . '</p>
            <p><strong>RBC:</strong> ' . htmlspecialchars($RBC) . '</p>
            <p><strong>Hemoglobin:</strong> ' . htmlspecialchars($Hemoglobin) . '</p>
            <p><strong>Hematocrit:</strong> ' . htmlspecialchars($Hematocrit) . '</p>
            <p><strong>WBC:</strong> ' . htmlspecialchars($WBC) . '</p>
            <p><strong>Lymphocytes:</strong> ' . htmlspecialchars($Lymphocytes) . '</p>
            <p><strong>Segments:</strong> ' . htmlspecialchars($Segments) . '</p>
            <p><strong>Monocytes:</strong> ' . htmlspecialchars($Monocytes) . '</p>
            <p><strong>Eosinophils:</strong> ' . htmlspecialchars($Eosinophils) . '</p>
            <p><strong>Basophils:</strong> ' . htmlspecialchars($Basophils) . '</p>
            <p><strong>Platelet Count:</strong> ' . htmlspecialchars($Platelet_Count) . '</p>
            <p><strong>Clotting Time:</strong> ' . htmlspecialchars($Clotting_Time) . '</p>
            <p><strong>Bleeding Time:</strong> ' . htmlspecialchars($Bleeding_Time) . '</p>
            <p><strong>Others Hematology:</strong> ' . htmlspecialchars($Others_Hematology) . '</p>
            <p><strong>Urinalysis Color:</strong> ' . htmlspecialchars($Urinalysis_Color) . '</p>
            <p><strong>Urinalysis Character:</strong> ' . htmlspecialchars($Urinalysis_Character) . '</p>
            <p><strong>Urinalysis Specific Gravity:</strong> ' . htmlspecialchars($Urinalysis_Specific_Gravity) . '</p>
            <p><strong>Urinalysis Reaction:</strong> ' . htmlspecialchars($Urinalysis_Reaction) . '</p>
            <p><strong>Urinalysis Ketone:</strong> ' . htmlspecialchars($Urinalysis_Ketone) . '</p>
            <p><strong>Urinalysis Blood:</strong> ' . htmlspecialchars($Urinalysis_Blood) . '</p>
            <p><strong>Urinalysis Protein:</strong> ' . htmlspecialchars($Urinalysis_Protein) . '</p>
            <p><strong>Urinalysis Glucose:</strong> ' . htmlspecialchars($Urinalysis_Glucose) . '</p>
            <p><strong>Urinalysis Urobilinogen:</strong> ' . htmlspecialchars($Urinalysis_Urobilinogen) . '</p>
            <p><strong>Urinalysis Bilirubin:</strong> ' . htmlspecialchars($Urinalysis_Bilirubin) . '</p>
            <p><strong>Urinalysis Nitrite:</strong> ' . htmlspecialchars($Urinalysis_Nitrite) . '</p>
            <p><strong>Urinalysis Leukocyte Esterase:</strong> ' . htmlspecialchars($Urinalysis_Leukocyte_Esterase) . '</p>
            <p><strong>Microscopic PUS:</strong> ' . htmlspecialchars($Microscopic_PUS) . '</p>
            <p><strong>Microscopic RBC:</strong> ' . htmlspecialchars($Microscopic_RBC) . '</p>
            <p><strong>Microscopic Epithelial Cells:</strong> ' . htmlspecialchars($Microscopic_Epithelial_Cells) . '</p>
            <p><strong>Microscopic Mucous Threads:</strong> ' . htmlspecialchars($Microscopic_Mucous_Threads) . '</p>
            <p><strong>Microscopic Amorphous Urates:</strong> ' . htmlspecialchars($Microscopic_Amorphous_Urates) . '</p>
            <p><strong>Microscopic Amorphous PO4:</strong> ' . htmlspecialchars($Microscopic_Amorphous_PO4) . '</p>
            <p><strong>Microscopic Crystals:</strong> ' . htmlspecialchars($Microscopic_Crystals) . '</p>
            <p><strong>Microscopic Albumin:</strong> ' . htmlspecialchars($Microscopic_Albumin) . '</p>
            <p><strong>Microscopic Bacteria:</strong> ' . htmlspecialchars($Microscopic_Bacteria) . '</p>
            <p><strong>Microscopic Coarse Granular:</strong> ' . htmlspecialchars($Microscopic_Coarse_Granular) . '</p>
            <p><strong>Microscopic Hyaline Cast:</strong> ' . htmlspecialchars($Microscopic_Hyaline_Cast) . '</p>
            <p><strong>Microscopic Others:</strong> ' . htmlspecialchars($Microscopic_Others) . '</p>
        ';

        $mail->send();
        $message .= " Email sent to the user successfully!";
    } catch (Exception $e) {
        $message .= " Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Results Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .header img {
            height: 100px;
            margin-right: 20px;
        }
        .header .header-text {
            text-align: left;
        }
        .header h2 {
            margin-top: 5px;
            margin-bottom: 0;
            font-size: 22px;
            font-weight: bold;
            font-family: 'Arial Black', sans-serif;
        }
        .header h3 {
            font-size: 16px;
            font-weight: bold;
        }
        .contact-info {
            font-size: 14px;
            font-weight: normal;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .details-table td, .details-table th {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .details-table th {
            background-color: #B4E380;
            text-align: left;
            width: 25%;
        }

        .table-container {
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            width: 50%;
            border: 1px solid black;
            text-align: center;
        }
        th {
            background-color: #ffcc00;
            color: #003366;
            font-weight: bold;
        }
        td:nth-child(even) {
            background-color: #f0f8e6; /* Light beige for even rows */
        }
        td:nth-child(odd) {
            background-color: #ffffff; /* White for odd rows */
        }

        input[type="text"], input[type="date"] {
            border: 1px solid #ccc;
            background-color: #fff;
            text-align: center;
            width: 100%;
        }

        .footer {
            margin-top: 50px;
        }
        .footer p {
            margin: 0;
        }
        .signature {
            text-align: center;
            margin-top: 50px;
        }
        .signature p {
            margin-bottom: 0;
        }

        .medical-staff {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
        }
        .medical-staff div {
            text-align: center;
        }
        .medical-staff p {
            margin: 0;
            font-weight: bold;
        }
        .medical-staff .role {
            font-style: italic;
        }

        .dropdown-container {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
    <script>
        function toggleForm() {
            var category = document.getElementById('form_category').value;
            document.getElementById('serology-form').style.display = (category == 'Serology') ? 'block' : 'none';
            document.getElementById('fecalysis-form').style.display = (category == 'Fecalysis') ? 'block' : 'none';
            document.getElementById('blood-chemistry-form').style.display = (category == 'Blood Chemistry') ? 'block' : 'none';
            document.getElementById('hematology-form').style.display = (category == 'Hematology') ? 'block' : 'none';
            document.getElementById('urinalysis-form').style.display = (category == 'Urinalysis') ? 'block' : 'none';
            document.getElementById('microscopic-form').style.display = (category == 'Microscopic') ? 'block' : 'none';
        }
    </script>
</head>
<body>

<!-- Header with logo to the left and text to the right -->
<div class="header">
    <img src="jeda.png" alt="Jeda Laboratory & Diagnostic Center">
    <div class="header-text">
        <h2>JEDA LABORATORY & DIAGNOSTIC CENTER</h2>
        <h3>Poblacion Norte, Sta. Maria, Ilocos Sur</h3>
        <p class="contact-info">Email: jedalaboratorystamaria@gmail.com | Contact No: 0953-520-0058</p>
    </div>
</div>

<!-- Dropdown positioned in the upper-right corner -->
<div class="dropdown-container no-print">
    <form method="POST">
        <label for="form_category">Category:</label>
        <select name="form_category" id="form_category" onchange="toggleForm()" required>
            <option value="" disabled selected>Select Category</option>
            <option value="Serology">Serology</option>
            <option value="Fecalysis">Fecalysis</option>
            <option value="Blood Chemistry">Blood Chemistry</option> <!-- New Blood Chemistry option -->
            <option value="Hematology">Hematology</option> <!-- New Hematology option -->
            <option value="Urinalysis">Urinalysis</option> <!-- New Urinalysis option -->
            <option value="Microscopic">Microscopic</option> <!-- New Microscopic option -->
        </select>
    </form>
</div>

<!-- Patient Information Details Table -->
<table class="details-table">
    <tr>
        <th>Name:</th>
        <td>
            <input type="text" name="name" value="<?php echo htmlspecialchars($appointment['name'] ?? ''); ?>" readonly style="width: 200px;">
        </td>
        <th>Age:</th>
        <td>
            <input type="text" name="age" value="<?php echo htmlspecialchars($appointment['age'] ?? ''); ?>" readonly style="width: 80px;">
        </td>
        <th>Sex:</th>
        <td>
            <input type="text" name="sex" value="<?php echo htmlspecialchars($appointment['sex'] ?? ''); ?>" readonly style="width: 80px;">
        </td>
    </tr>
    <tr>
        <th>Req. Physician:</th>
        <td colspan="3">
            <input type="text" name="Physician" value="<?php echo htmlspecialchars($medical_result['Physician'] ?? ''); ?>" required>
        </td>
        <th>Date:</th>
        <td>
            <input type="date" name="Date" value="<?php echo htmlspecialchars($medical_result['Date'] ?? date('Y-m-d')); ?>" required>
        </td>
    </tr>
</table>


<!-- Serology Form -->
<div id="serology-form" style="display:none;">
    <div class="table-container">
        <h4 style="text-align: center;">SEROLOGY</h4>
        <table>
            <thead>
                <tr>
                    <th>EXAMINATIONS</th>
                    <th>RESULTS</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>HBsAg</td>
                    <td><input type="text" name="HBsAg" value="<?php echo htmlspecialchars($medical_result['HBsAg'] ?? ''); ?>"></td>
                </tr>
                <tr>
                    <td>HAV IgM</td>
                    <td><input type="text" name="HAV_IgM" value="<?php echo htmlspecialchars($medical_result['HAV_IgM'] ?? ''); ?>"></td>
                </tr>
                <tr>
                    <td>RPR/VDRL</td>
                    <td><input type="text" name="RPR_VDRL" value="<?php echo htmlspecialchars($medical_result['RPR_VDRL'] ?? ''); ?>"></td>
                </tr>
                <tr>
                    <td>HIV Screening Test</td>
                    <td><input type="text" name="HIV_Test" value="<?php echo htmlspecialchars($medical_result['HIV_Test'] ?? ''); ?>"></td>
                </tr>
                <tr>
                    <td>Blood Type</td>
                    <td><input type="text" name="Blood_Type" value="<?php echo htmlspecialchars($medical_result['Blood_Type'] ?? ''); ?>"></td>
                </tr>
                <tr>
                    <td>Pregnancy Test</td>
                    <td><input type="text" name="Pregnancy_Test" value="<?php echo htmlspecialchars($medical_result['Pregnancy_Test'] ?? ''); ?>"></td>
                </tr>
                <tr>
                    <td>Others</td>
                    <td><input type="text" name="Others" value="<?php echo htmlspecialchars($medical_result['Others'] ?? ''); ?>"></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Fecalysis Form -->
<div id="fecalysis-form" style="display:none;">
    <div class="table-container">
        <h4 style="text-align: center;">FECALYSIS</h4>
        <table>
            <thead>
                <tr>
                    <th>EXAMINATIONS</th>
                    <th>RESULTS</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Color</td>
                    <td><input type="text" name="Color" value="<?php echo htmlspecialchars($medical_result['Color'] ?? ''); ?>"></td>
                </tr>
                <tr>
                    <td>Consistency</td>
                    <td><input type="text" name="Consistency" value="<?php echo htmlspecialchars($medical_result['Consistency'] ?? ''); ?>"></td>
                </tr>
                <tr>
                    <td>RBC</td>
                    <td><input type="text" name="RBC" value="<?php echo htmlspecialchars($medical_result['RBC'] ?? ''); ?>"></td>
                </tr>
                <tr>
                    <td>WBC</td>
                    <td><input type="text" name="WBC" value="<?php echo htmlspecialchars($medical_result['WBC'] ?? ''); ?>"></td>
                </tr>
                <tr>
                    <td>Occult Blood</td>
                    <td><input type="text" name="Occult_Blood" value="<?php echo htmlspecialchars($medical_result['Occult_Blood'] ?? ''); ?>"></td>
                </tr>
                <tr>
                    <td>Parasite ova/cyst</td>
                    <td><input type="text" name="Parasite_ova" value="<?php echo htmlspecialchars($medical_result['Parasite_ova'] ?? ''); ?>"></td>
                </tr>
                <tr>
                    <td>Others</td>
                    <td><input type="text" name="Others" value="<?php echo htmlspecialchars($medical_result['Others'] ?? ''); ?>"></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Blood Chemistry Form -->
<div id="blood-chemistry-form" style="display:none;">
    <div class="table-container">
        <h4 style="text-align: center;">BLOOD CHEMISTRY</h4>
        <table>
            <thead>
                <tr>
                    <th>EXAMINATIONS</th>
                    <th>RESULTS</th>
                    <th>NORMAL VALUES</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Glucose (FBS)</td>
                    <td><input type="text" name="Glucose_FBS" value="<?php echo htmlspecialchars($medical_result['Glucose_FBS'] ?? ''); ?>"></td>
                    <td>70 - 105 mg/dl</td>
                </tr>
                <tr>
                    <td>Glucose (RBS)</td>
                    <td><input type="text" name="Glucose_RBS" value="<?php echo htmlspecialchars($medical_result['Glucose_RBS'] ?? ''); ?>"></td>
                    <td>100 - 173 mg/dl</td>
                </tr>
                <tr>
                    <td>Total Cholesterol</td>
                    <td><input type="text" name="Total_Cholesterol" value="<?php echo htmlspecialchars($medical_result['Total_Cholesterol'] ?? ''); ?>"></td>
                    <td>139 - 200 mg/dl</td>
                </tr>
                <tr>
                    <td>Triglycerides</td>
                    <td><input type="text" name="Triglycerides" value="<?php echo htmlspecialchars($medical_result['Triglycerides'] ?? ''); ?>"></td>
                    <td>30 - 150 mg/dl</td>
                </tr>
                <tr>
                    <td>HDL</td>
                    <td><input type="text" name="HDL" value="<?php echo htmlspecialchars($medical_result['HDL'] ?? ''); ?>"></td>
                    <td>M: 40 - 60 mg/dl<br>F: 50 - 70 mg/dl</td>
                </tr>
                <tr>
                    <td>LDL</td>
                    <td><input type="text" name="LDL" value="<?php echo htmlspecialchars($medical_result['LDL'] ?? ''); ?>"></td>
                    <td>< 100 mg/dl</td>
                </tr>
                <tr>
                    <td>Uric Acid (BUA)</td>
                    <td><input type="text" name="Uric_Acid" value="<?php echo htmlspecialchars($medical_result['Uric_Acid'] ?? ''); ?>"></td>
                    <td>M: 3.4 - 7.0 mg/dl<br>F: 2.6 - 6.0 mg/dl</td>
                </tr>
                <tr>
                    <td>Urea (BUN)</td>
                    <td><input type="text" name="Urea" value="<?php echo htmlspecialchars($medical_result['Urea'] ?? ''); ?>"></td>
                    <td>7 - 18 mg/dl</td>
                </tr>
                <tr>
                    <td>Creatinine</td>
                    <td><input type="text" name="Creatinine" value="<?php echo htmlspecialchars($medical_result['Creatinine'] ?? ''); ?>"></td>
                    <td>0.6 - 1.2 mg/dl</td>
                </tr>
                <tr>
                    <td>SGOT/AST</td>
                    <td><input type="text" name="SGOT_AST" value="<?php echo htmlspecialchars($medical_result['SGOT_AST'] ?? ''); ?>"></td>
                    <td>10 - 40 IU/L</td>
                </tr>
                <tr>
                    <td>SGPT/ALT</td>
                    <td><input type="text" name="SGPT_ALT" value="<?php echo htmlspecialchars($medical_result['SGPT_ALT'] ?? ''); ?>"></td>
                    <td>10 - 40 IU/L</td>
                </tr>
                <tr>
                    <td>Sodium (Na)</td>
                    <td><input type="text" name="Sodium" value="<?php echo htmlspecialchars($medical_result['Sodium'] ?? ''); ?>"></td>
                    <td>134 - 145 mEq/L</td>
                </tr>
                <tr>
                    <td>Potassium (K)</td>
                    <td><input type="text" name="Potassium" value="<?php echo htmlspecialchars($medical_result['Potassium'] ?? ''); ?>"></td>
                    <td>3.5 - 5.0 mEq/L</td>
                </tr>
                <tr>
                    <td>HBA1C</td>
                    <td><input type="text" name="HBA1C" value="<?php echo htmlspecialchars($medical_result['HBA1C'] ?? ''); ?>"></td>
                    <td>4.5 - 6.5 %</td>
                </tr>
                <tr>
                    <td>Phosphorus</td>
                    <td><input type="text" name="Phosphorus" value="<?php echo htmlspecialchars($medical_result['Phosphorus'] ?? ''); ?>"></td>
                    <td>2.5 - 4.5 mg/dl</td>
                </tr>
                <tr>
                    <td>Chloride</td>
                    <td><input type="text" name="Chloride" value="<?php echo htmlspecialchars($medical_result['Chloride'] ?? ''); ?>"></td>
                    <td>98 - 107 mEq/L</td>
                </tr>
                <tr>
                    <td>Calcium</td>
                    <td><input type="text" name="Calcium" value="<?php echo htmlspecialchars($medical_result['Calcium'] ?? ''); ?>"></td>
                    <td>8.5 - 10.4 mg/dl</td>
                </tr>
                <tr>
                    <td>Albumin</td>
                    <td><input type="text" name="Albumin" value="<?php echo htmlspecialchars($medical_result['Albumin'] ?? ''); ?>"></td>
                    <td>3.5 - 5.5 g/dl</td>
                </tr>
                <tr>
                    <td>Magnesium</td>
                    <td><input type="text" name="Magnesium" value="<?php echo htmlspecialchars($medical_result['Magnesium'] ?? ''); ?>"></td>
                    <td>1.5 - 2.5 mEq/L</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Hematology Form -->
<div id="hematology-form" style="display:none;">
    <div class="table-container">
        <h4 style="text-align: center;">HEMATOLOGY</h4>
        <table>
            <thead>
                <tr>
                    <th>EXAMINATIONS</th>
                    <th>RESULTS</th>
                    <th>NORMAL VALUES</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>RBC</td>
                    <td><input type="text" name="RBC" value="<?php echo htmlspecialchars($medical_result['RBC'] ?? ''); ?>"></td>
                    <td>M: 4.6 - 6.2 x 10/L<br>F: 4.2 - 5.4 x 10/L</td>
                </tr>
                <tr>
                    <td>Hemoglobin</td>
                    <td><input type="text" name="Hemoglobin" value="<?php echo htmlspecialchars($medical_result['Hemoglobin'] ?? ''); ?>"></td>
                    <td>M: 140 - 180 g/L<br>F: 120 - 160 g/L</td>
                </tr>
                <tr>
                    <td>Hematocrit</td>
                    <td><input type="text" name="Hematocrit" value="<?php echo htmlspecialchars($medical_result['Hematocrit'] ?? ''); ?>"></td>
                    <td>M: 0.40 - 0.54<br>F: 0.37 - 0.47</td>
                </tr>
                <tr>
                    <td>WBC</td>
                    <td><input type="text" name="WBC" value="<?php echo htmlspecialchars($medical_result['WBC'] ?? ''); ?>"></td>
                    <td>5.0 - 10.0 x 10/L</td>
                </tr>
                <tr>
                    <td>Lymphocytes</td>
                    <td><input type="text" name="Lymphocytes" value="<?php echo htmlspecialchars($medical_result['Lymphocytes'] ?? ''); ?>"></td>
                    <td>0.25 - 0.40</td>
                </tr>
                <tr>
                    <td>Segments</td>
                    <td><input type="text" name="Segments" value="<?php echo htmlspecialchars($medical_result['Segments'] ?? ''); ?>"></td>
                    <td>0.50 - 0.70</td>
                </tr>
                <tr>
                    <td>Monocytes</td>
                    <td><input type="text" name="Monocytes" value="<?php echo htmlspecialchars($medical_result['Monocytes'] ?? ''); ?>"></td>
                    <td>0.05 - 0.08</td>
                </tr>
                <tr>
                    <td>Eosinophils</td>
                    <td><input type="text" name="Eosinophils" value="<?php echo htmlspecialchars($medical_result['Eosinophils'] ?? ''); ?>"></td>
                    <td>0.01 - 0.04</td>
                </tr>
                <tr>
                    <td>Basophils</td>
                    <td><input type="text" name="Basophils" value="<?php echo htmlspecialchars($medical_result['Basophils'] ?? ''); ?>"></td>
                    <td>0.0 - 0.1</td>
                </tr>
                <tr>
                    <td>Platelet Count</td>
                    <td><input type="text" name="Platelet_Count" value="<?php echo htmlspecialchars($medical_result['Platelet_Count'] ?? ''); ?>"></td>
                    <td>150 - 450 x 10/L</td>
                </tr>
                <tr>
                    <td>Clotting Time</td>
                    <td><input type="text" name="Clotting_Time" value="<?php echo htmlspecialchars($medical_result['Clotting_Time'] ?? ''); ?>"></td>
                    <td>2 - 7 mins</td>
                </tr>
                <tr>
                    <td>Bleeding Time</td>
                    <td><input type="text" name="Bleeding_Time" value="<?php echo htmlspecialchars($medical_result['Bleeding_Time'] ?? ''); ?>"></td>
                    <td>1 - 8 mins</td>
                </tr>
                <tr>
                    <td>Others</td>
                    <td><input type="text" name="Others_Hematology" value="<?php echo htmlspecialchars($medical_result['Others_Hematology'] ?? ''); ?>"></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Urinalysis Form -->
<div id="urinalysis-form" style="display:none;">
    <div class="table-container">
        <h4 style="text-align: center;">URINALYSIS</h4>
        <table>
            <thead>
                <tr>
                    <th>EXAMINATIONS</th>
                    <th>RESULTS</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Color</td>
                    <td><input type="text" name="Color" value="<?php echo htmlspecialchars($medical_result['Color'] ?? ''); ?>"></td>
                </tr>
                <tr>
                    <td>Character</td>
                    <td><input type="text" name="Character" value="<?php echo htmlspecialchars($medical_result['Character'] ?? ''); ?>"></td>
                </tr>
                <tr>
                    <td>Specific Gravity</td>
                    <td><input type="text" name="Specific_Gravity" value="<?php echo htmlspecialchars($medical_result['Specific_Gravity'] ?? ''); ?>"></td>
                </tr>
                <tr>
                    <td>Reaction</td>
                    <td><input type="text" name="Reaction" value="<?php echo htmlspecialchars($medical_result['Reaction'] ?? ''); ?>"></td>
                </tr>
                <tr>
                    <td>Ketone</td>
                    <td><input type="text" name="Ketone" value="<?php echo htmlspecialchars($medical_result['Ketone'] ?? ''); ?>"></td>
                </tr>
                <tr>
                    <td>Blood</td>
                    <td><input type="text" name="Blood" value="<?php echo htmlspecialchars($medical_result['Blood'] ?? ''); ?>"></td>
                </tr>
                <tr>
                    <td>Protein</td>
                    <td><input type="text" name="Protein" value="<?php echo htmlspecialchars($medical_result['Protein'] ?? ''); ?>"></td>
                </tr>
                <tr>
                    <td>Glucose</td>
                    <td><input type="text" name="Glucose" value="<?php echo htmlspecialchars($medical_result['Glucose'] ?? ''); ?>"></td>
                </tr>
                <tr>
                    <td>Urobilinogen</td>
                    <td><input type="text" name="Urobilinogen" value="<?php echo htmlspecialchars($medical_result['Urobilinogen'] ?? ''); ?>"></td>
                </tr>
                <tr>
                    <td>Bilirubin</td>
                    <td><input type="text" name="Bilirubin" value="<?php echo htmlspecialchars($medical_result['Bilirubin'] ?? ''); ?>"></td>
                </tr>
                <tr>
                    <td>Nitrite</td>
                    <td><input type="text" name="Nitrite" value="<?php echo htmlspecialchars($medical_result['Nitrite'] ?? ''); ?>"></td>
                </tr>
                <tr>
                    <td>Leukocyte Esterase</td>
                    <td><input type="text" name="Leukocyte_Esterase" value="<?php echo htmlspecialchars($medical_result['Leukocyte_Esterase'] ?? ''); ?>"></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Microscopic Form -->
<div id="microscopic-form" style="display:none;">
    <div class="table-container">
        <h4 style="text-align: center;">MICROSCOPIC</h4>
        <table>
            <thead>
                <tr>
                    <th>EXAMINATIONS</th>
                    <th>RESULTS</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>PUS/hpf</td>
                    <td><input type="text" name="PUS" value="<?php echo htmlspecialchars($medical_result['PUS'] ?? ''); ?>"></td>
                </tr>
                <tr>
                    <td>RBC/hpf</td>
                    <td><input type="text" name="RBC_microscopic" value="<?php echo htmlspecialchars($medical_result['RBC_microscopic'] ?? ''); ?>"></td>
                </tr>
                <tr>
                    <td>Epithelial Cells</td>
                    <td><input type="text" name="Epithelial_Cells" value="<?php echo htmlspecialchars($medical_result['Epithelial_Cells'] ?? ''); ?>"></td>
                </tr>
                <tr>
                    <td>Mucous Threads</td>
                    <td><input type="text" name="Mucous_Threads" value="<?php echo htmlspecialchars($medical_result['Mucous_Threads'] ?? ''); ?>"></td>
                </tr>
                <tr>
                    <td>Amorphous Urates</td>
                    <td><input type="text" name="Amorphous_Urates" value="<?php echo htmlspecialchars($medical_result['Amorphous_Urates'] ?? ''); ?>"></td>
                </tr>
                <tr>
                    <td>Amorphous PO4</td>
                    <td><input type="text" name="Amorphous_PO4" value="<?php echo htmlspecialchars($medical_result['Amorphous_PO4'] ?? ''); ?>"></td>
                </tr>
                <tr>
                    <td>Crystals</td>
                    <td><input type="text" name="Crystals" value="<?php echo htmlspecialchars($medical_result['Crystals'] ?? ''); ?>"></td>
                </tr>
                <tr>
                    <td>Albumin</td>
                    <td><input type="text" name="Albumin" value="<?php echo htmlspecialchars($medical_result['Albumin'] ?? ''); ?>"></td>
                </tr>
                <tr>
                    <td>Bacteria</td>
                    <td><input type="text" name="Bacteria" value="<?php echo htmlspecialchars($medical_result['Bacteria'] ?? ''); ?>"></td>
                </tr>
                <tr>
                    <td>Coarse Granular</td>
                    <td><input type="text" name="Coarse_Granular" value="<?php echo htmlspecialchars($medical_result['Coarse_Granular'] ?? ''); ?>"></td>
                </tr>
                <tr>
                    <td>Hyaline Cast</td>
                    <td><input type="text" name="Hyaline_Cast" value="<?php echo htmlspecialchars($medical_result['Hyaline_Cast'] ?? ''); ?>"></td>
                </tr>
                <tr>
                    <td>Others</td>
                    <td><input type="text" name="Others" value="<?php echo htmlspecialchars($medical_result['Others'] ?? ''); ?>"></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="footer">
    <p>Remarks: Specimen Rechecked; Results verified.</p>
</div>

<!-- Medical Staff Information Section -->
<div class="medical-staff">
    <div>
        <p>DOMECQ S. TANAWIT, MD</p>
        <p class="role">Pathologist</p>
        <p>LIC NO.: 0105214</p>
    </div>
    <div>
        <p>JENNIFER DAGDAG, RMT</p>
        <p class="role">Medical Technologist</p>
        <p>LIC NO.: 0033413</p>
    </div>
    <div>
        <p>PRINCESS CABRADILLA, RMT</p>
        <p class="role">Medical Technologist</p>
        <p>LIC NO.: 0113751</p>
    </div>
    <div>
        <p>YOLIMAR M. CABEBE, RMT</p>
        <p class="role">Medical Technologist</p>
        <p>LIC NO.: 0120809</p>
    </div>
</div>
<form>
    <input type="hidden" name="appointment_id" value="<?php echo htmlspecialchars($appointment_id); ?>">
    <!-- Other form fields go here -->
    <button type="submit" class="btn btn-success no-print">Submit Medical Results</button>
    <button type="button" class="btn btn-primary no-print" onclick="window.print();">Print Medical Results</button>
</form>

</form>

</body>
</html>
