

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo $base_url; ?>">Job Portal</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $base_url; ?>">Home</a>
                </li>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['user_type'] === 'poster'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_url; ?>posting/postjob/">Post Job</a>
                        </li>
                       
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_url; ?>posting/managejob/">Manage Jobs</a>
                        </li>
                    <?php elseif ($_SESSION['user_type'] === 'bidder'): ?>
                       
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_url; ?>bidding/">My Bids</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_url; ?>winners/">My Winnings</a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>

            <ul class="navbar-nav">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <!-- <li><a class="dropdown-item" href="<?php echo $base_url; ?>profile.php">Profile</a></li> -->
                            <!-- <li><a class="dropdown-item" href="<?php echo $base_url; ?>settings.php">Settings</a></li> -->
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?php echo $base_url; ?>api/auth/logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="btn btn-outline-light me-2" href="<?php echo $base_url; ?>auth/Login/">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary" href="<?php echo $base_url; ?>auth/Register/">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
