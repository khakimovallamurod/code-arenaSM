<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SamCoding | SamDU Algoritmlash Platformasi</title>
    <link rel="stylesheet" href="assets/css/styles-light.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="assets/css/home_style.css?v=<?php echo time(); ?>">
</head>
<body class="home-page">
    <!-- Navbar -->
    <?php include_once 'includes/novbar.php';?>

    <!-- Main Content -->
    <main class="container home-main">
        <section class="home-branding">
            <span class="university-tag">Samarqand davlat universiteti</span>
            <h1>SamCoding Platformasi</h1>
            <p>
                Sun'iy intellekt va raqamli texnologiyalar fakulteti talabalari uchun 
                algoritmlarni chuqur o'rganish va amaliy ko'nikmalarni mustahkamlash maydoni.
            </p>
            <div class="home-hero-actions">
                <a href="problems.php" class="btn btn-primary">Masalalarni ko'rish</a>
                <a href="contests.php" class="btn btn-secondary">Musobaqalar</a>
            </div>
        </section>

        <section class="home-stats">
            <article class="stat-card">
                <strong>1000+</strong>
                <span>Talabalar</span>
            </article>
            <article class="stat-card">
                <strong>200+</strong>
                <span>Masalalar</span>
            </article>
            <article class="stat-card">
                <strong>Haftalik</strong>
                <span>Musobaqalar</span>
            </article>
            <article class="stat-card">
                <strong>Live</strong>
                <span>Reyting</span>
            </article>
        </section>

        <section class="home-section">
            <div class="section-header">
                <h2>Tezkor yo'nalishlar</h2>
            </div>
            <div class="quick-nav-grid">
                <a href="problems.php" class="quick-nav-card">
                    <span class="quick-nav-icon">📚</span>
                    <div class="quick-nav-info">
                        <h3>Masalalar</h3>
                        <p>Algoritmlar va ma'lumotlar tuzilmasi bo'yicha masalalar to'plami</p>
                    </div>
                </a>
                <a href="contests.php" class="quick-nav-card">
                    <span class="quick-nav-icon">🏆</span>
                    <div class="quick-nav-info">
                        <h3>Musobaqalar</h3>
                        <p>Haqiqiy vaqt rejimida bilimingizni sinab ko'ring</p>
                    </div>
                </a>
                <a href="leaderboard.php" class="quick-nav-card">
                    <span class="quick-nav-icon">📈</span>
                    <div class="quick-nav-info">
                        <h3>Reyting</h3>
                        <p>Platforma foydalanuvchilari orasidagi o'rningizni ko'ring</p>
                    </div>
                </a>
                <a href="profile.php" class="quick-nav-card">
                    <span class="quick-nav-icon">👤</span>
                    <div class="quick-nav-info">
                        <h3>Profil</h3>
                        <p>Shaxsiy yutuqlar va yechilgan masalalar statistikasi</p>
                    </div>
                </a>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <?php include_once 'includes/footer.php';?>
    
    <script src="assets/js/change_style.js"></script>
</body>
</html>
