/**
 * leaflet-embed.js
 *
 * Initialize a Leaflet map plot-marking.
 *
 * @package Leaflet
 * @subpackage Leaflet.webroot.js
 */

/**
 * Leaflet map object.
 *
 * @var object
 */
var map;

/**
 * Ajax instance.
 *
 * @var object.
 */
var ajaxRequest;

/**
 * Plots' layers.
 *
 * @type array
 */
var plotlayers=[];

/**
 * Initialize map.
 *
 * @return void
 */
function leafletMap() {
	// set up AJAX request
	ajaxRequest = leafletGetXmlHttpObject();
	if (null == ajaxRequest) {
		alert ("This browser does not support HTTP Request");
		return;
	}

	leafletSetDefaults();

	map = new L.map(mapSettings.id, {
		center: new L.LatLng(mapSettings.lat.start, mapSettings.lng.start),
		fullscreenControl: mapSettings.fullscreenControl,
		layers: new L.tileLayer(mapSettings.provider, {
			attribution: mapSettings.attribution,
			maxZoom: mapSettings.zoom.max,
			minZoom: mapSettings.zoom.min
		}),
		maxBounds: new L.LatLngBounds(
			new L.LatLng(mapSettings.lat.min, mapSettings.lng.max),
			new L.LatLng(mapSettings.lat.max, mapSettings.lng.min)
		),
		zoom: mapSettings.zoom.start,
		zoomsliderControl: mapSettings.zoom.slider
	});

	if (!mapSettings.plotting) {
		return;
	}

	if (mapSettings.clustering) {
		clusterGroup = new L.MarkerClusterGroup();
	}

	leafletQueryPlots();
	map.on('moveend', leafletQueryPlots);
}

/**
 * Set default mapSettings.
 *
 * @return void
 */
function leafletSetDefaults() {
	var defaults = {
		attribution: 'Map data © OpenStreetMap contributors',
		autoload: false,
		fullscreenControl: false,
		id: 'map',
		lat: {
			max: 83.3391531415795,
			min: 41.705728515237524,
			start: 53
		},
		lng: {
			max: -52.119140625,
			min: -141.240234375,
			start: -92
		},
		provider: 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
		zoom: {
			max: 18,
			min: 1,
			slider: false,
			start: 1
		}
	};

	if (typeof mapSettings != 'object') {
		mapSettings = defaults;
		return;
	}

	for (var key in defaults) {
		if (typeof mapSettings[key] == 'undefined' || null == mapSettings[key]) {
			mapSettings[key] = defaults[key];
			continue;
		}

		if (typeof defaults[key] == 'object') {
			for (var kkey in defaults[key]) {
				if (typeof mapSettings[key][kkey] == 'undefined' || null == mapSettings[key][kkey]) {
					mapSettings[key][kkey] = defaults[key][kkey];
				}
			}
		}
	}
}

/**
 * Ajax instance.
 *
 * @return mixed
 */
function leafletGetXmlHttpObject() {
	// IE7+, Firefox, Chrome, Opera, Safari
	if (window.XMLHttpRequest) { return new XMLHttpRequest(); }
	// IE6, IE5
	if (window.ActiveXObject)  { return new ActiveXObject("Microsoft.XMLHTTP"); }
	return null;
}

/**
 * Query API for plots for the current bounds.
 *
 * @return void
 */
function leafletQueryPlots() {
	// request the marker info with AJAX for the current bounds
	var bounds = map.getBounds();
	var minll = bounds.getSouthWest();
	var maxll = bounds.getNorthEast();
	var uri = '/leaflet/plots/find/' + minll.lng + '/' + minll.lat + '/' + maxll.lng + '/' + maxll.lat;
	ajaxRequest.onreadystatechange = leafletStateChanged;
	ajaxRequest.open('GET', uri, true);
	ajaxRequest.send(null);
}

/**
 * Extract and mark plots.
 *
 * @return void
 */
function leafletStateChanged() {
	if (4 != ajaxRequest.readyState || 200 != ajaxRequest.status) return;

	// if AJAX returned a list of markers, add them to the map
	var plots = eval("(" + ajaxRequest.responseText + ")");
	leafletRemoveMarkers();
	for (i = 0; i < plots.length; i++) {
		var plot = plots[i];
		var marker = new L.Marker(new L.LatLng(plot.lat,plot.lon, true));
		if (typeof plotIcon != 'undefined') marker.setIcon(plotIcon);
		marker.data = plot;
		marker.bindPopup("<strong>" + plot.name + "</strong><br />" + plot.details);
		if (mapSettings.clustering) {
			clusterGroup.addLayer(marker);
			continue;
		}
		map.addLayer(marker);
		plotlayers.push(marker);
	}
	if (mapSettings.clustering) {
		map.addLayer(clusterGroup);
	}
}

/**
 * Remove markers.
 *
 * @return void
 */
function leafletRemoveMarkers() {
	if (mapSettings.clustering) {
		clusterGroup.clearLayers();
		clusterGroup = new L.MarkerClusterGroup();
		return;
	}

	for (i = 0; i < plotlayers.length; i++) {
		map.removeLayer(plotlayers[i]);
	}
	plotlayers=[];
}

window.onload = function () {
	if (mapSettings.autoload) leafletMap();
}
