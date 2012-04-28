function drawMap(mapElem, coords) {
    var map = new google.maps.Map(mapElem.get(0), {
        zoom: 13,
        center: coords,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    new google.maps.Marker({
        map: map,
        position: coords,
        title: "Client's address"
    });
}
