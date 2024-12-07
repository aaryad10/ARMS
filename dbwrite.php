
<?php



    $host = "localhost:3307";		         // host = localhost because database hosted on the same server where PHP files are hosted
    $dbname = "pothole";              // Database name
    $username = "root";		// Database username
    $password = "";	        // Database password


// Establish connection to MySQL database
$conn = new mysqli($host, $username, $password, $dbname);


// Check if connection established successfully
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

else { echo "Connected to mysql database. "; }

   
// Get date and time variables
    date_default_timezone_set('Asia/Kolkata');  // for other timezones, refer:- https://www.php.net/manual/en/timezones.asia.php
    $d = date("Y-m-d");
    $t = date("H:i:s");
    echo $d;
    echo $t;
    
// If values send by NodeMCU are not empty then insert into MySQL database table

  if(!empty($_GET['longitude']) && !empty($_GET['latitude']) && !empty($_GET['depth']) )
    {
		$latitude = $_GET['latitude'];
                $longitude = $_GET['longitude'];
                $depth = $_GET['depth'];
            }else{
                echo "Lat & long found !";
                $latitude = 18.463861;
                $longitude = 73.868733;
                $depth = "16.00";
            }

// Update your tablename here
	        $sql = "INSERT INTO pothole_reports (latitude, longitude, depth , date, time) VALUES ('".$latitude."','".$longitude."','".$depth."', '".$d."', '".$t."')"; 


		if ($conn->query($sql) === TRUE) {
		    echo "Values inserted in MySQL database table.";
		} else {
		    echo "Error: " . $sql . "<br>" . $conn->error;
		}



// Close MySQL connection
$conn->close();



?>
