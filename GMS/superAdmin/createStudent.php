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
        $file = $_FILES['file'];

        $allowedTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'];
        if (!in_array($file['type'], $allowedTypes)) {
            $registrationMessage = "Invalid file type. Please upload an Excel file.";
        } else {
            $spreadsheet = IOFactory::load($file['tmp_name']);
            $worksheet = $spreadsheet->getActiveSheet();

            foreach ($worksheet->getRowIterator(2) as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $data = [];
                
                foreach ($cellIterator as $cell) {
                    $data[] = $cell->getValue();
                }

                if (count($data) >= 4) {
                    $fName = $data[1];
                    $studentID = $data[2];
                    $password = $data[3];
                    $studentEmail = $data[4];
                    $totalCredit = 0;
                    $statusGrad = " ";

                    // Prepare the SQL statement
                    $insertSql = "INSERT INTO tblstudent (fName, studentID, password, studentEmail, totalCredit, statusGrad) VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt = $con->prepare($insertSql);
                    $stmt->bind_param("ssssis", $fName, $studentID, $password, $studentEmail, $totalCredit, $statusGrad);

                    if (!$stmt->execute()) {
                        $registrationMessage .= "Error inserting student: " . $stmt->error . "<br>";
                    }
                    $stmt->close();
                }
            }
            $registrationMessage .= "student uploaded successfully.";
        }
    } else {
        $registrationMessage = "Error uploading file.";
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
<script>
    function showValues(str) {
        if (str == "") {
            document.getElementById("txtHint").innerHTML = "";
            return;
        } else { 
            if (window.XMLHttpRequest) {
                xmlhttp = new XMLHttpRequest();
            } else {
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("txtHint").innerHTML = this.responseText;
                }
            };
            xmlhttp.open("GET","ajaxCall2.php?fid="+str,true);
            xmlhttp.send();
        }
    }
</script>
</head>
<body>
    <?php $page="student"; include 'includes/leftMenu.php';?>
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
                                    <li><a href="#">Student</a></li>
                                    <li class="active">Add Student</li>
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
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title"><h2 align="center">Upload New Students</h2></strong>
                            </div>
                            <div class="card-body">
                                <div id="pay-invoice">
                                    <div class="card-body">
                                        <div class="<?php echo $alertStyle; ?>" role="alert"><?php echo $statusMsg; ?></div>
                                            <form method="POST" action="" enctype="multipart/form-data">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label for="file" class="control-label mb-1">Upload Excel File</label>
                                                            <input id="file" name="file" type="file" class="form-control" required accept=".xls,.xlsx">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                <button type="submit" name="submit" class="btn btn-success">Upload Students</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br><br>
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title"><h2 align="center">All Student</h2></strong>
                            </div>
                            <div class="card-body">
                                <table id="bootstrap-data-table" class="table table-hover table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>FullName</th>
                                            <th>studentID</th>
                                            <th>studentIC</th>
                                            <th>studentEmail</th>
                                            <th>totalCredit</th>
                                            <th>statusGrad</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                            <?php
                    $ret=mysqli_query($con,"SELECT tblstudent.Id, tblstudent.fName,tblstudent.studentID,
                    tblstudent.password,tblstudent.studentEmail,tblstudent.totalCredit,tblstudent.statusGrad
                    from tblstudent");
                    $cnt=1;
                    while ($row=mysqli_fetch_array($ret)) {
                                        ?>
                    <tr>
                    <td><?php echo $cnt;?></td>
                    <td><?php  echo $row['fName'];?></td>
                    <td><?php  echo $row['studentID'];?></td>
                    <td><?php  echo $row['password'];?></td>
                    <td><?php  echo $row['studentEmail'];?></td>
                    <td><?php  echo $row['totalCredit'];?></td>
                    <td><?php  echo $row['statusGrad'];?></td>
                    <td><a href="editStudent.php?editStudentId=<?php echo $row['studentID'];?>" title="Edit Details"><i class="fa fa-edit fa-1x"></i></a>
                    <a onclick="return confirm('Are you sure you want to delete?')" href="deleteStudent.php?delid=<?php echo $row['studentID'];?>" title="Delete Student Details"><i class="fa fa-trash fa-1x"></i></a></td>
                    </tr>
                    <?php 
                    $cnt=$cnt+1;
                    }?>
                                                                                            
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
        <?php include 'includes/footer.php';?>
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
