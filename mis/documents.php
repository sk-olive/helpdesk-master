 <!-- session for who is login user    -->
 <?php 




//Set the session timeout for 1 hour

$timeout = 3600;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
//Set the maxlifetime of the session

ini_set( "session.gc_maxlifetime", $timeout );

//Set the cookie lifetime of the session

ini_set( "session.cookie_lifetime", $timeout );

  // session_start();
  
$s_name = session_name();
$url1=$_SERVER['REQUEST_URI'];

//Check the session exists or not

if(isset( $_COOKIE[ $s_name ] )) {

    setcookie( $s_name, $_COOKIE[ $s_name ], time() + $timeout, '/' );

    
}

else

    echo "Session is expired.<br/>";


// end of session timeout>";






session_start();

    if(!isset($_SESSION['connected'])){
      header("location: ../index.php");
    }


    
// connection php and transfer of session
include ("../includes/connect.php");
$user_dept = $_SESSION['department'];
$user_level=$_SESSION['level'];
$username = $_SESSION['username'];

$misusername =  $_SESSION['username'];
$uploader=  $_SESSION['name'];





if(isset($_POST['uploadDocs'])){
   $docuName= $_POST['docuName'];
    $nullFile =  implode($_FILES['uploadedFile']);

                    if($nullFile != "40"){
                        if (isset($_FILES['uploadedFile']) && $_FILES['uploadedFile']['error'] === UPLOAD_ERR_OK)
                        {
                        // get details of the uploaded file
                        $fileTmpPath = $_FILES['uploadedFile']['tmp_name'];
                        $fileName = $_FILES['uploadedFile']['name'];
                    
                        $fileSize = $_FILES['uploadedFile']['size'];
                        $fileType = $_FILES['uploadedFile']['type'];
                        $fileNameCmps = explode(".", $fileName);
                        $fileExtension = strtolower(end($fileNameCmps));
                    
                        // sanitize file-name
                        //   $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                        $newFileName = $fileName;
                    
                        // check if file has one of the following extensions
                        $allowedfileExtensions = array('jpg', 'gif', 'png', 'zip', 'txt', 'xls', 'doc' , 'pdf','csv','xlsx');
                    
                        if (in_array($fileExtension, $allowedfileExtensions))
                        {
                            // directory in which the uploaded file will be moved
                            $uploadFileDir = '../resources/documents/';
                            $dest_path = $uploadFileDir . $newFileName;
                    
                            if(move_uploaded_file($fileTmpPath,$dest_path)) 
                            {
                            $messageUpload ='File is successfully uploaded.';
                            }
                            else 
                            {
                            $messageUpload = 'There was some error moving the file to upload directory. Please make sure the upload directory is writable by web server.';
                            }
                        }
                        else
                        {
                            $messageUpload = 'Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions);
                        }
                        }
                        else
                        {
                        $messageUpload = 'There is some error in the file upload. Please check the following error.<br>';
                        $messageUpload .= 'Error:' . $_FILES['uploadedFile']['error'];
                        }
                            
                    }
                    else {
                        $dest_path == "";
                    }

                    if($messageUpload =="File is successfully uploaded."){
                        $sql = "INSERT INTO `documents`(`name`, `location`, `uploadedBy`) VALUES ('$docuName','$dest_path','$uploader')";
                    $results = mysqli_query($con,$sql);
                if($results){
                    echo "<script>alert('$messageUpload') </script>";
                }
                    }
                    else{
                        echo "<script>alert('$messageUpload') </script>";
                    }
                    
    }

    if(isset($_POST['deleteDocu'])){
        $docuId= $_POST['docuId'];
        $sql = "DELETE FROM `documents` WHERE `id` = '$docuId'";
                    $deleted = mysqli_query($con,$sql);
                    if ($deleted){
                        echo "<script>alert('Deleted Successfully') </script>";
                    }else{
                        echo "<script>alert('There is something wrong') </script>";
                    }
    }


?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FEM MIS Helpdesk</title>
    <link rel="shortcut icon" href="../resources/img/helpdesk.png">
    
    <!-- font awesome -->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" /> -->
    <link rel="stylesheet" href="../fontawesome-free-6.2.0-web/css/all.min.css">


    
  
    <link rel="stylesheet" href="../node_modules/DataTables/datatables.min.css">
    <link rel="stylesheet" type="text/css" href="../node_modules/DataTables/Responsive-2.3.0/css/responsive.dataTables.min.css"/>

    <link rel="stylesheet" href="index.css">
     <!-- tailwind play cdn -->
    <!-- <script src="https://cdn.tailwindcss.com"></script> -->
    <script src="../cdn_tailwindcss.js"></script>

  


    <!-- <link href="/dist/output.css" rel="stylesheet"> -->


     <!-- from flowbite cdn -->
    <!-- <link rel="stylesheet" href="https://unpkg.com/flowbite@1.5.3/dist/flowbite.min.css" /> -->
    <link rel="stylesheet" href="../node_modules/flowbite/dist/flowbite.min.css" />

    <!-- <link rel="stylesheet" href="css/style.css" /> -->




</head>
<body   class="static  bg-white dark:bg-gray-700"  >

    <!-- nav -->
    <?php require_once 'nav.php';?>


<!-- main -->






<div class=" ml-72 flex mt-16  left-10 right-5  flex-col  px-14 sm:px-8  pt-6 pb-14 z-50 ">
<div class="justify-center text-center flex items-start h-auto bg-gradient-to-r from-blue-900 to-teal-500 rounded-xl ">
<div class="text-center py-2 m-auto lg:text-center w-full">
        <!-- <h6 class="text-sm  tracking-tight text-gray-200 sm:text-lg">Good Day</h6> -->
        <!-- <div class="m-auto flex flex-col w-2/4  h-12">
<h2 class="text-xl font-bold tracking-tight text-gray-100 sm:text-xl">Total numbers of pending Job Order</h2>

</div> -->

<!--        
<div class="m-auto flex flex-col w-2/4">

<div class="mt-0 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-2 ">

  <div class="flex items-start rounded-xl bg-teal-700 dark:bg-white p-4 shadow-lg">
    <div class="flex h-12 w-12 overflow-hidden items-center justify-center rounded-full border border-red-100 bg-red-50">
    <img src="../resources/img/Engineer.png" class="h-full w-full text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">

    </div>

    <div class="ml-3">
      <h2 class="font-semibold text-gray-100 dark:text-gray-900">FEM Pending</h2>
      <p class="mt-2 text-xl text-left text-gray-100"><?php 
                                        $sql1 = "SELECT COUNT(id) as 'pending' FROM request WHERE request_to = 'fem' AND status2 = 'inprogress'";
                                        $result = mysqli_query($con, $sql1);
                                        while($count=mysqli_fetch_assoc($result))
                                        {
                                        echo $count["pending"];
                                      
                                        }
                            ?></p>
    </div>
  </div>
  <div class="flex items-start rounded-xl bg-sky-900 dark:bg-white p-4 shadow-lg">
    <div class="flex h-12 w-12 items-center overflow-hidden  justify-center rounded-full border border-indigo-100 bg-indigo-50">
    <img src="../resources/img/itboy.png" class="h-full w-full text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">

    </div>

    <div class="ml-3">
      <h2 class="font-semibold text-gray-100 dark:text-gray-900">MIS Pending</h2>
      <p class="mt-2 text-xl text-left text-gray-100"><?php 
                                        $sql1 = "SELECT COUNT(id) as 'pending' FROM request WHERE request_to = 'mis' AND status2 = 'inprogress'";
                                        $result = mysqli_query($con, $sql1);
                                        while($count=mysqli_fetch_assoc($result))
                                        {
                                        echo $count["pending"];
                                      
                                        }
                            ?></p>
    </div>
  </div>
 
</div>
</div>  -->
<div class="FrD3PA">
    <div class="QnQnDA" tabindex="-1">
        <div  role="tablist" class="_6TVppg sJ9N9w">
            <div class="uGmi4w">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500 dark:text-gray-400" id="tabExample" role="tablist">

                <li role="presentation">
                                    <div class="p__uwg" style="width: 106px; margin-right: 0px;">
                                        <button id="overallTab" type="button" role="tab"
                                            aria-controls="overall"
                                            class="_1QoxDw o4TrkA CA2Rbg Di_DSA cwOZMg zQlusQ uRvRjQ POMxOg _lWDfA"
                                            aria-selected="false">
                                            <div class="_1cZINw">
                                                <div class="_qiHHw Ut_ecQ kHy45A">

                                                <span class="gkK1Zg jxuDbQ"><svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48"><path fill="currentColor" d="M24 0C10.7 0 0 10.7 0 24s10.7 24 24 24 24-10.7 24-24S37.3 0 24 0zM11.9 15.2c.1-.1.2-.1.2-.1 1.6-.5 2.5-1.4 3-3 0 0 0-.1.1-.2l.1-.1c.1 0 .2-.1.3-.1.4 0 .5.3.5.3.5 1.6 1.4 2.5 3 3 0 0 .1 0 .2.1s.1.2.1.3c0 .4-.3.5-.3.5-1.6.5-2.5 1.4-3 3 0 0-.1.3-.4.3-.6.1-.7-.2-.7-.2-.5-1.6-1.4-2.5-3-3 0 0-.4-.1-.4-.5l.3-.3zm24.2 18.6c-.5.2-.9.6-1.3 1s-.7.8-1 1.3c0 0 0 .1-.1.2-.1 0-.1.1-.3.1-.3-.1-.4-.4-.4-.4-.2-.5-.6-.9-1-1.3s-.8-.7-1.3-1c0 0-.1 0-.1-.1-.1-.1-.1-.2-.1-.3 0-.3.2-.4.2-.4.5-.2.9-.6 1.3-1s.7-.8 1-1.3c0 0 .1-.2.4-.2.3 0 .4.2.4.2.2.5.6.9 1 1.3s.8.7 1.3 1c0 0 .2.1.2.4 0 .4-.2.5-.2.5zm-.7-8.7s-4.6 1.5-5.7 2.4c-1 .6-1.9 1.5-2.4 2.5-.9 1.5-2.2 5.4-2.2 5.4-.1.5-.5.9-1 .9v-.1.1c-.5 0-.9-.4-1.1-.9 0 0-1.5-4.6-2.4-5.7-.6-1-1.5-1.9-2.5-2.4-1.5-.9-5.4-2.2-5.4-2.2-.5-.1-.9-.5-.9-1h.1-.1c0-.5.4-.9.9-1.1 0 0 4.6-1.5 5.7-2.4 1-.6 1.9-1.5 2.4-2.5.9-1.5 2.2-5.4 2.2-5.4.1-.5.5-.9 1-.9s.9.4 1 .9c0 0 1.5 4.6 2.4 5.7.6 1 1.5 1.9 2.5 2.4 1.5.9 5.4 2.2 5.4 2.2.5.1.9.5.9 1h-.1.1c.1.5-.2.9-.8 1.1z"></path></svg></span>

                                                </div>
                                            </div>
                                            <p class="_5NHXTA _2xcaIA ZSdr0w CCfw7w GHIRjw">Documents</p>
                                        </button></div>
                                </li>
                 
            
              
                    </ul>
            </div>
            <div class="rzHaWQ theme light dark:bg-gray-700" id="diamond" style="transform: translateX(50px) translateY(2px) rotate(135deg);"></div>
        </div>
    </div>
</div>
<div class="hidden"> 
<ul class="uGmi4w  mb-1 m-4 flex text-sm font-medium text-center text-gray-500 divide-x divide-gray-200 rounded-lg shadow  dark:divide-gray-700 dark:text-gray-400">
    <li class="w-full relative">
        <a href="#" class="inline-block w-full p-4 text-gray-900 bg-gray-100 rounded-l-lg focus:ring-4 focus:ring-blue-300 active focus:outline-none dark:bg-gray-700 dark:text-white" aria-current="page">For Approval</a>
        <div class="rzHaWQ theme light" style="transform: translateX(198px) translateY(2px) rotate(135deg);"></div>
  
    </li>
    <li class="w-full">
        <a href="#" class="inline-block w-full p-4 bg-white hover:text-gray-700 hover:bg-gray-50 focus:ring-4 focus:ring-blue-300 focus:outline-none dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700">Dashboard</a>
    </li>
    <li class="w-full">
        <a href="#" class="inline-block w-full p-4 bg-white hover:text-gray-700 hover:bg-gray-50 focus:ring-4 focus:ring-blue-300 focus:outline-none dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700">Settings</a>
    </li>
    <li class="w-full">
        <a href="#" class="inline-block w-full p-4 bg-white rounded-r-lg hover:text-gray-700 hover:bg-gray-50 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700">Invoice</a>
    </li>

</ul>

</div>

</div>
</div>



<!-- <div class="grid grid-cols-2 m-auto flex flex-col w-full h-20 mt-4">
<div class="flex items-center justify-center h-full bg-teal-500 p-2">
<div class=" flex h-full w-20 overflow-hidden items-center justify-center rounded-full border border-red-100 bg-red-50">
    <img src="../resources/img/list.png" class="h-full w-full text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
    </div>
    <div class="ml-3">
      <h2 class="font-semibold text-4xl text-gray-100 dark:text-gray-900">My Job Order</h2>
    </div>
</div>
<div class="h-full bg-gray-500"></div>


</div> -->

<div id="myTabContent" class="mt-2">
<button data-modal-target="uploadDocument" data-modal-toggle="uploadDocument" type="button" class="w-full mt-5 text-white bg-gradient-to-br from-green-400 to-blue-600 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-green-200 dark:focus:ring-green-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center mr-2 mb-2">Upload a document</button>

    <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-200" id="overAll" role="tabpanel" aria-labelledby="dashboard-tab">
    <section class="mt-10">
<table id="pmsTable" class="display" style="width:100%">
        <thead>
            <tr>
              <th>No.</th>
              <th>Name</th>
              <th>Uploader</th>
              <th>Action</th>
               

            </tr>
        </thead>
        <tbody>
        <?php
                 
                   $sql="SELECT * FROM `documents`";
                   $result = mysqli_query($con,$sql);
   
                 


                
                 $increment = 1;
                 while($row=mysqli_fetch_assoc($result)){
             ?>
             <tr class="">
             <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
              <?php echo $increment;?> 
              </td>
             
              <td class="text-lg text-gray-900 font-light px-6 py-4 whitespace-nowrap">
              <?php echo $row['name'];?> 
              </td>
              <td class="text-lg text-gray-900 font-light px-6 py-4 whitespace-nowrap">
              <?php echo $row['uploadedBy'];?> 
              </td>
              <td>
              <a type="button" href="<?php echo $row['location']; ?>" id="download" class="inline-block px-6 py-2.5 bg-blue-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out"> 
                    Download
                    </a>
                    <a type="button"  onclick="modalShow(this)" data-docuid="<?php echo $row['id'];?>"  id="deletebtn" class="inline-block px-6 py-2.5 bg-red-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-red-700 hover:shadow-lg focus:bg-red-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-red-800 active:shadow-lg transition duration-150 ease-in-out"> 
                    Delete
                    </a>
              </td>
               
                 </tr>
             <?php
             $increment++;
                 }

        ?>
          </tbody>
    </table>

</section>  
 </div>

</div>




 </div> 



 

 <div id="uploadDocument" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative w-full max-w-md max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <button type="button" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-800 dark:hover:text-white" data-modal-toggle="uploadDocument">
                <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                <span class="sr-only">Close modal</span>
            </button>
            <div class="px-6 py-6 lg:px-8">
                <h3 class="mb-4 text-xl font-medium text-gray-900 dark:text-white">Upload your document</h3>
                <form class="space-y-6" method="POST" accept-charset="utf-8" enctype="multipart/form-data">
                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name of Document</label>
                        <input type="text" name="docuName" id="docuName" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"  required>
                    </div>
   
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="file_input">Upload file</label>
                <input name="uploadedFile" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" aria-describedby="file_input_help" id="file_input" type="file">
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">txt, xlsx, csv, xls, pdf, docx</p>

                    
                    <button type="submit" name="uploadDocs" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Upload</button>
                   
                </form>
            </div>
        </div>
    </div>
</div> 
 
    
<div id="deleteDocu" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <button type="button" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-800 dark:hover:text-white"  onclick="modalHide()">
                <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                <span class="sr-only">Close modal</span>
            </button>
            <form method="POST">
            <div class="p-6 text-center">
                <input type="text" id="docuId" class="hidden" name="docuId">
                <svg aria-hidden="true" class="mx-auto mb-4 text-gray-400 w-14 h-14 dark:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want to delete this product?</h3>
                <button  type="submit" name="deleteDocu" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                    Yes, I'm sure
                </button>
                <button onclick="modalHide()" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">No, cancel</button>
            </div>
            </form>
          
        </div>
    </div>
</div>

<!-- flowbite javascript -->

<!-- <script src="https://unpkg.com/flowbite@1.5.3/dist/flowbite.js"></script> -->

<script src="../node_modules/flowbite/dist/flowbite.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.5/flowbite.min.js"></script>
<script src="../node_modules/jquery/dist/jquery.min.js"></script>

<script type="text/javascript" src="../node_modules/DataTables/datatables.min.js"></script>

    <script type="text/javascript" src="../node_modules/DataTables/Responsive-2.3.0/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript" src="index.js"></script>

<script>
    const $targetElModal = document.getElementById('deleteDocu');

// options with default values
const optionsModal = {
  placement: 'center-center',
  backdrop: 'dynamic',
  backdropClasses: 'bg-gray-900 bg-opacity-50 dark:bg-opacity-80 fixed inset-0 z-40',
  closable: true,
  onHide: () => {
      console.log('modal is hidden');
  },
  onShow: () => {
      console.log('modal is shown');

  },
  onToggle: () => {
      console.log('modal has been toggled');

  }
};
const modal = new Modal($targetElModal, optionsModal);

function modalShow(element){

    document.getElementById("docuId").value =element.getAttribute("data-docuid");


    
    modal.toggle();

}
function modalHide(){
    modal.toggle();

}
    // set the drawer menu element
const $targetEl = document.getElementById('sidebar');

// options with default values
const options = {
  placement: 'left',
  backdrop: false,
  bodyScrolling: true,
  edge: false,
  edgeOffset: '',
  backdropClasses: 'bg-gray-900 bg-opacity-50 dark:bg-opacity-80 fixed inset-0 z-30',
  onHide: () => {
      console.log('drawer is hidden');
  },
  onShow: () => {
      console.log('drawer is shown');
  },
  onToggle: () => {
      console.log('drawer has been toggled');
  }
};

/*
* targetEl: required
* options: optional
*/
const drawer = new Drawer($targetEl, options);
drawer.show();
var show = true;
function shows(){
    if(show){
        drawer.hide();
        show = false;
    }
    else{
        drawer.show();
        show = true;
    }


}





// // Code for tabs
const tabElements= [

    {
        id: 'overAll',
        triggerEl: document.querySelector('#overallTab'),
        targetEl: document.querySelector('#overAll')
    },

];

// options with default values
const taboptions = {
    defaultTabId: 'overAll',
    activeClasses: 'text-amber-400 hover:text-amber-400 dark:text-amber-400 dark:hover:text-amber-400 border-amber-600 dark:border-amber-500',
    inactiveClasses: 'text-gray-300 hover:text-amber-500 dark:text-gray-400 border-gray-100 hover:border-gray-300 dark:border-gray-700 dark:hover:text-gray-300',
    onShow: () => {
        console.log('tab is shown');
    }
};

/*
* tabElements: array of tab objects
* options: optional
*/
const tabs = new Tabs(tabElements, taboptions);

// open tab item based on id
tabs.show('overAll');


// // get the tab object based on ID
// tabs.getTab('adminApproval1')

// // get the current active tab object
// tabs.getActiveTab()


function goToAdmin(){
    const myElement = document.querySelector('#diamond');
    
    document.getElementById("telephone").disabled = true;
    document.getElementById("datestart").disabled = true;
    document.getElementById("datefinish").disabled = true;
    document.getElementById("message").disabled = true;
    document.getElementById("computername").disabled = true;

    $("#assignedPersonnelDiv").addClass("hidden");

    $("#buttondiv").addClass("hidden");

    $("#actionDetailsDiv").addClass("hidden");

    

// Get the current transform value
const currentTransform = myElement.style.transform = 'translateX(180px) translateY(2px) rotate(135deg)';


// transform: translateX(55px) translateY(2px) rotate(135deg);
}

function goToMis(){
    document.getElementById("telephone").disabled = true;
    document.getElementById("datestart").disabled = true;
    document.getElementById("datefinish").disabled = true;
    document.getElementById("message").disabled = true;
    document.getElementById("computername").disabled = true;
    
    $("#assignedPersonnelDiv").removeClass("hidden");
    $("#buttondiv").addClass("hidden");


    $("#actionDetailsDiv").addClass("hidden");

    const myElement = document.querySelector('#diamond');

// Get the current transform value
const currentTransform = myElement.style.transform = 'translateX(300px) translateY(2px) rotate(135deg)';


// transform: translateX(55px) translateY(2px) rotate(135deg);
}
function goToRate(){
    document.getElementById("telephone").disabled = true;
    document.getElementById("datestart").disabled = true;
    document.getElementById("datefinish").disabled = true;
    document.getElementById("message").disabled = true;
    document.getElementById("computername").disabled = true;
    $("#assignedPersonnelDiv").removeClass("hidden");


    $("#actionDetailsDiv").removeClass("hidden");

    $("#buttondiv").addClass("hidden");


    const myElement = document.querySelector('#diamond');

// Get the current transform value
const currentTransform = myElement.style.transform = 'translateX(420px) translateY(2px) rotate(135deg)';


// transform: translateX(55px) translateY(2px) rotate(135deg);
}
function goToFinished(){
    document.getElementById("telephone").disabled = true;
    document.getElementById("datestart").disabled = true;
    document.getElementById("datefinish").disabled = true;
    document.getElementById("message").disabled = true;
    document.getElementById("computername").disabled = true;
    $("#assignedPersonnelDiv").removeClass("hidden");

    $("#ratingstar").removeClass("hidden");

    $("#actionDetailsDiv").removeClass("hidden");
    $("#actionsDiv").removeClass("hidden");

    $("#buttondiv").removeClass("hidden");
    $("#reasonCancelDiv").addClass("hidden");
    $("#cancelledByDiv").addClass("hidden");
    $("#actualDateFinishedDiv").removeClass("hidden");
    const myElement = document.querySelector('#diamond');

// Get the current transform value
const currentTransform = myElement.style.transform = 'translateX(50px) translateY(2px) rotate(135deg)';


// transform: translateX(55px) translateY(2px) rotate(135deg);
}
function goToOverall(){
    document.getElementById("telephone").disabled = true;
    document.getElementById("datestart").disabled = true;
    document.getElementById("datefinish").disabled = true;
    document.getElementById("message").disabled = true;
    document.getElementById("computername").disabled = true;
    $("#assignedPersonnelDiv").removeClass("hidden");

    $("#ratingstar").removeClass("hidden");

    $("#actionDetailsDiv").removeClass("hidden");
    $("#actionsDiv").removeClass("hidden");

    $("#buttondiv").removeClass("hidden");
    $("#reasonCancelDiv").addClass("hidden");
    $("#cancelledByDiv").addClass("hidden");
    $("#actualDateFinishedDiv").removeClass("hidden");
    const myElement = document.querySelector('#diamond');

// Get the current transform value
const currentTransform = myElement.style.transform = 'translateX(160px) translateY(2px) rotate(135deg)';


// transform: translateX(55px) translateY(2px) rotate(135deg);
}
function goToCancelled(){
    document.getElementById("telephone").disabled = true;
    document.getElementById("datestart").disabled = true;
    document.getElementById("datefinish").disabled = true;
    document.getElementById("message").disabled = true;
    document.getElementById("computername").disabled = true;
    $("#assignedPersonnelDiv").addClass("hidden");
    $("#reasonCancelDiv").removeClass("hidden");
    $("#cancelledByDiv").removeClass("hidden");
    $("#actualDateFinishedDiv").addClass("hidden");
    $("#ratingstar").addClass("hidden");
    $("#actionsDiv").addClass("hidden");

    


    
    $("#actionDetailsDiv").addClass("hidden");

    $("#buttondiv").addClass("hidden");


    const myElement = document.querySelector('#diamond');

// Get the current transform value
const currentTransform = myElement.style.transform = 'translateX(280px) translateY(2px) rotate(135deg)';


// transform: translateX(55px) translateY(2px) rotate(135deg);
}
function goToHead(){
    $("#buttondiv").removeClass("hidden");
   
    $("#actionDetailsDiv").addClass("hidden");
    $("#assignedPersonnelDiv").addClass("hidden");

    document.getElementById("telephone").disabled =false;
    document.getElementById("computername").disabled = false;
    document.getElementById("datestart").disabled = false;
    document.getElementById("datefinish").disabled = false;
    document.getElementById("message").disabled = false;
    const myElement = document.querySelector('#diamond');

// Get the current transform value
const currentTransform = myElement.style.transform = 'translateX(50px) translateY(2px) rotate(135deg)';


// transform: translateX(55px) translateY(2px) rotate(135deg);
}
function cancellation(){
    document.getElementById("reasonCancel").required = true;
}
function exitcancellation(){
    document.getElementById("reasonCancel").required = false;
}


var setdate2;
var setdate;


function testDate() {
    var chosendate = document.getElementById("datestart").value;


     console.log(chosendate)
    const x = new Date();
    const y = new Date(chosendate);

    if (x < y) {
        console.log("Valid");
        var monthNumber = new Date().getMonth() + 1;
        const asf = new Date(chosendate);
        asf.setDate(asf.getDate() + 1);
        var setdate = asf.getFullYear() + "-" + monthNumber + "-" + asf.getDate();
        document.getElementById("datefinish").value = setdate;

        setdate2 = asf.getFullYear() + "-" + monthNumber + "-" + asf.getDate();
        console.log(setdate)

    } else {
        alert("Sorry your request date is not accepted!")

        const z = new Date();
        var monthNumber = new Date().getMonth() + 1
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
function rate(id){
    console.log(id);

    if(id=="rate1"){
        document.getElementById("rateScore").value='1';
        $("#rate1").removeClass("text-gray-300");
        $("#rate2").removeClass("text-gray-300");
        $("#rate3").removeClass("text-gray-300");
        $("#rate4").removeClass("text-gray-300");
        $("#rate5").removeClass("text-gray-300");

        $("#rate1").removeClass("text-yellow-400");
        $("#rate2").removeClass("text-yellow-400");
        $("#rate3").removeClass("text-yellow-400");
        $("#rate4").removeClass("text-yellow-400");
        $("#rate5").removeClass("text-yellow-400");

        $("#rate1").addClass("text-yellow-400");
        $("#rate2").addClass("text-gray-300");
        $("#rate3").addClass("text-gray-300");
        $("#rate4").addClass("text-gray-300");
        $("#rate5").addClass("text-gray-300");
    }
    else if(id=="rate2"){
        document.getElementById("rateScore").value='2';

        $("#rate1").removeClass("text-gray-300");
        $("#rate2").removeClass("text-gray-300");
        $("#rate3").removeClass("text-gray-300");
        $("#rate4").removeClass("text-gray-300");
        $("#rate5").removeClass("text-gray-300");

        $("#rate1").removeClass("text-yellow-400");
        $("#rate2").removeClass("text-yellow-400");
        $("#rate3").removeClass("text-yellow-400");
        $("#rate4").removeClass("text-yellow-400");
        $("#rate5").removeClass("text-yellow-400");

        $("#rate1").addClass("text-yellow-400");
        $("#rate2").addClass("text-yellow-400");

        $("#rate3").addClass("text-gray-300");
        $("#rate4").addClass("text-gray-300");
        $("#rate5").addClass("text-gray-300");
    
    }
    else if(id=="rate3"){
        document.getElementById("rateScore").value='3';

        $("#rate1").removeClass("text-gray-300");
        $("#rate2").removeClass("text-gray-300");
        $("#rate3").removeClass("text-gray-300");
        $("#rate4").removeClass("text-gray-300");
        $("#rate5").removeClass("text-gray-300");

        $("#rate1").removeClass("text-yellow-400");
        $("#rate2").removeClass("text-yellow-400");
        $("#rate3").removeClass("text-yellow-400");
        $("#rate4").removeClass("text-yellow-400");
        $("#rate5").removeClass("text-yellow-400");

        $("#rate1").addClass("text-yellow-400");
        $("#rate2").addClass("text-yellow-400");
        $("#rate3").addClass("text-yellow-400");

        $("#rate4").addClass("text-gray-300");
        $("#rate5").addClass("text-gray-300");
    
    }
    else if(id=="rate4"){
        document.getElementById("rateScore").value='4';

        $("#rate1").removeClass("text-gray-300");
        $("#rate2").removeClass("text-gray-300");
        $("#rate3").removeClass("text-gray-300");
        $("#rate4").removeClass("text-gray-300");
        $("#rate5").removeClass("text-gray-300");

        $("#rate1").removeClass("text-yellow-400");
        $("#rate2").removeClass("text-yellow-400");
        $("#rate3").removeClass("text-yellow-400");
        $("#rate4").removeClass("text-yellow-400");
        $("#rate5").removeClass("text-yellow-400");

        $("#rate1").addClass("text-yellow-400");
        $("#rate2").addClass("text-yellow-400");
        $("#rate3").addClass("text-yellow-400");
        $("#rate4").addClass("text-yellow-400");

        $("#rate5").addClass("text-gray-300");
    
    }
    else if(id=="rate5"){
        document.getElementById("rateScore").value='5';

        $("#rate1").removeClass("text-gray-300");
        $("#rate2").removeClass("text-gray-300");
        $("#rate3").removeClass("text-gray-300");
        $("#rate4").removeClass("text-gray-300");
        $("#rate5").removeClass("text-gray-300");

        $("#rate1").removeClass("text-yellow-400");
        $("#rate2").removeClass("text-yellow-400");
        $("#rate3").removeClass("text-yellow-400");
        $("#rate4").removeClass("text-yellow-400");
        $("#rate5").removeClass("text-yellow-400");

        $("#rate1").addClass("text-yellow-400");
        $("#rate2").addClass("text-yellow-400");
        $("#rate3").addClass("text-yellow-400");
        $("#rate4").addClass("text-yellow-400");
        $("#rate5").addClass("text-yellow-400");

    
    }
}

$("#sidehome").removeClass("bg-gray-200");
$("#sideuser").removeClass("bg-gray-200");
$("#sideMyRequest").removeClass("bg-gray-200");
$("#sidepms").removeClass("bg-gray-200");
$("#sidedocs").addClass("text-white bg-gradient-to-r from-blue-900 to-teal-500");



$("#sidehome1").removeClass("bg-gray-200");
$("#sideuser1").removeClass("bg-gray-200");
$("#sideMyRequest1").removeClass("bg-gray-200");
$("#sidepms1").removeClass("bg-gray-200");
$("#sidedocs1").addClass("text-white bg-gradient-to-r from-blue-900 to-teal-500");


</script>

</body>
</html>