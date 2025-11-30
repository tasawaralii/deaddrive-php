<?PHP
require_once('check_login.php');
$page = array('title' => 'Dashboard');
require('functions.php');
require('db.php');
$email = AES('decrypt', $_COOKIE['ddeml']);
$user = userinfo($email, $pdo, true);
require('config.php');
require_once('includes/head.html');
?>
<body>
<?PHP require_once('includes/header.html'); ?>
<main style="margin-top: 58px">
  <div class="container pt-4">
    <section>
      <div class="row">
        <div class="col-xl-3 col-sm-6 col-12 mb-4">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between px-md-1">
                <div>
                  <h5 class="text-success">
                     <?PHP echo $user['total_files'] ?> </h5>
                  <p class="mb-0">Total Files</p>
                </div>
                <div class="align-self-center">
                  <i class="fab fa-google-drive text-success fa-2x"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 mb-4">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between px-md-1">
                <div>
                  <h5 class="text-warning">
                    <?php echo formatBytes($user['total_size']) ?> </h5>
                  <p class="mb-0">Storage Used</p>
                </div>
                <div class="align-self-center">
                  <i class="fas fa-hdd text-warning fa-2x"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 mb-4">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between px-md-1">
                <div>
                  <h5 id="totalDownloads" class="text-danger">
                     <?PHP echo $user['downloads'] ?></h5>
                  <p class="mb-0">
                    Total Downloads   </p>
                </div>
                <div class="align-self-center">
                  <i class="fa fa-arrow-circle-down text-danger fa-2x"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        
      
      
      <br>
      <?PHP require_once('includes/features.php'); ?>
      <?PHP require_once('includes/footer.html'); ?>
      
    </section>
  </div>
</main>

<div class="modal fade" id="Auth" tabindex="-1" aria-labelledby="AuthLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="AuthModalLabel">Authentication</h5>
        <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="authmsg"></div>
      <div class="modal-footer">
        <a class="btn btn-light" href="/share">Share</a>
        <a class="btn btn-light" href="/auth_token">Auth Token</a>
      </div>
    </div>
  </div>
</div>

<script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script>
  document.getElementById('trash').onclick = function () {
    this.disabled = true;
    var form = new FormData();
    form.append('action', 'trash');
    fetch('/dashboard', {
      method: 'POST',
      body: form
    }).then(response => response.text()).then(result => {
      document.getElementById('usageInDriveTrash').innerHTML = '0';
    }).catch(error => {
      console.log(error)
    });
  }

</script>

 <center>
     <br>
     <center>
            
        
</center> </center>
 

<script type="text/javascript">

    var l = window.location.pathname,
        e = l.split('/');

    if (e.length >= 2) {

        var n = (e.length > 2) ? 2 : 1,
            c = document.getElementById(e[n]);

        if (c) {
            c.classList.add('active')
        }

    }

</script>
<script type="text/javascript" src="/js/mdb.min.js"></script>


</body>

</html>