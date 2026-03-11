<?php
   session_start();
   include_once 'config.php';
   $db = new Database();
   if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
      header("Location: auth/login.php");
      exit;
   }
   date_default_timezone_set('Asia/Tashkent');
   
   $musobaqalar = $db->get_data_by_table_all("contests");
   
   $faol_contestlar = [];
   $kutilayotgan_contestlar = [];
   $tugagan_contestlar = [];
   
   $now = time();
   
   foreach ($musobaqalar as $contest) {
       $start = strtotime($contest['start_time']);
       $end = strtotime($contest['end_time']);
       
       if ($now < $start) {
           $actual_status = 0; 
           $category = &$kutilayotgan_contestlar;
       } elseif ($now >= $start && $now <= $end) {
           $actual_status = 1; 
           $category = &$faol_contestlar;
       } else {
           $actual_status = 2; 
           $category = &$tugagan_contestlar;
       }
       
       if ($contest['status'] != $actual_status) {
           $db->update("contests", ['status' => $actual_status], "id = ".$contest['id']);
           $contest['status'] = $actual_status;
       }
       
       $category[] = $contest;
   }

   $faol_count = count($faol_contestlar);
   $kutilayotgan_count = count($kutilayotgan_contestlar);
   $tugagan_count = count($tugagan_contestlar);
   
   function renderContestCard($contest) {
       $now = time();
       $start = strtotime($contest['start_time']);
       $end = strtotime($contest['end_time']);
       
       $status_data = [
           0 => ['text' => 'Kutilmoqda', 'class' => 'badge-medium'],
           1 => ['text' => 'Faol', 'class' => 'badge-easy'],
           2 => ['text' => 'Tugagan', 'class' => 'badge-hard']
       ];
       
       $status = $status_data[$contest['status']];
       
       $durationSeconds = abs($end - $start);
       $durationHours = floor($durationSeconds / 3600);
       $durationMinutes = floor(($durationSeconds % 3600) / 60);
       
       if ($durationMinutes > 0) {
           $durationText = $durationHours . " soat " . $durationMinutes . " daqiqa";
       } else {
           $durationText = $durationHours . " soat";
       }
       
       
       
       $registered_count = isset($contest['registered_count']) ? $contest['registered_count'] : 0;
       if ($contest['status'] === 0) {
           $scheduleText = "Boshlanishiga: " . max(0, floor(($start - $now) / 60)) . " daqiqa";
       } elseif ($contest['status'] === 1) {
           $scheduleText = "Tugashiga: " . max(0, floor(($end - $now) / 60)) . " daqiqa";
       } else {
           $scheduleText = "Musobaqa yakunlangan";
       }
       ?>
       <div class="card contest-card" onclick="openContestDetail(<?=$contest['id']?>)">
           <div class="contest-card-header">
               <h3><?= htmlspecialchars($contest['title']) ?></h3>
               <span class="badge <?=$status['class']?> contest-status"><?=$status['text']?></span>
           </div>
           <p class="text-secondary contest-description"><?= htmlspecialchars($contest['description']) ?></p>
           <div class="contest-timeline"><?=$scheduleText?></div>
           <div class="contest-meta-grid">
               <div class="contest-meta-item">
                   <div class="text-secondary">Boshlanish</div>
                   <strong><?= date('M j, H:i', $start) ?></strong>
               </div>
               <div class="contest-meta-item">
                   <div class="text-secondary">Tugash</div>
                   <strong><?= date('M j, H:i', $end) ?></strong>
               </div>
               <div class="contest-meta-item">
                   <div class="text-secondary">Davomiyligi</div>
                   <strong><?= $durationText ?></strong>
               </div>
               <div class="contest-meta-item">
                   <div class="text-secondary">Ro'yxatdan o'tdi</div>
                   <strong><?= $registered_count ?> kishi</strong>
               </div>
           </div>
           
       </div>
       <?php
   }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SamCoding - Musobaqalar</title>
    <link rel="stylesheet" href="assets/css/styles-light.css?v=<?php echo time(); ?>">

</head>
<body>
    <!-- Navbar -->
    <?php include_once 'includes/novbar.php';?>

    <!-- Main Content -->
    <div class="container">
        <section class="contests-hero">
            <h1 class="contests-title">Musobaqalar</h1>
            <p class="contests-subtitle">Raqobatlashish va o'z ko'nikmalaringizni real vaqt rejimida sinab ko'ring</p>
            <div class="contests-overview">
                <div class="contests-overview-item">
                    <span class="contests-overview-value"><?=$faol_count?></span>
                    <span class="contests-overview-label">Faol</span>
                </div>
                <div class="contests-overview-item">
                    <span class="contests-overview-value"><?=$kutilayotgan_count?></span>
                    <span class="contests-overview-label">Kutilayotgan</span>
                </div>
                <div class="contests-overview-item">
                    <span class="contests-overview-value"><?=$tugagan_count?></span>
                    <span class="contests-overview-label">Tugagan</span>
                </div>
            </div>
        </section>

        <!-- Active Contests -->
        <?php if (count($faol_contestlar) > 0): ?>
        <section class="contest-section">
        <h2 class="mb-1">Davom etayotgan musobaqalar</h2>
        <div class="card-grid contest-card-grid">
            <?php foreach ($faol_contestlar as $contest): ?>
                <?php renderContestCard($contest); ?>
            <?php endforeach; ?>
        </div>
        </section>
        <?php endif; ?>

        <!-- Upcoming Contests -->
        <?php if (count($kutilayotgan_contestlar) > 0): ?>
        <section class="contest-section">
        <h2 class="mb-1">Kutilayotgan musobaqalar</h2>
        <div class="card-grid contest-card-grid">
            <?php foreach ($kutilayotgan_contestlar as $contest): ?>
                <?php renderContestCard($contest); ?>
            <?php endforeach; ?>
        </div>
        </section>
        <?php endif; ?>

        <?php if (count($tugagan_contestlar) > 0): ?>
        <section class="contest-section">
        <h2 class="mb-1">Yakunlangan musobaqalar</h2>
        <div class="card-grid contest-card-grid">
            <?php foreach ($tugagan_contestlar as $contest): ?>
                <?php renderContestCard($contest); ?>
            <?php endforeach; ?>
        </div>
        </section>
        <?php endif; ?>

        <!-- Past Contests -->
        
        
        <?php if (empty($musobaqalar)): ?>
        <div class="card contest-empty">
            <h3>Hozircha musobaqalar yo'q</h3>
            <p class="text-secondary">Tez orada yangi musobaqalar boshlanadi!</p>
        </div>
        <?php endif; ?>
        
    </div>
    
    <?php include_once 'includes/footer.php';?>
    
    <script src="assets/js/change_style.js"></script>
    <script>
        function openContestDetail(contestId) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'contest-detail.php';
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'contestid';
            input.value = contestId;
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
        
        setTimeout(function() {
            location.reload();
        }, 60000);
    </script>
</body>
</html>
