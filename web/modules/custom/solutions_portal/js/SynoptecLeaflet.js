/**
 * @file
 * Behaviour to process changing of Synoptec process.
 */

(function (Drupal, drupalSettings) {

  'use strict';

  const mapIDNodeList = document.querySelectorAll('[id^="leaflet-map-widget-taxonomy-term-synoptec"]');

  const mapContainer = mapIDNodeList[0];

  const geoJSONId = 'edit-field-position-on-image-0-value';

  const southWest = [640, 0];
  const northEast = [0, 1840];

  const getMap = () => {

    mapContainer.innerHTML = '<div id=\'map\' style=\'width: 100%; height: 100%;\'></div>';

    let map = 'map';

    const bounds = new L.LatLngBounds(southWest, northEast);

    return L.map(map, {
      center: bounds.getCenter(),
      zoom: 0,
      crs: L.CRS.Simple,
      maxZoom: 4,
      maxBounds: bounds,
      maxBoundsViscosity: 1.0,
      drawControl: true,
      doubleClickZoom: false,
      zoomSnap: false
    });
  };

  const initializeDefaultMap = (map, imagePath) => {
    let imageUrl = '/sites/default/files' + imagePath;

    const bounds = new L.LatLngBounds(southWest, northEast);

    let imageOverlay = L.imageOverlay(imageUrl, bounds).addTo(map);

    map.pm.addControls({
      position: 'topright',
      drawCircle: false,
      drawPolygon: false,
      drawPolyline: false,
      drawRectangle: false,
      drawText: false,
      drawCircleMarker: false,
    });

    let geoField = document.getElementById(geoJSONId);

    map.on('pm:create', function (e) {
      let feature = e.layer.toGeoJSON();
      geoField.value = '';
      geoField.value = '{"type":"FeatureCollection","features":[{"type":"Feature","properties":{},"geometry":{"type":"Point","coordinates":[' + feature.geometry.coordinates + ']}}]}';
    });
  };

  Drupal.behaviors.solutionsPortalSynoptecLeaflet = {
    attach(context, settings) {
      jQuery.fn.changeLeafletCallback = function (argument) {
        initializeDefaultMap(getMap(), argument);
      };
    },
  };
  Drupal.behaviors.solutionsPortalSynoptecEdit = {
    attach(context, settings) {
      if (typeof context['location'] !== 'undefined') {
        let image_path = drupalSettings.solutions_portal.image_path;

        if (image_path !== '') {
          let map = getMap();
          initializeDefaultMap(map, image_path);
          let geoField = document.getElementById(geoJSONId);
          let geoFieldObject = JSON.parse(geoField.value);
          let marker = new L.Marker([geoFieldObject.coordinates[1], geoFieldObject.coordinates[0]]);
          marker.addTo(map);
        }
      }
    },
  };

}(Drupal, drupalSettings));
