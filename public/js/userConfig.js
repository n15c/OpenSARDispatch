function saveUserConfig() {
  var userObj = {
    "mail": document.getElementById('userConfigMail').value,
    "phone": document.getElementById('userConfigPhone').value,
    "firstname": document.getElementById('userConfigFirstName').value,
    "lastname": document.getElementById('userConfigLastName').value
  };

  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      location.reload();
    }
  }

xhttp.open("POST", "/app/userConfig/setConfig", true);
xhttp.send(JSON.stringify(userObj));
}
