<!DOCTYPE html>
<html>
<head>
    <title>Pothole Map</title>
    <!-- Include Leaflet CSS and JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <style>
        #map {
            height: 500px;
            width: 100%;
        }
    </style>
</head>
<body>
    <h3>Pothole Detection Map</h3>
    <div id="map"></div>

    <script>
        // Initialize the map and set its view to a specific location and zoom level
        var map = L.map('map').setView([18.5204, 73.8567], 12); // Center map on Pune (example coordinates)

        // Use OpenStreetMap tiles with Leaflet
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Fetch pothole cluster data from the server
        fetch("get_clusters.php")
            .then(response => response.json())
            .then(clusters => {
                clusters.forEach(cluster => {
                    // Add a marker for each pothole cluster
                    var marker = L.marker([parseFloat(cluster.latitude), parseFloat(cluster.longitude)]).addTo(map);
                    marker.bindPopup(`<b>Pothole Reports:</b> ${cluster.report_count}<br>Latitude: ${cluster.latitude}<br>Longitude: ${cluster.longitude}`);
                });
            })
            .catch(error => console.error('Error fetching cluster data:', error));
    </script>
</body>
</html>
