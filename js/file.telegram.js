    $(document).ready(function(){
      $("#showFormBtn").click(function(){
        $("#myForm").toggle();
      });
    });
    let oldServer = '';
let server = '';

function change() {
    var error = $('#error');
    
    if (server !== '') {
        oldServer = server;
        server = $('#Server').val();
        if (server == 0) {
        error.val('');
    } else {
        
        error.val(error.val().replace(oldServer, server));
    }
       } else  {
        server = $('#Server').val();
    }

    if (error.val() === '') {
        if (server == 0) {
            error.val('');
        } else { 
            
            error.val(`${server} is not Working.\nThe problem is`);
        }
    }
}
    function report(name, id) {
      var state = $('#Server').val();
    if(state == 0)
      {
        alert('Please Select Server');
      } else {
    var username = $('#name').val();
    var error = $('#error').val();
    var server = $('#Server').val();
    error = error.replace(/\n/g, "\\n");
    var url = `/report?name=${name}&id=${id}&uname=${username}&error=${error}&server=${server}`;
 fetch(url)
  .then(response => response.text())
  .then(data => {
    $("#myForm").hide();
    $("#showFormBtn").hide();
    $("#done").toggle();
  if(data == 1) {
    alert("Link Has Been Reported To Admin");
    }
  })
  .catch(error => {
    alert('There is a problem with the fetch operation:', error);
  });
      }
    }
    