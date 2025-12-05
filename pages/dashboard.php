<?PHP

$title = 'Dashboard';

?>
<div class="container pt-4">
  <section>
    <div class="row">
      <div class="col-xl-3 col-sm-6 col-12 mb-4">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between px-md-1">
              <div>
                <h5 class="text-success">
                  <?PHP echo $_SERVER['user']['total_files'] ?>
                </h5>
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
                  <?php echo formatBytes($_SERVER['user']['total_size']) ?>
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
                <h5 id="totalDownloads" class="text-danger">
                  <?PHP echo $_SERVER['user']['downloads'] ?>
                </h5>
                <p class="mb-0">
                  Total Downloads </p>
              </div>
              <div class="align-self-center">
                <i class="fa fa-arrow-circle-down text-danger fa-2x"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <br>
      <?PHP require_once('template/features.php'); ?>

  </section>
</div>