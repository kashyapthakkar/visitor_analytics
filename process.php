<?php

$DB['server'] = 'remotemysql.com';                                                //Server
$DB['user'] = '6CkDUmmAHw';                                                       //username
$DB['password'] = 'rptUj5wLe0';                                                       //password
$DB['db'] = '6CkDUmmAHw';                                               //database name
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
if(isset($_GET["website_name"])){
    $url = filter_var($_GET["website_name"], FILTER_SANITIZE_URL);
}

$result["url"] = $url;
if (preg_match( '/^(http|https):\\/\\/[a-z0-9_]+([\\-\\.]{1}[a-z_0-9]+)*\\.[_a-z]{2,5}'.'((:[0-9]{1,5})?\\/.*)?$/i' ,$url)) {
    $file = $url;
    $file_headers = @get_headers($file);
    if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') {
        $result["error"] = "404 Not Found";
    }
    else {
        try{
            $stmt = $conn->prepare("select * from websites");
            $stmt->execute();
            $urls = $stmt->fetchAll();
            
            $urlExist = false;
            for($i=0; $i<sizeof($urls); $i++){
                
                if($urls[$i]["url"] == $url){
                    
                    $urlExist = true;
                    try{
                        $stmt = $conn->prepare("update websites set visits=? where url_id=?");
                        $stmt->execute([($urls[$i]["visits"] + 1), $urls[$i]["url_id"]]);
                        if($stmt){
                            $result["message"] = "visit Updated Successfully!";

                            $result["url_data"] = array(
                                "visits"=>$urls[$i]["visits"] + 1,
                                "timestamp"=>$urls[$i]["date"]
                            );
                        }else{
                            $result["message"] = "Failed to Update Visits!";
                        }
                    }catch(Exception $e){
                        echo "Error: " . $e->getMessage();
                    }
                }
            }
            if(!$urlExist){
                try{
                    $stmt = $conn->prepare("insert into websites (url, visits) values(?, ?)");
                    $stmt->execute([$url, 1]);
                    if($stmt){
                        $result["message"] = "url Added Successfully!";
                    }else{
                        $result["message"] = "Failed to Add url!";
                    }
                }catch(Exception $e){
                    echo "Error: " . $e->getMessage();
                }
            }
        }catch(Exception $e){
            echo "Error: " . $e->getMessage();
        }
    }
} else {
    $result["error"] = "Invalid URL";
}

echo json_encode($result);

