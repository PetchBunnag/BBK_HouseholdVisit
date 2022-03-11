<html>

<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.8.0-beta.0/leaflet.css" integrity="sha512-R1nTzP7vsLwNjdybP0Y3uBI9Da0mdZ04VCmH13UyIYgzlIel6/C8uHWfC7WrwESWh2MyIWx0WMZgfkkYKtaQxg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.8.0-beta.0/leaflet-src.js" integrity="sha512-S9YaSljM33+fgQdCixCg1V3DD4G3WBfK3yfCUptb7P/7rZeonTwqOaJvfBxIPSKTf5giTMR1TwHhIGOLkFmw6w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-ajax/2.1.0/leaflet.ajax.js" integrity="sha512-eYE5o0mD7FFys0tVot8r4AnRXzVVXhjVpzNK+AcHkg4zNLvUAaCOJyLFKjmfpJMj6L/tuCzMN7LULBvNDhy5pA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-ajax/2.1.0/leaflet.ajax.min.js" integrity="sha512-Abr21JO2YqcJ03XGZRPuZSWKBhJpUAR6+2wH5zBeO4wAw4oksr8PRdF+BKIRsxvCdq+Mv4670rZ+dLnIyabbGw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="css/styledLayerControl.css" />
    <script src="src/styledLayerControl.js"></script>
    <script src="plugins/Bing.js"></script>
    <script src="build/heatmap.js"></script>
    <script src="plugins/leaflet-heatmap/leaflet-heatmap.js"></script>

    <style>
        body {
            padding: 0;
            margin: 0;
        }

        #map {
            width: 100%;
            height: 100%;
        }

        .leaflet-google-layer {
            z-index: 0;
        }

        .leaflet-map-pane {
            z-index: 100;
        }

        l {
            font-weight: bold;
        }
    </style>

</head>

<body>
    <div id="map"></div>

    <script>
        // Google layers
        var googleStreets = L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
        });
        var googleSat = L.tileLayer('http://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
        });

        // OSM layers
        var osmUrl = 'http://{s}.tile.osm.org/{z}/{x}/{y}.png';
        var osmAttrib = 'Map data © <a href="http://openstreetmap.org">OpenStreetMap</a> contributors';
        var osm = new L.TileLayer(osmUrl, {
            attribution: osmAttrib
        });

        // Bing layers
        var bing1 = new L.BingLayer("AvZ2Z8Jve41V_bnPTe2mw4Xi8YWTyj2eT87tSGSsezrYWiyaj0ldMaVdkyf8aik6", {
            type: 'Aerial'
        });
        var bing2 = new L.BingLayer("AvZ2Z8Jve41V_bnPTe2mw4Xi8YWTyj2eT87tSGSsezrYWiyaj0ldMaVdkyf8aik6", {
            type: 'Road'
        });

        var point = L.geoJson(null, {
            onEachFeature: function(feature, layer) {
                var popupContent = "<l>Location Name: </l>" + feature.properties["Location Name"] +
                    "<br><l>Address: </l>" + feature.properties["Address"] +
                    "<br><l>Date: </l>" + feature.properties["Date"] +
                    "<br><l>People: </l>" + feature.properties.people;
                layer.bindPopup(popupContent);
            }
        });

        $.getJSON('point.php', function(data) {
            point.addData(data).addTo(map);
        });

        var line = L.geoJson(null);

        $.getJSON('line.php', function(data) {
            line.addData(data).addTo(map);
        });

        var testData = {
          max: 8,
          data: [{lat: 24.6408, lng:46.7728, count: 3},{lat: 50.75, lng:-1.55, count: 1},{lat: 52.6333, lng:1.75, count: 1},{lat: 48.15, lng:9.4667, count: 1},{lat: 52.35, lng:4.9167, count: 2},{lat: 60.8, lng:11.1, count: 1},{lat: 43.561, lng:-116.214, count: 1},{lat: 47.5036, lng:-94.685, count: 1},{lat: 42.1818, lng:-71.1962, count: 1},{lat: 42.0477, lng:-74.1227, count: 1},{lat: 40.0326, lng:-75.719, count: 1},{lat: 40.7128, lng:-73.2962, count: 2},{lat: 27.9003, lng:-82.3024, count: 1},{lat: 38.2085, lng:-85.6918, count: 1},{lat: 46.8159, lng:-100.706, count: 1},{lat: 30.5449, lng:-90.8083, count: 1},{lat: 44.735, lng:-89.61, count: 1},{lat: 41.4201, lng:-75.6485, count: 2},{lat: 39.4209, lng:-74.4977, count: 1},{lat: 39.7437, lng:-104.979, count: 1},{lat: 39.5593, lng:-105.006, count: 1},{lat: 45.2673, lng:-93.0196, count: 1},{lat: 41.1215, lng:-89.4635, count: 1},{lat: 43.4314, lng:-83.9784, count: 1},{lat: 43.7279, lng:-86.284, count: 1},{lat: 40.7168, lng:-73.9861, count: 1},{lat: 47.7294, lng:-116.757, count: 1},{lat: 47.7294, lng:-116.757, count: 2},{lat: 35.5498, lng:-118.917, count: 1},{lat: 34.1568, lng:-118.523, count: 1},{lat: 39.501, lng:-87.3919, count: 3},{lat: 33.5586, lng:-112.095, count: 1},{lat: 38.757, lng:-77.1487, count: 1},{lat: 33.223, lng:-117.107, count: 1},{lat: 30.2316, lng:-85.502, count: 1},{lat: 39.1703, lng:-75.5456, count: 8},{lat: 30.0041, lng:-95.2984, count: 2},{lat: 29.7755, lng:-95.4152, count: 1},{lat: 41.8014, lng:-87.6005, count: 1},{lat: 37.8754, lng:-121.687, count: 7},{lat: 38.4493, lng:-122.709, count: 1},{lat: 40.5494, lng:-89.6252, count: 1},{lat: 42.6105, lng:-71.2306, count: 1},{lat: 40.0973, lng:-85.671, count: 1},{lat: 40.3987, lng:-86.8642, count: 1},{lat: 40.4224, lng:-86.8031, count: 4},{lat: 47.2166, lng:-122.451, count: 1},{lat: 32.2369, lng:-110.956, count: 1},{lat: 41.3969, lng:-87.3274, count: 2},{lat: 41.7364, lng:-89.7043, count: 2},{lat: 42.3425, lng:-71.0677, count: 1},{lat: 33.8042, lng:-83.8893, count: 1},{lat: 36.6859, lng:-121.629, count: 2},{lat: 41.0957, lng:-80.5052, count: 1},{lat: 46.8841, lng:-123.995, count: 1},{lat: 40.2851, lng:-75.9523, count: 2},{lat: 42.4235, lng:-85.3992, count: 1},{lat: 39.7437, lng:-104.979, count: 2},{lat: 25.6586, lng:-80.3568, count: 7},{lat: 33.0975, lng:-80.1753, count: 1},{lat: 25.7615, lng:-80.2939, count: 1},{lat: 26.3739, lng:-80.1468, count: 1},{lat: 37.6454, lng:-84.8171, count: 1},{lat: 34.2321, lng:-77.8835, count: 1},{lat: 34.6774, lng:-82.928, count: 1},{lat: 39.9744, lng:-86.0779, count: 1},{lat: 35.6784, lng:-97.4944, count: 2},{lat: 33.5547, lng:-84.1872, count: 1},{lat: 27.2498, lng:-80.3797, count: 1},{lat: 41.4789, lng:-81.6473, count: 1},{lat: 41.813, lng:-87.7134, count: 1},{lat: 41.8917, lng:-87.9359, count: 1},{lat: 35.0911, lng:-89.651, count: 1},{lat: 32.6102, lng:-117.03, count: 1},{lat: 41.758, lng:-72.7444, count: 1},{lat: 39.8062, lng:-86.1407, count: 1},{lat: 41.872, lng:-88.1662, count: 1},{lat: 34.1404, lng:-81.3369, count: 1},{lat: 46.15, lng:-60.1667, count: 1},{lat: 36.0679, lng:-86.7194, count: 1},{lat: 43.45, lng:-80.5, count: 1},{lat: 44.3833, lng:-79.7, count: 1},{lat: 45.4167, lng:-75.7, count: 2},{lat: 43.75, lng:-79.2, count: 2},{lat: 45.2667, lng:-66.0667, count: 3},{lat: 42.9833, lng:-81.25, count: 2},{lat: 44.25, lng:-79.4667, count: 3},{lat: 45.2667, lng:-66.0667, count: 2},{lat: 34.3667, lng:-118.478, count: 3},{lat: 42.734, lng:-87.8211, count: 1},{lat: 39.9738, lng:-86.1765, count: 1},{lat: 33.7438, lng:-117.866, count: 1},{lat: 37.5741, lng:-122.321, count: 1},{lat: 42.2843, lng:-85.2293, count: 1},{lat: 34.6574, lng:-92.5295, count: 1},{lat: 41.4881, lng:-87.4424, count: 1},{lat: 25.72, lng:-80.2707, count: 1},{lat: 34.5873, lng:-118.245, count: 1},{lat: 35.8278, lng:-78.6421, count: 1}]
        };

        var cfg = {
          // radius should be small ONLY if scaleRadius is true (or small radius is intended)
          "radius": 2,
          "maxOpacity": .8, 
          // scales the radius based on map zoom
          "scaleRadius": true, 
          // if set to false the heatmap uses the global maximum for colorization
          // if activated: uses the data maximum within the current map boundaries 
          //   (there will always be a red spot with useLocalExtremas true)
          "useLocalExtrema": true,
          // which field name in your data represents the latitude - default "lat"
          latField: 'lat',
          // which field name in your data represents the longitude - default "lng"
          lngField: 'lng',
          // which field name in your data represents the data value - default "value"
          valueField: 'count'
        };

        var heatmapLayer = new HeatmapOverlay(cfg);

        var map = L.map('map', {
            center: [13.6725183891, 100.427231182],
            zoom: 15,
            layers: [osm, heatmapLayer]
        });

        heatmapLayer.setData(testData);

        // make accessible for debugging
        layer = heatmapLayer;

        map.addLayer(bing1);

        var baseMaps = [{
            groupName: "Google Base Maps",
            expanded: true,
            layers: {
                "Satellite": googleSat,
                "Street View": googleStreets
            }
        }, {
            groupName: "OSM Base Maps",
            layers: {
                "OpenStreetMaps": osm
            }
        }];

        var overlays = [{
            groupName: "March 7, 2022",
            expanded: true,
            layers: {
                "Points": point,
                "Line": line
            }
        }];

        point.StyledLayerControl = {
            removable: false
        }

        var options = {
            container_width: "300px",
            group_maxHeight: "80px",
            //container_maxHeight : "350px", 
            exclusive: true
        };

        var control = L.Control.styledLayerControl(baseMaps, overlays, options);
        map.addControl(control);

        // test for adding new base layers dynamically
        // to create a new group simply add a layer with new group name
        control.addBaseLayer(bing1, "Bing Satellite", {
            groupName: "Bing Maps",
            expanded: true
        });
        control.addBaseLayer(bing2, "Bing Road", {
            groupName: "Bing Maps"
        });

        control.selectLayer(line);

        control.selectGroup("March 7, 2022");
    </script>
</body>

</html>