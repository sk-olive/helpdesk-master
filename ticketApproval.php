<?php
 include ("includes/connect.php");
if (isset( $_GET['id']) && isset( $_GET['head'])) 
{
    
    $sqllink = "SELECT `link` FROM `setting`";
    $resultlink = mysqli_query($con, $sqllink);
    $link = "";
    while($listlink=mysqli_fetch_assoc($resultlink))
    {
    $link=$listlink["link"];
    }

    $id = $_GET['id'];
    $query = mysqli_query($con, "SELECT * FROM `request` WHERE `id`='".$id."'");
    $row = mysqli_fetch_assoc($query);
    $approval_date = $row['ict_approval_date'];

	if($approval_date != NULL){
        echo "<script>alert('Request already approved! Date of approval: $approval_date');</script>";
        echo "<script>location.href= '$link/login.php';</script>";
    }
    else{
        $datenow = date("Y-m-d");
        $dateToday = date('Y-m-d H:i:s', time());
        $sql = "UPDATE `request` SET `status2` = 'inprogress', `admin_approved_date`='$datenow', `ict_approval_date`='$dateToday' WHERE `id` = '$id';";
        $results = mysqli_query($con,$sql);
              if ($results) {
                echo "<script>alert('Request has been approved successfully!');</script>";
                echo "<script>location.href='$link/login.php';</script>";
            } else {
                echo "<script>alert('There is a problem with filing. Please contact your administrator.');</script>";
            }
    } 
}

if (isset( $_GET['id']) && isset( $_GET['requestor'])) 
{
    $id = $_GET['id'];
    $query = mysqli_query($con, "SELECT * FROM `request` WHERE `id`='".$id."'");
    $row = mysqli_fetch_assoc($query);
    $approval_date = $row['requestor_approval_date'];

	if($approval_date != NULL){
        echo "<script>alert('Request already approved! Date of approval: $approval_date');</script>";
        echo "<script>location.href='$link/login.php';</script>";
    }
    else{
        $dateToday = date('Y-m-d H:i:s', time());
        $sql = "UPDATE `request` SET `requestor_approval_date`='$dateToday' WHERE `id` = '$id';";
        $results = mysqli_query($con,$sql);
        if ($results) {
            echo "<script>alert('Request has been approved successfully!');</script>";
            echo "<script>location.href='$link/login.php';</script>";
        } else {
            echo "<script>alert('There is a problem with filing. Please contact your administrator.');</script>";
        }
    }
    }


?>