<?php

$DB['server'] = 'localhost';                                                //Server
$DB['user'] = 'root';                                                       //username
$DB['password'] = '';                                                       //password
$DB['db'] = 'visitors_data';                                               //database name

try
{

    // connect to database
    $conn = new PDO("mysql:host=".$DB['server'].";dbname=".$DB['db'],
                    $DB['user'],
                    $DB['password']);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // have my fetch data returned as an associative array
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e)                                                          //thows an exception if anything goes wrong with database
{
    echo "Connection failed: " . $e->getMessage();
}


$url = "";                                                                   //action from front-end

//checks if action is provided
if(isset($_POST["website_name"])){
    $url = $_POST["website_name"];
}

$result["url"] = $url;
$temp = "http://example.com/sdff";
$result["temp"] = $temp;
if (preg_match( '/^(http|https):\\/\\/[a-z0-9_]+([\\-\\.]{1}[a-z_0-9]+)*\\.[_a-z]{2,5}'.'((:[0-9]{1,5})?\\/.*)?$/i' ,$temp)) {
    $result["error"] = "Valid URL";
} else {
    $result["error"] = "Invalid URL";
}

echo json_encode($result);

