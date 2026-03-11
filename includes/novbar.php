<?php if (!isset($_SESSION)) session_start(); ?>
<!-- Core Dependencies -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="assets/css/navbar.css?v=<?= time() ?>">

<nav class="sm-navbar">
    <div class="sm-nav-container">
        <!-- Logo -->
        <a href="index.php" class="sm-logo">
            <div class="sm-logo-icon"><i class="fas fa-terminal"></i></div>
            <span>SamCoding</span>
        </a>

        <!-- Mobile Toggle -->
        <button class="sm-hamburger" id="smHamburger" aria-label="Menu">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <!-- Main Nav -->
        <ul class="sm-nav-links" id="smNavLinks">
            <?php if (isset($_SESSION['username'])): ?>
                <li><a href="index.php" class="sm-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">Asosiy</a></li>
                <li><a href="problems.php" class="sm-link <?= basename($_SERVER['PHP_SELF']) == 'problems.php' ? 'active' : '' ?>">Masalalar</a></li>
                <li><a href="contests.php" class="sm-link <?= basename($_SERVER['PHP_SELF']) == 'contests.php' ? 'active' : '' ?>">Musobaqalar</a></li>
                <li><a href="leaderboard.php" class="sm-link <?= basename($_SERVER['PHP_SELF']) == 'leaderboard.php' ? 'active' : '' ?>">Reyting</a></li>
            <?php endif; ?>
        </ul>

        <!-- User Section -->
        <div class="sm-user-section">
            <?php if (isset($_SESSION['username'])): ?>
                <div class="sm-account-wrapper">
                    <button class="sm-account-btn" id="smAccountBtn" type="button">
                        <?= strtoupper(substr($_SESSION['fullname'] ?? 'U', 0, 1)) ?>
                    </button>
                    <!-- Account Dropdown -->
                    <div class="sm-dropdown" id="smAccountDropdown">
                        <div style="padding: 0.75rem 1rem; border-bottom: 1px solid #f1f5f9; margin-bottom: 0.25rem;">
                            <p style="font-weight: 700; font-size: 0.9rem; margin: 0; color: #1a202c;"><?= htmlspecialchars($_SESSION['fullname']) ?></p>
                            <p style="color: #718096; font-size: 0.8rem; margin: 0;">@<?= htmlspecialchars($_SESSION['username']) ?></p>
                        </div>
                        <a href="profile.php" class="sm-dropdown-item">
                            <i class="far fa-user"></i> Profilim
                        </a>
                        <a href="auth/logout.php" class="sm-dropdown-item sm-logout">
                            <i class="fas fa-sign-out-alt"></i> Chiqish
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <a href="auth/login.php" class="btn btn-primary" style="padding: 0.5rem 1.2rem;">Kirish</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const smHamburger = document.getElementById('smHamburger');
    const smNavLinks = document.getElementById('smNavLinks');
    const smAccountBtn = document.getElementById('smAccountBtn');
    const smAccountDropdown = document.getElementById('smAccountDropdown');

    // Hamburger toggle - open/close menu
    if (smHamburger && smNavLinks) {
        smHamburger.addEventListener('click', function(e) {
            e.stopPropagation();
            smHamburger.classList.toggle('active');
            smNavLinks.classList.toggle('active');
            if (smAccountDropdown) {
                smAccountDropdown.classList.remove('active');
            }
        });
    }

    // Close menu when clicking outside
    document.addEventListener('click', function() {
        if (smNavLinks && smNavLinks.classList.contains('active')) {
            smNavLinks.classList.remove('active');
            smHamburger.classList.remove('active');
        }
        if (smAccountDropdown && smAccountDropdown.classList.contains('active')) {
            smAccountDropdown.classList.remove('active');
        }
    });

    // Close menu when clicking on nav items
    if (smNavLinks) {
        const navItems = smNavLinks.querySelectorAll('a');
        navItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.stopPropagation();
                smNavLinks.classList.remove('active');
            });
        });

        // Prevent menu from closing when clicking inside
        smNavLinks.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

    // Account button toggle
    if (smAccountBtn && smAccountDropdown) {
        smAccountBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            smAccountDropdown.classList.toggle('active');
            if (smNavLinks) {
                smNavLinks.classList.remove('active');
            }
        });
    }

    // Prevent dropdown from closing when clicking inside
    if (smAccountDropdown) {
        smAccountDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
});
</script>
