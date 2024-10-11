<?php
require 'vendor/autoload.php';

function extractTextFromImage($imagePath) {
    // Path where Tesseract is installed (adjust if needed)
    $tesseractPath = 'tesseract'; // On Windows, use the full path if necessary
    $outputFile = 'ocr_output';
    $outputPath = "$outputFile.txt";

    // Command to run Tesseract OCR on the uploaded ID picture
    $command = "$tesseractPath " . escapeshellarg($imagePath) . " " . escapeshellarg($outputFile);

    // Execute the command
    exec($command);

    // Read the output from the OCR process
    $ocrText = file_get_contents($outputPath);

    // Clean up the output file
    unlink($outputPath);

    return $ocrText;
}

function validateID($id_picture_path, $user_name) {
    // Extract text from the uploaded image using Tesseract
    $extractedText = extractTextFromImage($id_picture_path);

    // Normalize the extracted text and user input for comparison
    $normalizedExtractedText = strtolower(trim($extractedText));
    $normalizedUserName = strtolower(trim($user_name));

    // Check if the user's name is found in the extracted text
    if (strpos($normalizedExtractedText, $normalizedUserName) !== false) {
        return true; // ID verification succeeded
    }

    return false; // ID verification failed
}
?>
