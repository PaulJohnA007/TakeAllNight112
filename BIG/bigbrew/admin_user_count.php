<?php
include("php/database.php");

$query = "SELECT COUNT(*) as total_admin FROM admin";
$result = mysqli_query($connection, $query);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    echo $row['total_admin'];
} else {
    echo '0';
}

mysqli_close($connection);
?>
