<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SamCoding</title>
    <link rel="stylesheet" href="assets/css/home_style.css">
</head>
<body>
    <!-- Navbar -->
    <?php include_once 'includes/novbar.php';?>

    <!-- Main Content -->
    <div class="container">
        <!-- Universitet Banneli -->
        <div class="university-banner">
            <div class="university-banner-icon">🎓</div>
            <div>
                <strong>SamDU Dasturchilar Loyihasi</strong>
                <p style="margin: 0; color: var(--text-secondary); font-size: 0.9rem;">
                    Talabalarni hamkorlikda o‘rganish va musobaqaviy dasturlash orqali dunyo miqyosidagi dasturchilarga aylantirish
                </p>
            </div>
        </div>
        <!-- Hero Section - One Million Developers -->
        <div class="hero-university">
            <div class="hero-content">
                <div class="hero-badge">🎓 SAMDU DASTURCHI LOYIHASI</div>
                <h1 class="hero-title">Algorithmlarni birga o'rganamiz!</h1>
                <p class="hero-subtitle">
                    Samarqand davlat universiteti Sun'iy intellekt va raqamli texnologiyalar fakultiteti tomonidan tashkil etilgan loyihaga qo'shiling. 
                    Bir million dasturchini tayyorlash maqsadida algoritmik fikrlashni rivojlantiring.
                    Bizning platformamiz talabalarning muvaffaqiyatli o‘sishini ta'minlaydi.
                </p>
                
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a href="problems.php" class="btn btn-primary">O‘rganishni Boshlash</a>
                    <a href="contests.php" class="btn btn-secondary">Musobaqaga Qo‘shilish</a>
                </div>

                <!-- Hero Stats -->
                <div class="hero-stats">
                    <div class="hero-stat-item">
                        <div class="hero-stat-number">...</div>
                        <div class="hero-stat-label">Talabalar Ro‘yxatdan O‘tgan</div>
                    </div>
                    <div class="hero-stat-item">
                        <div class="hero-stat-number">...</div>
                        <div class="hero-stat-label">Dasturlash Masalalari</div>
                    </div>
                    <div class="hero-stat-item">
                        <div class="hero-stat-number">...</div>
                        <div class="hero-stat-label">Fakultetlar Ishtirok Etmoqda</div>
                    </div>
                    <div class="hero-stat-item">
                        <div class="hero-stat-number">...</div>
                        <div class="hero-stat-label">Yechilgan Masalalar</div>
                    </div>
                </div>
            </div>
        </div>

       <!-- Tezkor Navigatsiya Kartalari -->
        <div class="section-header">
            <h2 class="section-title">Tezkor Kirish</h2>
        </div>
        <div class="quick-nav-grid">
            <a href="problems.php" class="quick-nav-card">
                <div class="quick-nav-icon floating">📚</div>
                <h3>Masalalar Kutubxonasi</h3>
                <p>Barcha qiyinchilik darajalari va mavzular tanlangan masalalar</p>
            </a>
            <a href="contests.php" class="quick-nav-card">
                <div class="quick-nav-icon floating" style="animation-delay: 0.2s;">🏆</div>
                <h3>Jonli Musobaqalar</h3>
                <p>Haftalik musobaqalarda ishtirok eting va mahoratingizni sinab ko‘ring</p>
            </a>
            <a href="leaderboard.php" class="quick-nav-card">
                <div class="quick-nav-icon floating" style="animation-delay: 0.4s;">📊</div>
                <h3>Reytinglar</h3>
                <p>Rivojlanishingizni kuzatib boring va global reytingingizni ko‘ring</p>
            </a>
            <a href="profile.php" class="quick-nav-card">
                <div class="quick-nav-icon floating" style="animation-delay: 0.6s;">👤</div>
                <h3>Profilingiz</h3>
                <p>Yutuqlaringiz, statistikalaringiz va o‘quv yo‘lingizni ko‘ring</p>
            </a>
        </div>
    </div>
    <!-- Footer -->
    <?php include_once 'includes/footer.php';?>
    <script>
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });

        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.card, .quick-nav-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.6s, transform 0.6s';
            observer.observe(card);
        });
    </script>
    <script src="assets/js/change_style.js"></script>
</body>
</html>
