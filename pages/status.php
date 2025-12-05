<?PHP

checklogin();

$title = 'Status';

$user = userinfo(AES('decrypt', $_COOKIE['ddeml']), $pdo, true);
$user_id = $user['user_id'];

?>

<div class="container pt-4">
  <section>
    <h3>User Status</h3> <br>
    <div class="row">
      <div class="col-xl-3 col-sm-6 col-12 mb-4">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between px-md-1">
              <div>
                <a href="/files">
                  <h5 class="text-success"><?php echo $user['total_files'] ?></h5>
                </a>
                <p class="mb-0">Total Files</p>
              </div>
              <div class="align-self-center">
                <i class="fas fa-file-medical text-success fa-2x"></i>
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
                  <?php echo formatBytes($user['total_size']) ?>
                </h5>
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
                <a href="/broken-files">
                  <h5 class="text-danger"><?php echo $user['broken'] ?></h5>
                </a>
                <p class="mb-0">Broken Files</p>
              </div>
              <div class="align-self-center">
                <i class="fas fa-unlink text-danger fa-2x"></i>
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
                <a href="/files">
                  <h5 class="text-primary"><?php echo $user['downloads'] ?></h5>
                </a>
                <p class="mb-0">Total Downloads</p>
              </div>
              <div class="align-self-center">
                <i class="fas fa-cloud-download-alt text-primary fa-2x"></i>
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
                <a href="/files">
                  <h5 class="text-danger"><?php echo $user['views'] ?></h5>
                </a>
                <p class="mb-0">Total Views</p>
              </div>
              <div class="align-self-center">
                <i class="fas fa-eye text-danger fa-2x"></i>
              </div>
            </div>
          </div>
        </div>
      </div>


    </div>


    <br />
    <?php require('template/features.php'); ?>

  </section>
</div>