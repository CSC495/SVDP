src="http://maps.google.com/maps/api/js?sensor=false";
	
var geocoder;
var map;

function drawmap(){
	geocoder = new google.maps.Geocoder();
	var latlng = new google.maps.LatLng(41.7750796, -88.1436542);
	var options = {
			zoom: 12,
			center: latlng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
	};
}

function drawmap2(){
	drawmap(41.7750796, -88.1436542);
	geocoder.geocode({'address': address}, function(results, status){
		if(status== gooogle.maps.GeocoderStatus.OK){
			var marker = new google.maps.Marker({
				map: map,
				position: results[0].geometry.location
			});
		}
		else{
			return FALSE;
		}
	});
}