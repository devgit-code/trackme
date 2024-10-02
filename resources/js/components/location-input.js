window.requestLocation = function (el, data) {
    if (el.checked) {
        function updatePos(position) {
            // It can be spoofed and I don't care
            data.lat = position.coords.latitude;
            data.lon = position.coords.longitude;
            data.accuracy = position.coords.accuracy;
        }
        navigator.geolocation.getCurrentPosition(updatePos, null, {
            enableHighAccuracy: true,
            maximumAge: 30000,
            timeout: 27000,
        });
        window.posWatch = navigator.geolocation.watchPosition(updatePos, null, {
            enableHighAccuracy: true
        });
    } else {
        data.lat = null;
        data.lon = null;
        data.accuracy = null;
        navigator.geolocation.clearWatch(posWatch);
    }
}
