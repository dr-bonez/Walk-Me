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
			var globEvt, globMarker, lat, lng, datas, dlat, dlong, map, datas, pos, polyline;
			var icon = new H.map.Icon('/WalkMe/img/user.png');
			var dicon = new H.map.Icon('/WalkMe/img/marker.png');
			function calculateRouteFromAtoB (platform) {
  				var router = platform.getRoutingService(),
    			routeRequestParams = {
      				mode: 'shortest;pedestrian',
      				representation: 'display',
      				waypoint0: pos.coords.latitude+','+ pos.coords.longitude , 
      				waypoint1: datas.dlat+','+datas.dlng , // Destination
      				routeattributes: 'waypoints,summary,shape,legs',
     			 	maneuverattributes: 'direction,action'
    			};
  				router.calculateRoute(
    				routeRequestParams,
    				onSuccess,
    				onError
  				);
			}

			function onError(error) {
  				console.log(error);
			}

			function onSuccess(result) {
  				var route = result.response.route[0];
  				addRouteShapeToMap(route);
  				addManueversToMap(route);
  			}

		    function showPosition(map, position, make, currentMarker, destMarker, makeRoute){
		    	pos = position;
		    	if (make) {
					map.setCenter({lat:position.coords.latitude, lng:position.coords.longitude});
					map.setZoom(15);
				}
				
				// set current position as a marker
				if (!make) {
					map.removeObject(currentMarker);
				}
				currentMarker = new H.map.Marker({lat:position.coords.latitude, lng:position.coords.longitude},{icon:icon});
				globMarker = currentMarker;
				map.addObject(currentMarker);

				$.ajax({method:"post", url:"/WalkMe/maps/latlng",data:{lat:position.coords.latitude,lng:position.coords.longitude}}).success(function(data){
					datas = eval(data)[0];
					console.log(destMarker);
					if (datas.dlat != null) {
						if (!make && destMarker != null && destMarker != undefined && destMarker != []) {
    						map.removeObject(destMarker);
    					}
						destMarker = new H.map.Marker({lat:datas.dlat,lng:datas.dlng},{icon : dicon});;
						map.addObject(destMarker);
						if (datas.dlat != dlat || datas.dlng != dlng) {
							if (!makeRoute && polyline != undefined && polyline != null) {
								map.removeObject(polyline);
							}
							calculateRouteFromAtoB (platform);
							dlat = datas.dlat;
							dlng = datas.dlng;
						}
					}
					window.setTimeout(function () {navigator.geolocation.getCurrentPosition(function (position){showPosition(map,position, false, currentMarker, destMarker, false);});}, 10000);
				}).error(function() {
					window.setTimeout(function () {navigator.geolocation.getCurrentPosition(function (position){showPosition(map,position, false, currentMarker, destMarker, false);});}, 10000);
				});
			}
			var platform = new H.service.Platform({
			app_id: 'qB42RwI8Kum9fXo2xpsJ',
			app_code: 'XcdhsTMx5naHN3Zi-e6_iQ',
			useCIT: true,
			useHTTPS: true
			});
			
			function addRouteShapeToMap(route){
  				var strip = new H.geo.Strip(),
    			routeShape = route.shape;

  				routeShape.forEach(function(point) {
    				var parts = point.split(',');
    				strip.pushLatLngAlt(parts[0], parts[1]);
  				});

  				polyline = new H.map.Polyline(strip, {
    				style: {
      					lineWidth: 4,
      					strokeColor: 'rgba(0, 128, 255, 0.7)'
    				}
  				});
 				// Add the polyline to the map
  				map.addObject(polyline);
  				// And zoom to its bounding rectangle
 				map.setViewBounds(polyline.getBounds(), true);
			}

			function addManueversToMap(route){
  			/*	var svgMarkup = '<svg width="18" height="18" ' +
    			'xmlns="http://www.w3.org/2000/svg">' +
    			'<circle cx="8" cy="8" r="8" ' +
      			'fill="#1b468d" stroke="white" stroke-width="1"  />' +
    			'</svg>',
    			dotIcon = new H.map.Icon(svgMarkup, {anchor: {x:8, y:8}}),
    			group = new  H.map.Group(),
   				i,
    			j;

  				// Add a marker for each maneuver
  				for (i = 0;  i < route.leg.length; i += 1) {
    				for (j = 0;  j < route.leg[i].maneuver.length; j += 1) {
      					// Get the next maneuver.
     					maneuver = route.leg[i].maneuver[j];
      					// Add a marker to the maneuvers group
      					var marker =  new H.map.Marker({
        					lat: maneuver.position.latitude,
        					lng: maneuver.position.longitude} ,
        					{icon: dotIcon});
      					marker.instruction = maneuver.instruction;
      					group.addObject(marker);
    				}
  				}
  				// Add the maneuvers group to the map
  				map.addObject(group);*/
			}

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
			navigator.geolocation.getCurrentPosition(function (position){showPosition(map,position, true, null, [], true);});
			Number.prototype.toMMSS = function () {
  				return  Math.floor(this / 60)  +' minutes '+ (this % 60)  + ' seconds.';
			}
			
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