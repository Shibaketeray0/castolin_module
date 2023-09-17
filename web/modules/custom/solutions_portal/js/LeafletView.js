(function (Drupal) {

  'use strict';

  let proxied = Drupal.Leaflet.prototype.initialise;

  const southWest = new L.LatLng(640, 0);
  const northEast = new L.LatLng(0, 1840);

  Drupal.Leaflet.prototype.initialise = function (mapid) {

    let self = this;

    const bounds = L.latLngBounds(southWest, northEast);

    self.map_settings.center = bounds.getCenter();
    self.map_settings.zoom = 0;
    self.map_settings.crs = L.CRS.Simple;
    self.map_settings.zoomSnap = 0;
    self.map_settings.maxZoom = 4;
    self.map_settings.maxBounds = bounds;
    self.map_settings.maxBoundsViscosity = 1.0;

    proxied.apply(this, arguments);

    let overlay = self.map_definition.overlay;

    L.imageOverlay(overlay.imageUrl, bounds).addTo(self.lMap);

    let resizeObserver = new ResizeObserver(() => {
      self.lMap.invalidateSize();
      self.lMap.fitBounds(bounds);
    });
    resizeObserver.observe(document.getElementById(mapid));
  };

})(Drupal);
