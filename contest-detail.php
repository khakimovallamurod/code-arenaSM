<?php
   session_start();
   include_once 'config.php';
   $db = new Database();
   if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
      header("Location: auth/login.php");
      exit;
   }
   
   date_default_timezone_set('Asia/Tashkent');
   $contest_id = isset($_POST['contestid']) ? intval($_POST['contestid']) : 0;
   if ($contest_id == 0) {
      header("Location: contests.php");
      exit;
   }
   
   $contest = $db->get_data_by_table("contests", ['id'=>$contest_id]);
   $contest_problems = $db->get_data_by_table_all("contest_problems", "WHERE contest_id = $contest_id"); 
   if (!$contest) {
      header("Location: contests.php");
      exit;
   }
   
   $actual_status = $contest['status'];
   $start = strtotime($contest['start_time']);
   $end = strtotime($contest['end_time']);
   $registered_count = isset($contest['registered_count']) ? $contest['registered_count'] : 234;
   $registered_users = $db->get_contest_registered_users($contest_id);
   
   // Tekshirish: foydalanuvchi ro'yxatdan o'tganmi?
   $user_id = $_SESSION['id'];
   $is_registered = $db->get_data_by_table("contest_register", [
       'contest_id' => $contest_id, 
       'user_id' => $user_id
   ]);
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SamCoding</title>
    
</head>
<body>
    <?php include_once 'includes/novbar.php';?>

    <div class="container">
        <div style="margin-bottom: 1rem;">
            <a href="contests.php" class="btn btn-secondary">← Orqaga</a>
        </div>

        <div class="contest-header">
            <h1><?= htmlspecialchars($contest['title']) ?></h1>
            <p class="text-secondary" style="margin-bottom: 1rem;"><?= htmlspecialchars($contest['description']) ?></p>
        </div>

        <!-- Status Banner -->
        <?php if ($actual_status == 0): // Kutilmoqda ?>
        <div class="countdown-timer">
            <h2 style="margin-bottom: 1rem;">Musobaqa boshlanishiga:</h2>
            <div class="countdown-grid">
                <div class="countdown-item">
                    <span class="countdown-value" id="days">0</span>
                    <div class="countdown-label">Kun</div>
                </div>
                <div class="countdown-item">
                    <span class="countdown-value" id="hours">0</span>
                    <div class="countdown-label">Soat</div>
                </div>
                <div class="countdown-item">
                    <span class="countdown-value" id="minutes">0</span>
                    <div class="countdown-label">Daqiqa</div>
                </div>
                <div class="countdown-item">
                    <span class="countdown-value" id="seconds">0</span>
                    <div class="countdown-label">Soniya</div>
                </div>
            </div>
            <?php if (!$is_registered): ?>
            <form method="POST" id="contestRegisterform" action="contest-register.php">
                <input type="hidden" name="contestid" value="<?= $contest_id ?>">
                <input type="hidden" name="userid" value="<?= $_SESSION['id'] ?>">
                <button class="btn" style="margin-top: 1rem; background: white; color: #54eb36ff; font-weight: bold; border: none;">
                    Ro'yxatdan o'tish
                </button>
            </form>
            <?php else: ?>
            <div style="margin-top: 1rem; padding: 0.75rem; background: rgba(84, 235, 54, 0.2); border-radius: 0.5rem; color: black;">
                ✅ Siz musobaqadan ro'yxatdan o'tgansiz. 
            </div>
            <?php endif; ?>
        </div>
        
        <?php elseif ($actual_status == 1): // Faol ?>
        <div class="countdown-timer">
            <h2 style="margin-bottom: 1rem;">Musobaqa tugashiga:</h2>
            <div class="countdown-grid">
                <div class="countdown-item">
                    <span class="countdown-value" id="days">0</span>
                    <div class="countdown-label">Kun</div>
                </div>
                <div class="countdown-item">
                    <span class="countdown-value" id="hours">0</span>
                    <div class="countdown-label">Soat</div>
                </div>
                <div class="countdown-item">
                    <span class="countdown-value" id="minutes">0</span>
                    <div class="countdown-label">Daqiqa</div>
                </div>
                <div class="countdown-item">
                    <span class="countdown-value" id="seconds">0</span>
                    <div class="countdown-label">Soniya</div>
                </div>
            </div>
            <?php if (!$is_registered): ?>
            <form method="POST" id="contestRegisterform" action="contest-register.php">
                <input type="hidden" name="contestid" value="<?= $contest_id ?>">
                <input type="hidden" name="userid" value="<?= $_SESSION['id'] ?>">
                <button class="btn" style="margin-top: 1rem; background: white; color: #54eb36ff; font-weight: bold; border: none;">
                    Ro'yxatdan o'tish
                </button>
            </form>
            <?php else: ?>
            <div style="margin-top: 1rem; padding: 0.75rem; background: rgba(84, 235, 54, 0.2); border-radius: 0.5rem; color: black;">
                ✅ Siz musobaqada ishtirok etyapsiz ...
            </div>
            <?php endif; ?>
        </div>
        
        <?php elseif ($actual_status == 2): // Tugagan ?>
        <div class="countdown-timer" style="background: #dd380aff">
            <h2 style="margin-bottom: 1rem;">Musobaqa yakunlandi</h2>
            <p style="font-size: 1.2rem; margin: 1rem 0;">
                <?= date('j F Y, H:i', $end) ?> da tugagan
            </p>
            <?php if ($is_registered): ?>
            <div style="margin-top: 1rem; padding: 0.75rem; background: rgba(84, 235, 54, 0.2); border-radius: 0.5rem; color: black;">
                ✅ Siz musobaqada ishtirok etdingiz.
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="contest-tabs" style="margin-top: 2rem;">
            <button class="contest-tab active" onclick="switchTab('problems')">Masalalar</button>
            <button class="contest-tab" onclick="switchTab('participants')">Qatnashuvchilar</button>
        </div>

        <div id="problemsTab" class="tab-content active">
            <?php if ($actual_status >= 1): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 60px;">Status</th>
                            <th>Masala</th>
                            <th>Qiyinchiligi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contest_problems as $problem): ?>
                        <tr onclick="openContestProblems(<?=$problem['id']?>, <?=$problem['contest_id']?>)" style="cursor: pointer;">
                            <td style="font-size: 1.5rem; color: var(--text-secondary);">—</td>
                            <td>
                                <strong><?= htmlspecialchars($problem['title']) ?></strong>
                                <div style="margin-top: 0.5rem;">
                                    <?php
                                        $difficulty_badges = [
                                            'beginner' => 'badge-beginner',
                                            'easy' => 'badge-easy',
                                            'medium' => 'badge-medium',
                                            'hard' => 'badge-hard',
                                            'expert'=> 'badge-expert'
                                        ];
                                        $badge_class = $difficulty_badges[$problem['difficulty']] ?? 'badge-tag';
                                    ?>
                                    
                                </div>
                            </td>
                            <td>
                                <span class="badge <?= $badge_class ?>">
                                    <?= ucfirst($problem['difficulty']) ?>
                                </span>
                            </td>
                            
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="card" style="text-align: center; padding: 2rem;">
                <h3>Musobaqa hali boshlanmagan</h3>
                <p class="text-secondary">Musobaqa boshlanganda masalalar ko'rinadi</p>
            </div>
            <?php endif; ?>
        </div>

        <div id="participantsTab" class="tab-content">
            <h2 class="mb-1">Qatnashuvchilar</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th>Foydalanuvchi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($registered_users)): ?>
                            <?php foreach ($registered_users as $index => $user): ?>
                                <tr>
                                    <td><strong style="color: var(--primary);"><?= $index + 1 ?></strong></td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                                            <div class="user-avatar" style="width: 40px; height: 40px; font-size: 1rem;">
                                                <?= strtoupper(substr($user['fullname'], 0, 2)) ?>
                                            </div>
                                            <strong><?= htmlspecialchars($user['fullname']) ?></strong>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2" style="text-align: center; padding: 2rem;">
                                    <p class="text-secondary">Hali hech qanday qatnashuvchi yo'q</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
    </div>
    
    <?php include_once 'includes/footer.php';?>
  
    <script src="assets/js/change_style.js"></script>
    <script>
        function openContestProblems(problemId, contestId) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'contest-problems.php';
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'contestid';
            input.value = contestId;
            const input2 = document.createElement('input');
            input2.type = 'hidden';
            input2.name = 'problemid';
            input2.value = problemId;
            form.appendChild(input2);
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(event) {
                event.preventDefault(); 

                const formData = new FormData(this);

                fetch('contest-register.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastr.success(data.message,  {timeOut: 1000});
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        toastr.error(data.message, {timeOut: 1000});
                    }
                })
                .catch(error => {
                    console.error('Xatolik yuz berdi:', error);
                });
            });
        });
        
        // Contest ma'lumotlari
        const contestStatus = <?=$actual_status?>;
        const startTime = <?=$start?> * 1000;
        const endTime = <?=$end?> * 1000;
        
        function updateCountdown() {
            const now = new Date().getTime();
            let targetTime;
            
            if (contestStatus === 0) { 
                targetTime = startTime;
            } else if (contestStatus === 1) { 
                targetTime = endTime;
            } else {
                return; 
            }
            
            const distance = targetTime - now;
            
            if (distance < 0) {
                location.reload();
                return;
            }
            
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            const daysEl = document.getElementById('days');
            const hoursEl = document.getElementById('hours');
            const minutesEl = document.getElementById('minutes');
            const secondsEl = document.getElementById('seconds');
            
            if (daysEl) daysEl.textContent = days;
            if (hoursEl) hoursEl.textContent = hours.toString().padStart(2, '0');
            if (minutesEl) minutesEl.textContent = minutes.toString().padStart(2, '0');
            if (secondsEl) secondsEl.textContent = seconds.toString().padStart(2, '0');
        }
        
        if (contestStatus === 0 || contestStatus === 1) {
            updateCountdown();
            setInterval(updateCountdown, 1000);
        }
        
        function switchTab(tab) {
            document.querySelectorAll('.contest-tab').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');

            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            document.getElementById(tab + 'Tab').classList.add('active');
        }
    </script>
</body>
</html>