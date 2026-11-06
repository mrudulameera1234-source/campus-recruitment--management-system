<?php
$dbhost='localhost';
$dbuser='root';
$dbpass='';
$db='campus_recruitment_db';// replace it with your database
$conn=mysqli_connect($dbhost,$dbuser,$dbpass,$db,3307);
if(!$conn){
    echo "Error connecting to database: ";
}
?>
