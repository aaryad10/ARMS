<?php
$servername = "localhost:3307";
$username = "root";
$password = "";
$dbname = "pothole";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Parameters for clustering
$proximityRadius = 20; // meters

// Function to calculate distance between two latitude/longitude points in meters
function haversineGreatCircleDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371000; // in meters
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    $a = sin($dLat / 2) * sin($dLat / 2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($dLon / 2) * sin($dLon / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    return $earthRadius * $c;
}

// Retrieve all reports from pothole_reports
$sql = "SELECT * FROM pothole_reports";
$result = mysqli_query($conn, $sql);

while ($report = mysqli_fetch_assoc($result)) {
    $latitude = $report['latitude'];
    $longitude = $report['longitude'];
    $foundCluster = false;

    // Check if this report is near any existing cluster
    $sql_clusters = "SELECT * FROM pothole_clusters";
    $clusters = mysqli_query($conn, $sql_clusters);

    while ($cluster = mysqli_fetch_assoc($clusters)) {
        $distance = haversineGreatCircleDistance(
            $latitude, $longitude,
            $cluster['latitude'], $cluster['longitude']
        );

        if ($distance <= $proximityRadius) {
            // Update existing cluster
            $clusterId = $cluster['id'];
            $sql_update = "UPDATE pothole_clusters SET report_count = report_count + 1 WHERE id = '$clusterId'";
            mysqli_query($conn, $sql_update);
            $foundCluster = true;
            break;
        }
    }

    // If no cluster is nearby, create a new one
    if (!$foundCluster) {
        $sql_insert = "INSERT INTO pothole_clusters (latitude, longitude) VALUES ('$latitude', '$longitude')";
        mysqli_query($conn, $sql_insert);
    }
}

mysqli_close($conn);
?>
