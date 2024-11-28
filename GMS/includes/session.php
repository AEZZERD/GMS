
<?php
include('dbconnection.php');
session_start(); 

if (isset($_SESSION['adminID']))
{
    $adminID = $_SESSION['adminID'];

}
else if(isset($_SESSION['studentID'])){

   $studentID = $_SESSION['studentID'];
}

else{
  echo "<script type = \"text/javascript\">
  window.location = (\"../index.php\");
  </script>";

}
    
?>