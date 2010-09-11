/*
*@author Dani Remeseiro
*@license Share Beer License
*
*/

$(document).ready(function(){

    //first get geolocation from chrome browser
    getGeo();

});

function getFeed(woeid){

    //give some info to the user (searching ads feed...)
    $('#content').html('<p>Buscando lista de anuncios cercanos a tu ubicacion ... <img height="16" width="16" src="http://nolotiro.org/css/loader.gif"> </p>');

   
    //now we get the woeid lets fetch the right rss feed
    var query = "SELECT * FROM feed WHERE url='http://nolotiro.org/es/rss/feed/woeid/"+woeid+"/ad_type/give' ";

    // Storing the seconds since the epoch in now:
    var now = (new Date()).getTime()/1000;
    // set the expires time
    var expires = 60; // 1 minute
    // for developing disable cache
    //var expires = 0;

    // no cache set in localStorage, or the cache is older than 1 minute:
    if(!localStorage.cacheFeed || now - parseInt(localStorage.time) > expires)
    {
        $.get("http://query.yahooapis.com/v1/public/yql?q="+encodeURIComponent(query)+"&format=json&callback=?",function(msg){

            var items = msg.query.results.item;
            var feedString = "";

            for(var i=0;i<items.length;i++)
            {
                var itemnolo = items[i];
                feedString += '<div class="itemnolo">\
								<a href="'+itemnolo.link+'" target="_blank"><h2>'+itemnolo.title+'</h2></a>\
								<p>'+itemnolo.description+'</p>\
								</div>';
            }

            // Setting the cache
            localStorage.cacheFeed = feedString;
            localStorage.time	= now;

            //$('#debug').append('cache no');
            // updating the content div (feed rss):
            $('#content').html( feedString);
        },'json');
    }
    else{
        // The cache is fresh, use it:
        //$('#debug').append('cache si');
        $('#content').html(localStorage.cacheFeed);
    }
}





function getGeo(){
    navigator.geolocation.getCurrentPosition(exportPosition, errorPosition);
}



function errorPosition() {
    $('#georesults').html('<p>:-( No se pudo acceder a tu  ubicacion.</p>');
}

function googleMapShow(latitude,longitude) {
    var latlng = new google.maps.LatLng(latitude,longitude);
    var myOptions = {
        zoom: 15,
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
        title:"tu ubicacion"
    });

}

// paint the map and debug info
function exportPosition(position) {
    $('#georesults').html( '<div id="map_canvas" style="float: right; width: 560px; height: 90px"></div>' );

    googleMapShow(
        position.coords.latitude,
        position.coords.longitude,
        {
            maximumAge:600000
        });

    //$('#debug').append( 'Lat: ' + position.coords.latitude + '&nbsp;' + 'Long: ' + position.coords.longitude );


    //now lets do reverse geocoding to get the city and country name
    req = new XMLHttpRequest();
    req.open('GET',  "http://maps.google.com/maps/api/geocode/json?latlng="+ position.coords.latitude+","+position.coords.longitude +"&sensor=false" );
    req.onload = parseJson;
    req.send();    
}


function parseJson(){
    var res = JSON.parse(req.responseText);
    //$('#debug').append('&nbsp;'+ res.results[0].formatted_address);
    $('#hachetres').append( ' <b>"'+ res.results[0].formatted_address + '"</b>');
    getWoeid(res.results[0].formatted_address);
}




function getWoeid(string){
    
    var query = 'SELECT woeid FROM geo.places.parent where child_woeid in (select woeid from geo.places where text=" '+ string +' ")';

    $.get("http://query.yahooapis.com/v1/public/yql?q="+encodeURIComponent(query)+"&format=json&callback=?",function(cb){
    
    woeid = cb.query.results.place.woeid;
    //$('#debug').append('&nbsp;(woeid:' + woeid + ')');

       //getNameFromWoeid(woeid);
       getFeed(woeid);
    },'json');
}


//damm! i can not use this function , the param lang doesnt work in spanish!! grrrr
function getNameFromWoeid(woeid){

    var query = 'SELECT * FROM geo.places WHERE woeid="'+ woeid +'"';

    $.get("http://query.yahooapis.com/v1/public/yql?q="+encodeURIComponent(query)+"&lang=es&format=json&callback=?",function(cbn){

    name = cbn.query.results.place.name;
    admin1 = cbn.query.results.place.admin1.type;
    country = cbn.query.results.place.country.content;
    $('#hachetres').append(' - ' + name + ', ' + admin1  +', ' + country);
       
    },'json');
}