// get html map id
var map = L.map('map')
// Open default map from mapbox
L.tileLayer(
    "https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}",
    {
        maxZoom: 20,
        minZoom: 5,
        zoom: 16,
        id: "mapbox/streets-v11",
        accessToken:
          "pk.eyJ1IjoianVyamVudiIsImEiOiJjazZyb2s0c2UwNXlmM2dwOWpoam1veWtvIn0.Wz1L39sbP_yOIek4zP7W9Q"
    }
).addTo(map);

// Add dutch buidlings as layer from Geoserver
var Buidlings= L.tileLayer.wms("http://localhost:8080/geoserver/Netherlands/wms", {
    layers: 'Netherlands:NL buildings',
    transparent: true,
    format: 'image/png',
});
map.addLayer(Buidlings);
// Add dutch railways as layer from Geoserver
var Railways= L.tileLayer.wms("http://localhost:8080/geoserver/Netherlands/wms", {
    layers: 'Netherlands:railways',
    transparent: true,
    format: 'image/png',
});
map.addLayer(Railways);

var Points= L.tileLayer.wms("http://localhost:8080/geoserver/Netherlands/wms", {
    layers: 'Netherlands:points',
    transparent: true,
    format: 'image/png',
});
map.addLayer(Points);

// Set our initial location and zoomlevel
map.setView([52.132633, 5.291266], 6);

var shipIcon = L.Icon.extend({
    options: {
        iconSize:     [18, 18], // size of the icon  
    }
});
var ShipIcon = new shipIcon({iconUrl: 'img/ship.png'}),
    CarIcon = new shipIcon({iconUrl: 'img/car.png'})

L.icon = function (options) {
    return new L.Icon(options);
};


L.marker([53.181844, 5.298157], {icon: ShipIcon}).addTo(map);
L.marker([53.281844, 5.268157], {icon: ShipIcon}).addTo(map);
L.marker([53.311844, 5.278157], {icon: ShipIcon}).addTo(map);
L.marker([53.281844, 5.398157], {icon: ShipIcon}).addTo(map);
L.marker([52.981844, 5.198157], {icon: ShipIcon}).addTo(map);
L.marker([53.581844, 5.498157], {icon: ShipIcon}).addTo(map);

L.marker([53.025688, 5.531616], {icon: CarIcon}).addTo(map);
L.marker([52.281844, 5.268157], {icon: CarIcon}).addTo(map);
L.marker([53.101844, 5.628157], {icon: CarIcon}).addTo(map);
L.marker([52.981844, 5.508157], {icon: CarIcon}).addTo(map);
L.marker([52.531844, 5.708157], {icon: CarIcon}).addTo(map);
L.marker([52.401844, 5.808157], {icon: CarIcon}).addTo(map);