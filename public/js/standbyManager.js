function addDays(date, days) {
  var result = new Date(date);
  result.setDate(result.getDate() + days);
  return result;
}

function createEvent(){
  var sendObj = {
    'fromDate': (document.getElementById('InputEventFrom').value),
    'toDate': (addDays(document.getElementById('InputEventTo').value, 0)),
    'user': (document.getElementById('EventUser').value),
    'status': (document.getElementById('EventStatus').value)
  };

  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      $('#entryModal').modal('hide');
      calsrc = calendar.getEventSources();
      calsrc[0].refetch()

    }
    else if (this.readyState == 4 && this.status != 200){
      document.getElementById('modalAlert').innerHTML = "Dies hat nicht funktioniert!";
      document.getElementById('modalAlert').style = "";
    }
  };
  xhttp.open("POST", "/app/standbies/createStandby", true);
  xhttp.setRequestHeader("Content-type", "text/json");
  xhttp.send(JSON.stringify(sendObj));
}

function updateEvent(){
  var updateID = document.getElementById('InputEventId').value;
  var sendObj = {
    'fromDate': (document.getElementById('InputEventFrom').value),
    'toDate': (document.getElementById('InputEventTo').value),
    'user': (document.getElementById('EventUser').value),
    'status': (document.getElementById('EventStatus').value)
  };

  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      $('#entryModal').modal('hide');
      calsrc = calendar.getEventSources();
      calsrc[0].refetch()

    }
    else if (this.readyState == 4 && this.status != 200){
      document.getElementById('modalAlert').innerHTML = "Dies hat nicht funktioniert!";
      document.getElementById('modalAlert').style = "";
    }
  };
  xhttp.open("POST", "/app/standbies/update/" + updateID, true);
  xhttp.setRequestHeader("Content-type", "text/json");
  xhttp.send(JSON.stringify(sendObj));
}

function deleteEvent(){
  var delID = document.getElementById('InputEventId').value;
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      $('#entryModal').modal('hide');
      calsrc = calendar.getEventSources();
      calsrc[0].refetch()

    }
    else if (this.readyState == 4 && this.status != 200){
      document.getElementById('modalAlert').innerHTML = "Dies hat nicht funktioniert!";
      document.getElementById('modalAlert').style = "";
    }
  };
  xhttp.open("GET", "/app/standbies/delete/" + delID, true);
  xhttp.send();
}

function clickDate(info){
  var startDate = addDays(info.start, 1);
  document.getElementById('InputEventId').value = "";
  document.getElementById('InputEventFrom').valueAsDate = startDate;
  document.getElementById('InputEventTo').valueAsDate = info.end;
  document.getElementById('BtnSave').onclick = createEvent;
  document.getElementById('modalAlert').style = "display:none";
  document.getElementById('BtnDelete').style = "display:none";
  $('#entryModal').modal('show');
}

function clickEvent(info) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        var selStby = JSON.parse(this.responseText);
        document.getElementById('InputEventId').value = selStby["id"];
        document.getElementById('InputEventFrom').valueAsDate = new Date(selStby["start"]);
        document.getElementById('InputEventTo').valueAsDate = addDays(new Date(selStby["end"]),-1);
        var optionsStatus = document.getElementById('EventStatus').options;
        for (var i = 0; i < optionsStatus.length; i++) {
          if (optionsStatus[i].value == selStby["status"]) {
            optionsStatus[i].selected = true;
          }
        }
        var optionsUser = document.getElementById('EventUser').options;
        for (var i = 0; i < optionsUser.length; i++) {
          if (optionsUser[i].value == selStby["user"]) {
            optionsUser[i].selected = true;
          }
        }
        document.getElementById('BtnSave').onclick = updateEvent;
        document.getElementById('BtnDelete').onclick = deleteEvent;
        document.getElementById('modalAlert').style = "display:none";
        document.getElementById('BtnDelete').style = "";
        $('#entryModal').modal('show');
      }
      else if (this.readyState == 4 && this.status != 200) {
        document.getElementById('modalAlert').style = "";
      }
    };
    xhttp.open("GET", "/app/standby/getStandby/" + info.event.id, true);
    xhttp.send();
}

var calendarEl = document.getElementById('calendar');
var calendar = new FullCalendar.Calendar(calendarEl, {
  locale: "de",
  selectable: true,
  initialView: 'dayGridMonth',
  events: '/app/standbies/getStandbies',
  select: clickDate,
  eventClick: clickEvent
});
calendar.render();
