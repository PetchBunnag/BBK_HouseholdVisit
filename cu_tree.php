<?php

// Initialize connection variables.
//require_once("connect_inc.php")
include("connect_inc.php");


# Build GeoJSON feature collection array
$geojson = array(
   'type'      => 'FeatureCollection',
   'features'  => array()
);

# Build SQL SELECT statement and return the geometry as a GeoJSON element
$sql = "select gid, t.tree_id, survey_date, geohash, ST_AsGeoJSON( the_geom, 6) AS geojson,
dbh, cirbh, total_h, verti_h, slop, stem_curve, bush_area, bush_thickness, url_image,
name_id, name_t, name_e, genus, species, protection, royal_owner, condition, historical, address, plant_date, description, flowers, pods
from tree t 
left join tree_information tinf on t.tree_id = tinf.tree_id
left join tree_inventory tinv on t.tree_id = tinv.tree_id
where t.status = TRUE;";

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
    unset($properties['the_geom']);
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





?>