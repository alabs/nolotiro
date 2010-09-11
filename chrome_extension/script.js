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






// Use jQuery to display useful information about our position.
function exportPosition(position) {
    $('#georesults').html(
        '<div id="map_canvas" style="float: right; width: 560px; height: 90px"></div>' +
        '<p>'
        + 'Latitude: ' + position.coords.latitude + '&nbsp;'
        + 'Longitude: ' + position.coords.longitude 
        + '</p>'
        );
    googleMapShow(
        position.coords.latitude,
        position.coords.longitude,
        {
            maximumAge:600000
        });


     ////////////////////////////////////////////////////////////////////////////////////

      //now lets do reverse geocoding to get the city and country name

        req = new XMLHttpRequest();
	req.open('GET',  "http://maps.google.com/maps/api/geocode/json?latlng="+ position.coords.latitude+","+position.coords.longitude +"&sensor=false" );
	req.onload = parseJson;
	req.send();

           
    
}


function parseJson()
{
    var res = JSON.parse(req.responseText);
        //	tweets = res.concat(tweets);
        $('#debug').html('<p>'+ res.toString() +'</p>');
  

}



function errorPosition() {
    $('#georesults').html('<p>The page could not get your location.</p>');
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

