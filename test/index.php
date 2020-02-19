<!DOCTYPE html>
<html>
    <head>
        <!-- Title document -->
        <title> leaflet webviewer with Geoserver</title>
        <!--Load the style stylesheet of leaflet -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.1/dist/leaflet.css" integrity="sha512-Rksm5RenBEKSKFjgI3a41vrjkw4EVPlJ3+OiI65vTjIdo9brlAacEuKOiQ5OFh7cOI1bkDwLqdLw3Zg0cRJAAQ==" crossorigin=""/>
        <!--Load leaflet -->
        <script src="https://unpkg.com/leaflet@1.3.1/dist/leaflet.js" integrity="sha512-/Nsx9X4HebavoBvEBuyp3I7od5tA0UzAxs+j83KgC8PU0kgB4XiK4Lfe4y4cgBtaRJQEIFCW+oC506aPT2L1zw==" crossorigin=""></script>
        <!--Load vectorGrid plugin for Leaflet -->
        <script src="https://unpkg.com/leaflet.vectorgrid@latest/dist/Leaflet.VectorGrid.bundled.js"></script>

        <!-- mapbox cdn -->
        <script src='https://api.mapbox.com/mapbox-gl-js/v1.7.0/mapbox-gl.js'></script>
        <link href='https://api.mapbox.com/mapbox-gl-js/v1.7.0/mapbox-gl.css' rel='stylesheet' />

        <!-- boat icon -->
        <script src="https://unpkg.com/leaflet.boatmarker/leaflet.boatmarker.min.js"></script>

        <!-- meta tags -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    </head>
<div id='map'></div>
<style type="text/css">
html, body {
    margin: 0;
    height: 100%;
}
#map{
    height: 100%;
    width: 100%;
}
</style>
<script src="script.js"></script>
</html>