<?php
$date1 = new DateTime('2024-04-12 13:00:00');
$date2 = new DateTime('2024-04-18 10:00:00');
 
// Define holidays array
$holidays = array(
    '2024-04-15',
    '2024-04-16',
 
);
 
// Calculate the difference
$interval = $date1->diff($date2);
 
// Get the difference in hours considering weekends and holidays
$hours = $interval->days * 8 + $interval->h;
 
// Check if there are Saturdays, Sundays, and holidays within the interval
$start = clone $date1;
$end = clone $date2;
$interval_days = new DateInterval('P1D');
$period = new DatePeriod($start, $interval_days, $end);
 
foreach ($period as $day) {
    if ($day->format('N') >= 6 || in_array($day->format('Y-m-d'), $holidays)) {
        $hours -= 8; // Subtract 24 hours for each weekend day or holiday
    }
}
 
$hours1 = $end->format('H');
 
if($hours1 <=11 ){
  $finalHours = $hours - 15;
echo "The difference in hours considering weekends and holidays is: $finalHours";
 
}
else if($hours1 ==12 ){
  $finalHours = $hours - 16;
  echo "The difference in hours considering weekends and holidays is: $finalHours";
}
else if($hours1 >12 ){
  $finalHours = $hours;
  echo "The difference in hours considering weekends and holidays is: $finalHours";
}

// Define your DateTime objects
$first_response_time = new DateTime('2024-04-12 13:00:00');
$ict_approval_time = new DateTime('2024-04-16 16:00:00');

// Calculate the time difference
$time_diff = $ict_approval_time->diff($first_response_time);

// Output the result
echo "Time difference: " . $time_diff->format('%d days, %h hours, %i minutes') . PHP_EOL;

// Array of holidays
$holidays = array(
    '2024-04-15',
    '2024-04-18'
  
);

// Define non-working hours (assuming 7:00 to 16:00 as working hours)
$working_hours_start = new DateTime('07:00:00');
$working_hours_end = new DateTime('16:00:00');

// Start and end dates for the time difference calculation
$approval_date = new DateTime('2024-04-12 13:00:00');
$responded_date = new DateTime('2024-04-16 16:00:00');

// Initialize total time difference
$total_time_difference = 0;

// Iterate through each date in the range
$current_date = clone $approval_date;

while ($current_date <= $responded_date) {
    // Check if the current date is a holiday, Saturday, or Sunday
    $formatted_date = $current_date->format('Y-m-d');
    $day_of_week = $current_date->format('N');
    if (in_array($formatted_date, $holidays) || $day_of_week >= 6) {
        // If it's a holiday or weekend, skip to the next day
        $current_date->modify('+1 day');
        continue;
    }

    // Check if the current time is within working hours
    $current_time = $current_date->format('H:i:s');
    if ($current_time < $working_hours_start->format('H:i:s') || $current_time >= $working_hours_end->format('H:i:s')) {
        // If it's outside working hours or exactly at the end time, move to the next day
        $current_date->modify('+1 day');
        continue;
    }

    // Calculate the start and end timestamps within working hours
    $start_timestamp = max($current_date->getTimestamp(), $approval_date->getTimestamp());
    $end_timestamp = min($current_date->getTimestamp() + 86400, $responded_date->getTimestamp(), strtotime($current_date->format('Y-m-d') . ' ' . $working_hours_end->format('H:i:s')));
    echo "<br>";
    echo $start_timestamp;
    echo "<br>";
    // Calculate the time difference for the current day within working hours
    $working_seconds = max(0, $end_timestamp - $start_timestamp); // Ensure the working seconds are not negative

    // Add the working seconds to the total time difference
    $total_time_difference += $working_seconds;

    // Move to the next day
    $current_date->modify('+1 day');
}

// Convert total time difference to hours, minutes, and seconds
$total_hours = floor($total_time_difference / 3600);
$total_minutes = floor(($total_time_difference % 3600) / 60);
$total_seconds = $total_time_difference % 60;

echo "Total time difference (excluding holidays, weekends, and non-working hours): $total_hours hours, $total_minutes minutes, $total_seconds seconds\n";

$holidays = ['2024-04-15'];
$working_hours_start = new DateTime('07:00:00');
$working_hours_end = new DateTime('16:00:00');
$approval_date = new DateTime('2024-04-12 13:00:00');
$responded_date = new DateTime('2024-04-16 16:00:00');

// Function to check if a date is a holiday
function isHoliday($date, $holidays) {
    return in_array($date->format('Y-m-d'), $holidays);
}

// Function to check if a date is a Saturday or Sunday
function isWeekend($date) {
    $weekday = $date->format('N'); // 1 (Monday) to 7 (Sunday)
    return ($weekday == 6 || $weekday == 7);
}

// Function to adjust date to the next working day and within working hours
function adjustDate($date, $working_hours_start, $working_hours_end, $holidays) {
    // If date is outside working hours, move to the next working hour
    if ($date < $working_hours_start || $date > $working_hours_end) {
        $date = clone $date;
        $date->setTime($working_hours_start->format('H'), $working_hours_start->format('i'));
    }

    // If date is a holiday or weekend, move to the next working day
    while (isHoliday($date, $holidays) || isWeekend($date)) {
        $date->modify('+1 day');
    }

    return $date;
}

// Adjust dates if they fall on holidays, weekends, or outside working hours
$approval_date = adjustDate($approval_date, $working_hours_start, $working_hours_end, $holidays);
$responded_date = adjustDate($responded_date, $working_hours_start, $working_hours_end, $holidays);

// Calculate the time difference
$time_diff = $responded_date->diff($approval_date);

// Output the result
echo "Time difference between first_response_time and ict_approval_time: " . $time_diff->format('%d days, %h hours') . PHP_EOL;


$request_type = $_GET['request_type'];
$month = $_GET['month'];
$year = $_GET['year'];

// header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
// header("Content-Disposition: attachment; filename=Summary Report for the Month of ".$month.".xls");  //File name extension was wrong
// header("Expires: 0");
// header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
// header("Cache-Control: private",false);

include ("includes/connect.php");

    $con->next_result();

    $sql = mysqli_query($con, "SELECT req.id,  req.date_filled,  req.requestor,  req.department,  req.request_type,  req.ticket_category,  req.assignedPersonnelName, req.ict_approval_date, req.first_responded_date, req.completed_date, req.action, req.recommendation,  cat.level, cat.hours, cat.days, cat.req_type FROM `request` req LEFT JOIN `categories` cat ON cat.c_name = req.ticket_category WHERE req.ict_approval_date IS NOT NULL AND req.first_responded_date IS NOT NULL");


?>

<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<body>
    <center>
        <b>
            <font color="blue">GLORY (PHILIPPINES), INC.</font>
        </b>
        <br>
        <b>ICT HELPDESK</b>
        <br>
       <h3> <b> Summary Report for the Month of <?php echo $month ?></b></h3>
        <br>
    </center>
    <br>

    <div id="table-scroll">
        <table width="100%" border="1" align="left">
                        <thead>
                            <tr>
                                <th rowspan="2">Request No.</th>
                                <th rowspan="2">Requestor</th>
                                <th rowspan="2">Department</th>
                                <th rowspan="2">Request Category (Details)</th>
                                <th rowspan="2">In-charge</th>
                                <th colspan="3">Requirements</th>
                                <th rowspan="2">ICT Date Approval</th>
                                <th rowspan="2">Date Responded</th>
                                <th rowspan="2">Response Rate</th>
                                <th rowspan="2">Remarks</th>
                                <th rowspan="2">Date Finished</th>
                                <th rowspan="2">Accomplishment Rate</th>
                                <th rowspan="2">Remarks</th>
                                <th rowspan="2">Action Taken</th>
                                <th rowspan="2">Recommendation</th>
                            </tr>
                            <tr>  
                                <th>Priority Level</th>
                                <th>Hours</th>
                                <th>Days</th>
                            </tr>
                            <?php
                   
                            ?>

                        </thead> 
                        <tbody>
                        <?php
                                 
                                while($row = mysqli_fetch_array($sql))
                                {


                                    $dateFilled = new DateTime($row['date_filled']);
                                    $year = $dateFilled->format('y');
                                    $month = $dateFilled->format('m');

                                    if($row['req_type'] == "TS")
                                    {
                                        $request_no = "TS-".$year.$month."-".$row['id'];
                                    }
                                    elseif ($row['req_type'] == "JO")
                                    {
                                        $request_no = "JO-".$year.$month."-".$row['id'];
                                    }  
                                    elseif ($row['req_type'] == "SR")
                                    {
                                        $request_no = "SR-".$year.$month."-".$row['id'];
                                    }

                                    $requestor = $row['requestor'];
                                    $department = $row['department'];
                                    $request_category = $row['request_type']." (".$row['ticket_category'].")";
                                    $in_charge = $row['assignedPersonnelName'];
                                    $piority_level = $row['level'];
                                    $required_response_time = $row['hours'];
                                    $required_completion_days = $row['days'];

                                    $ict_approval_date = $row['ict_approval_date'];
                                    $time_responded = $row['first_responded_date'];
                                    $dateResponded = new DateTime($row['first_responded_date']);
                                    $ictApprovalDate = new DateTime($row['ict_approval_date']);
                                    $diff = $dateResponded->diff($ictApprovalDate);
                                    $days = $diff->days;
                                    $hours = $diff->h;
                                    $minutes = $diff->i;
                                    $seconds = $diff->s;
                                   
                                    $date_finished = $row['completed_date'];
                                    $action_taken = $row['action'];
                                    $recommendation = $row['recommendation'];


                                    echo "<tr>

                                    <td>$request_no</td>
                                    <td>$requestor</td>
                                    <td>$department</td>
                                    <td>$request_category</td>
                                    <td>$in_charge</td>
                                    <td>$piority_level</td>
                                    <td>$required_response_time</td>
                                    <td>$required_completion_days</td>
                                    <td>$ict_approval_date</td>
                                    <td>$time_responded </td>
                                    <td>$days</td>
                                    <td>$hours</td>
                                    <td>$date_finished</td>
                                    <td>Accomplishment rate</td>
                                    <td>Remarks</td>
                                    <td>$action_taken</td>
                                    <td>$recommendation</td>
                                    </tr>";

                                }

                        ?>
                        </tbody>
        </table>
    </div>
</body>

</html>