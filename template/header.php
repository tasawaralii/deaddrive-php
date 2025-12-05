<header>
  <?php
  if (isset($_SERVER['user'])):
    ?>
    <!-- Sidebar -->
    <nav id="sidebarMenu" class="collapse d-lg-block sidebar" data-mdb-perfect-scrollbar='true'>
      <div class="position-sticky">
        <div class="list-group list-group-flush mx-3 mt-4">

          <a href="/dashboard" class="list-group-item list-group-item-action py-2 ripple" id="dashboard"
            aria-current="true">
            <i class="fas fa-tachometer-alt fa-fw me-3"></i>
            <span>Dashboard</span>
          </a>

          <a href="/share" class="list-group-item list-group-item-action py-2 ripple" id="share">
            <i class="fas fa-share-alt fa-fw me-3"></i>
            <span>Share</span>
          </a>
          <a href="/files" class="list-group-item list-group-item-action py-2 ripple" id="files">
            <i class="fas fa-file fa-fw me-3"></i>
            <span>Files</span>
          </a>
          <a href="/videos" class="list-group-item list-group-item-action py-2 ripple" id="videos">
            <i class="fas fa-play fa-fw me-3"></i>
            <span>Videos</span>
          </a>
          <a href="/status" class="list-group-item list-group-item-action py-2 ripple" id="status">
            <i class="fas fa-server fa-fw me-3"></i>
            <span>Status</span>
          </a>

          <a href="/account" class="list-group-item list-group-item-action py-2 ripple" id="account">
            <i class="fas fa-user fa-fw me-3"></i>
            <span>Account</span>
          </a>
          <a href="/setting" class="list-group-item list-group-item-action py-2 ripple" id="setting">
            <i class="fas fa-cog fa-fw me-3"></i>
            <span>Setting</span>
          </a>
        </div>
      </div>
    </nav>
    <!-- !Sidebar -->
    <!-- Navbar -->
    <nav id="main-navbar" class="navbar navbar-expand-lg bg-dark fixed-top">
      <!-- Container wrapper -->
      <div class="container-fluid">
        <!-- Toggle button -->
        <button class="navbar-toggler" type="button" data-mdb-toggle="collapse" data-mdb-target="#sidebarMenu"
          aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
          <i style="color:white;" class="fas fa-bars"></i>
        </button>
        <!-- Brand -->
        <center> <a href="/" class="navbar-brand">
            <i class="fab fa-google-drive"></i> &nbsp; DeadDrive
          </a></center>
        <style>
          @media only screen and (max-width: 767px) {
            .navbar-brand {
              display: none;
            }
          }
        </style>
        <!-- Right links -->
        <ul class="navbar-nav ms-auto d-flex flex-row">
          <!-- Avatar -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle hidden-arrow d-flex align-items-center" href="#"
              id="navbarDropdownMenuLink" role="button" data-mdb-toggle="dropdown" aria-expanded="false">
              <span><?php echo $_SERVER['user']['email'] ?></span>
              <img style="margin-left: 1rem;" src="<?php echo $_SERVER['user']['picture'] ?>" class="rounded-circle"
                height="22" alt="" loading="lazy" />
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">
              <li>
                <a class="dropdown-item" href="/account">Account</a>
              </li>
              <li>
                <a class="dropdown-item" href="/logout">Logout</a>
              </li>
            </ul>
          </li>
        </ul>
      </div>
      <!-- Container wrapper -->
    </nav>
    <!-- Navbar -->

    <?php
  else:
    ?>
    <nav id="main-navbar" class="navbar navbar-expand-lg bg-dark fixed-top">
      <!-- Container wrapper -->
      <div class="container-fluid">
        <center> <a href="/" class="navbar-brand">
            <i class="fab fa-google-drive"></i> &nbsp; DeadDrive
          </a></center>
        <!-- Right links -->
        <ul class="navbar-nav ms-auto d-flex flex-row">
          <!-- Avatar -->
          <li class="nav-item dropdown">
            <a href="/login" class="nav-link">
              <i class="fa fa-sign-in-alt"></i> Login </a>
          </li>
        </ul>
      </div>
    </nav>
    <?php
  endif;
  ?>
</header>