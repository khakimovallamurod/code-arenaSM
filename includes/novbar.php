<?php session_start(); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<nav class="navbar">
    <div class="nav-container">
        <a href="index.php" class="logo">🎓 SamCoding</a>
        <ul class="nav-menu">
            <li><a href="index.php" class="active">Bosh sahifa</a></li>
            <li><a href="problems.php">Masalalar</a></li>
            <li><a href="contests.php">Musobaqalar</a></li>
            <li><a href="leaderboard.php">Reyting</a></li>
        </ul>

        <div class="user-section">
            <?php if (isset($_SESSION['username'])): ?>
                <div class="user-area">
                    <?php
                    $name = explode(' ', trim($_SESSION['fullname']));
                    $initials = strtoupper($name[0][0] . ($name[1][0] ?? ''));
                    ?>
                    <button class="user-avatar" id="userBtn">
                        <span><?= $initials ?></span>
                    </button>
                    <div class="user-panel" id="userPanel">
                        <a href="profile.php"><i class="fa fa-user"></i> Mening profilim</a>
                        <a href="auth/logout.php"><i class="fa fa-sign-out-alt"></i> Chiqish</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="auth-links">
                    <a href="auth/login.php"><i class="fa fa-sign-in-alt"></i> Kirish</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- 🔹 CSS -->
<style>


.user-section {
    display: flex;
    align-items: center;
}
.auth-links a {
    margin-left: 1rem;
    text-decoration: none;
    color: #6de962;
    font-weight: 500;
}
.user-area {
    position: relative;
}

.user-panel {
    position: absolute;
    right: -5px;
    top: 50%;
    transform: translateX(110%); 
    background: #fff;
    border: 1px solid #ddd;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-radius: 8px;
    display: none;
    flex-direction: column;
    padding: 0.3rem 0;
    width: 170px;
}
.user-panel a {
    padding: 10px 15px;
    text-decoration: none;
    color: #333;
    display: flex;
    align-items: center;
    gap: 8px;
}
.user-panel a:hover {
    background: #f2f2f2;
}
.user-panel.active {
    display: flex;
}
</style>

<!-- 🔹 JS: Yon panelni ochish/yopish -->
<script>
const userBtn = document.getElementById('userBtn');
const userPanel = document.getElementById('userPanel');

if (userBtn) {
    userBtn.addEventListener('click', () => {
        userPanel.classList.toggle('active');
    });
    document.addEventListener('click', (e) => {
        if (!userPanel.contains(e.target) && !userBtn.contains(e.target)) {
            userPanel.classList.remove('active');
        }
    });
}
</script>
