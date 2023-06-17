window.DashboardMap = function () {
    $(document).ready(function () {
        $('.country-list').empty();
        $('.city-list').empty();
        $('#map').empty();
        let styles = [
            {
                "elementType": "labels",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "administrative.land_parcel",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "administrative.neighborhood",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "poi",
                "elementType": "labels.text",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "poi.business",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "road",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "road",
                "elementType": "labels.icon",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "transit",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            }
        ];

        var myLatLng = {lat: 50.608408, lng: 12.133973};
        let map = new google.maps.Map(document.getElementById('map'), {
            center: myLatLng,
            zoom: 5,
            zoomControl: true,
            mapTypeControl: false,
            styles: styles
        });

        var getGoogleClusterInlineSvg = function () {
            var encoded = window.btoa(
                '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="50" viewBox="0 0 100 50"><rect width="100" height="50" rx="12" fill="#fff" fill-opacity="0.8"/></svg>'
            );

            return ('data:image/svg+xml;base64,' + encoded);
        };

        $('.btn.countries, .btn.cities').on('click', function () {
            let buttons = $('.btn.countries, .btn.cities');
            let lists = $('.city-list, .country-list');

            buttons.each(function(){
                $(this).removeClass('bg-orange text-white').addClass('btn-secondary');
            });

            lists.each(function(){
                $(this).hide();
            });

            if ($(this).hasClass('countries')) {
                $('.country-list').show()
            } else {
                $('.city-list').show()
            }

            $(this).addClass('bg-orange text-white').removeClass('btn-secondary');
        });

        $.ajax({
            url: "/orders/orders_by_country",
            context: document.body,
            data: {
                startDate: $(document).find('input[name="dashboard_filter_date_start"]').val(),
                endDate: $(document).find('input[name="dashboard_filter_date_end"]').val()
            }
        }).done(function(data) {
            let ordersByCountry = data;

            data.map(function (country){
                let line = '<div><span class="bg-orange text-white">'+ country.total_orders +'</span>' +
                    '<i class="flag-icon flag-icon-' + country.iso_3166_2.toLowerCase() +'">' +
                    '</i><a>'+ country.name +'</a></div>'

                $('.country-list').append(line)
            })
        });

        $.ajax({
            url: "/orders/orders_by_cities_limited",
            context: document.body,
            data: {
                startDate: $(document).find('input[name="dashboard_filter_date_start"]').val(),
                endDate: $(document).find('input[name="dashboard_filter_date_end"]').val()
            }
        }).done(function(data) {
            let ordersByCity = data;

            data.map(function (city){
                let line = '<div><span class="bg-orange text-white">'+ city.total_orders +'</span>' +
                    '<i class="flag-icon flag-icon-' + city.countryCode.toLowerCase() +'">' +
                    '</i><a>'+ city.title +'</a></div>';

                $('.city-list').append(line);
            });
        });

        $.ajax({
            url: "/orders/orders_by_cities",
            context: document.body,
            data: {
                startDate: $(document).find('input[name="dashboard_filter_date_start"]').val(),
                endDate: $(document).find('input[name="dashboard_filter_date_end"]').val()
            }
        }).done(function(data) {
            let ordersByCities = data;
            let bounds = new google.maps.LatLngBounds();

            const markers = ordersByCities.map((location, i) => {
                let position = {lat: Number(location.lat), lng: Number(location.lng)}
                bounds.extend(position)
                map.fitBounds(bounds);
                let flag = '';
                let countryCode = '';

                if (location.countryCode) {
                    //flag += '<img src="/flags/4x3/' + location.countryCode.toLowerCase() + '.svg"/>';
                    flag += '<img src="/images/' + location.countryCode.toLowerCase() + '.svg"/>';
                    countryCode = location.countryCode.toLowerCase();
                }

                flag += '<strong>' + location.count + '</strong>';

                var MarkerWithLabel = require('markerwithlabel')(google.maps);

                return new MarkerWithLabel({
                    map: map,
                    position: position,
                    clickable: false,
                    draggable: false,
                    customText: ''+location.count+'',
                    customCountryCode: ''+countryCode+'',
                    labelContent: ''+flag+'',
                    labelClass: "custom-marker",
                    icon: {
                        path: "M1021.256,581l22.725,22.725L1066.707,581Z",
                        fillColor: '#fff',
                        anchor: new google.maps.Point(0,0),
                        // scaledSize: new google.maps.Size(500,500),
                        strokeWeight: 0
                    }
                });
            });

            new MarkerClusterer(map, markers, {
                calculator: function (markers, numStyles){
                    var index = 0;
                    var count = markers.length;
                    var dv = count;
                    let countries = [];

                    while (dv !== 0) {
                        dv = parseInt(dv / 10, 10);
                        index++;
                    }

                    count = 0;

                    for (const i in markers) {
                        count += parseInt(markers[i].customText);

                        if (!countries.includes(markers[i].customCountryCode)) {
                            countries.push(markers[i].customCountryCode);
                        }
                    }

                    index = Math.min(index, numStyles);

                    let flag = '/images/default-flag.png';
                    let defaultClass = 'default-cluster';

                    if(countries.length === 1) {
                        //flag = '/flags/4x3/' + countries[0] + '.svg';
                        flag = '/images/' + countries[0] + '.svg';
                        defaultClass = 'custom-cluster';
                    }

                    return {
                        text: '<div class="custom-marker ' + defaultClass + '"><img src="' + flag + '"/><strong>' + count + '</strong></div>',
                        index: index
                    };
                },

                styles: [
                    {
                        height: 50,
                        width: 100,
                        textColor: "black",
                        url: getGoogleClusterInlineSvg(),
                        textSize: 16,
                        averageCenter: false,
                        fontFamily: "Inter, serif",
                        fontWeight: '600',
                        z_index: 0
                    }
                ],
            });
        });

    });
};
