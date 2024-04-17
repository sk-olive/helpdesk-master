<?php
 include ("includes/connect.php");
if (isset( $_GET['id']) && isset( $_GET['head'])) 
{
    $id = $_GET['id'];
    $datenow = date("Y-m-d");
    $dateToday = date('Y-m-d H:i:s', time());
    $sql = "UPDATE `request` SET status2 = `inprogress`, `admin_approved_date`='$datenow', `ict_approval_date`='$dateToday' WHERE `id` = '$id';";
    $results = mysqli_query($con,$sql);
          if ($results) {
            echo "<script>alert('Request has been approved successfully!');</script>";
            echo "<script>location.href='http://helpdesk.glory.ph/helpdesk/login.php';</script>";
        } else {
            echo "<script>alert('There is a problem with filing. Please contact your administrator.');</script>";
        }
}

if (isset( $_GET['id']) && isset( $_GET['requestor'])) 
{
    $id = $_GET['id'];

    $dateToday = date('Y-m-d H:i:s', time());
    $sql = "UPDATE `request` SET `requestor_approval_date`='$dateToday' WHERE `id` = '$id';";
    $results = mysqli_query($con,$sql);

    if ($results) {
        echo "<script>alert('Request has been approved successfully!');</script>";
        echo "<script>location.href='http://helpdesk.glory.ph/helpdesk/login.php';</script>";
    } else {
        echo "<script>alert('There is a problem with filing. Please contact your administrator.');</script>";
    }
}

?>