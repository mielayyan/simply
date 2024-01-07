<?php
include ('project_config.php');
$conn = mysqli_connect($db_hostname, $db_username, $db_password, $db_database);
// Check connection
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

$sql = "SELECT user_detail_id, user_detail_name,simply_url FROM 39_user_details";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html>
<title>Live Database</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<body>

<div class="w3-container">
  <h2>DataShowing</h2>

  <table class="w3-table-all w3-xlarge">
    <tr>
      <th>ID</th>
      <th>Username</th>
      <th>Link</th>
    </tr>
   
 
  
<?php
if (mysqli_num_rows($result) > 0) {
  // output data of each row
  while($row = mysqli_fetch_assoc($result)) {

    $user_id=$row["user_detail_id"];
    $user_detail_name=$row["user_detail_name"];
    $simply_url=$row["simply_url"];
   
?>
 <tr>
      <td><?=$user_id?></td>
      <td><?=$user_detail_name?></td>
      <td><?=$simply_url?></td>
      <td><?=$check_done?></td>
    </tr>
    <tr>
<?php
  

  }
} else {
  echo "0 results";
}
?>
</table>
</div>

</body>
</html> 
<?php
mysqli_close($conn);



?>