<?php


$features = [
  ['feature' => 'Google Login Support Added', 'des' => 'Now you can Login without remembering your Password', 'icon' => 'fa fa-sign-in-alt', 'color' => 'text-primary'],
  ['feature' => 'File Explorer', 'des' => 'You can Browse your All Files And Vidoes from our Panel', 'icon' => 'fab fa-google-drive', 'color' => 'text-danger'],
  ['feature' => 'Video Streaming Support', 'des' => 'Using Our Sharer You Can Embed Videos In Your Streaming Site.', 'icon' => 'fa fa-play', 'color' => 'text-danger'],
  ['feature' => 'Instant Resumable Download ', 'des' => 'All Users have access to Direct Download.Users Can Download without SignUp or any ads.', 'icon' => 'fa fa-download', 'color' => 'text-warning'],
  ['feature' => 'Advance Statics System', 'des' => 'Users Can Check Their Stats , Files Download, Total Files, Total Storage, Broken Files.', 'icon' => 'fa fa-rocket', 'color' => 'text-info'],
  ['feature' => 'No File-Download Limit', 'des' => 'There is no File Download Limit.', 'icon' => 'fa fa-check', 'color' => 'text-success'],
  ['feature' => 'SECURE TO USE', 'des' => 'Share your file in more secure way, create a short link for any file which you share', 'icon' => 'fa fa-shield-alt', 'color' => 'text-success'],
  ['feature' => 'UNLIMITED', 'des' => 'No quota limit. No annoying waiting times, Share & Download as many files as you want', 'icon' => 'fas fa-rocket', 'color' => 'text-warning'],
  ['feature' => 'TRUSTED SHARER', 'des' => 'Our Sharer is Trusted We do not Spam Ads and Simple one click Download', 'icon' => 'far fa-heart', 'color' => 'text-danger'],
  ['feature' => '', 'des' => '', 'icon' => '', 'color' => ''],
];

?>

<section class="mb-4">
  <div class="card">
    <div class="card-header py-3">
      <h6 class="mb-0 text-center">
        <strong> Features of <?php $_ENV['NAME'] ?>
          <a href="/about-us#version3"><?= $_ENV['VERSION'] ?> (<?= $_ENV['VERSION_RELEASE_DATE'] ?>)</a> </strong>
      </h6>
    </div>
  </div>
</section>

<div class="row">


  <?php

  foreach ($features as $f) {
    echo '<div class="col-xl-6 col-md-12 mb-4">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between p-md-1">
                <div class="d-flex flex-row">
                  <div class="align-self-center">
                   <i class="' . $f['icon'] . ' ' . $f['color'] . ' ' . 'fa-3x me-4"></i>
                  </div>
                  <div>
                    <h5>' . $f['feature'] . '</h5>
                    <p class="mb-0">' . $f['des'] . '</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>';
  }

  ?>


</div>