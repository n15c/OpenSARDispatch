function saveUser() {
  var userObj = {};
  userObj["id"] = document.getElementById('ModalFormUserID').value;
  userObj["username"] = document.getElementById('ModalFormUsername').value;
  userObj["phone"] = document.getElementById('ModalFormPhone').value;
  userObj["firstname"] = document.getElementById('ModalFormFirstName').value;
  userObj["lastname"] = document.getElementById('ModalFormLastName').value;
  userObj["roles"] = [];
  var selectedOpts = document.getElementById('ModalFormRole').options;
  for (var i = 0; i < selectedOpts.length; i++) {
    if (selectedOpts[i].selected) {
      userObj["roles"].push(selectedOpts[i].value);
    }
  }

  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      $('#UserModal').modal('hide');
      location.reload();
    }
  }
  xhttp.open("POST", "/admin/updateUser/" + userObj["id"], true);
  xhttp.send(JSON.stringify(userObj));
}

  function createUser() {
    var userObj = {};
    userObj["username"] = document.getElementById('ModalFormUsername').value;
    userObj["phone"] = document.getElementById('ModalFormPhone').value;
    userObj["firstname"] = document.getElementById('ModalFormFirstName').value;
    userObj["lastname"] = document.getElementById('ModalFormLastName').value;
    userObj["roles"] = [];
    var selectedOpts = document.getElementById('ModalFormRole').options;
    for (var i = 0; i < selectedOpts.length; i++) {
      if (selectedOpts[i].selected) {
        userObj["roles"].push(selectedOpts[i].value);
      }
    }

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        $('#UserModal').modal('hide');
        location.reload();
      }
    }

  xhttp.open("POST", "/admin/createUser", true);
  xhttp.send(JSON.stringify(userObj));
}

function delUser() {
  var userID = document.getElementById('ModalFormUserID').value;
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      $('#UserModal').modal('hide');
      location.reload();
    }
    else if(this.readyState == 4) {
      alert("ERROR");
    }
  }
  xhttp.open("GET", "/admin/deleteUser/" + userID, true);
  xhttp.send();
}

function editUserModal(userID){
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      var selUser = JSON.parse(this.responseText);
      document.getElementById('ModalFormUserID').value = selUser["id"];
      document.getElementById('ModalFormUsername').value = selUser["username"];
      document.getElementById('ModalFormPhone').value = selUser["phone"];
      document.getElementById('ModalFormFirstName').value = selUser["firstname"];
      document.getElementById('ModalFormLastName').value = selUser["lastname"];
      document.getElementById('saveUsrBtn').onclick = saveUser;
      document.getElementById('ModalFormRole').value = "";
      var options = document.getElementById('ModalFormRole').options;
      selUser["roles"].forEach((role) => {
        for (var i = 0; i < options.length; i++) {
          if (options[i].value == role) {
            options[i].selected = true;
          }
        }
      });

      document.getElementById('delUsrBtn').style = "";
      document.getElementById('delUsrBtn').onclick= delUser;
      $('#UserModal').modal('show');
    }
    else if (this.readyState == 4 && this.status != 200) {

    }
  };
  xhttp.open("GET", "/admin/getUserInfo/" + userID, true);
  xhttp.send();
}

function addUserModal() {
  document.getElementById('ModalFormUserID').value = "";
  document.getElementById('ModalFormUsername').value = "";
  document.getElementById('ModalFormPhone').value = "";
  document.getElementById('ModalFormFirstName').value = "";
  document.getElementById('ModalFormLastName').value = "";
  document.getElementById('saveUsrBtn').onclick = createUser;
  document.getElementById('ModalFormRole').value = "";
  document.getElementById('delUsrBtn').style = "display: none;";
  $('#UserModal').modal('show');
}
