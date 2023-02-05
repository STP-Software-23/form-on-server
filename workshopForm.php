<?php

$connectionParameters = [
    "host" => "localhost:3306",
    "username" => "stpeg123",
    "password" => "STePss@21/CP_BoArd&16Y",
    "dbName" => "stpeg123_stpeg23_workshops",

];


$connection = mysqli_connect($connectionParameters['host'],$connectionParameters['username'],$connectionParameters['password'],$connectionParameters['dbName']);

//Check connection
if(!$connection){
    
    echo 'Connection error' . mysqli_connect_error() ;
}


$name = $_POST["name"];
$Phone = $_POST["phone"];
$email = $_POST["email"];
$faculty = $_POST["faculty"];
$graduationYear = $_POST["graduationYear"];
$university = $_POST["university"];
$firstPreference = $_POST["firstPreference"];
$secondPreference = $_POST["secondPreference"];
$experience = $_POST["experience"];
$registeredAt;


if($firstPreference == null || $secondPreference == null){
    die("Please fill all required fields!");
}

if (
    empty($name) || empty($Phone) || empty($email) ||
    empty($faculty)  || empty($graduationYear) || empty($university) ||
    empty($experience)
) {
    echo "<script> alert('Please fill all  required fields!'); window.history.back(); </script>";
}

// /^(00201|\+201|01)[0125][0-9]{8}$/ 
// /^01[0125][0-9]{8}$/ 
// Validate phone numbers in Egypt for the 4 major Service providers
if (!preg_match("/^(00201|\+201|01)[0125][0-9]{8}$/", $Phone)) {
    echo "<script> alert('Enter your phone number again.'); window.history.back(); </script>";
}

// $email_validation_regex = "/^[a-z0-9!#$%&'*+\\/=?^_`{|}~-]+(?:\\.[a-z0-9!#$%&'*+\\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/"; 
// Validate email
// if (!preg_match($email_validation_regex, $email)) {
//     echo "<script> alert('Valid email is required.'); window.history.back(); </script>";
// }




$sql = $connection->prepare("SELECT * FROM participants WHERE email ='" . $email . "'");
$sql->execute();
$result = $sql->get_result();
if ($result->num_rows > 0) {
    echo "<script>alert('You can`t submit twice!');window.location = 'https://stp-org.com/';</script>";
    exit();
}


// Store in DB
// get sqli connection


// Initialize prepared statement
$stmt = $connection->stmt_init();


$date = new DateTime("now", new DateTimeZone('Africa/Cairo') );
$Ndate = $date->format('Y-m-d h:i:s') ;

// Insert the data into db
try {
    $sql = "INSERT INTO participants (name, phone, email, faculty, graduationYear, university, firstPreference, secondPreference, experience, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?,?)";


    if (!$stmt->prepare($sql)) {
        die("SQL error: ......................." . $connection->error);
    }


    $stmt->bind_param(
        "ssssssssss",
        $name,
        $Phone,
        $email,
        $faculty,
        $graduationYear,
        $university,
        $firstPreference,
        $secondPreference,
        $experience,
        $Ndate
    );
} catch (Exception $e) {
    echo 'Message: ' . $e->getMessage();
}

try {
    if ($stmt->execute()) {
        // Redirect...
        echo "<script> alert('Kindly check your Email for more informations!'); window.location = 'https://stp-org.com/'; </script>";
        include('sendMail.php');
    } else {
        die($mysqli->error . " " . $mysqli->errno);
    }
} catch (Exception $e) {
    echo 'Message: ' . $e->getMessage();
    echo "<script>window.history.back(); </script>";
}




