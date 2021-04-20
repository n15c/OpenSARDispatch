function initMap() {
  var coords = JSON.parse(document.getElementById("coords").value);
  var lat = coords["lat"];
  var lon = coords["lng"];

  var mymap = L.map('intelMap').setView([lat, lon], 13);
  var marker;

  L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
      attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
      maxZoom: 18,
      id: 'mapbox/streets-v11',
      tileSize: 512,
      zoomOffset: -1,
      accessToken: 'pk.eyJ1IjoibmlzYyIsImEiOiJja25vdHE0a24wZHI4MnVudzdvMDN6eTR6In0.-wdVL8FXmLmjfum4Vok66A'
  }).addTo(mymap);
  var marker = L.marker([lat, lon]).addTo(mymap);
}

function setOpCompleted(e) {
  if (e.checked) {
    var reqURL = "/app/operations/close/" + e.value;
    location.href = reqURL;
  }
}

initMap();
