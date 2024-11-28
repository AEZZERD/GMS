<?php
include('../includes/dbconnection.php');
include('../includes/session.php');
require '../vendor/autoload.php'; // Include the PhpSpreadsheet library

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Suppress error reporting
error_reporting(0);

// Check if student ID is provided
if (isset($_GET['student_id'])) {
    $studentID = $_GET['student_id'];

    // Fetch student data along with course codes and results
    $query = "
        SELECT s.studentID, s.fName, s.totalCredit, c.courseCode, g.session, g.resultOne 
        FROM tblstudent s
        LEFT JOIN tblgrade g ON s.studentID = g.studentID
        LEFT JOIN tblcourse c ON g.courseCode = c.courseCode
        WHERE s.studentID = ?
    ";
    
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $studentID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Load the Excel template
        $spreadsheet = IOFactory::load('template.xlsx');
        $sheet = $spreadsheet->getActiveSheet();

        // Set specific coordinates for Student ID and Name
        $sheet->setCellValue('E4', ' ' . $studentID);
        $studentData = $result->fetch_assoc();
        $sheet->setCellValue('C4', ' ' . $studentData['fName']);
        $sheet->setCellValue('E85', ' ' . $studentData['totalCredit']);
        
        // Reset the pointer to the result set
        $result->data_seek(0);

        // Store grades in an associative array
        $grades = [];
        while ($data = $result->fetch_assoc()) {
            // Store both session and resultOne in the grades array
            $grades[$data['courseCode']] = [
                'session' => $data['session'],
                'resultOne' => $data['resultOne']
            ];
        }

        // Search for course codes in the template and insert grades
        $highestRow = $sheet->getHighestRow();

        for ($row = 9; $row <= $highestRow; $row++) {
            $courseCodeCell = $sheet->getCell('B' . $row)->getValue();

            if (isset($grades[$courseCodeCell])) {
                // Get the session associated with the current course code
                $sessionValue = $grades[$courseCodeCell]['session'];
                $resultValue = $grades[$courseCodeCell]['resultOne'];
        
                $sheet->setCellValue('G' . $row, $sessionValue); // Set session in column G
                $sheet->setCellValue('H' . $row, $resultValue); // Set resultOne in column H
            }
        }

        // Set filename and download
        $writer = new Xlsx($spreadsheet);
        $fileName = 'student_data_' . $studentID . '.xlsx';
        
        // Clear any previous output
        ob_end_clean();
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $writer->save('php://output');
        exit;
    } else {
        echo "No records found for the given student ID.";
    }
} else {
    echo "Student ID is required.";
}

$con->close();
?>