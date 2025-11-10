<?php
   session_start();
   include_once 'config.php';
   $db = new Database();
   if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
      header("Location: auth/login.php");
      exit;
   }
   
   date_default_timezone_set('Asia/Tashkent');
   $contest_id = isset($_POST['contestid']) ? intval($_POST['contestid']) : 8;
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
   
   // Urinishlar va natijalarni olish (agar musobaqa faol yoki tugagan bo'lsa)
   $contest_attempts = [];
   $leaderboard = [];
   if ($actual_status >= 1) {
       // Foydalanuvchining barcha urinishlarini olish
       $contest_attempts = $db->get_data_by_table_all("contest_attempts", 
           "WHERE contest_id = $contest_id AND user_id = $user_id ORDER BY created_at DESC");
       
       // Leaderboard ma'lumotlari
       $leaderboard = $db->get_reyting_by_user();
   }
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($contest['title']) ?> - SamCoding</title>
    <style>
        /* Contest Navigation Tabs */
        .contest-navigation {
            background: var(--bg-secondary, #fff);
            border-bottom: 2px solid var(--border-color, #e0e0e0);
            padding: 0;
            margin-top: 1rem;
            margin-bottom: 2rem;
            position: sticky;
            top: 60px;
            z-index: 50;
            overflow-x: auto;
            scrollbar-width: thin;
        }

        .contest-navigation::-webkit-scrollbar {
            height: 4px;
        }

        .contest-navigation::-webkit-scrollbar-thumb {
            background: var(--border-color, #e0e0e0);
            border-radius: 4px;
        }

        .contest-nav-container {
            display: flex;
            gap: 0;
            min-width: max-content;
        }

        .contest-nav-tab {
            padding: 1rem 1.5rem;
            background: transparent;
            border: none;
            color: var(--text-secondary, #666);
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            border-bottom: 3px solid transparent;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .contest-nav-tab:hover:not(.disabled) {
            color: var(--primary-color, #007bff);
            background: var(--bg-tertiary, #f5f5f5);
        }

        .contest-nav-tab.active {
            color: var(--primary-color, #007bff);
            border-bottom-color: var(--primary-color, #007bff);
            background: var(--bg-tertiary, #f5f5f5);
        }

        .contest-nav-tab.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
        }

        /* Tab Content */
        .contest-tab-content {
            display: none;
            animation: fadeIn 0.3s ease-in;
        }

        .contest-tab-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
            .contest-nav-tab {
                padding: 0.75rem 1rem;
                font-size: 0.9rem;
            }
        }
    </style>
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
            <div style="margin-top: 1rem; padding: 0.75rem; background: rgba(84, 235, 54, 0.2); border-radius: 0.5rem; color: white;">
                ✅ Siz ro'yxatdan o'tgansiz
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
            <div style="margin-top: 1rem; padding: 0.75rem; background: rgba(84, 235, 54, 0.2); border-radius: 0.5rem; color: white;">
                ✅ Siz ro'yxatdan o'tgansiz
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
            <div style="margin-top: 1rem; padding: 0.75rem; background: rgba(84, 235, 54, 0.2); border-radius: 0.5rem; color: white;">
                ✅ Siz ro'yxatdan o'tgansiz
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Contest Navigation Tabs -->
        <nav class="contest-navigation">
            <div class="contest-nav-container">
                <button class="contest-nav-tab active" onclick="switchContestTab('info')">
                    <span>📋</span> Ma'lumot
                </button>
                <button class="contest-nav-tab" onclick="switchContestTab('participants')">
                    <span>👥</span> Qatnashuvchilar
                </button>
                <button class="contest-nav-tab <?= $actual_status < 1 ? 'disabled' : '' ?>" 
                        onclick="switchContestTab('problems')" 
                        <?= $actual_status < 1 ? 'disabled' : '' ?>>
                    <span>🎯</span> Masalalar
                </button>
                <button class="contest-nav-tab <?= $actual_status < 1 ? 'disabled' : '' ?>" 
                        onclick="switchContestTab('attempts')"
                        <?= $actual_status < 1 ? 'disabled' : '' ?>>
                    <span>📝</span> Urinishlar
                </button>
                <button class="contest-nav-tab <?= $actual_status < 1 ? 'disabled' : '' ?>" 
                        onclick="switchContestTab('results')"
                        <?= $actual_status < 1 ? 'disabled' : '' ?>>
                    <span>🏆</span> Natijalar
                </button>
            </div>
        </nav>

        <!-- Ma'lumot Tab -->
        <div id="infoTab" class="contest-tab-content active">
            <div class="card">
                <h2 class="mb-1">Musobaqa haqida</h2>
                <div style="margin-top: 1rem;">
                    <p style="line-height: 1.8; color: var(--text-secondary);">
                        <?= nl2br(htmlspecialchars($contest['description'])) ?>
                    </p>
                </div>
                
                <div style="margin-top: 2rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <div style="padding: 1rem; background: var(--bg-tertiary); border-radius: 0.5rem;">
                        <div style="font-size: 0.875rem; color: var(--text-secondary);">Boshlanish vaqti</div>
                        <div style="font-weight: 600; margin-top: 0.5rem;">
                            <?= date('j F Y, H:i', $start) ?>
                        </div>
                    </div>
                    <div style="padding: 1rem; background: var(--bg-tertiary); border-radius: 0.5rem;">
                        <div style="font-size: 0.875rem; color: var(--text-secondary);">Tugash vaqti</div>
                        <div style="font-weight: 600; margin-top: 0.5rem;">
                            <?= date('j F Y, H:i', $end) ?>
                        </div>
                    </div>
                    <div style="padding: 1rem; background: var(--bg-tertiary); border-radius: 0.5rem;">
                        <div style="font-size: 0.875rem; color: var(--text-secondary);">Davomiyligi</div>
                        <div style="font-weight: 600; margin-top: 0.5rem;">
                            <?php
                                $duration = ($end - $start) / 3600;
                                if ($duration >= 24) {
                                    echo round($duration / 24, 1) . ' kun';
                                } else {
                                    echo round($duration, 1) . ' soat';
                                }
                            ?>
                        </div>
                    </div>
                    <div style="padding: 1rem; background: var(--bg-tertiary); border-radius: 0.5rem;">
                        <div style="font-size: 0.875rem; color: var(--text-secondary);">Masalalar soni</div>
                        <div style="font-weight: 600; margin-top: 0.5rem;">
                            <?= count($contest_problems) ?> ta
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Qatnashuvchilar Tab -->
        <div id="participantsTab" class="contest-tab-content">
            <h2 class="mb-1">Qatnashuvchilar (<?= count($registered_users) ?>)</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th>Foydalanuvchi</th>
                            <th>Ro'yxatdan o'tgan vaqt</th>
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
                                    <td>
                                        <span style="color: var(--text-secondary); font-size: 0.875rem;">
                                            <?= date('j F Y, H:i', strtotime($user['registered_at'])) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" style="text-align: center; padding: 2rem;">
                                    <p class="text-secondary">Hali hech qanday qatnashuvchi yo'q</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Masalalar Tab -->
        <div id="problemsTab" class="contest-tab-content">
            <?php if ($actual_status >= 1): ?>
            <h2 class="mb-1">Masalalar</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 60px;">Status</th>
                            <th>Muammo</th>
                            <th>Qiyinchiligi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contest_problems as $problem): ?>
                        <?php
                            // Check if user solved this problem
                            $user_solved = false;
                            foreach ($contest_attempts as $attempt) {
                                if ($attempt['problem_id'] == $problem['id'] && $attempt['status'] === 'Accept') {
                                    $user_solved = true;
                                    break;
                                }
                            }
                        ?>
                        <tr onclick="openContestProblems(<?=$problem['id']?>, <?=$problem['contest_id']?>)" style="cursor: pointer;">
                            <td style="font-size: 1.5rem;">
                                <?= $user_solved ? '<span style="color: #28a745;">✓</span>' : '<span style="color: var(--text-secondary);">—</span>' ?>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($problem['title']) ?></strong>
                            </td>
                            <td>
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

        <!-- Urinishlar Tab -->
        <div id="attemptsTab" class="contest-tab-content">
            <?php if ($actual_status >= 1): ?>
            <h2 class="mb-1">Mening urinishlarim</h2>
            <?php if (!empty($contest_attempts)): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Masala</th>
                            <th>Status</th>
                            <th>Til</th>
                            <th>Vaqt</th>
                            <th>Xotira</th>
                            <th>Yuborilgan vaqt</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contest_attempts as $attempt): ?>
                        <?php 
                            $problem = $db->get_data_by_table("contest_problems", ['id' => $attempt['problem_id']]);
                            $status = $attempt['status'];
                            $statusClass = 'status-badge ';
                            
                            if($status === 'Accept') {
                                $statusClass .= 'status-accepted';
                            } elseif(strpos($status, 'Wrong Answer') !== false || strpos($status, 'Runtime Error') !== false) {
                                $statusClass .= 'status-wrong';
                            } else {
                                $statusClass .= 'status-error';
                            }
                        ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($problem['title']) ?></strong></td>
                            <td>
                                <span class="<?= $statusClass ?>">
                                    <?php if ($status === 'Accept'): ?>
                                        <?= htmlspecialchars($status); ?>
                                    <?php else: ?>
                                        <?= htmlspecialchars($status . " (test " . ($attempt['tests_passed']) . ")"); ?>
                                    <?php endif; ?>
                                </span>
                            </td>
                            <td><span class="lang-badge"><?= htmlspecialchars($attempt['language']) ?></span></td>
                            <td><?= intval($attempt['runTime']) ?> ms</td>
                            <td><?= intval($attempt['memory']/1024) ?> KB</td>
                            <td style="color: var(--text-secondary); font-size: 0.875rem;">
                                <?= date('d.m.Y H:i', strtotime($attempt['created_at'])) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="card" style="text-align: center; padding: 2rem;">
                <h3>Hali urinishlar yo'q</h3>
                <p class="text-secondary">Masalalarni yechishni boshlang</p>
            </div>
            <?php endif; ?>
            <?php else: ?>
            <div class="card" style="text-align: center; padding: 2rem;">
                <h3>Musobaqa hali boshlanmagan</h3>
                <p class="text-secondary">Musobaqa boshlanganda urinishlaringiz ko'rinadi</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Natijalar Tab -->
        <div id="resultsTab" class="contest-tab-content">
            <?php if ($actual_status >= 1): ?>
            <h2 class="mb-1">Leaderboard</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 60px;">O'rin</th>
                            <th>Foydalanuvchi</th>
                            <th>Yechilgan masalalar</th>
                            <th>Ball</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($leaderboard)): ?>
                            <?php foreach ($leaderboard as $index => $entry): ?>
                                <tr <?= $entry['user_id'] == $user_id ? 'style="background: var(--bg-tertiary);"' : '' ?>>
                                    <td>
                                        <strong style="font-size: 1.2rem; color: var(--primary);">
                                            <?php
                                                if ($index == 0) echo '🥇';
                                                elseif ($index == 1) echo '🥈';
                                                elseif ($index == 2) echo '🥉';
                                                else echo $index + 1;
                                            ?>
                                        </strong>
                                    </td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                                            <div class="user-avatar" style="width: 40px; height: 40px; font-size: 1rem;">
                                                <?= strtoupper(substr($entry['fullname'], 0, 2)) ?>
                                            </div>
                                            <strong>
                                                <?= htmlspecialchars($entry['fullname']) ?>
                                                <?= $entry['user_id'] == $user_id ? ' (Siz)' : '' ?>
                                            </strong>
                                        </div>
                                    </td>
                                    <td><strong><?= $entry['solved_count'] ?> / <?= count($contest_problems) ?></strong></td>
                                    <td><strong style="color: var(--primary);"><?= $entry['total_score'] ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 2rem;">
                                    <p class="text-secondary">Hali natijalar yo'q</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="card" style="text-align: center; padding: 2rem;">
                <h3>Musobaqa hali boshlanmagan</h3>
                <p class="text-secondary">Musobaqa boshlanganda natijalar ko'rinadi</p>
            </div>
            <?php endif; ?>
        </div>
        
    </div>
    
    <?php include_once 'includes/footer.php';?>
  
    <script src="assets/js/change_style.js"></script>
    <script>
        function switchContestTab(tabName) {
            // Remove active class from all tabs
            document.querySelectorAll('.contest-nav-tab').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Add active class to clicked tab
            event.target.classList.add('active');

            // Hide all tab contents
            document.querySelectorAll('.contest-tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Show selected tab content
            document.getElementById(tabName + 'Tab').classList.add('active');
        }

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
    </script>
</body>
</html>