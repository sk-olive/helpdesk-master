<?php 


$user_dept = $_SESSION['department'];
$user_level=$_SESSION['level'];


?>
<section class="mt-10">
<table id="cancelledTable" class="display" style="width:100%">
        <thead>
            <tr>
                <th>JO Number</th>
                <th>Action</th>
                <th>Details</th>
                <th>Requestor</th>
                <th>Date Filed</th>
                <th>Date Cancelled</th>
                <th>Category</th>
                <th>Assigned to</th>
            </tr>
        </thead>
        <tbody>
              <?php
                $a=1;

                  $sql="select * from `request` WHERE `request_to` = 'fem' and `status2` = 'cancelled' order by id asc  ";
                  $result = mysqli_query($con,$sql);

                while($row=mysqli_fetch_assoc($result)){
                  ?>
              <tr class="">
              <td class=""><?php
              $date = new DateTime($row['date_filled']);
              $date = $date->format('ym');
              echo $date.'-'.$row['id'];?> </td>
              <td >
                    <!-- <a href="#" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Select</a> -->
                    <button type="button" id="viewdetails" onclick="modalShow(this)"  data-requestor="<?php echo $row['requestor'] ?>"  data-cancelledby="<?php echo $row['cancelledBy']?>"  data-reason="<?php echo $row['reasonOfCancellation']?>" data-joidprint="<?php $date = new DateTime($row['date_filled']); $date = $date->format('ym');  echo $date.'-'.$row['id']; ?>" data-joid="<?php echo $row['id']; ?>" data-datefiled="<?php $date = new DateTime($row['date_filled']); $date = $date->format('F d, Y');echo $date;?>" data-section="<?php if($row['request_to'] === "fem"){  echo "FEM";} else if($row['request_to'] === "mis"){ echo "MIS";  }?> " data-category="<?php echo $row['request_category']; ?>" data-telephone="<?php echo $row['telephone']; ?>" data-attachment="<?php echo $row['attachment']; ?>"  data-comname="<?php echo $row['computerName']; ?>" data-start="<?php echo $row['reqstart_date']; ?>" data-end="<?php echo $row['reqfinish_date']; ?>" data-details="<?php echo $row['request_details']; ?>" class="inline-block px-6 py-2.5 bg-blue-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out"> 
                    View more
                    </button>
                </td>

              <td class="text-sm text-red-700 font-light px-6 py-4 whitespace-nowrap truncate max-w-xs">
              <?php echo $row['request_details'];?> 
              </td>
              <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
              <?php echo $row['requestor'];?> 
              
              </td>

              <!-- to view pdf -->
              <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
              <?php echo $row['date_filled'];?> 
              
              </td>
              <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
              <?php echo $row['dateOfCancellation'];?> 
              
              </td>
              <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
              <?php echo $row['request_category'];?> 
              </td>
              <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">

              <?php if($row['request_to'] == "fem"){
                echo "FEM";}
                else if($row['request_to'] == "mis"){
                echo "MIS";
                }
                ?> 
              </td>



              




                </tr>
                  <?php

            }
               ?>
          </tbody>
    </table>

</section>




  