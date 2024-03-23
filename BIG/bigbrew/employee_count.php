<?php
// Include your database connection code
include("php/database.php");

// Check if connection is successful
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Query to get total number of employees
$sql = "SELECT COUNT(*) as total_employees FROM employees";
$result = mysqli_query($connection, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    // Fetch the count
    $row = mysqli_fetch_assoc($result);
    $totalEmployees = $row['total_employees'];

    // Return the count as response
    echo $totalEmployees;
} else {
    // If no employees found, return 0
    echo "0";
}

// Close database connection
mysqli_close($connection);
?>
