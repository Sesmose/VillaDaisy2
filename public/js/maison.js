$(document).ready(function(){
    $('.carousel.carousel-multi-item.v-2 .carousel-item').each(function () {
        var next = $(this).next();
        if (!next.length) {
            next = $(this).siblings(':first');
        }
        next.children(':first-child').clone().appendTo($(this));

        for (var i = 0; i < 4; i++) {
            next = next.next();
            if (!next.length) {
                next = $(this).siblings(':first');
            }
            next.children(':first-child').clone().appendTo($(this));
        }
    });


    $(window).scroll( function(){

        /* Check the location of each desired element */
        $('hr').each( function(i){

            var bottom_of_object = $(this).offset().top + $(this).outerHeight();
            var bottom_of_window = $(window).scrollTop() + $(window).height();

            /* If the object is completely visible in the window, fade it in */
            if( bottom_of_window > bottom_of_object ){

                $(this).animate({'opacity':'1'},500);
            }

        });

            
    });

    });

        $(window).scroll(function() {
            if($(window).scrollTop() + $(window).height() > $(document).height() - 100 && $(window).width() >= 768) {
             $('#reserver').modal('show');
    }
    });


            // On initialise la latitude et la longitude de Paris (centre de la carte)
            var lat = 41.6218101;
            var lon = 9.3415879;
            var macarte = null;
            // Fonction d'initialisation de la carte

            function initMap() {
                // Nous ajoutons un marqueur

                // Créer l'objet "macarte" et l'insèrer dans l'élément HTML qui a l'ID "map"
                macarte = L.map('map').setView([lat, lon], 11);
                // Leaflet ne récupère pas les cartes (tiles) sur un serveur par défaut. Nous devons lui préciser où nous souhaitons les récupérer. Ici, openstreetmap.fr
                L.tileLayer('https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png', {
                    // Il est toujours bien de laisser le lien vers la source des données
                    attribution: 'données © <a href="//osm.org/copyright">OpenStreetMap</a>/ODbL - rendu <a href="//openstreetmap.fr">OSM France</a>',
                    minZoom: 1,
                    maxZoom: 20,
                    id: 'mapbox.streets'
                }).addTo(macarte);
               var marker = L.marker([lat, lon]).addTo(macarte);
            }
            window.onload = function(){
		// Fonction d'initialisation qui s'exécute lorsque le DOM est chargé
		initMap(); 

            }