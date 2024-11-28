<?php
include('../includes/dbconnection.php');
include('../includes/session.php');
require '../vendor/autoload.php'; // Include the PhpSpreadsheet library

use PhpOffice\PhpSpreadsheet\IOFactory;

error_reporting(0);
$alertStyle = "";
$statusMsg = "";
if (isset($_POST['submit'])) {
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $file = $_FILES['file']['tmp_name'];
        // Load the spreadsheet
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();

        // Get course codes from the first row (D1, F1, H1)
        $courseCodes = [];
        $courseColumns = [4, 6, 8, 10, 12]; // D=4, F=6, H=8
        foreach ($courseColumns as $col) {
            $courseCode = $sheet->getCellByColumnAndRow($col, 1)->getValue();
            $courseCodes[] = $courseCode; // Store course codes for later use
        }

        // Loop through each row of the spreadsheet starting from row 3
        for ($row = 3; $row <= $sheet->getHighestRow(); $row++) {
            $studentName = $sheet->getCellByColumnAndRow(2, $row)->getValue(); // Column B
            $studentID = $sheet->getCellByColumnAndRow(3, $row)->getValue(); // Column C

            // Loop through each course code and corresponding result
            foreach ($courseColumns as $index => $col) {
                // Determine the result column based on the course index
                $resultColumn = $col + 1; // E=5, G=7, I=9
                $result = $sheet->getCellByColumnAndRow($resultColumn, $row)->getValue(); // Get result from E, G, I

                // Insert data into GRADE table
                $stmt = $con->prepare("INSERT INTO tblgrade (studentID, courseCode, resultOne) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $studentID, $courseCodes[$index], $result);
                $stmt->execute();
                $stmt->close();

                // Check if the result is a passing grade (you can define what a passing grade is)
                if (!empty($result) && $result !== 'F') { // Assuming 'F' is a failing grade
                    // Update total credits for the student
                    $stmt = $con->prepare("SELECT credit FROM tblcourse WHERE courseCode = ?");
                    $stmt->bind_param("s", $courseCodes[$index]);
                    $stmt->execute();
                    $stmt->bind_result($credit);
                    $stmt->fetch();
                    $stmt->close();

                    // Update total credits for the student if credit is found
                    if ($credit) {
                        $stmt = $con->prepare("UPDATE tblstudent SET totalCredit = totalCredit + ? WHERE studentID = ?");
                        $stmt->bind_param("is", $credit, $studentID);
                        $stmt->execute();
                        $stmt->close();
                    }
                }
            }
        }
        echo "Data uploaded successfully!";
    } else {
        echo "File upload error!";
    }
}
?>
<!doctype html>
<html class="no-js" lang="">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?php include 'includes/title.php';?>
    <meta name="description" content="Ela Admin - HTML5 Admin Template">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="apple-touch-icon" href="https://i.imgur.com/QRAUqs9.png">
    <link rel="shortcut icon" href="../assets/img/student-grade.png" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/normalize.css@8.0.0/normalize.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lykmapipo/themify-icons@0.1.2/css/themify-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pixeden-stroke-7-icon@1.2.3/pe-icon-7-stroke/dist/pe-icon-7-stroke.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.2.0/css/flag-icon.min.css">
    <link rel="stylesheet" href="../assets/css/cs-skin-elastic.css">
    <link rel="stylesheet" href="../assets/css/lib/datatable/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style2.css">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800' rel='stylesheet' type='text/css'>
</head>
<body>
    <?php $page="result"; include 'includes/leftMenu.php';?>
    <div id="right-panel" class="right-panel">
        <?php include 'includes/header.php';?>
        <div class="breadcrumbs">
            <div class="breadcrumbs-inner">
                <div class="row m-0">
                    <div class="col-sm-4">
                        <div class="page-header float-left">
                            <div class="page-title">
                                <h1>Dashboard</h1>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <div class="page-header float-right">
                            <div class="page-title">
                                <ol class="breadcrumb text-right">
                                    <li><a href="#">Dashboard</a></li>
                                    <li><a href="#">Result</a></li>
                                    <li class="active">All Students Results</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="animated fadeIn">
                <div class="row">
                    <br><br>
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title"><h2 align="center">All Students Result</h2></strong>
                            </div>
                            <div class="card-body">
                                <table id="bootstrap-data-table" class="table table-hover table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Full Name</th>
                                            <th>Student ID</th>
                                            <th>Total Credit</th>
                                            <th>Status Grad</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $ret = mysqli_query($con, "
                                        SELECT 
                                            s.Id,
                                            s.fName,
                                            s.studentID,
                                            s.studentEmail,
                                            s.totalCredit,
                                            CASE 
                                                WHEN s.totalCredit >= 120 THEN 'PASS' 
                                                ELSE 'FAIL' 
                                            END AS statusGrad
                                        FROM 
                                            tblstudent s
                                        LEFT JOIN 
                                            tblgrade g ON s.studentID = g.studentID
                                        GROUP BY 
                                            s.studentID
                                    ");
                                        $cnt = 1;
                                        while ($row = mysqli_fetch_array($ret)) {
                                        ?>
                                        <tr>
                                            <td><?php echo $cnt; ?></td>
                                            <td><?php echo $row['fName']; ?></td>
                                            <td><?php echo $row['studentID']; ?></td>
                                            <td><?php echo $row['totalCredit']; ?></td>
                                            <td><?php echo $row['statusGrad']; ?></td>
                                            <td>
                                                <button class="btn btn-info" onclick="window.open('exportResult.php?student_id=<?php echo urlencode($row['studentID']); ?>', '_blank');">Show Results</button>
                                            </td>
                                        </tr>
                                        <?php 
                                        $cnt++;
                                        }?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <div class="clearfix"></div>
    <?php include 'includes/footer.php'; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.4/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-match-height@0.7.2/dist/jquery.matchHeight.min.js"></script>
    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/lib/data-table/datatables.min.js"></script>
    <script src="../assets/js/lib/data-table/dataTables.bootstrap.min.js"></script>
    <script src="../assets/js/lib/data-table/dataTables.buttons.min.js"></script>
    <script src="../assets/js/lib/data-table/buttons.bootstrap.min.js"></script>
    <script src="../assets/js/lib/data-table/jszip.min.js"></script>
    <script src="../assets/js/lib/data-table/vfs_fonts.js"></script>
    <script src="../assets/js/lib/data-table/buttons.html5.min.js"></script>
    <script src="../assets/js/lib/data-table/buttons.print.min.js"></script>
    <script src="../assets/js/lib/data-table/buttons.colVis.min.js"></script>
    <script src="../assets/js/init/datatables-init.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#bootstrap-data-table-export').DataTable();
        });

        $('#menuToggle').on('click', function(event) {
            var windowWidth = $(window).width();   		 
            if (windowWidth<1010) { 
                $('body').removeClass('open'); 
                if (windowWidth<760){ 
                    $('#left-panel').slideToggle(); 
                } else {
                    $('#left-panel').toggleClass('open-menu');  
                } 
            } else {
                $('body').toggleClass('open');
                $('#left-panel').removeClass('open-menu');  
            } 
        }); 
    </script>
</body>
</html>