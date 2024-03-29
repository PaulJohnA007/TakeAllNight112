    <?php
    include("php/database.php");

    // Handle Time In, Time Out, Overtime Time In, and Overtime Time Out actions
    if (
        isset($_POST['action']) &&
        isset($_POST['employeeName']) &&
        isset($_POST['time'])
    ) {
        $action = $_POST['action'];
        $employeeName = $_POST['employeeName'];
        $time = $_POST['time'];

        date_default_timezone_set('Asia/Manila');
        $currentDateTime = date('Y-m-d H:i:s');
        echo "Employee Name: " . $employeeName;
        echo "
        ";
        echo "Current Time: " . date('g:i A', strtotime($currentDateTime));
        echo "
        ";

        $sqlEmployee = "SELECT id FROM employees WHERE name = '$employeeName'";
        $resultEmployee = mysqli_query($connection, $sqlEmployee);

        if (!$resultEmployee) {
            die("Query failed: " . mysqli_error($connection));
        }

        if (mysqli_num_rows($resultEmployee) > 0) {
            $rowEmployee = mysqli_fetch_assoc($resultEmployee);
            $employeeId = $rowEmployee['id'];

            // Check if it's within the normal working hours
            $morningStart = date('Y-m-d') . " 07:30:00"; //
            $morningEnd = date('Y-m-d') . " 12:20:00";
            $afternoonStart = date('Y-m-d') . " 13:00:00";
            $afternoonEnd = date('Y-m-d') . " 17:10:00";
             $overtimeStart = date('Y-m-d') . " 17:10:01";
            $overtimeEnd = date('Y-m-d') . " 24:00:00"; 

            if ($currentDateTime >= $morningStart && $currentDateTime <= $morningEnd) {
                // Within morning working hours
                if ($action === 'Time In') {
                    $status = 'Time In';
                    $sqlInsert = "INSERT INTO attendance (name, morning_time_in, status) VALUES ('$employeeName', '$currentDateTime', '$status')";
                
                    if (mysqli_query($connection, $sqlInsert)) {
                        echo "TIME - IN (Morning) " . date('g:i A', strtotime($currentDateTime)) . " ✔️ ";
                    } else {
                        echo "Error: " . $sqlInsert . "<br>" . mysqli_error($connection);
                    }
                } else if ($action === 'Time Out') {
                    $status = 'Time Out';
                    $sqlUpdate = "UPDATE attendance SET morning_time_out = '$currentDateTime', morning_total_hours = TIMESTAMPDIFF(SECOND, morning_time_in, '$currentDateTime') / 3600, status = '$status' WHERE name = '$employeeName' AND morning_time_out IS NULL";
                
                    if (mysqli_query($connection, $sqlUpdate)) {
                        echo "TIME - OUT (Morning) " . date('g:i A', strtotime($currentDateTime)) . " ✔️ ";
                    } else {
                        echo "Error updating attendance: " . mysqli_error($connection);
                    }
                }
            } else if ($currentDateTime >= $afternoonStart && $currentDateTime <= $afternoonEnd) {
                // Within afternoon working hours
                if ($action === 'Time In') {
                    $status = 'Time In';
                    $sqlUpdate = "UPDATE attendance SET afternoon_time_in='$currentDateTime', status='$status' WHERE name='$employeeName'";
    
    
                    if (mysqli_query($connection, $sqlUpdate)) {
                        echo "TIME - IN (Afternoon) " . date('g:i A', strtotime($currentDateTime)) . " ✔️ ";
                    } else {
                        echo "Error updating attendance: " . mysqli_error($connection);
                    }           
                } else if ($action === 'Time Out') {
                    $status = 'Time Out';
                    $sqlUpdate = "UPDATE attendance SET afternoon_time_out = '$currentDateTime', afternoon_total_hours = TIMESTAMPDIFF(SECOND, afternoon_time_in, '$currentDateTime') / 3600, status = '$status' WHERE name = '$employeeName' AND afternoon_time_out IS NULL";
                
                    if (mysqli_query($connection, $sqlUpdate)) {
                        echo "TIME - OUT (Afternoon) " . date('g:i A', strtotime($currentDateTime)) . " ✔️ ";
                    } else {
                        echo "Error updating attendance: " . mysqli_error($connection);
                    }
                }
            } else if ($currentDateTime >= $overtimeStart && $currentDateTime <= $overtimeEnd) {
                if ($action === 'Time In') {
                    $status = 'Overtime Time In';
                    $sqlUpdate = "UPDATE attendance SET overtime_time_in='$currentDateTime', status='$status' WHERE name='$employeeName'";
    
    
                    if (mysqli_query($connection, $sqlUpdate)) {
                        echo "TIME - IN (overtime) " . date('g:i A', strtotime($currentDateTime)) . " ✔️ ";
                    } else {
                        echo "Error updating attendance: " . mysqli_error($connection);
                    }  
                } else if ($action === 'Time Out') {
                    $status = 'Overtime Time Out';
                    $sqlUpdate = "UPDATE attendance SET overtime_time_out = '$currentDateTime', 
                    overtime_total_hours = TIMESTAMPDIFF(SECOND, overtime_time_in, '$currentDateTime') / 3600, 
                    status = '$status' 
                    WHERE name = '$employeeName' AND overtime_time_out IS NULL";

                    if (mysqli_query($connection, $sqlUpdate)) {
                        echo "OVERTIME TIME - OUT " . date('g:i A', strtotime($currentDateTime)) . " ✔️ ";
                    } else {
                        echo "Error updating overtime: " . mysqli_error($connection);
                    }
                }
            }
                /* if ($action === 'Overtime Time In') {
                    $status = 'Time In';
                    $sqlUpdate = "UPDATE attendance SET overtime_time_in='$currentDateTime', status='$status' WHERE name='$employeeName'";
    
    
                    if (mysqli_query($connection, $sqlUpdate)) {
                        echo "TIME - IN (overtime) " . date('g:i A', strtotime($currentDateTime)) . " ✔️ ";
                    } else {
                        echo "Error updating attendance: " . mysqli_error($connection);
                    }  
                } else if ($action === 'Overtime Time Out') {
                    $status = 'Overtime Time Out';
                    $sqlUpdate = "UPDATE attendance SET overtime_time_out = '$currentDateTime', 
                    overtime_total_hours = TIMESTAMPDIFF(SECOND, overtime_time_in, '$currentDateTime') / 3600, 
                    status = '$status' 
                    WHERE name = '$employeeName' AND overtime_time_out IS NULL";

                    if (mysqli_query($connection, $sqlUpdate)) {
                        echo "OVERTIME TIME - OUT " . date('g:i A', strtotime($currentDateTime)) . " ✔️ ";
                    } else {
                        echo "Error updating overtime: " . mysqli_error($connection);
                    }
                }
            } */
        } else {
            echo "Employee not found";
        }
    } else if (isset($_POST['action']) && $_POST['action'] === 'getAttendance') {
        $sql = "SELECT *, 
            DATE_FORMAT(morning_time_in, '%h:%i %p') AS formatted_morning_time_in,
            DATE_FORMAT(morning_time_out, '%h:%i %p') AS formatted_morning_time_out,
            DATE_FORMAT(afternoon_time_in, '%h:%i %p') AS formatted_afternoon_time_in,
            DATE_FORMAT(afternoon_time_out, '%h:%i %p') AS formatted_afternoon_time_out,
            DATE_FORMAT(overtime_time_in, '%h:%i %p') AS formatted_overtime_time_in,
            DATE_FORMAT(overtime_time_out, '%h:%i %p') AS formatted_overtime_time_out,
            
            TIMESTAMPDIFF(SECOND, morning_time_in, morning_time_out) AS morning_total_seconds,
            TIMESTAMPDIFF(SECOND, afternoon_time_in, afternoon_time_out) AS afternoon_total_seconds,
            (TIMESTAMPDIFF(SECOND, morning_time_in, morning_time_out) + TIMESTAMPDIFF(SECOND, afternoon_time_in, afternoon_time_out)) AS overall_total_seconds,
            TIMESTAMPDIFF(SECOND, overtime_time_in, overtime_time_out) AS overtime_total_seconds


            FROM attendance";

        $result = mysqli_query($connection, $sql);

        if (!$result) {
            die("Query failed: " . mysqli_error($connection));
        }

        $attendanceData = array(); // Initialize an empty array

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                // Convert the 24-hour format times to 12-hour format
                // Inside the while loop where you fetch attendance data
                $formattedMorningIn = date('h:i A', strtotime($row['morning_time_in']));
                $formattedMorningOut = date('h:i A', strtotime($row['morning_time_out']));
                $formattedAfternoonIn = date('h:i A', strtotime($row['afternoon_time_in']));
                $formattedAfternoonOut = date('h:i A', strtotime($row['afternoon_time_out']));
                $formattedOTIn = date('h:i A', strtotime($row['overtime_time_in']));
                $formattedOTOut = date('h:i A', strtotime($row['overtime_time_out']));
                
                // Calculate morning total hours
                $morningTotalSeconds = $row['morning_total_seconds'];
                $morningTotalHours = floor($morningTotalSeconds / 3600); // 3600 seconds in an hour
                $morningTotalMinutes = floor(($morningTotalSeconds % 3600) / 60); // Remaining seconds converted to minutes

                // Correct the total hours if minutes are 60 or more
                if ($morningTotalMinutes >= 60) {
                    $morningTotalHours += 1;
                    $morningTotalMinutes -= 60;
                }

                // Format the total hours and minutes
                $formattedMorningTotalHours = sprintf('%02d', $morningTotalHours);
                $formattedMorningTotalMinutes = sprintf('%02d', $morningTotalMinutes);
                $formattedMorningTotal = $formattedMorningTotalHours . ':' . $formattedMorningTotalMinutes;

                // Add the formatted morning total to the row
                $row['formatted_morning_total'] = $formattedMorningTotal;

                // Calculate AFTERNOON total hours
                $afternoonTotalSeconds = $row['afternoon_total_seconds'];
                $afternoonTotalHours = floor($afternoonTotalSeconds / 3600); // 3600 seconds in an hour
                $afternoonTotalMinutes = floor(($afternoonTotalSeconds % 3600) / 60); // Remaining seconds converted to minutes

                // Correct the total hours if minutes are 60 or more
                if ($afternoonTotalMinutes >= 60) {
                    $afternoonTotalHours += 1;
                    $afternoonTotalMinutes -= 60;
                }

                // Format the total hours and minutes
                $formattedAfternoonTotalHours = sprintf('%02d', $afternoonTotalHours);
                $formattedAfternoonTotalMinutes = sprintf('%02d', $afternoonTotalMinutes);
                $formattedAfternoonTotal = $formattedAfternoonTotalHours . ':' . $formattedAfternoonTotalMinutes;

                // Add the formatted afternoon total to the row
                $row['formatted_afternoon_total'] = $formattedAfternoonTotal;

                // Calculate overtime total hours
                $overtimeTotalSeconds = $row['overtime_total_seconds'];
                $overtimeTotalHours = floor($overtimeTotalSeconds / 3600); // 3600 seconds in an hour
                $overtimeTotalMinutes = floor(($overtimeTotalSeconds % 3600) / 60); // Remaining seconds converted to minutes

                // Correct the total hours if minutes are 60 or more
                if ($overtimeTotalMinutes >= 60) {
                    $overtimeTotalHours += 1;
                    $overtimeTotalMinutes -= 60;
                }

                // Format the total hours and minutes
                $formattedOvertimeTotalHours = sprintf('%02d', $overtimeTotalHours);
                $formattedOvertimeTotalMinutes = sprintf('%02d', $overtimeTotalMinutes);
                $formattedOvertimeTotal = $formattedOvertimeTotalHours . ':' . $formattedOvertimeTotalMinutes;

                // Add the formatted overtime total to the row
                $row['formatted_overtime_total'] = $formattedOvertimeTotal;

                // Calculate overall total hours
                $overallTotalSeconds = $row['morning_total_seconds'] + $row['afternoon_total_seconds'];
                $overallTotalHours = floor($overallTotalSeconds / 3600); // Calculate overall total hours
                $overallTotalMinutes = floor(($overallTotalSeconds % 3600) / 60); // Remaining seconds converted to minutes

                // Correct the total hours if minutes are 60 or more
                if ($overallTotalMinutes >= 60) {
                    $overallTotalHours += 1;
                    $overallTotalMinutes -= 60;
                }

                // Format the total hours and minutes
                $formattedOverallTotalHours = sprintf('%02d', $overallTotalHours);
                $formattedOverallTotalMinutes = sprintf('%02d', $overallTotalMinutes);
                $formattedOverallTotal = $formattedOverallTotalHours . ':' . $formattedOverallTotalMinutes;

                // Add the formatted overall total to the row
                $row['formatted_overall_total'] = $formattedOverallTotal;

                            $attendanceData[] = $row;
                        }
                    }

                    echo json_encode($attendanceData); // Return the JSON encoded attendance data
                }
                ?>
