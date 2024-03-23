<?php
session_start();
include("php/database.php");

if(isset($_POST['lname'],$_POST['fname'],$_POST['mid'],$_POST['mobile_number'], $_POST['employee_id'], $_POST['gender'], $_POST['department'], $_POST['salary'], $_POST['start_date'])) {

    $lname = $_POST['lname'];
    $fname = $_POST['fname'];
    $mid = $_POST['mid'];
    $name = $lname . ', ' . $fname . ' ' . $mid;
    $mobile_number = $_POST['mobile_number'];
    $employee_id = $_POST['employee_id'];
    $gender = $_POST['gender'];
    $department = implode(',', (array)$_POST['department']); 
    $salary = $_POST['salary'];
    $start_date = $_POST['start_date'];

    $stmt = $connection->prepare("INSERT INTO employees (lname, fname, middle_initial, name, mobile_number, employee_id, gender, department, salary, start_date) VALUES (?,?,?,?,?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssss", $lname, $fname, $mid, $name, $mobile_number, $employee_id, $gender, $department, $salary, $start_date);

    if ($stmt->execute()) {
        header("Location: positionlist.php");
        exit();
    } else {
        echo "Error: " . $connection->error;
    }
    $stmt->close();
} else {
    echo "All fields are required";
}
$connection->close();
?>
