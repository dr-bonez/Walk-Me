<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
$this->layout = 'maps';?>
<div id="mapContainer" >
		<script type="text/javascript">
		var globEvt, globMarker, lat, lng, dlat, dlng, map, datas;
			
		    function showPosition(map, position, make, currentMarker, destMarker){
		    	if (make) {
					map.setCenter({lat:position.coords.latitude, lng:position.coords.longitude});
					map.setZoom(15);
				}
				
				// set current position as a marker
				if (!make) {
					map.removeObject(currentMarker);
				}
				currentMarker = new H.map.DomMarker({lat:position.coords.latitude, lng:position.coords.longitude});
				globMarker = currentMarker;
				map.addObject(currentMarker);

				$.ajax({method:"post", url:"/WalkMe/maps/latlng",data:{lat:position.coords.latitude,lng:position.coords.longitude}}).success(function(data){
					datas = eval(data)[0];
					if (datas.dlat != null) {
						destMarker = new H.map.DomMarker({lat:datas.dlat,lng:datas.dlng});;
						map.addObject(destMarker);
					}
					window.setTimeout(function () {navigator.geolocation.getCurrentPosition(function (position){showPosition(map,position, false, currentMarker, destMarker);});}, 10000);
				}).error(function() {
					window.setTimeout(function () {navigator.geolocation.getCurrentPosition(function (position){showPosition(map,position, false, currentMarker, destMarker);});}, 10000);
				});
			}
			var platform = new H.service.Platform({
			app_id: 'qB42RwI8Kum9fXo2xpsJ',
			app_code: 'XcdhsTMx5naHN3Zi-e6_iQ',
			useCIT: true,
			useHTTPS: true
			});
			
			var defaultLayers = platform.createDefaultLayers();
			//Step 2: initialize a map  - not specificing a location will give a whole world view.
			map = new H.Map(document.getElementById('mapContainer'),
				defaultLayers.normal.map);

			//make the map interactive
			// MapEvents enables the event system
			// Behavior implements default interactions for pan/zoom (also on mobile touch environments)
			var mapEvents = new H.mapevents.MapEvents(map);
			var behavior = new H.mapevents.Behavior(mapEvents);


			// Create the default UI components
			var ui = H.ui.UI.createDefault(map, defaultLayers);
			navigator.geolocation.getCurrentPosition(function (position){showPosition(map,position, true, null, []);});
			
		</script>
</div>
<div class="modal fade" id="myModal" role="dialog" style="top: 72px;">
  <div class="modal-dialog" role="document">
    <div class="modal-content panel panel-primary">
      <div class="modal-header panel-heading">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="walkerName">Modal title</h4>
      </div>
      <div class="modal-body panel-body" id="walkerBio">
        ...
      </div>
      <div class="modal-footer panel-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="request()">Request</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="requestModal" role="dialog" style="top: 72px;">
  <div class="modal-dialog" role="document">
    <div class="modal-content panel panel-primary">
      <div class="modal-header panel-heading">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="titleModal">Modal title</h4>
      </div>
      <div class="modal-body panel-body" id="formModal">
        
      </div>
      <div class="modal-footer panel-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal" onclick="cancel()">Cancel</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="go()">GO!</button>
      </div>
    </div>
  </div>
</div>