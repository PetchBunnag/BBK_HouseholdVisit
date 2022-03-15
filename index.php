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

        .leaflet-tooltip.my-labels {
            background-color: transparent;
            border: transparent;
            box-shadow: none;
            font-weight: bold;
            font-size: 16px;
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

        var testData = {
            max: 10,
            data: [{
                lat: lat[0],
                lng: lon[0],
                count: people[0]
            }, {
                lat: lat[1],
                lng: lon[1],
                count: people[1]
            }, {
                lat: lat[2],
                lng: lon[2],
                count: people[2]
            }, {
                lat: lat[3],
                lng: lon[3],
                count: people[3]
            }, {
                lat: lat[4],
                lng: lon[4],
                count: people[4]
            }, {
                lat: lat[5],
                lng: lon[5],
                count: people[5]
            }, {
                lat: lat[6],
                lng: lon[6],
                count: people[6]
            }, {
                lat: lat[7],
                lng: lon[7],
                count: people[7]
            }, {
                lat: lat[8],
                lng: lon[8],
                count: people[8]
            }, {
                lat: lat[9],
                lng: lon[9],
                count: people[9]
            }, {
                lat: lat[10],
                lng: lon[10],
                count: people[10]
            }, {
                lat: lat[11],
                lng: lon[11],
                count: people[11]
            }, {
                lat: lat[12],
                lng: lon[12],
                count: people[12]
            }, {
                lat: lat[13],
                lng: lon[13],
                count: people[13]
            }, {
                lat: lat[14],
                lng: lon[14],
                count: people[14]
            }, {
                lat: lat[15],
                lng: lon[15],
                count: people[15]
            }, {
                lat: lat[16],
                lng: lon[16],
                count: people[16]
            }, {
                lat: lat[17],
                lng: lon[17],
                count: people[17]
            }, {
                lat: lat[18],
                lng: lon[18],
                count: people[18]
            }, {
                lat: lat[19],
                lng: lon[19],
                count: people[19]
            }, {
                lat: lat[20],
                lng: lon[20],
                count: people[20]
            }, {
                lat: lat[21],
                lng: lon[21],
                count: people[21]
            }, {
                lat: lat[22],
                lng: lon[22],
                count: people[22]
            }, {
                lat: lat[23],
                lng: lon[23],
                count: people[23]
            }, {
                lat: lat[24],
                lng: lon[24],
                count: people[24]
            }, {
                lat: lat[25],
                lng: lon[25],
                count: people[25]
            }, {
                lat: lat[26],
                lng: lon[26],
                count: people[26]
            }, {
                lat: lat[27],
                lng: lon[27],
                count: people[27]
            }, {
                lat: lat[28],
                lng: lon[28],
                count: people[28]
            }, {
                lat: lat[29],
                lng: lon[29],
                count: people[29]
            }, {
                lat: lat[30],
                lng: lon[30],
                count: people[30]
            }, {
                lat: lat[31],
                lng: lon[31],
                count: people[31]
            }, {
                lat: lat[32],
                lng: lon[32],
                count: people[32]
            }, {
                lat: lat[33],
                lng: lon[33],
                count: people[33]
            }]
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
            center: [13.6725183891, 100.427231182],
            zoom: 15,
            layers: [heatmapLayer]
        });

        heatmapLayer.setData(testData);

        // make accessible for debugging
        layer = heatmapLayer;

        map.addLayer(googleStreets);
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