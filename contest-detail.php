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

   $reying_users = $db->get_contest_reyting_by_user($contest_id);
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
            <button class="contest-tab" onclick="switchTab('results')">Natijalar</button>
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
        <!-- Natijalar Tab Content - Mavjud resultsTab div ichiga qo'ying -->
        <div id="resultsTab" class="tab-content">
            <h2 class="mb-1">Natijalar jadvali</h2>
            
            <?php if ($actual_status < 1 || !$is_registered): ?>
                <div class="card" style="text-align: center; padding: 2rem;">
                    <h3>Natijalarni ko'rish uchun</h3>
                    <p class="text-secondary">
                        <?php if ($actual_status < 1): ?>
                            Musobaqa boshlanishini va ro'yxatdan o'tishingizni kutamiz
                        <?php else: ?>
                            Musobaqadan ro'yxatdan o'ting
                        <?php endif; ?>
                    </p>
                </div>
            <?php else: ?>
                <div class="table-container" style="overflow-x: auto;">
                    <table style="min-width: 800px;">
                        <thead>
                            <tr>
                                <th style="width: 50px; position: sticky; left: 0; background: var(--bg-primary); z-index: 10;">#</th>
                                <th style="width: 200px; position: sticky; left: 50px; background: var(--bg-primary); z-index: 10;">Foydalanuvchi</th>
                                <?php foreach ($contest_problems as $index=>$problem): ?>
                                    <th style="width: 120px; text-align: center;">
                                        <?= chr(65 + $index) ?>
                                    </th>
                                <?php endforeach; ?>
                                <th style="width: 100px; text-align: center;">Jami Ball</th>
                                <th style="width: 100px; text-align: center;">Ishlangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $rank = 1;
                            foreach ($reying_users as $user): 
                                $total_score = $user['total_score'];
                                $solved_count = $user['solved_count'];
                            ?>
                            <tr>
                                <td style="position: sticky; left: 0; background: var(--bg-primary); z-index: 9;">
                                    <strong style="color: var(--primary);"><?= $rank++ ?></strong>
                                </td>
                                <td style="position: sticky; left: 50px; background: var(--bg-primary); z-index: 9;">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div class="user-avatar" style="width: 35px; height: 35px; font-size: 0.9rem;">
                                            <?= strtoupper(substr($user['fullname'], 0, 2)) ?>
                                        </div>
                                        <strong><?= htmlspecialchars($user['fullname']) ?></strong>
                                    </div>
                                </td>
                                <?php foreach ($contest_problems as $idx => $problem): 
                                    $check_solved_problem = $db->check_problem_solved($user['user_id'], $problem['id'], $contest_id);
                                    
                                    if ($check_solved_problem == NULL) {
                                        $status = 'not_attempted';
                                        $attempts = 0;
                                    } elseif ($check_solved_problem['solved'] == 0) {
                                        $status = 'attempted';
                                        $attempts = $check_solved_problem['attempted'];
                                    } else {
                                        $status = 'accepted';
                                        $attempts = $check_solved_problem['attempted'];
                                    }
                                ?>
                                    <td style="text-align: center; padding: 0;">
                                        <?php
                                            $cell_color = 'transparent';
                                            $text_content = '—';
                                            $attempts_text = '';
                                            
                                            if ($status == 'accepted') {
                                                $cell_color = 'rgba(34, 197, 94, 0.15)';
                                                $text_content = "✓";
                                                $attempts_text = $attempts > 0 ? "($attempts)" : '';
                                            } elseif ($status == 'attempted') {
                                                $cell_color = 'rgba(239, 68, 68, 0.15)';
                                                $text_content = '×';
                                                $attempts_text = $attempts > 0 ? "($attempts)" : '';
                                            }
                                        ?>
                                        <div style="background: <?= $cell_color ?>; padding: 0.75rem; min-height: 50px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                                            <span style="font-size: 1.5rem; font-weight: bold;">
                                                <?= $text_content ?>
                                            </span>
                                            <?php if ($attempts_text): ?>
                                                <span style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.25rem;">
                                                    <?= $attempts_text ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                <?php endforeach; ?>
                                <td style="text-align: center;">
                                    <strong style="color: var(--primary); font-size: 1.1rem;">
                                        <?= $total_score ?>
                                    </strong>
                                </td>
                                <td style="text-align: center;">
                                    <strong style="color: #22c55e;">
                                        <?= $solved_count ?> / <?= count($contest_problems) ?>
                                    </strong>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div style="margin-top: 1.5rem; padding: 1rem; background: var(--bg-secondary); border-radius: 0.5rem;">
                    <h4 style="margin-bottom: 0.75rem;">Izoh:</h4>
                    <div style="display: flex; gap: 1.5rem; flex-wrap: wrap;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <div style="width: 30px; height: 30px; background: rgba(34, 197, 94, 0.15); border-radius: 0.25rem; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">✓</div>
                            <span>To'g'ri ishlangan</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <div style="width: 30px; height: 30px; background: rgba(239, 68, 68, 0.15); border-radius: 0.25rem; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">×</div>
                            <span>Xato urinish</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <div style="width: 30px; height: 30px; background: transparent; border: 1px solid var(--border); border-radius: 0.25rem; display: flex; align-items: center; justify-content: center;">—</div>
                            <span>Urinish yo'q</span>
                        </div>
                    </div>
                    <p style="margin-top: 0.75rem; font-size: 0.9rem; color: var(--text-secondary);">
                        Qavs ichidagi raqamlar urinishlar sonini bildiradi
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include_once 'includes/footer.php';?>
  
    <script src="assets/js/change_style.js"></script>
    <script>
        // 
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