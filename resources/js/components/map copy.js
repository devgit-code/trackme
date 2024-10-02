// import { ModelAnimation } from 'cesium';
import 'ol/ol.css';

document.addEventListener('livewire:navigated', () => {
    [...document.getElementsByClassName('ol-map')].forEach(async el => {
        const Map = (await import('ol/Map.js')).default
        const Feature = (await import('ol/Feature.js')).default;
        const View = (await import('ol/View.js')).default;
        const TileLayer = (await import('ol/layer/Tile.js')).default;
        const VectorSource = (await import('ol/source/Vector.js')).default;
        const ClusterSource = (await import('ol/source/Cluster.js')).default;
        const VectorLayer = (await import('ol/layer/Vector.js')).default;
        const Control = (await import('ol/control/Control.js')).default;
        const OSM = (await import('ol/source/OSM.js')).default;
        const { defaults: defaultControls } = await import('ol/control/defaults');
        const { defaults: defaultInteractions } = await import('ol/interaction/defaults');
        const {
            Style,
            Fill,
            Stroke,
            Circle,
            Text
        } = (await import('ol/style.js'));
        const { LineString, Point } = (await import('ol/geom.js'));
        const fromLonLat = (await import('ol/proj.js')).fromLonLat;
        const Geolocation = (await import('ol/Geolocation.js')).default;
        // import * as Cesium from 'cesium'
        // import OLCesium from 'ol-cesium';

        function initMapComponent(el, map) {
            const projection = 'EPSG:3857';
            let locations = JSON.parse(el.getAttribute("data-locations"));

            // Project locations onto map, probably a better way to do this :(
            locations = locations.map(function ([lon, lat, label]) {
                let projected = fromLonLat([lon, lat], projection);
                projected[2] = label;
                return projected;
            });

            const mapPinFeatures = new VectorSource();
            locations.forEach((loc) => {
                mapPinFeatures.addFeature(new Feature({
                    geometry: new Point([loc[0], loc[1]]),
                    label: loc[2],
                    // author_name: ping.author_name,
                    // loc_name: ping.loc_name,
                    // ping_id: ping.id,
                    // date: (new Date(ping.timestamp * 1000)).toLocaleDateString()
                }));
            });
            const clusterSource = new ClusterSource({
                distance: 20,
                source: mapPinFeatures
            });

            const mapClusterVectorLayer = new VectorLayer({
                source: clusterSource,
                style: function (feature) {
                    const styleCache = {};
                    const size = feature.get('features').length;
                    let style = styleCache[size];
                    if (!style) {
                        if (el.getAttribute("data-small")) {
                            if (size > 1) {
                                style = new Style({
                                    image: new Circle({
                                        radius: 5,
                                        stroke: new Stroke({
                                            color: '#fff',
                                            width: 1,
                                        }),
                                        fill: new Fill({
                                            color: '#3399CC',
                                        }),
                                    }),
                                    text: new Text({
                                        text: size.toString(),
                                        scale: 0.7,
                                        fill: new Fill({
                                            color: '#fff',
                                        }),
                                    }),
                                });
                            } else {
                                style = new Style({
                                    image: new Circle({
                                        radius: 5,
                                        fill: new Fill({
                                            color: '#3399CC',
                                        }),
                                        stroke: new Stroke({
                                            color: '#fff',
                                            width: 1,
                                        }),
                                    })
                                })
                            }
                        } else {
                            if (size > 1) {
                                style = new Style({
                                    image: new Circle({
                                        radius: 15,
                                        stroke: new Stroke({
                                            color: '#fff',
                                            width: 2,
                                        }),
                                        fill: new Fill({
                                            color: '#3399CC',
                                        }),
                                    }),
                                    text: new Text({
                                        text: size.toString(),
                                        scale: 1.2,
                                        fill: new Fill({
                                            color: '#fff',
                                        }),
                                    }),
                                });
                            } else {
                                style = new Style({
                                    text: new Text({
                                        font: '12px sans-serif',
                                        textAlign: 'center',
                                        text: feature.get('features')[0].get('label'),
                                        offsetY: -20,
                                        offsetX: 0,
                                        backgroundFill: new Fill({
                                            color: 'rgba(255, 255, 255, 0.7)',
                                        }),
                                        backgroundStroke: new Stroke({
                                            color: 'rgba(227, 227, 227, 1)',
                                        }),
                                        padding: [5, 2, 2, 5]
                                    }),
                                    image: new Circle({
                                        radius: 8,
                                        fill: new Fill({
                                            color: '#3399CC',
                                        }),
                                        stroke: new Stroke({
                                            color: '#fff',
                                            width: 2,
                                        }),
                                    })
                                })
                            }
                        }
                    }
                    styleCache[size] = style;
                    return style;
                },
            });

            const mapLineVectorLayer = new VectorLayer({
                source: new VectorSource({
                    features: [
                        new Feature({
                            geometry: new LineString(locations)
                        })
                    ]
                }),
                style: new Style({
                    stroke: new Stroke({
                        width: 3,
                        color: '#3399CC'
                    })
                })
            });

            if (map) {
                map.getLayers().forEach((layer) => {
                    if (layer instanceof VectorLayer) {
                        map.removeLayer(layer);
                    }
                });
                map.addLayer(mapLineVectorLayer);
                map.addLayer(mapClusterVectorLayer);
            } else {
                let interactions;
                let controls;
                if (el.getAttribute("data-small")) {
                    interactions = [];
                    controls = [];
                } else {
                    interactions = defaultInteractions({});
                    controls = defaultControls({});
                }
                map = new Map({
                    target: el,
                    layers: [
                        new TileLayer({
                            source: new OSM(),
                            label: 'OpenStreetMap',
                        }),
                        mapLineVectorLayer,
                        mapClusterVectorLayer,
                    ],
                    view: new View({
                        projection: projection,
                        center: [0, 0],
                        maxZoom: 18,
                        zoom: 2,
                    }),
                    controls: controls,
                    interactions: interactions,
                });
            }
            // const ol3d = new OLCesium({map: map}); // ol2dMap is the ol.Map instance
            // ol3d.setEnabled(true);
            // console.log("Done init map");
            // console.log(ol3d);
            try {
                map.getView().fit(mapLineVectorLayer.getSource().getExtent(), map.getSize());
                if (el.getAttribute("data-small")) {
                    map.getView().adjustZoom(-0.1);
                } else {
                    map.getView().adjustZoom(-0.5);
                }
            } catch (e) { }

            if (el.getAttribute("data-geolocation")) {
                const geoAccuracyInput = document.createElement("input");
                geoAccuracyInput.type = "hidden";
                geoAccuracyInput.name = "geo-accuracy";
                const geoLatInput = document.createElement("input");
                geoLatInput.type = "hidden";
                geoLatInput.name = "geo-lat";
                const geoLonInput = document.createElement("input");
                geoLonInput.type = "hidden";
                geoLonInput.name = "geo-lon";
                el.parentNode.insertBefore(geoAccuracyInput, el);
                el.parentNode.insertBefore(geoLatInput, el);
                el.parentNode.insertBefore(geoLonInput, el);
                const accuracyFeature = new Feature({ projection: projection });
                const positionFeature = new Feature({ projection: projection });
                const geoLayer = new VectorLayer({
                    source: new VectorSource({
                        features: [accuracyFeature, positionFeature],
                    }),
                });
                map.addLayer(geoLayer);
                const geolocation = new Geolocation({
                    // enableHighAccuracy must be set to true to have the heading value.
                    trackingOptions: {
                        enableHighAccuracy: true,
                    },
                    projection: projection,
                });

                geolocation.on('change:accuracyGeometry', function () {
                    accuracyFeature.setGeometry(geolocation.getAccuracyGeometry());
                });

                // update the HTML page when the position changes.
                geolocation.on('change', function () {
                    const position = geolocation.getPosition();
                    const accuracy = geolocation.getAccuracy();
                    positionFeature.getStyle().getText().setText(position[0].toFixed(4) + ', ' + position[1].toFixed(4) + "\n" + 'Acc: ' + accuracy.toFixed(1) + "m");
                    positionFeature.setGeometry(position ? new Point(position) : null);
                    map.getView().fit(geoLayer.getSource().getExtent(), map.getSize());
                    map.getView().adjustZoom(-0.5);
                    geoAccuracyInput.value = accuracy;
                    geoLatInput.value = position[0];
                    geoLonInput.value = position[1];
                });

                // handle geolocation error.
                geolocation.on('error', function (error) {
                    const info = document.getElementById('info');
                    info.innerHTML = error.message;
                    info.style.display = '';
                });

                positionFeature.setStyle(
                    new Style({
                        image: new Circle({
                            radius: 6,
                            fill: new Fill({
                                color: '#3399CC',
                            }),
                            stroke: new Stroke({
                                color: '#fff',
                                width: 2,
                            }),
                        }),
                        text: new Text({
                            font: '16px sans-serif',
                            textAlign: 'center',
                            text: 'Waiting for location...',
                            offsetY: -30,
                            offsetX: 0,
                            backgroundFill: new Fill({
                                color: 'rgba(255, 255, 255, 0.7)',
                            }),
                            backgroundStroke: new Stroke({
                                color: 'rgba(227, 227, 227, 1)',
                            }),
                            padding: [5, 2, 2, 5]
                        }),
                    })
                );
                const locate = document.createElement('div');
                locate.className = 'ol-control ol-unselectable locate';
                locate.innerHTML = 'Enable Location Tracking';
                locate.style = 'top: 6em; left: .5em;';
                locate.addEventListener('click', function () {
                    geolocation.setTracking(true);
                });
                map.addControl(
                    new Control({
                        element: locate,
                    })
                );
            }
            return map;
        }
        const map = initMapComponent(el, null);
        const changeChecker = new MutationObserver(() => { initMapComponent(el, map) })
        changeChecker.observe(el, { attributes: true, attributeFilter: ["data-locations"] });
        document.addEventListener('livewire:navigating', function destroyMap(event) {
            map.getAllLayers().forEach((layer) => {
                layer.getSource()?.dispose();
                layer.dispose();
            });
            map.getView().dispose();
            map.dispose();
            map.setTarget(null);
            changeChecker.disconnect();
            document.removeEventListener('livewire:navigating', destroyMap);
        });
    });
});

// document.addEventListener('DOMContentLoaded', () => {
//     console.log("DOMContentLoaded called");
//     [...document.getElementsByClassName('map')].forEach(el => {
//         var map = initMapComponent(el, null);
//         new MutationObserver(() => { initMapComponent(el, map) }).observe(el, { attributes: true, attributeFilter: ["data-locations"] });
//     })
// });
