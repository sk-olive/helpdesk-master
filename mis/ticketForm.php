<?php 


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//set timeout
$timeout = 3600;
ini_set( "session.gc_maxlifetime", $timeout );
ini_set( "session.cookie_lifetime", $timeout );
$s_name = session_name();
$url1=$_SERVER['REQUEST_URI'];
    header("Refresh: 500; URL=$url1");
if(isset( $_COOKIE[ $s_name ] )) {
    setcookie( $s_name, $_COOKIE[ $s_name ], time() + $timeout, '/' );    
}
else
    echo "Session is expired.<br/>";
// end of set timeout



    session_start();

    function convertToSentenceCase($string) {
        $sentences = preg_split('/(?<=[.?!])\s+/', $string, -1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
        $output = '';
        foreach ($sentences as $sentence) {
            $sentence = trim($sentence);
            if (!empty($sentence)) {
                $sentence = strtolower($sentence);
                $sentence = ucfirst($sentence);
                $output .= $sentence . ' ';
            }
        }
        $output = rtrim($output); // Remove trailing space
        return $output;
    }
    
    // $requestor=$_SESSION['name'];


    if(!isset($_SESSION['connected'])){
      header("location: index.php");
    }

    include ("../includes/connect.php");

    $sqllink = "SELECT `link` FROM `setting`";
    $resultlink = mysqli_query($con, $sqllink);
    $link = "";
    while($listlink=mysqli_fetch_assoc($resultlink))
    {
    $link=$listlink["link"];
    
    
      }


    $user_dept = $_SESSION['department'];
    $user_level=$_SESSION['level'];
    $user_name=$_SESSION['name'];
    $username = $_SESSION['username'];


    $sql1 = "Select * FROM `user` WHERE `level` = 'admin'";
    $result = mysqli_query($con, $sql1);
    while($list=mysqli_fetch_assoc($result))
    {
    $adminemail=$list["email"];
    $adminname=$list["name"];

    }    
    
         //code for submiting the JO

        $dest_path = "";
        $jono=date("ym-dH-is");

        if(isset($_POST['submitTicket'])){
            $selectedOption = $_POST['immediateHeadSelect'];
            $requestor = $_POST['r_name'];
            $requestorIdnumber = $_POST['r_IdNumber'];
            $requestorEmail = $_POST['r_email'];
            $requestorDepartment  = $_POST['r_department'];
            $immediateHeadEmail = $_POST['immediateHeadEmail'];
            $r_categories = $_POST['r_categories'];
            $r_cat_level = $_POST['r_cat_level'];
            if(isset($_POST['r_personnels'])) {
                $r_personnels = $_POST['r_personnels'];
                $r_personnelsName = $_POST['r_personnelsName'];
            }else
            {
                $r_personnels = NULL;
                $r_personnelsName = NULL;
            }
            $detailsOfRequest = $_POST['detailsOfRequest'];
            $datenow = date("Y-m-d");
            $ticket_category = $_POST['r_categories'];
            $onthespot_ticket ="";
            if(isset($_POST['on_the_spot'])) {
                $onthespot_ticket = $_POST['on_the_spot'];
                $action = $_POST['requestAction'];
                $recommendation = $_POST['recommendation'];
                $status = "Done";
            }
            else{
                $onthespot_ticket = NULL;
                $action = NULL;
                $recommendation = NULL;
                $status = "admin";
            } 

        
            $sql = mysqli_query($con,"INSERT INTO request (date_filled, status2, requestor, requestorUsername, email, department, request_type, request_to, request_details, assignedPersonnel, assignedPersonnelName, action, recommendation, onthespot_ticket, ticket_category, category_level, ticket_filer)
            VALUES ('$datenow', '$status', '$requestor','$requestorIdnumber', '$requestorEmail', '$requestorDepartment', 'Technical Support', 'mis', '$detailsOfRequest', '$r_personnels', '$r_personnelsName', '$action', '$recommendation', '$onthespot_ticket', '$ticket_category', '$r_cat_level', '$user_name')");
            


            if($sql){
                $sqllink = "SELECT `link` FROM `setting`";
                $resultlink = mysqli_query($con, $sqllink);
                $link = "";
                while($listlink=mysqli_fetch_assoc($resultlink))
                {
                $link=$listlink["link"];
                }

                $getid = mysqli_query($con, "SELECT id FROM request ORDER BY id DESC LIMIT 1");
                $row = mysqli_fetch_assoc($getid);
                $id = $row['id'];
                $date = new DateTime($datenow);
                $date = $date->format('ym');
                $ticketNumber = 'TS-'.$date.'-'.$id.'';

                $headApprovalLink = 'http://192.168.60.47/helpdesk-master/ticketApproval.php?id='.$id.'&head=true';
                $requestorApprovalLink = 'http://192.168.60.47/helpdesk-master/ticketApproval.php?id='.$id.'&requestor=true';
                $sql2 = "Select * FROM `sender`";
                $result2 = mysqli_query($con, $sql2);
                while($list=mysqli_fetch_assoc($result2))
                {
                $account=$list["email"];
                $accountpass=$list["password"];
                }    

                 require '../vendor/autoload.php';
    
                 $mail = new PHPMailer(true);                      
                 try {
                  //Server settings
                    $mail->isSMTP();                                      // Set mailer to use SMTP
                    $mail->Host = 'mail.glorylocal.com.ph';               // Specify main and backup SMTP servers
                    $mail->SMTPAuth = true;                               // Enable SMTP authentication
                    $mail->Username = $account;    
                    $mail->Password = $accountpass;                     
                    $mail->SMTPOptions = array(
                        'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                        )
                                                );                         
                    $mail->SMTPSecure = 'none';                           
                    $mail->Port = 465;                                   
            
                    //Send Email
                    $mail->setFrom('mis.dev@glory.com.ph', 'Helpdesk');
                           

                    if ($onthespot_ticket == '1'){
                    $subject = 'On the Spot Ticket Request Closed';
                    // Message to Requestor
                    $mail->addAddress($requestorEmail);
                    $mail->isHTML(true);                                  
                    $mail->Subject = $subject;
                    $mail->Body    = 'Hi '.$requestor.',<br> <br>   Your ticket request has been closed. Please find the details below: <br><br> Ticket No.: '.$ticketNumber.'<br> Requestor: '.$requestor.'<br> Requestor Email: '.$requestorEmail.'<br> Requestor Department: '.$requestorDepartment.'<br> Request Details: '.$detailsOfRequest.'<br> Assigned Personnel: '.$r_personnelsName.'<br> Action: '.$action.'<br> Ticket Category: '.$ticket_category.'<br> Ticket Filer: '.$user_name.'<br><br> If you agree with the closure of this ticket, please click the link below to confirm: <br> Click <a href="'.$requestorApprovalLink.'">this</a>  to confirm. <br><br><br> This is a generated email. Please do not reply. <br><br> Helpdesk';
            
                    $mail->send();
                         
                    //Message to ICT HEAD & Dept Head
                    $mail->clearAddresses();
                    $mail->addAddress($immediateHeadEmail);  // dept head          
                    $mail->AddCC('j.nemedez@glory.com.ph');  // ict head     
                    
                    $mail->isHTML(true);                                  
                    $mail->Subject = $subject;
                    $mail->Body    = 'Hi,<br> <br>   A ticket request has been closed. Please find the details below: <br><br> Ticket No.: '.$ticketNumber.'<br> Requestor: '.$requestor.'<br> Requestor Email: '.$requestorEmail.'<br> Requestor Department: '.$requestorDepartment.'<br> Request Details: '.$detailsOfRequest.'<br> Assigned Personnel: '.$r_personnelsName.'<br> Action: '.$action.'<br> Ticket Category: '.$ticket_category.'<br> Ticket Filer: '.$user_name.'<br><br>  This is a generated email. Please do not reply. <br><br> Helpdesk';;

                    $mail->send();                               
                    }
                    else
                    {
                    $subject = 'Ticket Request Created';
                    // Message to Requestor & Dept Head
                    $mail->AddCC($immediateHeadEmail); // dept head   
                    $mail->addAddress($requestorEmail); // requestor
                    $mail->isHTML(true);                                  
                    $mail->Subject = $subject;
                    $mail->Body    = 'Hi '.$requestor.',<br> <br>   Your ticket request has been created. Please find the details below: <br><br> Ticket No.: '.$ticketNumber.'<br> Requestor: '.$requestor.'<br> Requestor Email: '.$requestorEmail.'<br> Requestor Department: '.$requestorDepartment.'<br> Request Details: '.$detailsOfRequest.'<br> Assigned Personnel: '.$r_personnelsName.'<br>  Ticket Category: '.$ticket_category.'<br> Ticket Filer: '.$user_name.'<br><br> You can check the status of your ticket by signing in into our Helpdesk <br> Click this '.$link.' to signin. <br><br><br> This is a generated email. Please do not reply. <br><br> Helpdesk';
            
                    $mail->send();

                    //Message to ICT HEAD
                    $mail->clearAddresses();
                    $mail->clearCCs();
                      // $mail->AddCC('j.nemedez@glory.com.ph');  // ict head     
                    $mail->addAddress('o.bugarin@glory.com.ph');   // ict head        
                    $mail->isHTML(true);                                  
                    $mail->Subject = $subject;
                    $mail->Body    = 'Hi,<br> <br>   A ticket request has been created. Please find the details below: <br><br> Ticket No.: '.$ticketNumber.'<br> Requestor: '.$requestor.'<br>  Requestor Email: '.$requestorEmail.'<br> Requestor Department: '.$requestorDepartment.'<br> Request Details: '.$detailsOfRequest.'<br> Assigned Personnel: '.$r_personnelsName.'<br>  Ticket Category: '.$ticket_category.'<br> Ticket Filer: '.$user_name.'<br><br> Please approve or reject the ticket by signing in into our Helpdesk <br> Click this '.$link.' to signin. Or just click the link below to approve: <br> Click <a href="'.$headApprovalLink.'">this</a> to approve.<br><br><br> This is a generated email. Please do not reply. <br><br> Helpdesk';

                    $mail->send();     
                    }
                    $_SESSION['message'] = 'Message has been sent';
                    echo "<script>alert('Thank you! Your request has been sent.') </script>";
                    echo "<script> location.href='index.php'; </script>";

                        // header("location: form.php");
                    } catch (Exception $e) {
                        $_SESSION['message'] = 'Message could not be sent. Mailer Error: '.$mail->ErrorInfo;
                        $error = $_SESSION['message'];
                    echo "<script>alert('Message could not be sent. Mailer Error. $error') </script>";

                    }

               
               
            }
            else{
                echo "<script>alert('There is a problem with filing. Please contact your administrator.') </script>";

            }
        }
                ?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Form</title>
    <link rel="shortcut icon" href="../resources/img/helpdesk.png">
    
    <!-- font awesome -->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" /> -->
    <link rel="stylesheet" href="../fontawesome-free-6.2.0-web/css/all.min.css">
    <link href="../node_modules/select2/dist/css/select2.min.css" rel="stylesheet" />

    
  
    <link rel="stylesheet" href="../node_modules/DataTables/datatables.min.css">
    <link rel="stylesheet" type="text/css" href="../node_modules/DataTables/Responsive-2.3.0/css/responsive.dataTables.min.css"/>

    <link rel="stylesheet" href="index.css">
    <style>

</style>
     <!-- tailwind play cdn -->
    <!-- <script src="https://cdn.tailwindcss.com"></script> -->
    <script src="../cdn_tailwindcss.js"></script>

  


    <!-- <link href="/dist/output.css" rel="stylesheet"> -->


     <!-- from flowbite cdn -->
    <!-- <link rel="stylesheet" href="https://unpkg.com/flowbite@1.5.3/dist/flowbite.min.css" /> -->
    <link rel="stylesheet" href="../node_modules/flowbite/dist/flowbite.min.css" />


    <!-- <link rel="stylesheet" href="css/style.css" /> -->




</head>
<body   class="static  bg-white dark:bg-gray-900"  >

    <!-- nav -->
    <?php require_once 'nav.php';?>


<!-- main -->





<div class=" ml-72 flex mt-16  left-10 right-5  flex-col  px-14 sm:px-8  pt-6 pb-14 z-50 ">
  
<form class="px-20" method="POST" accept-charset="utf-8" enctype="multipart/form-data">
    <div class=" p-2 border border-inherit rounded-md ">
        <div class="flex items-center font-semibold"> 
        <h3 class=" flex items-center text-xl  text-gray-900 ">
                    Create a Ticket
                </h3>
                <div class="flex items-center order-1 w-2/3"></div>
                <div class="flex items-center order-2">
                <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="checkbox" id="on_the_spot" name="on_the_spot" value="1" class="sr-only peer">
                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                <span class="ms-3 text-lg font-semibold text-gray-900 ">On the Spot</span>
                </label>
                </div>

        </div>
<hr class="mt-2">
<br>
        <h3 class=" font-light text-md text-gray-900 ">
                    Requestors' Details
                </h3>
                <br>

                <div class="grid md:grid-cols-2 md:gap-x-6 gap-y-3">
    <div class="relative z-0 w-full  group">
    <label for="r_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name</label>
  <select id="r_name" name="r_name" class="js-example-basic-single bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
  <option disabled selected>Search Name</option>

  <?php
             //    $sql1 = "Select * FROM `user` WHERE `username`='$username'";
               $sql1 = "Select * FROM `user`";
               $result = mysqli_query($con, $sql1);
                while($list=mysqli_fetch_assoc($result))
                {
                    $userId=$list["id"];
                    $name=$list["name"];
                    $department=$list["department"];
                    $username=$list["username"];
                    $email=$list["email"];


                     ?>

                   <!-- <option selected  disabled class="text-gray-900">Choose Head:</option>  -->
                  <option value="<?php echo  $name; ?>" data-email = "<?php echo  $email; ?>" data-department = "<?php echo  $department; ?>"  data-username = "<?php echo  $username; ?>"> <?php echo  $name; ?>  </option> <?php 
                    
                }    

                ?>
  </select>
    </div>
    <div class="relative z-0 w-full  group">
    <label for="r_IdNumber" class="block mb-2 text- font-medium text-gray-900 dark:text-white">ID Number</label>
    <input type="text" id="r_IdNumber" name="r_IdNumber" class="bg-gray-50 border border-gray-300 text-gray-900 text-xl rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"  required />
    </div>

    <div class="relative z-0 w-full  group">
    <label for="r_email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your email</label>
    <input type="email" id="r_email" name="r_email" class="bg-gray-50 border border-gray-300 text-gray-900 text-xl rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="name@glory.com.ph" required />
    </div>

    
    <div class="relative z-0 w-full  group">
    <label for="r_department" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Department</label>
    <input type="text" id="r_department" name="r_department" class="bg-gray-50 border border-gray-300 text-gray-900 text-xl rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"  required />
    </div>
    <div class="relative z-0 w-full  group ">
    <label for="" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Immediate Head</label>
  <select id="immediateHeadSelect" name="immediateHeadSelect" class="js-example-basic-single bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
  <option disabled selected>Search Immediate</option>

  
  </select>
    </div>
    <div class="relative z-0 w-full  group">
    <label for="r_department" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Immediate Heads' Email</label>
    <input type="text" name="immediateHeadEmail" id="immediateHeadEmail" class="bg-gray-50 border border-gray-300 text-gray-900 text-xl rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"  required />
    </div>
  </div>
  <hr class="mt-2">
<br>
        <h3 class=" font-light text-md text-gray-900 ">
                    Requests' Details
                </h3>
                <br>
                <div class="grid md:grid-cols-3 md:gap-x-6 gap-y-3">
                <div class="relative z-0 w-full  group">
                        <label for="r_categories" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Categories</label>
                    <select id="r_categories" name="r_categories" class="js-example-basic-single bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option disabled selected>Search Categories</option>

                    <?php
                                //    $sql1 = "Select * FROM `user` WHERE `username`='$username'";
                                $sql1 = "Select * FROM `categories` WHERE req_type = 'TS'  ";
                                $result = mysqli_query($con, $sql1);
                                    while($list=mysqli_fetch_assoc($result))
                                    {
                                        $c_name=$list["c_name"];
                                        $hours=$list["hours"];
                                        $level=$list["level"];
                                        ?>

                                    <!-- <option selected  disabled class="text-gray-900">Choose Head:</option>  -->
                                    <option value="<?php echo  $c_name; ?>" data-hours = "<?php echo  $hours; ?>" data-level = "<?php echo  $level; ?>" ><?php echo  $c_name; ?>   </option> <?php 
                                        
                                    }    

                                    ?>
                    </select>
    </div>
    <div class="relative z-0 w-full  group">
    <label for="r_cat_level" class="block mb-2 text- font-medium text-gray-900 dark:text-white">Priority Level</label>
    <input type="text" id="r_cat_level" name="r_cat_level" class="bg-gray-50 border border-gray-300 text-gray-900 text-xl rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"  required />
    </div>
    <div class="relative z-0 w-full  group">
    <label for="r_cat_hours" class="block mb-2 text- font-medium text-gray-900 dark:text-white">Minutes/Hours</label>
    <input type="text" id="r_cat_hours" name="r_cat_hours" class="bg-gray-50 border border-gray-300 text-gray-900 text-xl rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"  required />
    </div>
                </div>

                <div class="grid md:grid-cols-1 md:gap-x-6 gap-y-3">
                <div class="relative z-0 w-full  group">
                        <label for="r_personnels" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Personnel</label>
                    <select id="r_personnels" name="r_personnels" class="js-example-basic-single bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option disabled selected>Search Personnel</option>

                    <?php
                                //    $sql1 = "Select * FROM `user` WHERE `username`='$username'";
                                $sql1 = "SELECT u.*, 
            (SELECT COUNT(id) FROM request 
             WHERE  `status2` = 'inprogress' 
             AND `assignedPersonnel` = u.username) AS 'pending'
     FROM `user` u WHERE u.level = 'mis' or u.level = 'admin' AND u.leader = 'mis'";
                                $result = mysqli_query($con, $sql1);
                                    while($row=mysqli_fetch_assoc($result))
                                    {
                                        // $name=$list["name"];
                                        // $username=$list["username"];
                                        // $email=$list["email"];
                                        ?>

                                    <!-- <option selected  disabled class="text-gray-900">Choose Head:</option>  -->
                                    <option data-sectionassign="<?php echo $row['level'];?>" data-pending = "<?php echo $row['pending']?>" data-personnelsname = "<?php echo $row['name']?>" value="<?php echo $row['username'];?>"><?php echo $row['name'];?> (<?php echo $row['pending']?>)</option>; <?php 
                                        
                                    }    

                                    ?>
                    </select>
    </div>

    <input class= "hidden" type="text" id="r_personnelsName" name="r_personnelsName" class="bg-gray-50 border border-gray-300 text-gray-900 text-xl rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"  />

    <label for="message" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Details</label>
  <textarea id="detailsOfRequest" name="detailsOfRequest" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="What is the problem?"></textarea>
<div id="detailsOfAction" class="hidden">
<label for="message" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Action</label>
  <textarea id="requestAction" name="requestAction" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="What is your action"></textarea>
  <label for="message" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Recommendation</label>
  <textarea id="recommendation" name="recommendation" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="What is your recommendation"></textarea>
</div>

  
                </div>
                <br>
                <button type="submit" name="submitTicket" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Submit</button>
                <button type="button" class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">Cancel</button>

                
    </div>
</form>




</div>


<div id="terms" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] md:h-full">
    <div class="relative w-full h-full max-w-2xl md:h-auto">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Terms of Service
                </h3>
                <button type="button" data-modal-target="terms" data-modal-toggle="terms" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white" >
                    <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <div class="p-6 space-y-6">
                <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                Welcome to Helpdesk System. We’re so glad you’re here. Please read the following terms before using our Services; you will be agreeing to, 
        and will be bound by them through the continued use of our Services.
                </p>
                <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                1. First come first serve base on the approved Job Order request.
                </p>
                <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                1. 2. For the relayout must be attached the approve layout plan and request must be file in advance minimum of 3 days including approval time.
                </p>
                
            </div>
            <!-- Modal footer -->
            <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
              
                <button data-modal-target="terms"data-modal-toggle="terms" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Close</button>
            </div>
        </div>
    </div>
</div>
  

 


<!-- end of main -->
    

<!-- flowbite javascript -->

<!-- <script src="https://unpkg.com/flowbite@1.5.3/dist/flowbite.js"></script> -->

<script src="../node_modules/flowbite/dist/flowbite.js"></script>
<script src="../node_modules/jquery/dist/jquery.min.js"></script>
<script src="../node_modules/select2/dist/js/select2.min.js"></script>
<script type="text/javascript" src="../node_modules/DataTables/datatables.min.js"></script>

    <script type="text/javascript" src="../node_modules/DataTables/Responsive-2.3.0/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript" src="index.js"></script>

<script>
  


  $('.js-example-basic-single').select2();

  $(document).ready(function() {
        $('#r_name').change(function() {
            var selectedUsername = $(this).find('option:selected').data('username');
            $('#r_IdNumber').val(selectedUsername);
            var requestorsName = $(this).find('option:selected').data('name');
            $('#requestor_Name').val(requestorsName);
            console.log(selectedUsername);
            var selectedEmail = $(this).find('option:selected').data('email');
            $('#r_email').val(selectedEmail);
            var selectedDepartment = $(this).find('option:selected').data('department');
            $('#r_department').val(selectedDepartment);
           
         

            var xhr1 = new XMLHttpRequest();
            xhr1.onreadystatechange = function() {
            if (xhr1.readyState === XMLHttpRequest.DONE) {
                if (xhr1.status === 200) {
                    var options = JSON.parse(xhr1.responseText);


                    $("#immediateHeadSelect").empty();

                    options.forEach(function(option) {

                        var newOptions = "<option data-heademail='"+option.email+"' value='" + option.name + "'>" + option.name + "</option>";
                        $("#immediateHeadSelect").append(newOptions);

                        console.log(option.name)

                    });
                    getSelectedHead();
                } else {
                    console.log("Error: " + xhr1.status);
                }
            }
            };
            xhr1.open("GET", "getImmediateHead.php?department="+selectedDepartment, true);
            xhr1.send();


    
           

        });
        $('#immediateHeadSelect').change(function() {

            var selectedValueHead = $('#immediateHeadSelect').val();
        console.log("selected1: " + selectedValueHead);
        var selectedOption = $('#immediateHeadSelect').find(":selected");
        var headEmail = selectedOption.attr("data-heademail");
        console.log("selected1: " + headEmail);

        $("#immediateHeadEmail").val(headEmail);

        });

        $('#r_categories').change(function() {
            var selectedLevel = $(this).find('option:selected').data('level');
            var selectedHours = $(this).find('option:selected').data('hours');
          
            if(selectedHours<1){
                var selectedMinutes = Math.round(selectedHours * 60);
                $('#r_cat_hours').val(selectedMinutes + " minutes");
            }
            else{
                $('#r_cat_hours').val(Math.round(selectedHours) + " hours");
            }
            $('#r_cat_level').val(selectedLevel);
                // $('#r_cat_hours').val(selectedHours);

                

        });

        $('#r_personnels').change(function() {
            var selectedpersonnel = $(this).find('option:selected').data('personnelsname');
            $('#r_personnelsName').val(selectedpersonnel);
           
        });

        $('.peer').on('click', function() {
        
    // Check if the checkbox is checked or not
    if ($(this).prop('checked')) {
        console.log('Checkbox is checked');
     
        $("#detailsOfAction").removeClass("hidden");

    } else {
        $("#detailsOfAction").addClass("hidden");
       
        console.log('Checkbox is not checked');
    }
});

    });

function getSelectedHead(){
        var selectedValueHead = $('#immediateHeadSelect').val();
        var selectedOption = $('#immediateHeadSelect').find(":selected");
        var headEmail = selectedOption.attr("data-heademail");
        console.log("selected1: " + headEmail);

        $("#immediateHeadEmail").val(headEmail);

}

    $('#femmis').change(function () {
        var $options = $('#type')
            .val('')
            .find('option')
            .show();
        if (this.value != '0')
            $options
            .not('[data-val="' + this.value + '"], [data-val=""]')
            .hide();
        $('#type option:eq(0)').prop('selected', true)


    })

    $('#type').change(function () {
        if (this.value == 'Computer'){
        $("#gridtochange").removeClass("grid-col-1").addClass("grid-cols-2");
        $("#computerdiv").removeClass("hidden");
        $("#computerName").prop("required", true);
        }
      
        else{
        $("#gridtochange").addClass("grid-col-1").removeClass("grid-cols-2");

        $("#computerdiv").addClass("hidden");
        $("#computerName").prop("required", false);


        }
        console.log(this.value)
    })
    var $options = $('#type')
        .val('')
        .find('option')
        .show();
    if (this.value != '0')
        $options
        .not('[data-val="' + this.value + '"], [data-val=""]')
        .hide();
    $('#type option:eq(0)').prop('selected', true)

   

    var setdate2;

    function testDate() {
        var chosendate = document.getElementById("datestart").value;


        //  console.log(chosendate)
        const x = new Date();
        const y = new Date(chosendate);

        if (x < y) {
            console.log("Valid");
            var monthNumber = (new Date().getMonth() + 1).toString().padStart(2, '0');

            const asf = new Date(chosendate);
            var type = document.getElementById("type").value;
            if (type === "Relayout") {
            asf.setDate(asf.getDate() + 4);
            } else {
            asf.setDate(asf.getDate() + 2);
            }
            const year = asf.getFullYear();
            const month = (asf.getMonth() + 1).toString().padStart(2, "0");
            const day = asf.getDate().toString().padStart(2, "0");

            console.log(`${year}-${month}-${day}`);

            if (asf.getDay() === 0) {
                asf.setDate(asf.getDate() + 1);
            }

            var setdate = `${year}-${month}-${day}`;
            document.getElementById("datefinish").value = setdate;

            setdate2 = `${year}-${month}-${day}`;
            console.log(setdate)

        } else {
            alert("Sorry your requested date is not accepted!")

            const z = new Date();
            var monthNumber = (new Date().getMonth() + 1).toString().padStart(2, '0');
            z.setDate(z.getDate() + 1);
            console.log(z);
            var setdate = z.getFullYear() + "-" + monthNumber + "-" + z.getDate();
            document.getElementById("datestart").value = setdate;
            console.log(setdate)

            const asf2 = new Date(setdate);
            asf2.setDate(asf2.getDate() + 2);
            setdate2 = asf2.getFullYear() + "-" + monthNumber + "-" + asf2.getDate();
            document.getElementById("datefinish").value = setdate2;

        }
    }


    function endDate() {
        console.log(setdate2);


        var chosendate3 = document.getElementById("datefinish").value;
        console.log(chosendate3);

        const x = new Date(setdate2);
        const y = new Date(chosendate3);

        if (x < y) {

        } else {
            alert("Sorry your request date is not accepted!")
            document.getElementById("datefinish").value = setdate2;

        }
    }
    $("#sidehome").removeClass("bg-gray-200");
$("#sidehistory").removeClass("bg-gray-200");
$("#sideMyRequest").removeClass("bg-gray-200");

$("#sidepms").removeClass("bg-gray-200");


</script>
</body>
</html>