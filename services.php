<?php
session_start();
// Include database connection if needed
include('config.php'); // Adjust the path as necessary
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic Laboratory Services</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
    <style>
        .carousel img {
            width: 100%;
            height: 400px; /* Set a consistent height for all images */
            object-fit: cover; /* Ensures the images fill the container properly */
        }
        .bg-blue-900 {
            background-color: #003366; /* Darker blue color for better contrast */
        }
        .bg-light-blue {
            background-color: #b3e5fc; /* Light blue for a friendly feel */
        }
        .text-brown {
            color: #4e342e; /* Brown color for text, matching the sign */
        }
        .text-blue {
            color: #1976d2; /* Blue color for emphasis */
        }
        .text-yellow {
            color: #fbc02d; /* Yellow color for highlights */
        }
        .clinic-hours {
            background-color: #ffffff;
            border-radius: 0.5rem;
            padding: 1.5rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .service-announcement {
            background-color: #ffe0b2;
            border-radius: 0.5rem;
            padding: 1.5rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 2rem;
            text-align: center;
        }
        .service-announcement h2 {
            color: #d32f2f;
            font-size: 2.5rem;
            font-weight: bold;
        }
        .service-announcement p {
            font-size: 1.25rem;
            color: #4e342e;
            font-weight: bold;
        }
        .fasting-info {
            background-color: #e3f2fd;
            border-radius: 0.5rem;
            padding: 1.5rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 2rem;
            font-size: 1.25rem;
            color: #1a237e;
        }
        .fasting-info .highlight {
            background-color: #ffeb3b;
            padding: 0.2rem 0.4rem;
            border-radius: 0.25rem;
            font-weight: bold;
        }
        .fasting-info .instruction {
            background-color: #a5d6a7;
            padding: 0.5rem;
            border-radius: 0.25rem;
            font-weight: bold;
            margin-top: 1rem;
        }
        .ultrasound-prices, .blood-chemistry-prices, .enzymes-prices, .thyroid-function-prices, 
        .electrolytes-prices, .hematology-prices, .hepatitis-profile-prices, 
        .hormones-prices, .tumor-markers-prices, .dengue-prices, .serology-prices {
            background-color: #fff3e0;
            border-radius: 0.5rem;
            padding: 1.5rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 2rem;
        }
        .ultrasound-prices h2, .blood-chemistry-prices h2, .enzymes-prices h2, 
        .thyroid-function-prices h2, .electrolytes-prices h2, .hematology-prices h2, 
        .hepatitis-profile-prices h2, .hormones-prices h2, .tumor-markers-prices h2, 
        .dengue-prices h2, .serology-prices h2 {
            color: #e65100;
            font-size: 2rem;
            font-weight: bold;
            text-align: center;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body class="bg-light-blue">

<!-- Navbar -->
<nav class="bg-blue-900 p-4">
    <div class="container mx-auto flex justify-between items-center">
        <a href="#" class="text-white text-2xl font-bold flex items-center">
            <img src="jeda.png" alt="Logo" class="h-12 mr-2"> <!-- Logo -->
            JEDA LABORATORY & DIAGNOSTIC CENTER
        </a>
        <div>
            <a href="login.php" class="text-white px-4 py-2 rounded hover:bg-blue-700">Login</a>
            <a href="register.php" class="text-white px-4 py-2 rounded hover:bg-blue-700">Register</a>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container mx-auto my-10 p-6 bg-white rounded-lg shadow-lg">
    <div class="flex flex-wrap">
        <!-- Carousel on the left -->
        <div class="w-full md:w-1/2 p-4">
            <div class="carousel">
                <div><img src="X-ray.jpg" alt="X-Ray" class="w-full rounded-lg"></div>
                <div><img src="ultrasound.jpg" alt="Ultrasound" class="w-full rounded-lg"></div>
            </div>
        </div>

        <!-- Clinic Hours on the right -->
        <div class="w-full md:w-1/2 p-4 clinic-hours">
            <h1 class="text-4xl font-bold text-center text-brown mb-6">We're Open!</h1>
            <h2 class="text-3xl text-center text-blue mb-4">Clinic Hours</h2>
            <div class="text-center mb-10">
                <p class="text-xl text-brown font-semibold">Mon - Fri: <span class="text-yellow">7:00 AM - 5:00 PM</span></p>
                <p class="text-xl text-brown font-semibold">Saturday: <span class="text-yellow">7:00 AM - 4:00 PM</span></p>
                <p class="text-xl text-brown font-semibold">Sunday: <span class="text-yellow">7:00 AM - 12:00 PM</span></p>
                <p class="text-2xl font-bold text-blue mt-4">Please Come in!</p>
            </div>
        </div>
    </div>

    <!-- 2D Echo Announcement -->
    <div class="service-announcement">
        <h2>2D ECHO IS NOW AVAILABLE</h2>
        <p>We care for your HEART</p>
        <p>Monday - Friday: 7:30 AM - 5:00 PM</p>
        <p>Saturday: 7:30 AM - 4:00 PM</p>
        <p>Sunday: 7:30 AM - 12:00 NN</p>
    </div>

    <!-- Fasting Blood Test Information -->
    <div class="fasting-info">
        <h2 class="text-2xl font-bold text-center mb-4">Panagsagana para iti Fasting Blood Test</h2>
        <ul class="list-disc pl-6">
            <li>Para iti Fasting Blood Sugar (Glucose) ken Lipid Tests, saan a mangan wenno uminum iti <span class="highlight">8 - 12 nga oras</span> sakbay a mapan iti laboratory.</li>
            <li>Kalpasan a malpas ti eksamen, nawayakan a mangituloy iti normal a rutinam.</li>
        </ul>
        <div class="instruction text-center mt-4">
            Suroten dagitoy nga instruksyon tapno nasaysayaat ti resultana
        </div>
    </div>

    <!-- Ultrasound Prices Section -->
    <div class="ultrasound-prices">
        <h2>ULTRASOUND PRICES</h2>
        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr class="bg-blue-900 text-white">
                    <th class="py-2 px-4 border-b-2 border-gray-300">UTZ Examinations</th>
                    <th class="py-2 px-4 border-b-2 border-gray-300">Non-Senior (PHP)</th>
                    <th class="py-2 px-4 border-b-2 border-gray-300">Senior (PHP)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">KUB (Kidney, Urinary Bladder)</td>
                    <td class="py-2 px-4 border-b border-gray-200">1200</td>
                    <td class="py-2 px-4 border-b border-gray-200">1050</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">KUB (w/ Prostate / Pelvic / Uterus)</td>
                    <td class="py-2 px-4 border-b border-gray-200">1400</td>
                    <td class="py-2 px-4 border-b border-gray-200">1150</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">Whole Abdomen</td>
                    <td class="py-2 px-4 border-b border-gray-200">1400</td>
                    <td class="py-2 px-4 border-b border-gray-200">1225</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">Whole Abdomen (w/ Prostate / Pelvic / Uterus)</td>
                    <td class="py-2 px-4 border-b border-gray-200">1600</td>
                    <td class="py-2 px-4 border-b border-gray-200">1400</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">Upper Abdomen (Liver, Gall Bladder, Pancreas, Spleen)</td>
                    <td class="py-2 px-4 border-b border-gray-200">1200</td>
                    <td class="py-2 px-4 border-b border-gray-200">1050</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">Lower Abdomen (Kidney, Urinary Bladder, Pelvic)</td>
                    <td class="py-2 px-4 border-b border-gray-200">1200</td>
                    <td class="py-2 px-4 border-b border-gray-200">1050</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">Breast (Single)</td>
                    <td class="py-2 px-4 border-b border-gray-200">1200</td>
                    <td class="py-2 px-4 border-b border-gray-200">1050</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">Breast (Both)</td>
                    <td class="py-2 px-4 border-b border-gray-200">1400</td>
                    <td class="py-2 px-4 border-b border-gray-200">1225</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">Single Organ</td>
                    <td class="py-2 px-4 border-b border-gray-200">1200</td>
                    <td class="py-2 px-4 border-b border-gray-200">1050</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">Soft Tissue</td>
                    <td class="py-2 px-4 border-b border-gray-200">1200</td>
                    <td class="py-2 px-4 border-b border-gray-200">1050</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Blood Chemistry Prices Section -->
    <div class="blood-chemistry-prices">
        <h2>BLOOD CHEMISTRY PRICES</h2>
        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr class="bg-blue-900 text-white">
                    <th class="py-2 px-4 border-b-2 border-gray-300">Blood Chemistry</th>
                    <th class="py-2 px-4 border-b-2 border-gray-300">Price (PHP)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">Albumin</td>
                    <td class="py-2 px-4 border-b border-gray-200">390</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">TPAG</td>
                    <td class="py-2 px-4 border-b border-gray-200">600</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">B1B2</td>
                    <td class="py-2 px-4 border-b border-gray-200">650</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">Iron</td>
                    <td class="py-2 px-4 border-b border-gray-200">1,050</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">TIBC</td>
                    <td class="py-2 px-4 border-b border-gray-200">1,050</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">Ferritin</td>
                    <td class="py-2 px-4 border-b border-gray-200">1,250</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">TROP I (Quant)</td>
                    <td class="py-2 px-4 border-b border-gray-200">1,950</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">HBA1C</td>
                    <td class="py-2 px-4 border-b border-gray-200">750</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Enzymes Prices Section -->
    <div class="enzymes-prices">
        <h2>ENZYMES PRICES</h2>
        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr class="bg-blue-900 text-white">
                    <th class="py-2 px-4 border-b-2 border-gray-300">Enzymes</th>
                    <th class="py-2 px-4 border-b-2 border-gray-300">Price (PHP)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">Alka. Phosphatase</td>
                    <td class="py-2 px-4 border-b border-gray-200">550</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">Amylase</td>
                    <td class="py-2 px-4 border-b border-gray-200">600</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">Lipase</td>
                    <td class="py-2 px-4 border-b border-gray-200">630</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">CPK-MB</td>
                    <td class="py-2 px-4 border-b border-gray-200">850</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">CPK-Total</td>
                    <td class="py-2 px-4 border-b border-gray-200">850</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">LDH</td>
                    <td class="py-2 px-4 border-b border-gray-200">630</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">GGTP</td>
                    <td class="py-2 px-4 border-b border-gray-200">650</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">SGOT/AST</td>
                    <td class="py-2 px-4 border-b border-gray-200">190</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">SGPT/ALT</td>
                    <td class="py-2 px-4 border-b border-gray-200">190</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Thyroid Function Prices Section -->
    <div class="thyroid-function-prices">
        <h2>THYROID FUNCTION TEST PRICES</h2>
        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr class="bg-blue-900 text-white">
                    <th class="py-2 px-4 border-b-2 border-gray-300">Thyroid Function Test</th>
                    <th class="py-2 px-4 border-b-2 border-gray-300">Price (PHP)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">T3</td>
                    <td class="py-2 px-4 border-b border-gray-200">750</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">T4</td>
                    <td class="py-2 px-4 border-b border-gray-200">750</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">FT3</td>
                    <td class="py-2 px-4 border-b border-gray-200">750</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">FT4</td>
                    <td class="py-2 px-4 border-b border-gray-200">750</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">TSH</td>
                    <td class="py-2 px-4 border-b border-gray-200">750</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">Thyroglobulin</td>
                    <td class="py-2 px-4 border-b border-gray-200">3,450</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Electrolytes Prices Section -->
    <div class="electrolytes-prices">
        <h2>ELECTROLYTES PRICES</h2>
        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr class="bg-blue-900 text-white">
                    <th class="py-2 px-4 border-b-2 border-gray-300">Electrolytes</th>
                    <th class="py-2 px-4 border-b-2 border-gray-300">Price (PHP)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">Calcium</td>
                    <td class="py-2 px-4 border-b border-gray-200">390</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">Ionized Calcium</td>
                    <td class="py-2 px-4 border-b border-gray-200">800</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">Inorg. Phosphorous</td>
                    <td class="py-2 px-4 border-b border-gray-200">630</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">Magnesium</td>
                    <td class="py-2 px-4 border-b border-gray-200">500</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Hematology Prices Section -->
    <div class="hematology-prices">
        <h2>HEMATOLOGY PRICES</h2>
        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr class="bg-blue-900 text-white">
                    <th class="py-2 px-4 border-b-2 border-gray-300">Hematology</th>
                    <th class="py-2 px-4 border-b-2 border-gray-300">Price (PHP)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">Retics CT.</td>
                    <td class="py-2 px-4 border-b border-gray-200">600</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">Protime</td>
                    <td class="py-2 px-4 border-b border-gray-200">650</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">APTT</td>
                    <td class="py-2 px-4 border-b border-gray-200">700</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">Peripheral Smear/CBC</td>
                    <td class="py-2 px-4 border-b border-gray-200">700</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">D-Dimer</td>
                    <td class="py-2 px-4 border-b border-gray-200">2,450</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Hepatitis Profile Prices Section -->
    <div class="hepatitis-profile-prices">
        <h2>HEPATITIS PROFILE PRICES</h2>
        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr class="bg-blue-900 text-white">
                    <th class="py-2 px-4 border-b-2 border-gray-300">Hepatitis Profile</th>
                    <th class="py-2 px-4 border-b-2 border-gray-300">Price (PHP)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">HbsAg w/ titer</td>
                    <td class="py-2 px-4 border-b border-gray-200">650</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">Anti-HBs</td>
                    <td class="py-2 px-4 border-b border-gray-200">670</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">HBeAg</td>
                    <td class="py-2 px-4 border-b border-gray-200">730</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">Anti-Hbe</td>
                    <td class="py-2 px-4 border-b border-gray-200">730</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">Anti-HBc IgG</td>
                    <td class="py-2 px-4 border-b border-gray-200">730</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">Anti-HBc IgM</td>
                    <td class="py-2 px-4 border-b border-gray-200">730</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">Anti-HAV IgG</td>
                    <td class="py-2 px-4 border-b border-gray-200">750</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">Anti-HCV (Screening)</td>
                    <td class="py-2 px-4 border-b border-gray-200">850</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">Anti-HCV (Titer)</td>
                    <td class="py-2 px-4 border-b border-gray-200">950</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">Hepatitis B Profile</td>
                    <td class="py-2 px-4 border-b border-gray-200">1950</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">Hepatitis Prof (ABC)</td>
                    <td class="py-2 px-4 border-b border-gray-200">2,450</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Hormones Prices Section -->
    <div class="hormones-prices">
        <h2>HORMONES PRICES</h2>
        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr class="bg-blue-900 text-white">
                    <th class="py-2 px-4 border-b-2 border-gray-300">Hormones</th>
                    <th class="py-2 px-4 border-b-2 border-gray-300">Price (PHP)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">FSH</td>
                    <td class="py-2 px-4 border-b border-gray-200">1,050</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">LH</td>
                    <td class="py-2 px-4 border-b border-gray-200">1,050</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">Prolactin</td>
                    <td class="py-2 px-4 border-b border-gray-200">1,050</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">Estradiol</td>
                    <td class="py-2 px-4 border-b border-gray-200">1,650</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">Estrogen</td>
                    <td class="py-2 px-4 border-b border-gray-200">1,850</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">Progesteron</td>
                    <td class="py-2 px-4 border-b border-gray-200">1,750</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">Cortisol</td>
                    <td class="py-2 px-4 border-b border-gray-200">1,150</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Tumor Markers Prices Section -->
    <div class="tumor-markers-prices">
        <h2>TUMOR MARKERS PRICES</h2>
        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr class="bg-blue-900 text-white">
                    <th class="py-2 px-4 border-b-2 border-gray-300">Tumor Markers</th>
                    <th class="py-2 px-4 border-b-2 border-gray-300">Price (PHP)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">AFP</td>
                    <td class="py-2 px-4 border-b border-gray-200">1,050</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">CEA</td>
                    <td class="py-2 px-4 border-b border-gray-200">1,050</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">PSA</td>
                    <td class="py-2 px-4 border-b border-gray-200">800</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">B-HCG</td>
                    <td class="py-2 px-4 border-b border-gray-200">1,050</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">CA 12-5</td>
                    <td class="py-2 px-4 border-b border-gray-200">1,450</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">CA 15-3</td>
                    <td class="py-2 px-4 border-b border-gray-200">1,950</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">CA 19-9</td>
                    <td class="py-2 px-4 border-b border-gray-200">1,950</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Dengue Prices Section -->
    <div class="dengue-prices">
        <h2>DENGUE TEST PRICES</h2>
        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr class="bg-blue-900 text-white">
                    <th class="py-2 px-4 border-b-2 border-gray-300">Dengue Test</th>
                    <th class="py-2 px-4 border-b-2 border-gray-300">Price (PHP)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">Dengue DUO (NS1, IgG, IgM)</td>
                    <td class="py-2 px-4 border-b border-gray-200">1,100</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Serology Prices Section -->
    <div class="serology-prices">
        <h2>SEROLOGY PRICES</h2>
        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr class="bg-blue-900 text-white">
                    <th class="py-2 px-4 border-b-2 border-gray-300">Serology</th>
                    <th class="py-2 px-4 border-b-2 border-gray-300">Price (PHP)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">TPHA (quanti)</td>
                    <td class="py-2 px-4 border-b border-gray-200">850</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">Procalcitonin</td>
                    <td class="py-2 px-4 border-b border-gray-200">2,450</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">ASO (quanti)</td>
                    <td class="py-2 px-4 border-b border-gray-200">1,050</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">CRP (quanti)</td>
                    <td class="py-2 px-4 border-b border-gray-200">1,050</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">RA Latex</td>
                    <td class="py-2 px-4 border-b border-gray-200">650</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">C3- 3 Days Result</td>
                    <td class="py-2 px-4 border-b border-gray-200">1,050</td>
                </tr>
                <tr>
                    <td class="py-2 px-4 border-b border-gray-200">ANA</td>
                    <td class="py-2 px-4 border-b border-gray-200">1,050</td>
                </tr>
            </tbody>
        </table>
    </div>
    </div>
    
    <!-- Continue with other sections as you had before -->
</div>

<!-- Footer -->
<footer class="bg-blue-900 p-4 mt-10">
    <div class="container mx-auto text-center text-white">
        <p>&copy; 2024 JEDA LABORATORY & DIAGNOSTIC CENTER. All Rights Reserved.</p>
    </div>
</footer>

<script>
    // Initialize the carousel
    $(document).ready(function(){
        $('.carousel').slick({
            dots: true,
            infinite: true,
            speed: 500,
            slidesToShow: 1,
            adaptiveHeight: true,
            autoplay: true,
            autoplaySpeed: 2000,
            cssEase: 'linear'
        });
    });
</script>
</body>
</html>
