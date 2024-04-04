<?php

// Check if all required POST parameters are set
if (!isset($_POST["provider"]) || !isset($_POST["terms"]) || !isset($_POST["userid"])){
    exit("Not enough information provided");
}

// Get POST data and sanitize
$provider = htmlspecialchars($_POST["provider"]);
$terms = htmlspecialchars($_POST["terms"]);
$userid = htmlspecialchars($_POST["userid"]);

// Simulate a long search process
sleep(1); 

// Function to call API
function callAPI($method, $url, $data){
    $curl = curl_init();
    switch ($method){
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);
            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);			 					
            break;
        default:
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
    }
    // OPTIONS:
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    // EXECUTE:
    $result = curl_exec($curl);
    if(!$result){
        $result = "No results found!";
    }
    curl_close($curl);
    return $result;
}

// Construct the URL for the API call
$theurl = 'http://localhost'.$provider.'?userid='.$userid.'&terms='.$terms;

// Call the API
$get_data = callAPI('GET', $theurl, false);

// Output the result
echo $get_data;
?>