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
       ?>
       <div class="card" onclick="openContestDetail(<?=$contest['id']?>)" style="cursor: pointer;">
           <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
               <h3><?= htmlspecialchars($contest['title']) ?></h3>
               <span class="badge <?=$status['class']?>"><?=$status['text']?></span>
           </div>
           <p class="text-secondary" style="margin-bottom: 1rem;"><?= htmlspecialchars($contest['description']) ?></p>
           <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1rem;">
               <div>
                   <div class="text-secondary" style="font-size: 0.9rem;">Boshlanish</div>
                   <strong><?= date('M j, H:i', $start) ?></strong>
               </div>
               <div>
                   <div class="text-secondary" style="font-size: 0.9rem;">Tugash</div>
                   <strong><?= date('M j, H:i', $end) ?></strong>
               </div>
               <div>
                   <div class="text-secondary" style="font-size: 0.9rem;">Davomiyligi</div>
                   <strong><?= $durationText ?></strong>
               </div>
               <div>
                   <div class="text-secondary" style="font-size: 0.9rem;">Ro'yxatdan o'tdi</div>
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
    <link rel="stylesheet" href="assets/css/styles-light.css">

</head>
<body>
    <!-- Navbar -->
    <?php include_once 'includes/novbar.php';?>

    <!-- Main Content -->
    <div class="container">
        <h1 style="margin-bottom: 0.5rem;">Musobaqalar</h1>
        <p class="text-secondary" style="margin-bottom: 2rem;">Raqobatlashish va o'z ko'nikmalatingizni sinab ko'ring</p>

        <!-- Active Contests -->
        <?php if (count($faol_contestlar) > 0): ?>
        <h2 class="mb-1" style="margin-top: 3rem;">Davom etayotgan musobaqalar</h2>
        <div class="card-grid">
            <?php foreach ($faol_contestlar as $contest): ?>
                <?php renderContestCard($contest); ?>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Upcoming Contests -->
        <?php if (count($kutilayotgan_contestlar) > 0): ?>
        <h2 class="mb-1" style="margin-top: 3rem;">Kutilayotgan musobaqalar</h2>
        <div class="card-grid">
            <?php foreach ($kutilayotgan_contestlar as $contest): ?>
                <?php renderContestCard($contest); ?>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Past Contests -->
        
        
        <?php if (empty($musobaqalar)): ?>
        <div class="card" style="text-align: center; padding: 3rem;">
            <h3>Hozircha musobaqalar yo'q</h3>
            <p class="text-secondary">Tez orada yangi musobaqalar boshlanad5i!</p>
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