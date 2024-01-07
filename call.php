<?php
include ('project_config.php');
$conn = mysqli_connect($db_hostname, $db_username, $db_password, $db_database);
// Check connection
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

$sql = "SELECT user_detail_id, user_detail_name FROM 39_user_details where simply_url !='' or simply_url !='simply_url'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
  // output data of each row
  while($row = mysqli_fetch_assoc($result)) {

    $user_id=$row["user_detail_id"];
    $user_detail_name=$row["user_detail_name"];
  // $final_url= callAPI($user_id,$user_detail_name);
  // $sql_update = "UPDATE 39_user_details SET simply_url='$final_url' WHERE user_detail_id=$user_id";
   //mysqli_query($conn, $sql_update);

  }
} else {
  echo "0 results";
}

mysqli_close($conn);

function callAPI($userId1,$username1){
// API endpoint
$url = 'https://backendapp.simply37.com/api/user/register';

// Your values
$appKey = 'f9r!G$2T?7k%u3@M4x';
$userId = $userId1;
$username = $username1;
$type='s1500';

// Data to be sent
$data = array(
    'app_key' => $appKey,
    'user_id' => $userId,
    'username' => $username,
    'Type' => $type,
);

// Initialize curl session
$ch = curl_init($url);

// Set curl options
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute curl session
$response = curl_exec($ch);

// Check for errors
if ($response === false) {
   // echo 'Curl error: ' . curl_error($ch);
   return NULL;
} else {
    // Handle the API response
    $decodedResponse = json_decode($response, true);
    return $decodedResponse['data'];
}

// Close curl session
curl_close($ch);
}

?>