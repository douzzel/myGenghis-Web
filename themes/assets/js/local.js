function locate() {
    navigator.geolocation.getCurrentPosition(function(position){
        document.querySelector("#location").innerHTML = "lat : " + position.coords.latitude + " - lng : " + position.coords.longitude;
    });
}