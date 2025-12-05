<div class="container pt-4">
    <div class="card">
        <div class="card-body">
            <center>
                <h4><i class="fab fa-google-drive" style="color:red"></i>
                    <?= $_ENV['NAME'] . " " . $_ENV['VERSION'] . " " . $_ENV['VERSION_RELEASE_DATE'] ?></h4>
                <hr />

                <p><?= $_ENV['NAME'] ?> Released.Stay with us to get <a href="<?= $_ENV['CONTACT_TELEGRAM'] ?>">Notified
                        about our New
                        Release.</a></p>
                <p>
                    Your friends can download or play the video Through Multiple Servers including FilePress, Doodstream
                    etc.
                </p>

                <p>Sharing Your Files For Free has Never been this Easy! Grow with Us. Join and Get full Control of your
                    Data</p>

                <p>
                    <?= $_ENV['NAME'] ?> provide a unique solution to share google drive files without any issue.
                </p>
                <p>
                    Read More About <?= $_ENV['NAME'] ?> On Our <a href="/about-us"><b> About Us Page Here</b></a>.
                </p>




                <div class="container-fluid">
                    <?PHP if (!isset($_SERVER['user'])) {

                        echo '<a href="/login" class="btn btn-block btn-danger ripple-surface" style="max-width: 300px;"> <i class="fab fa-google-plus mr-2"></i> Sign in using Google+ </a>';
                    } else {
                        echo '<a href="/dashboard" class="btn btn-block btn-info ripple-surface" style="max-width: 300px;">Go to Dashboard </a>';

                    }
                    ?>

                </div>
                <hr>
                <?PHP require_once('template/features.php') ?>
            </center>
        </div>
    </div>
</div>