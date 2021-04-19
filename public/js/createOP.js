var mymap = L.map('chooseMap').setView([47.02050, 9.04054], 13);
var marker;

function onMapClick(e) {
  console.log(e);
  if(marker){
    marker.remove();
  }
  marker = L.marker(e.latlng).addTo(mymap);
  document.getElementById('InputPos').value = JSON.stringify(e.latlng);
  //e.latlng
}

function checkSubmit(e){
  if (document.getElementById('InputPos').value == "") {
    alert("Es wurde kein Standort angegeben!");
    e.preventDefault();
  }
}

L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
    maxZoom: 18,
    id: 'mapbox/streets-v11',
    tileSize: 512,
    zoomOffset: -1,
    accessToken: 'pk.eyJ1IjoibmlzYyIsImEiOiJja25vdHE0a24wZHI4MnVudzdvMDN6eTR6In0.-wdVL8FXmLmjfum4Vok66A'
}).addTo(mymap);
mymap.on('click', onMapClick);
