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

        $.getJSON('http://infraplus-ru.org:4005/point', function(data) {
            point.addData(data);
        });

        point.on('add', () => {
            map.fitBounds(point.getBounds())
        })

        var lat = [];
        var lng = [];
        var people = [];

        var point_geojson = JSON.parse(Get('point.php'));
        for (var i = 0; i < point_geojson.features.length; i++) {
            lat.push(point_geojson.features[i].properties.Latitude)
            lng.push(point_geojson.features[i].properties.Longitude)
            people.push(point_geojson.features[i].properties.people)
        }

        var line = L.geoJson(null);

        $.getJSON('http://infraplus-ru.org:4005/line', function(data) {
            line.addData(data);
        });

        line.on('add', () => {
            map.fitBounds(line.getBounds())
        })

        // Add 6 Zones

        var zone_01 = L.geoJson(null, {
            style: function(feature) {
                return {
                    weight: 2,
                    opacity: 1,
                    color: '#BFC9CA',
                    fillColor: '#800026',
                    fillOpacity: 0.5,
                };
            },
            onEachFeature: function(feature, layer) {
                var popupContent = "<l>????????????????????????: </l>" + feature.properties.z_name +
                    "<br><l>??????????????????????????????: </l>" + feature.properties.no_male +
                    "<br><l>?????????????????????????????????: </l>" + feature.properties.no_female +
                    "<br><l>???????????????????????????: </l>" + feature.properties.no_house +
                    "<br><l>???????????????: </l>" + feature.properties.no_commu +
                    "<br><l>?????????????????????: </l>" + feature.properties.z_area;
                layer.bindPopup(popupContent);
            }
        });

        var zone_02 = L.geoJson(null, {
            style: function(feature) {
                return {
                    weight: 2,
                    opacity: 1,
                    color: '#BFC9CA',
                    fillColor: '#DFFF00',
                    fillOpacity: 0.5,
                };
            },
            onEachFeature: function(feature, layer) {
                var popupContent = "<l>????????????????????????: </l>" + feature.properties.z_name +
                    "<br><l>??????????????????????????????: </l>" + feature.properties.no_male +
                    "<br><l>?????????????????????????????????: </l>" + feature.properties.no_female +
                    "<br><l>???????????????????????????: </l>" + feature.properties.no_house +
                    "<br><l>???????????????: </l>" + feature.properties.no_commu +
                    "<br><l>?????????????????????: </l>" + feature.properties.z_area;
                layer.bindPopup(popupContent);
            }
        });

        var zone_03 = L.geoJson(null, {
            style: function(feature) {
                return {
                    weight: 2,
                    opacity: 1,
                    color: '#BFC9CA',
                    fillColor: '#FFBF00',
                    fillOpacity: 0.5,
                };
            },
            onEachFeature: function(feature, layer) {
                var popupContent = "<l>????????????????????????: </l>" + feature.properties.z_name +
                    "<br><l>??????????????????????????????: </l>" + feature.properties.no_male +
                    "<br><l>?????????????????????????????????: </l>" + feature.properties.no_female +
                    "<br><l>???????????????????????????: </l>" + feature.properties.no_house +
                    "<br><l>???????????????: </l>" + feature.properties.no_commu +
                    "<br><l>?????????????????????: </l>" + feature.properties.z_area;
                layer.bindPopup(popupContent);
            }
        });

        var zone_04 = L.geoJson(null, {
            style: function(feature) {
                return {
                    weight: 2,
                    opacity: 1,
                    color: '#BFC9CA',
                    fillColor: '#FF7F50',
                    fillOpacity: 0.5,
                };
            },
            onEachFeature: function(feature, layer) {
                var popupContent = "<l>????????????????????????: </l>" + feature.properties.z_name +
                    "<br><l>??????????????????????????????: </l>" + feature.properties.no_male +
                    "<br><l>?????????????????????????????????: </l>" + feature.properties.no_female +
                    "<br><l>???????????????????????????: </l>" + feature.properties.no_house +
                    "<br><l>???????????????: </l>" + feature.properties.no_commu +
                    "<br><l>?????????????????????: </l>" + feature.properties.z_area;
                layer.bindPopup(popupContent);
            }
        });

        var zone_05 = L.geoJson(null, {
            style: function(feature) {
                return {
                    weight: 2,
                    opacity: 1,
                    color: '#BFC9CA',
                    fillColor: '#DE3163',
                    fillOpacity: 0.5,
                };
            },
            onEachFeature: function(feature, layer) {
                var popupContent = "<l>????????????????????????: </l>" + feature.properties.z_name +
                    "<br><l>??????????????????????????????: </l>" + feature.properties.no_male +
                    "<br><l>?????????????????????????????????: </l>" + feature.properties.no_female +
                    "<br><l>???????????????????????????: </l>" + feature.properties.no_house +
                    "<br><l>???????????????: </l>" + feature.properties.no_commu +
                    "<br><l>?????????????????????: </l>" + feature.properties.z_area;
                layer.bindPopup(popupContent);
            }
        });

        var zone_06 = L.geoJson(null, {
            style: function(feature) {
                return {
                    weight: 2,
                    opacity: 1,
                    color: '#BFC9CA',
                    fillColor: '#9FE2BF',
                    fillOpacity: 0.5,
                };
            },
            onEachFeature: function(feature, layer) {
                var popupContent = "<l>????????????????????????: </l>" + feature.properties.z_name +
                    "<br><l>??????????????????????????????: </l>" + feature.properties.no_male +
                    "<br><l>?????????????????????????????????: </l>" + feature.properties.no_female +
                    "<br><l>???????????????????????????: </l>" + feature.properties.no_house +
                    "<br><l>???????????????: </l>" + feature.properties.no_commu +
                    "<br><l>?????????????????????: </l>" + feature.properties.z_area;
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

        zone_01.on('add', () => {
            map.fitBounds(zone_01.getBounds())
        })

        zone_02.on('add', () => {
            map.fitBounds(zone_02.getBounds())
        })

        zone_03.on('add', () => {
            map.fitBounds(zone_03.getBounds())
        })

        zone_04.on('add', () => {
            map.fitBounds(zone_04.getBounds())
        })

        zone_05.on('add', () => {
            map.fitBounds(zone_05.getBounds())
        })

        zone_06.on('add', () => {
            map.fitBounds(zone_06.getBounds())
        })

        // Add district layer

        var district = L.geoJson(null, {
            style: function(feature) {
                return {
                    weight: 2,
                    opacity: 1,
                    color: '#F1C40F',
                    fillColor: '#FCF3CF',
                    fillOpacity: 0.5,
                };
            },
            onEachFeature: function(feature, layer) {
                var popupContent = "<l>?????????: </l>" + feature.properties.dname +
                    "<br><l>?????????????????????: </l>" + feature.properties.area +
                    "<br><l>??????????????????????????????: </l>" + feature.properties.no_male +
                    "<br><l>?????????????????????????????????: </l>" + feature.properties.no_female +
                    "<br><l>??????????????????????????????: </l>" + feature.properties.no_health +
                    "<br><l>?????????: </l>" + feature.properties.no_temple +
                    "<br><l>???????????????: </l>" + feature.properties.no_commu +
                    // "<br><l>???????????????????????????: </l>" + feature.properties.no_hos +
                    "<br><l>????????????????????????: </l>" + feature.properties.no_sch;
                layer.bindPopup(popupContent);
            }
        });

        $.getJSON('http://infraplus-ru.org:4005/district', function(data) {
            district.addData(data).addTo(map);
        });

        district.on('add', () => {
            map.fitBounds(district.getBounds())
        })

        // Add community layer

        var geojsonMarkerOptions = {
            radius: 8,
            fillColor: '#239B56',
            color: '#28B463',
            weight: 1,
            opacity: 1,
            fillOpacity: 0.8
        };

        var community = L.geoJson(null, {
            pointToLayer: function(feature, latlng) {
                // label = String(feature.properties.comm_id)
                return L.circleMarker(latlng, geojsonMarkerOptions)
                // .bindTooltip(label, {
                //     permanent: true,
                //     direction: "top",
                //     className: "my-labels"
                // }).openTooltip();
            },
            onEachFeature: function(feature, layer) {
                var popupContent = "<l>ID: </l>" + feature.properties.comm_id +
                "<br><l>???????????????: </l>" + feature.properties.name;
                layer.bindPopup(popupContent);
            }
        });

        $.getJSON('community.php', function(data) {
            community.addData(data);
        });

        community.on('add', () => {
            map.fitBounds(community.getBounds())
        })

        // Add heatmap layer

        var value = [];

        for (var i = 0; i < 34; i++) {
            var data = {
                lat: lat[i],
                lng: lng[i],
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
                    "????????????????????????????????????????????????": zone_01
                }
            },
            {
                groupName: "Zone 2",
                expanded: false,
                layers: {
                    "?????????????????????????????????????????????": zone_02
                }
            },
            {
                groupName: "Zone 3",
                expanded: false,
                layers: {
                    "???????????????????????????????????????????????????": zone_03
                }
            },
            {
                groupName: "Zone 4",
                expanded: false,
                layers: {
                    "????????????????????????????????????????????????????????????": zone_04
                }
            },
            {
                groupName: "Zone 5",
                expanded: false,
                layers: {
                    "????????????????????????????????????????????????": zone_05
                }
            },
            {
                groupName: "Zone 6",
                expanded: false,
                layers: {
                    "??????????????????????????????????????????": zone_06,
                    "March 7, 2022: line": line,
                    "March 7, 2022: point": point
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