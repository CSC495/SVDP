function renderMap(clientCoords) {
    // Declare the vertices of a polygon demarcating the parish's boundaries.
    var RAPHAEL_BOUNDARY_VERTICES = [
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
    
    var HOLYSPIRIT_BOUNDARY_VERTICES = [
        new google.maps.LatLng(41.710911, -88.149139),
        new google.maps.LatLng(41.709702, -88.147690),
        new google.maps.LatLng(41.703800, -88.147682),
        new google.maps.LatLng(41.699139, -88.158409),
        new google.maps.LatLng(41.690231, -88.166481),
        new google.maps.LatLng(41.679779, -88.165970),
        new google.maps.LatLng(41.666889, -88.165451),
        new google.maps.LatLng(41.665981, -88.223732),
        new google.maps.LatLng(41.709629, -88.225357),
        new google.maps.LatLng(41.710529, -88.163391),
        new google.maps.LatLng(41.709888, -88.159958),
        new google.maps.LatLng(41.710590, -88.158928),
        new google.maps.LatLng(41.710911, -88.149139)
    ];
    
	var STELIZABETH_BOUNDARY_VERTICES = [
	    new google.maps.LatLng(41.748390, -88.129921),
	    new google.maps.LatLng(41.749599, -88.082542),
	    new google.maps.LatLng(41.749279, -88.077652),
	    new google.maps.LatLng(41.749920, -88.070778),
	    new google.maps.LatLng(41.750000, -88.064003),
	    new google.maps.LatLng(41.748322, -88.063232),
	    new google.maps.LatLng(41.746719, -88.062798),
	    new google.maps.LatLng(41.738079, -88.062881),
	    new google.maps.LatLng(41.729889, -88.062881),
	    new google.maps.LatLng(41.725712, -88.064262),
	    new google.maps.LatLng(41.720459, -88.066833),
	    new google.maps.LatLng(41.719818, -88.092789),
	    new google.maps.LatLng(41.719769, -88.096100),
	    new google.maps.LatLng(41.719639, -88.097214),
	    new google.maps.LatLng(41.719200, -88.098900),
	    new google.maps.LatLng(41.717670, -88.103508),
	    new google.maps.LatLng(41.717201, -88.105118),
	    new google.maps.LatLng(41.716789, -88.106720),
	    new google.maps.LatLng(41.716599, -88.107536),
	    new google.maps.LatLng(41.716469, -88.108368),
	    new google.maps.LatLng(41.712582, -88.108292),
	    new google.maps.LatLng(41.713482, -88.111977),
	    new google.maps.LatLng(41.707520, -88.116867),
	    new google.maps.LatLng(41.707581, -88.126999),
	    new google.maps.LatLng(41.708805, -88.127472),
	    new google.maps.LatLng(41.708740, -88.132828),
	    new google.maps.LatLng(41.714569, -88.134644),
	    new google.maps.LatLng(41.715530, -88.135406),
	    new google.maps.LatLng(41.718990, -88.132751),
	    new google.maps.LatLng(41.721329, -88.132874),
	    new google.maps.LatLng(41.721931, -88.133148),
	    new google.maps.LatLng(41.722530, -88.133293),
	    new google.maps.LatLng(41.723099, -88.133240),
	    new google.maps.LatLng(41.723660, -88.133110),
	    new google.maps.LatLng(41.727909, -88.130852),
	    new google.maps.LatLng(41.728981, -88.130211),
	    new google.maps.LatLng(41.730080, -88.129730),
	    new google.maps.LatLng(41.732262, -88.129089),
	    new google.maps.LatLng(41.735901, -88.128342),
	    new google.maps.LatLng(41.737629, -88.128143),
	    new google.maps.LatLng(41.740891, -88.127937),
	    new google.maps.LatLng(41.743019, -88.127907),
	    new google.maps.LatLng(41.744061, -88.128090),
	    new google.maps.LatLng(41.746300, -88.128853),
	    new google.maps.LatLng(41.747379, -88.129288),
	    new google.maps.LatLng(41.748390, -88.129921),
	    new google.maps.LatLng(41.747490, -88.129356)
	];

	var STMARGARET_BOUNDARY_VERTICES = [
	    new google.maps.LatLng(41.748379, -88.129898),
	    new google.maps.LatLng(41.750179, -88.131172),
	    new google.maps.LatLng(41.751869, -88.125092),
	    new google.maps.LatLng(41.752239, -88.125160),
	    new google.maps.LatLng(41.760181, -88.122208),
	    new google.maps.LatLng(41.762981, -88.121262),
	    new google.maps.LatLng(41.766571, -88.120720),
	    new google.maps.LatLng(41.776878, -88.119621),
	    new google.maps.LatLng(41.784271, -88.119507),
	    new google.maps.LatLng(41.790291, -88.107819),
	    new google.maps.LatLng(41.792198, -88.103859),
	    new google.maps.LatLng(41.792759, -88.101913),
	    new google.maps.LatLng(41.793201, -88.099777),
	    new google.maps.LatLng(41.781479, -88.099960),
	    new google.maps.LatLng(41.781681, -88.098778),
	    new google.maps.LatLng(41.781849, -88.097412),
	    new google.maps.LatLng(41.782059, -88.093781),
	    new google.maps.LatLng(41.782219, -88.092171),
	    new google.maps.LatLng(41.782589, -88.090370),
	    new google.maps.LatLng(41.783100, -88.089020),
	    new google.maps.LatLng(41.783661, -88.087898),
	    new google.maps.LatLng(41.776829, -88.087044),
	    new google.maps.LatLng(41.764462, -88.086060),
	    new google.maps.LatLng(41.757290, -88.084969),
	    new google.maps.LatLng(41.757431, -88.081718),
	    new google.maps.LatLng(41.757545, -88.080116),
	    new google.maps.LatLng(41.757729, -88.078499),
	    new google.maps.LatLng(41.758060, -88.076881),
	    new google.maps.LatLng(41.758476, -88.075249),
	    new google.maps.LatLng(41.759178, -88.071999),
	    new google.maps.LatLng(41.760792, -88.065178),
	    new google.maps.LatLng(41.756451, -88.064842),
	    new google.maps.LatLng(41.752041, -88.064583),
	    new google.maps.LatLng(41.749989, -88.064079),
	    new google.maps.LatLng(41.749908, -88.071037),
	    new google.maps.LatLng(41.749352, -88.077560),
	    new google.maps.LatLng(41.749538, -88.082970),
	    new google.maps.LatLng(41.749149, -88.103653),
	    new google.maps.LatLng(41.748379, -88.129898)
	];

	var SSPETER_BOUNDARY_VERTICES = [
	    new google.maps.LatLng(41.806419, -88.156998),
	    new google.maps.LatLng(41.805580, -88.157036),
	    new google.maps.LatLng(41.804775, -88.156830),
	    new google.maps.LatLng(41.803932, -88.156097),
	    new google.maps.LatLng(41.803150, -88.155243),
	    new google.maps.LatLng(41.802757, -88.154991),
	    new google.maps.LatLng(41.802319, -88.154808),
	    new google.maps.LatLng(41.801838, -88.154701),
	    new google.maps.LatLng(41.801331, -88.154716),
	    new google.maps.LatLng(41.795540, -88.154762),
	    new google.maps.LatLng(41.794418, -88.154381),
	    new google.maps.LatLng(41.785839, -88.154510),
	    new google.maps.LatLng(41.779598, -88.154289),
	    new google.maps.LatLng(41.779129, -88.170349),
	    new google.maps.LatLng(41.775570, -88.167686),
	    new google.maps.LatLng(41.772011, -88.165291),
	    new google.maps.LatLng(41.771568, -88.165298),
	    new google.maps.LatLng(41.771149, -88.165466),
	    new google.maps.LatLng(41.770260, -88.164330),
	    new google.maps.LatLng(41.769520, -88.161728),
	    new google.maps.LatLng(41.769508, -88.159027),
	    new google.maps.LatLng(41.768108, -88.159027),
	    new google.maps.LatLng(41.766392, -88.158730),
	    new google.maps.LatLng(41.766109, -88.150703),
	    new google.maps.LatLng(41.765949, -88.148827),
	    new google.maps.LatLng(41.762470, -88.149231),
	    new google.maps.LatLng(41.761379, -88.149162),
	    new google.maps.LatLng(41.760880, -88.148956),
	    new google.maps.LatLng(41.759880, -88.148331),
	    new google.maps.LatLng(41.759510, -88.147957),
	    new google.maps.LatLng(41.758541, -88.146660),
	    new google.maps.LatLng(41.757931, -88.145729),
	    new google.maps.LatLng(41.757381, -88.144768),
	    new google.maps.LatLng(41.756550, -88.142982),
	    new google.maps.LatLng(41.753262, -88.135109),
	    new google.maps.LatLng(41.752022, -88.132919),
	    new google.maps.LatLng(41.751289, -88.132080),
	    new google.maps.LatLng(41.750198, -88.131157),
	    new google.maps.LatLng(41.751850, -88.125122),
	    new google.maps.LatLng(41.752239, -88.125153),
	    new google.maps.LatLng(41.760231, -88.122200),
	    new google.maps.LatLng(41.763000, -88.121269),
	    new google.maps.LatLng(41.769138, -88.120369),
	    new google.maps.LatLng(41.771721, -88.120071),
	    new google.maps.LatLng(41.776878, -88.119621),
	    new google.maps.LatLng(41.784241, -88.119698),
	    new google.maps.LatLng(41.789440, -88.109482),
	    new google.maps.LatLng(41.796490, -88.110260),
	    new google.maps.LatLng(41.800011, -88.110947),
	    new google.maps.LatLng(41.805901, -88.111122),
	    new google.maps.LatLng(41.806419, -88.156998)
	];
	
	var STTHOMAS_BOUNDARY_VERTICES = [
	    new google.maps.LatLng(41.753441, -88.205963),
	    new google.maps.LatLng(41.778301, -88.205620),
	    new google.maps.LatLng(41.777660, -88.225021),
	    new google.maps.LatLng(41.776501, -88.232048),
	    new google.maps.LatLng(41.801720, -88.230080),
	    new google.maps.LatLng(41.806309, -88.171463),
	    new google.maps.LatLng(41.806431, -88.156998),
	    new google.maps.LatLng(41.805431, -88.157028),
	    new google.maps.LatLng(41.804798, -88.156822),
	    new google.maps.LatLng(41.804340, -88.156509),
	    new google.maps.LatLng(41.803860, -88.156029),
	    new google.maps.LatLng(41.803452, -88.155540),
	    new google.maps.LatLng(41.803009, -88.155128),
	    new google.maps.LatLng(41.802311, -88.154778),
	    new google.maps.LatLng(41.801846, -88.154724),
	    new google.maps.LatLng(41.801311, -88.154701),
	    new google.maps.LatLng(41.795559, -88.154716),
	    new google.maps.LatLng(41.794350, -88.154381),
	    new google.maps.LatLng(41.779629, -88.154457),
	    new google.maps.LatLng(41.779121, -88.170341),
	    new google.maps.LatLng(41.775570, -88.167686),
	    new google.maps.LatLng(41.772018, -88.165260),
	    new google.maps.LatLng(41.771549, -88.165321),
	    new google.maps.LatLng(41.771130, -88.165482),
	    new google.maps.LatLng(41.770279, -88.164421),
	    new google.maps.LatLng(41.769508, -88.161758),
	    new google.maps.LatLng(41.769508, -88.159012),
	    new google.maps.LatLng(41.768188, -88.159050),
	    new google.maps.LatLng(41.767342, -88.158913),
	    new google.maps.LatLng(41.767151, -88.160652),
	    new google.maps.LatLng(41.766800, -88.162270),
	    new google.maps.LatLng(41.760422, -88.184158),
	    new google.maps.LatLng(41.753441, -88.205963)
	];
	    

    // Display the map.
    var mapElem = $('#map').get(0);

    var map = new google.maps.Map(mapElem, {
        zoom: 11,
        center: clientCoords,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    });
    
 // Add an overlay for the Holy Spirit parish boundary.
    var holyspiritPolygon = new google.maps.Polygon({
        map: map,
        paths: HOLYSPIRIT_BOUNDARY_VERTICES,
        strokeWeight: 2,
        strokeOpacity: 0.9,
        fillOpacity: 0.3,
    });
    
    // Add an overlay for the St. Elizabeth parish boundary.
    var stelizabethPolygon = new google.maps.Polygon({
        map: map,
        paths: STELIZABETH_BOUNDARY_VERTICES,
        strokeWeight: 2,
        strokeOpacity: 0.9,
        fillOpacity: 0.3,
    });
    
    // Add an overlay for the St. Margaret parish boundary.
    var stmargaretPolygon = new google.maps.Polygon({
        map: map,
        paths: STMARGARET_BOUNDARY_VERTICES,
        strokeWeight: 2,
        strokeOpacity: 0.9,
        fillOpacity: 0.3,
    });
    
    // Add an overlay for the SS.Peter parish boundary.
    var sspeterPolygon = new google.maps.Polygon({
        map: map,
        paths: SSPETER_BOUNDARY_VERTICES,
        strokeWeight: 2,
        strokeOpacity: 0.9,
        fillOpacity: 0.3,
    });
    
    // Add an overlay for the St. Thomas parish boundary.
    var stthomasPolygon = new google.maps.Polygon({
        map: map,
        paths: STTHOMAS_BOUNDARY_VERTICES,
        strokeWeight: 2,
        strokeOpacity: 0.9,
        fillOpacity: 0.3,
    });
    
    // Add an overlay for the St. Raphael parish boundary.
    var raphaelPolygon = new google.maps.Polygon({
        map: map,
        paths: RAPHAEL_BOUNDARY_VERTICES,
        strokeWeight: 2,
        strokeOpacity: 0.9,
        fillOpacity: 0.3,
    });
    
    holyspiritPolygon.setOptions({
    	strokeColor: '#fff600',
        fillColor: '#fff600'
    });
    stelizabethPolygon.setOptions({
    	strokeColor: '#fff600',
        fillColor: '#fff600'
    });
    
    stmargaretPolygon.setOptions({
    	strokeColor: '#fff600',
        fillColor: '#fff600'
    });
    
    sspeterPolygon.setOptions({
    	strokeColor: '#fff600',
        fillColor: '#fff600'
    });
    
    stthomasPolygon.setOptions({
    	strokeColor: '#fff600',
        fillColor: '#fff600'
    });

    // Check if the potential client lives within the parish boundaryj
    if (google.maps.geometry.poly.containsLocation(clientCoords, raphaelPolygon)) {
        raphaelPolygon.setOptions({
            strokeColor: '#468847',
            fillColor: '#468847'
        });

        $('#newClient').addClass('btn-success');
        $('#alerts').append(
            '<p class="alert alert-success">This address lies within the parish boundaries.</p>'
        );
	} else if(google.maps.geometry.poly.containsLocation(clientCoords, holyspiritPolygon)) {
	    raphaelPolygon.setOptions({
	        strokeColor: '#b94a48',
	        fillColor: '#b94a48'
	    });
	
	    $('#newClient').addClass('btn-danger');
	    $('#alerts').append(
	        '<p class="alert alert-error">This address lies outside the parish boundaries. Refer client to Holy Spirit parish: (630) 922-0081.</p>'
	    );
	} else if(google.maps.geometry.poly.containsLocation(clientCoords, stelizabethPolygon)) {
	    raphaelPolygon.setOptions({
	        strokeColor: '#b94a48',
	        fillColor: '#b94a48'
	    });
	
	    $('#newClient').addClass('btn-danger');
	    $('#alerts').append(
	        '<p class="alert alert-error">This address lies outside the parish boundaries. Refer client to St. Elizabeth parish: (630) 643-6006.</p>'
	    );
	} else if(google.maps.geometry.poly.containsLocation(clientCoords, stmargaretPolygon)) {
	    raphaelPolygon.setOptions({
	        strokeColor: '#b94a48',
	        fillColor: '#b94a48'
	    });
	
	    $('#newClient').addClass('btn-danger');
	    $('#alerts').append(
	        '<p class="alert alert-error">This address lies outside the parish boundaries. Refer client to St. Margaret parish: (630) 428-9914.</p>'
	    );
	} else if(google.maps.geometry.poly.containsLocation(clientCoords, sspeterPolygon)) {
	    raphaelPolygon.setOptions({
	        strokeColor: '#b94a48',
	        fillColor: '#b94a48'
	    });
	
	    $('#newClient').addClass('btn-danger');
	    $('#alerts').append(
	        '<p class="alert alert-error">This address lies outside the parish boundaries. Refer client to SS. Peter & Paul parish: (630) 718-2127.</p>'
	    );
	} else if(google.maps.geometry.poly.containsLocation(clientCoords, stthomasPolygon)) {
	    raphaelPolygon.setOptions({
	        strokeColor: '#b94a48',
	        fillColor: '#b94a48'
	    });
	
	    $('#newClient').addClass('btn-danger');
	    $('#alerts').append(
	        '<p class="alert alert-error">This address lies outside the parish boundaries. Refer client to St. Thomas parish: (630) 355-8980  x250.</p>'
	    );
	}
    else {
        raphaelPolygon.setOptions({
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
    var maritalStatusDropbox = $('#maritalStatus');
    var doNotHelpCheckbox = $('#doNotHelp');

    var memberSpouseDivs = $('.member-spouse');
    var memberDoNotHelpDiv = $('.member-donothelp');

    function update() {
        if (maritalStatusDropbox.val() == 'Married') {
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

    maritalStatusDropbox.change(update);
    doNotHelpCheckbox.click(update);

    update();

    // Attach jQuery UI widgets/plugin behavior.
    $('.date').datepicker();
    $('.phone').mask('(999) 999-9999');
}
