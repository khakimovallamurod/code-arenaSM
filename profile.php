<?php
   include_once 'config.php';
   session_start();
   if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
      header("Location: auth/login.php");
      exit;
   }
   $user_id = $_SESSION['id'];
   $obj = new Database();
   $user = $obj->get_data_by_table('users', ['id' => $user_id]);
?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SamCoding</title>
</head>
<body>
    <!-- Navbar -->
    <?php include_once 'includes/novbar.php';?>
    
    <!-- Main Content -->
    <div class="container">
        <!-- Profil Sarlavhasi -->
        <div class="profile-header">
            <?php
            $name = explode(' ', trim($user['fullname']));
            $initials = strtoupper($name[0][0] . ($name[1][0] ?? ''));
            ?>
            <div class="profile-avatar-large"><?=$initials?></div>
            <div class="profile-info" style="flex: 1;">
                <h2><?=$user['fullname']?></h2>
                <p class="text-secondary"><?=$user['email']?> • Ro‘yxatdan o‘tgan: <?=$user['created_at']?></p>
                <p class="text-secondary" style="margin-top: 0.5rem;">Telefon: <?=$user['phone']?></p>
                <div style="margin-top: 1rem; display: flex; gap: 1rem; flex-wrap: wrap;">
                    <button class="btn btn-primary">Profilni tahrirlash</button>
                    <button class="btn btn-secondary">Topshiriqlarni ko‘rish</button>
                </div>
            </div>
        </div>

        <!-- Statistika Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value">4,567</div>
                <div class="text-secondary">Umumiy Ball</div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 70%;"></div>
                </div>
                <div class="text-secondary" style="margin-top: 0.5rem; font-size: 0.9rem;">Dunyo bo‘yicha eng yaxshi 15%</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">234</div>
                <div class="text-secondary">Yechilgan Masalalar</div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 60%;"></div>
                </div>
                <div class="text-secondary" style="margin-top: 0.5rem; font-size: 0.9rem;">Barcha masalalarning 9.5%</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">45</div>
                <div class="text-secondary">Ishtirok etilgan Tanlovlar</div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 45%;"></div>
                </div>
                <div class="text-secondary" style="margin-top: 0.5rem; font-size: 0.9rem;">12 ta g‘alaba</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">42</div>
                <div class="text-secondary">Global Reyting</div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 85%;"></div>
                </div>
                <div class="text-secondary" style="margin-top: 0.5rem; font-size: 0.9rem;">45,789 foydalanuvchi orasida</div>
            </div>
        </div>
    
        <!-- Masalalar Bo‘yicha Statistika -->
        <h2 class="mb-1">📊 Masalalar Bo‘yicha Statistika</h2>
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 3rem;">
            <div class="stat-card">
                <div class="stat-value" style="color: var(--success);">156</div>
                <div class="text-secondary">Oson Masalalar</div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 78%; background: var(--success);"></div>
                </div>
                <div class="text-secondary" style="margin-top: 0.5rem; font-size: 0.9rem;">78% bajarildi</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color: var(--warning);">67</div>
                <div class="text-secondary">O‘rta Masalalar</div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 45%; background: var(--warning);"></div>
                </div>
                <div class="text-secondary" style="margin-top: 0.5rem; font-size: 0.9rem;">45% bajarildi</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color: var(--danger);">11</div>
                <div class="text-secondary">Qiyin Masalalar</div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 15%; background: var(--danger);"></div>
                </div>
                <div class="text-secondary" style="margin-top: 0.5rem; font-size: 0.9rem;">15% bajarildi</div>
            </div>
        </div>   

        <!-- Faoliyat Taqvimi -->
        <h2 class="mb-1" style="margin-top: 3rem;">📅 Faoliyat Taqvimi</h2>
        <div class="card" style="margin-bottom: 3rem;">
            <p class="text-secondary" style="margin-bottom: 1rem;">So‘nggi bir yilda 234 ta topshiriq</p>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(12px, 1fr)); gap: 3px;">
                <!-- Faoliyat Grid (52 hafta × 7 kun = 364 katak) -->
                <script>
                    for (let i = 0; i < 364; i++) {
                        const activity = Math.random();
                        let color;
                        if (activity < 0.7) color = 'var(--bg-tertiary)';
                        else if (activity < 0.85) color = 'rgba(99, 102, 241, 0.3)';
                        else if (activity < 0.95) color = 'rgba(99, 102, 241, 0.6)';
                        else color = 'var(--primary)';
                        document.write(`<div style="width: 12px; height: 12px; background: ${color}; border-radius: 2px;" title="Faoliyat"></div>`);
                    }
                </script>
            </div>
            <div style="margin-top: 1rem; display: flex; justify-content: flex-end; align-items: center; gap: 0.5rem;">
                <span class="text-secondary" style="font-size: 0.9rem;">Kam</span>
                <div style="width: 12px; height: 12px; background: var(--bg-tertiary); border-radius: 2px;"></div>
                <div style="width: 12px; height: 12px; background: rgba(99, 102, 241, 0.3); border-radius: 2px;"></div>
                <div style="width: 12px; height: 12px; background: rgba(99, 102, 241, 0.6); border-radius: 2px;"></div>
                <div style="width: 12px; height: 12px; background: var(--primary); border-radius: 2px;"></div>
                <span class="text-secondary" style="font-size: 0.9rem;">Ko‘p</span>
            </div>
        </div>
    </div>
    <!-- Footer -->
    <?php include_once 'includes/footer.php';?>
    <script src="assets/js/change_style.js"></script>
</body>
</html>