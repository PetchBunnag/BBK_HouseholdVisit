<?php

// Initialize connection variables.
include("ru_connect.php");


# Build GeoJSON feature collection array
$geojson = array(
    'type'      => 'FeatureCollection',
    'features'  => array()
);

# Build SQL SELECT statement and return the geometry as a GeoJSON element
$sql = "select st_asgeojson(st_makeline(geom)) as geojson from savelocation_07_mar_22_10_35_37_am";

//echo $sql;

# Try query or error
$rs = pg_query($connection, $sql);
if (!$rs) {
    echo 'An SQL error occured.\n';
    exit;
}

# Loop through rows to build feature arrays
while ($row = pg_fetch_assoc($rs)) {
    $properties = $row;
    //echo $properties;
    # Remove geojson and geometry fields from properties
    unset($properties['geojson']);
    unset($properties['geom']);
    $feature = array(
        'type' => 'Feature',
        'geometry' => json_decode($row['geojson'], true),
        'properties' => $properties
    );
    //echo $feature;
    # Add feature arrays to feature collection array
    array_push($geojson['features'], $feature);
}
header('Content-type: application/json');
echo json_encode($geojson, JSON_NUMERIC_CHECK);
$conn = NULL;
