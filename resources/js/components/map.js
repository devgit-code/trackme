// import { ModelAnimation } from 'cesium';
import 'ol/ol.css';

document.addEventListener('alpine:init', () => {
    Alpine.data('map', function () {
        return {
            map: {},
            async initComponent(locations, small) {
                const Map = (await import('ol/Map.js')).default
                const Feature = (await import('ol/Feature.js')).default;
                const View = (await import('ol/View.js')).default;
                const TileLayer = (await import('ol/layer/Tile.js')).default;
                const VectorSource = (await import('ol/source/Vector.js')).default;
                const ClusterSource = (await import('ol/source/Cluster.js')).default;
                const VectorLayer = (await import('ol/layer/Vector.js')).default;
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

                const projection = 'EPSG:3857';
                locations = JSON.parse(locations);

                // Project locations onto map, probably a better way to do this :(
                locations = locations.map(function ([lat, lon, label]) {
                    let projected = fromLonLat([lon, lat], projection);
                    projected[2] = label;
                    return projected;
                });

                const mapPinFeatures = new VectorSource();
                locations.forEach((loc) => {
                    mapPinFeatures.addFeature(new Feature({
                        geometry: new Point([loc[0], loc[1]]),
                        label: loc[2],
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
                            if (small) {
                                if (size > 1) {
                                    style = new Style({
                                        image: new Circle({
                                            radius: 7,
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
                                            scale: 0.8,
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
                                            offsetY: -32,
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
                let interactions;
                let controls;
                if (small) {
                    interactions = [];
                    controls = [];
                } else {
                    interactions = defaultInteractions({});
                    controls = defaultControls({});
                }
                this.map = new Map({
                    target: this.$refs.map,
                    layers: [
                        new TileLayer({
                            source: new OSM({ transition: 0 }),
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
                // const ol3d = new OLCesium({map: map}); // ol2dMap is the ol.Map instance
                // ol3d.setEnabled(true);
                // console.log("Done init map");
                // console.log(ol3d);
                try {
                    this.map.getView().fit(mapLineVectorLayer.getSource().getExtent(), this.map.getSize());
                    if (small) {
                        this.map.getView().adjustZoom(-0.2);
                    } else {
                        this.map.getView().adjustZoom(-0.5);
                    }
                } catch (e) { }

                document.addEventListener('livewire:navigating', this.disposeMapComponent(this.map));
            },
            async disposeMapComponent() {
                return function destroyMap() {
                    this.map.getAllLayers().forEach((layer) => {
                        layer.getSource()?.dispose();
                        layer.dispose();
                    });
                    this.map.getView().dispose();
                    this.map.dispose();
                    this.map.setTarget(null);
                    this.changeChecker.disconnect();
                    document.removeEventListener('livewire:navigating', destroyMap);
                }
            }
        };
    });
});

// document.addEventListener('livewire:navigated', () => {
//     [...document.getElementsByClassName('ol-map')].forEach(async el => {
//         initMapComponent(el, null);
//     });
// });
