function renderMap(clientCoords) {
    // Declare the vertices of a polygon demarcating the parish's boundaries.
    var PARISH_BOUNDARY_VERTICES = [
        new google.maps.LatLng(41.742050, -88.206093),
        new google.maps.LatLng(41.732700, -88.206223),
        new google.maps.LatLng(41.732250, -88.229912),
        new google.maps.LatLng(41.727440, -88.230507),
        new google.maps.LatLng(41.726742, -88.230171),
        new google.maps.LatLng(41.726421, -88.229477),
        new google.maps.LatLng(41.726551, -88.225700),
        new google.maps.LatLng(41.716549, -88.225449),
        new google.maps.LatLng(41.716679, -88.217888),
        new google.maps.LatLng(41.709759, -88.217812),
        new google.maps.LatLng(41.710018, -88.189140),
        new google.maps.LatLng(41.710548, -88.163628),
        new google.maps.LatLng(41.710320, -88.161697),
        new google.maps.LatLng(41.709759, -88.159958),
        new google.maps.LatLng(41.710548, -88.159012),
        new google.maps.LatLng(41.710690, -88.157753),
        new google.maps.LatLng(41.710850, -88.149231),
        new google.maps.LatLng(41.711361, -88.143242),
        new google.maps.LatLng(41.713081, -88.139740),
        new google.maps.LatLng(41.715340, -88.135498),
        new google.maps.LatLng(41.718990, -88.132751),
        new google.maps.LatLng(41.721310, -88.132874),
        new google.maps.LatLng(41.722080, -88.133202),
        new google.maps.LatLng(41.722809, -88.133293),
        new google.maps.LatLng(41.723541, -88.133179),
        new google.maps.LatLng(41.727310, -88.131187),
        new google.maps.LatLng(41.729191, -88.130142),
        new google.maps.LatLng(41.730129, -88.129723),
        new google.maps.LatLng(41.732948, -88.128899),
        new google.maps.LatLng(41.734829, -88.128517),
        new google.maps.LatLng(41.738361, -88.128052),
        new google.maps.LatLng(41.741261, -88.127899),
        new google.maps.LatLng(41.742889, -88.127899),
        new google.maps.LatLng(41.744061, -88.128090),
        new google.maps.LatLng(41.745312, -88.128479),
        new google.maps.LatLng(41.747711, -88.129440),
        new google.maps.LatLng(41.749859, -88.130920),
        new google.maps.LatLng(41.751610, -88.132431),
        new google.maps.LatLng(41.752129, -88.133072),
        new google.maps.LatLng(41.752918, -88.134438),
        new google.maps.LatLng(41.753620, -88.135918),
        new google.maps.LatLng(41.755482, -88.140480),
        new google.maps.LatLng(41.756790, -88.143539),
        new google.maps.LatLng(41.757488, -88.144981),
        new google.maps.LatLng(41.757889, -88.145699),
        new google.maps.LatLng(41.759010, -88.147346),
        new google.maps.LatLng(41.759750, -88.148232),
        new google.maps.LatLng(41.760349, -88.148682),
        new google.maps.LatLng(41.760818, -88.148949),
        new google.maps.LatLng(41.761379, -88.149139),
        new google.maps.LatLng(41.762470, -88.149246),
        new google.maps.LatLng(41.765930, -88.148819),
        new google.maps.LatLng(41.766090, -88.150291),
        new google.maps.LatLng(41.766380, -88.158760),
        new google.maps.LatLng(41.767319, -88.158882),
        new google.maps.LatLng(41.767181, -88.160583),
        new google.maps.LatLng(41.766869, -88.162064),
        new google.maps.LatLng(41.762020, -88.179008),
        new google.maps.LatLng(41.753410, -88.206009)
    ];

    // Display the map.
    var mapElem = $('#map').get(0);

    var map = new google.maps.Map(mapElem, {
        zoom: 11,
        center: clientCoords,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    // Add an overlay for the parish boundary.
    var parishPolygon = new google.maps.Polygon({
        map: map,
        paths: PARISH_BOUNDARY_VERTICES,
        strokeWeight: 2,
        strokeOpacity: 0.9,
        fillOpacity: 0.3,
    });

    // Check if the potential client lives within the parish boundaryj
    if (google.maps.geometry.poly.containsLocation(clientCoords, parishPolygon)) {
        parishPolygon.setOptions({
            strokeColor: '#468847',
            fillColor: '#468847'
        });

        $('#newClient').addClass('btn-success');
        $('#alerts').append(
            '<p class="alert alert-success">This address lies within the parish boundaries.</p>'
        );
    } else {
        parishPolygon.setOptions({
            strokeColor: '#b94a48',
            fillColor: '#b94a48'
        });

        $('#newClient').addClass('btn-danger');
        $('#alerts').append(
            '<p class="alert alert-error">This address lies outside the parish boundaries. Only allow under special circumstances.</p>'
        );
    }

    // When the map finises loading, add an address marker at the potential client's location.
    google.maps.event.addListenerOnce(map, 'idle', function () {
        new google.maps.Marker({
            map: map,
            position: clientCoords,
            animation: google.maps.Animation.DROP,
            title: "Client's address"
        });
    });
}

function initEditClientForm() {
    // Attach event handlers.
    var marriedCheckbox = $('#married');
    var doNotHelpCheckbox = $('#doNotHelp');

    var memberSpouseDivs = $('.member-spouse');
    var memberDoNotHelpDiv = $('.member-donothelp');

    function update() {
        if (marriedCheckbox.is(':checked')) {
            memberSpouseDivs.removeClass('hide');
        } else {
            memberSpouseDivs.addClass('hide');
        }

        if (doNotHelpCheckbox.is(':checked')) {
            memberDoNotHelpDiv.removeClass('invisible');
        } else {
            memberDoNotHelpDiv.addClass('invisible');
        }
    }

    marriedCheckbox.click(update);
    doNotHelpCheckbox.click(update);

    update();

    // Attach jQuery UI widgets/plugin behavior.
    $('.date').datepicker();
    $('.phone').mask('(999) 999-9999');
}
