<html>

<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.8.0-beta.0/leaflet.css" integrity="sha512-R1nTzP7vsLwNjdybP0Y3uBI9Da0mdZ04VCmH13UyIYgzlIel6/C8uHWfC7WrwESWh2MyIWx0WMZgfkkYKtaQxg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.8.0-beta.0/leaflet-src.js" integrity="sha512-S9YaSljM33+fgQdCixCg1V3DD4G3WBfK3yfCUptb7P/7rZeonTwqOaJvfBxIPSKTf5giTMR1TwHhIGOLkFmw6w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-ajax/2.1.0/leaflet.ajax.js" integrity="sha512-eYE5o0mD7FFys0tVot8r4AnRXzVVXhjVpzNK+AcHkg4zNLvUAaCOJyLFKjmfpJMj6L/tuCzMN7LULBvNDhy5pA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-ajax/2.1.0/leaflet.ajax.min.js" integrity="sha512-Abr21JO2YqcJ03XGZRPuZSWKBhJpUAR6+2wH5zBeO4wAw4oksr8PRdF+BKIRsxvCdq+Mv4670rZ+dLnIyabbGw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="css/styledLayerControl.css" />
    <script src="src/styledLayerControl.js"></script>
    <!-- <script src="plugins/Bing.js"></script> -->
    <script src="build/heatmap.js"></script>
    <script src="plugins/leaflet-heatmap/leaflet-heatmap.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun&display=swap" rel="stylesheet">

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

        .leaflet-container {
            font-family: 'Sarabun', sans-serif;
        }

        .leaflet-tooltip.my-labels {
            background-color: transparent;
            border: transparent;
            box-shadow: none;
            font-family: 'Sarabun', sans-serif;
            /* font-weight: bold; */
            font-size: 16px;
        }

        label {
            font-family: 'Sarabun', sans-serif;
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
        var osmUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
        var osm = new L.TileLayer(osmUrl, {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            subdomains: ['a', 'b', 'c']
        });

        // Bing layers
        // var bing1 = new L.BingLayer("AvZ2Z8Jve41V_bnPTe2mw4Xi8YWTyj2eT87tSGSsezrYWiyaj0ldMaVdkyf8aik6", {
        //     type: 'Aerial'
        // });
        // var bing2 = new L.BingLayer("AvZ2Z8Jve41V_bnPTe2mw4Xi8YWTyj2eT87tSGSsezrYWiyaj0ldMaVdkyf8aik6", {
        //     type: 'Road'
        // });

        function Get(yourUrl) {
            var Httpreq = new XMLHttpRequest(); // a new request
            Httpreq.open("GET", yourUrl, false);
            Httpreq.send(null);
            return Httpreq.responseText;
        }

        var point = L.geoJson(null, {
            onEachFeature: function(feature, layer) {
                var popupContent = "<l>Location Name: </l>" + feature.properties["Location Name"] +
                    "<br><l>Address: </l>" + feature.properties.Address +
                    "<br><l>Date: </l>" + feature.properties.Date +
                    "<br><l>People: </l>" + feature.properties.people;
                layer.bindPopup(popupContent);
            }
        });

        $.getJSON('point.php', function(data) {
            point.addData(data).addTo(map);
        });

        var lat = [];
        var lon = [];
        var people = [];

        var point_geojson = JSON.parse(Get('point.php'));
        for (var i = 0; i < point_geojson.features.length; i++) {
            lat.push(point_geojson.features[i].properties.Latitude)
            lon.push(point_geojson.features[i].properties.Longitude)
            people.push(point_geojson.features[i].properties.people)
        }

        var line = L.geoJson(null);

        $.getJSON('line.php', function(data) {
            line.addData(data).addTo(map);
        });

        // Add 6 Zones

        var zone_01 = L.geoJson(null, {
            onEachFeature: function(feature, layer) {
                var popupContent = "<l>กลุ่มเขต: </l>" + feature.properties.z_name +
                    "<br><l>ประชากรชาย: </l>" + feature.properties.no_male +
                    "<br><l>ประชากรหญิง: </l>" + feature.properties.no_female +
                    "<br><l>ครัวเรือน: </l>" + feature.properties.no_house +
                    "<br><l>ชุมชน: </l>" + feature.properties.no_commu +
                    "<br><l>พื้นที่: </l>" + feature.properties.z_area;
                layer.bindPopup(popupContent);
            }
        });

        var zone_02 = L.geoJson(null, {
            onEachFeature: function(feature, layer) {
                var popupContent = "<l>กลุ่มเขต: </l>" + feature.properties.z_name +
                    "<br><l>ประชากรชาย: </l>" + feature.properties.no_male +
                    "<br><l>ประชากรหญิง: </l>" + feature.properties.no_female +
                    "<br><l>ครัวเรือน: </l>" + feature.properties.no_house +
                    "<br><l>ชุมชน: </l>" + feature.properties.no_commu +
                    "<br><l>พื้นที่: </l>" + feature.properties.z_area;
                layer.bindPopup(popupContent);
            }
        });

        var zone_03 = L.geoJson(null, {
            onEachFeature: function(feature, layer) {
                var popupContent = "<l>กลุ่มเขต: </l>" + feature.properties.z_name +
                    "<br><l>ประชากรชาย: </l>" + feature.properties.no_male +
                    "<br><l>ประชากรหญิง: </l>" + feature.properties.no_female +
                    "<br><l>ครัวเรือน: </l>" + feature.properties.no_house +
                    "<br><l>ชุมชน: </l>" + feature.properties.no_commu +
                    "<br><l>พื้นที่: </l>" + feature.properties.z_area;
                layer.bindPopup(popupContent);
            }
        });

        var zone_04 = L.geoJson(null, {
            onEachFeature: function(feature, layer) {
                var popupContent = "<l>กลุ่มเขต: </l>" + feature.properties.z_name +
                    "<br><l>ประชากรชาย: </l>" + feature.properties.no_male +
                    "<br><l>ประชากรหญิง: </l>" + feature.properties.no_female +
                    "<br><l>ครัวเรือน: </l>" + feature.properties.no_house +
                    "<br><l>ชุมชน: </l>" + feature.properties.no_commu +
                    "<br><l>พื้นที่: </l>" + feature.properties.z_area;
                layer.bindPopup(popupContent);
            }
        });

        var zone_05 = L.geoJson(null, {
            onEachFeature: function(feature, layer) {
                var popupContent = "<l>กลุ่มเขต: </l>" + feature.properties.z_name +
                    "<br><l>ประชากรชาย: </l>" + feature.properties.no_male +
                    "<br><l>ประชากรหญิง: </l>" + feature.properties.no_female +
                    "<br><l>ครัวเรือน: </l>" + feature.properties.no_house +
                    "<br><l>ชุมชน: </l>" + feature.properties.no_commu +
                    "<br><l>พื้นที่: </l>" + feature.properties.z_area;
                layer.bindPopup(popupContent);
            }
        });

        var zone_06 = L.geoJson(null, {
            onEachFeature: function(feature, layer) {
                var popupContent = "<l>กลุ่มเขต: </l>" + feature.properties.z_name +
                    "<br><l>ประชากรชาย: </l>" + feature.properties.no_male +
                    "<br><l>ประชากรหญิง: </l>" + feature.properties.no_female +
                    "<br><l>ครัวเรือน: </l>" + feature.properties.no_house +
                    "<br><l>ชุมชน: </l>" + feature.properties.no_commu +
                    "<br><l>พื้นที่: </l>" + feature.properties.z_area;
                layer.bindPopup(popupContent);
            }
        });

        $.getJSON('zone_01.php', function(data) {
            zone_01.addData(data);
        });

        $.getJSON('zone_02.php', function(data) {
            zone_02.addData(data);
        });

        $.getJSON('zone_03.php', function(data) {
            zone_03.addData(data);
        });

        $.getJSON('zone_04.php', function(data) {
            zone_04.addData(data);
        });

        $.getJSON('zone_05.php', function(data) {
            zone_05.addData(data);
        });

        $.getJSON('zone_06.php', function(data) {
            zone_06.addData(data);
        });

        // Add district layer

        var district = L.geoJson(null, {
            onEachFeature: function(feature, layer) {
                var popupContent = "<l>เขต: </l>" + feature.properties.dname +
                    "<br><l>พื้นที่: </l>" + feature.properties.area +
                    "<br><l>ประชากรชาย: </l>" + feature.properties.no_male +
                    "<br><l>ประชากรหญิง: </l>" + feature.properties.no_female +
                    "<br><l>สถานพยาบาล: </l>" + feature.properties.no_health +
                    "<br><l>วัด: </l>" + feature.properties.no_temple +
                    "<br><l>ชุมชน: </l>" + feature.properties.no_commu +
                    // "<br><l>โรงพยาบาล: </l>" + feature.properties.no_hos +
                    "<br><l>โรงเรียน: </l>" + feature.properties.no_sch;
                layer.bindPopup(popupContent);
            }
        });

        $.getJSON('district.php', function(data) {
            district.addData(data).addTo(map);
        });

        // Add community layer

        var geojsonMarkerOptions = {
            radius: 8,
            fillColor: "#ff7800",
            color: 'transparent',
            weight: 1,
            opacity: 1,
            fillOpacity: 0.8
        };

        var community = L.geoJson(null, {
            pointToLayer: function(feature, latlng) {
                label = String(feature.properties.comm_id)
                return L.circleMarker(latlng, geojsonMarkerOptions).bindTooltip(label, {
                    permanent: true,
                    direction: "top",
                    className: "my-labels"
                }).openTooltip();
            },
            onEachFeature: function(feature, layer) {
                var popupContent = "<l>ชุมชน: </l>" + feature.properties.name;
                layer.bindPopup(popupContent);
            }
        });

        $.getJSON('community.php', function(data) {
            community.addData(data);
        });

        // Add heatmap layer

        var value = [];

        for (var i = 0; i < 34; i++) {
            var data = {
                lat: lat[i],
                lng: lon[i],
                count: people[i]
            }
            value.push(data);
        }

        console.log(value);

        var testData = {
            max: 10,
            data: value
        };

        var cfg = {
            // radius should be small ONLY if scaleRadius is true (or small radius is intended)
            "radius": 0.0009,
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
            center: [13.751330328, 100.489664708],
            zoom: 10,
            layers: [heatmapLayer]
        });

        heatmapLayer.setData(testData);

        // make accessible for debugging
        layer = heatmapLayer;

        map.addLayer(osm);
        // map.addLayer(bing1);

        var baseMaps = [{
            groupName: "Google Base Maps",
            expanded: false,
            layers: {
                "Satellite": googleSat,
                "Street View": googleStreets
            }
        }, {
            groupName: "OSM Base Maps",
            expanded: false,
            layers: {
                "OpenStreetMaps": osm
            }
        }];

        var overlays = [{
                groupName: "Layers",
                expanded: true,
                layers: {
                    "District": district,
                    "Community": community
                }
            },
            {
                groupName: "Zone 1",
                expanded: false,
                layers: {
                    "กลุ่มกรุงเทพกลาง": zone_01,
                    "March 7, 2022": line
                }
            },
            {
                groupName: "Zone 2",
                expanded: false,
                layers: {
                    "กลุ่มกรุงเทพใต้": zone_02,
                    "March 7, 2022": point
                }
            },
            {
                groupName: "Zone 3",
                expanded: false,
                layers: {
                    "กลุ่มกรุงเทพเหนือ": zone_03,
                }
            },
            {
                groupName: "Zone 4",
                expanded: false,
                layers: {
                    "กลุ่มกรุงเทพตะวันออก": zone_04,
                }
            },
            {
                groupName: "Zone 5",
                expanded: false,
                layers: {
                    "กลุ่มกรุงธนเหนือ": zone_05,
                }
            },
            {
                groupName: "Zone 6",
                expanded: false,
                layers: {
                    "กลุ่มกรุงธนใต้": zone_06,
                }
            }
        ];

        var options = {
            container_width: "200px",
            group_maxHeight: "80px",
            //container_maxHeight : "350px", 
            exclusive: true,
            collapsed: false
        };

        var control = L.Control.styledLayerControl(baseMaps, overlays, options);
        map.addControl(control);
    </script>
</body>

</html>