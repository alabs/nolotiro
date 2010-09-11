$(document).ready(function(){

    var query = "SELECT * FROM feed WHERE url='http://nolotiro.org/es/rss/feed/woeid/766273/ad_type/give' LIMIT 20";

    // Storing the seconds since the epoch in now:
    var now = (new Date()).getTime()/1000;
    // set the expires time
    //var expires = 1*60*60;
    var expires = 0;

    // If there is no cache set in localStorage, or the cache is older than 1 hour:
    if(!localStorage.cache || now - parseInt(localStorage.time) > expires)
    {
        $.get("http://query.yahooapis.com/v1/public/yql?q="+encodeURIComponent(query)+"&format=json&callback=?",function(msg){

            // msg.query.results.item is an array:
            var items = msg.query.results.item;
            var htmlString = "";

            for(var i=0;i<items.length;i++)
            {
                var itemnolo = items[i];
                htmlString += '<div class="itemnolo">\
								<a href="'+itemnolo.link+'" target="_blank"><h2>'+itemnolo.title+'</h2></a>\
								<p>'+itemnolo.description+'</p>\
								</div>';
            }

            // Setting the cache
            localStorage.cache	= htmlString;
            localStorage.time	= now;

            //get geolocation
            getGeo();
            


            // Updating the content div:
            $('#content').html(htmlString);
        },'json');
    }
    else{
        // The cache is fresh, use it:
        $('#content').html(localStorage.cache);
    }
});





function getGeo(){
    navigator.geolocation.getCurrentPosition(exportPosition, errorPosition);
   
}






// paint the map and debug info
function exportPosition(position) {
    $('#georesults').html( '<div id="map_canvas" style="float: right; width: 560px; height: 90px"></div>' );

    $('#debug').html( 'Lat: ' + position.coords.latitude + '&nbsp;' + 'Long: ' + position.coords.longitude );

    googleMapShow(
        position.coords.latitude,
        position.coords.longitude,
        {
            maximumAge:600000
        });



    //now lets do reverse geocoding to get the city and country name
    req = new XMLHttpRequest();
    req.open('GET',  "http://maps.google.com/maps/api/geocode/json?latlng="+ position.coords.latitude+","+position.coords.longitude +"&sensor=false" );
    req.onload = parseJson1;
    req.send();
         
    
}


function parseJson1(){
     var res = JSON.parse(req.responseText);
    $('#debug').append('<br>'+ res.results[0].formatted_address);
}

function parseJson2(){
    var res = JSON.parse(req.responseText);
    $('#debug').append('<br>'+ res);


}


function getWoeid(string){
    
    var query = 'SELECT woeid FROM geo.places.parent where child_woeid in (select woeid from geo.places where text=" '+ string +' ")';

    req = new XMLHttpRequest();
    req.open('GET', "http://query.yahooapis.com/v1/public/yql?q="+encodeURIComponent(query)+"&format=json&callback=cbfunc");
    req.onload = parseJson2 ;
    req.send();

}




function errorPosition() {
    $('#georesults').html('<p>No se pudo acceder a tu  ubicacion.</p>');
}

function googleMapShow(latitude,longitude) {
    var latlng = new google.maps.LatLng(latitude,longitude);
    var myOptions = {
        zoom: 13,
        center: latlng,
        mapTypeControl: false,
        navigationControl: false,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

    var companyPos = new google.maps.LatLng(latitude,longitude);
    var companyMarker = new google.maps.Marker({
        position: companyPos,
        map: map,
        title:"Some title"
    });

}

