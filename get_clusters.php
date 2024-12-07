
<?php
$servername = "localhost:3307";
$username = "root";
$password = "";
$dbname = "pothole";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "SELECT * FROM pothole_clusters";
$result = mysqli_query($conn, $sql);

$clusters = [];
while ($row = mysqli_fetch_assoc($result)) {
    $clusters[] = $row;
}

mysqli_close($conn);

// Return JSON response
header('Content-Type: application/json');
echo json_encode($clusters);
?>
