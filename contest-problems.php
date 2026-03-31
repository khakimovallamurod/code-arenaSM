<?php
    include_once 'config.php';
    session_start();
    if (!isset($_SESSION['id']) || empty($_SESSION['id']) ) {
        header("Location: auth/login.php");
        exit;
    }
   $user_id = $_SESSION['id'];
   $problem_id = intval($_POST['problemid']); 
   $contest_id = intval($_POST['contestid']);

   $db = new Database();

   // Verify contest is active and user is registered
   $contest_info = $db->get_data_by_table("contests", ['id' => $contest_id]);
   if (!$contest_info || intval($contest_info['status']) < 1) {
       header("Location: contests.php");
       exit;
   }
   $register_data = $db->is_register_user($contest_id, $user_id);
   $is_registered = (is_array($register_data) && array_key_exists('status', $register_data) && intval($register_data['status']) === 1);
   if (!$is_registered) {
       header("Location: contests.php");
       exit;
   }

   $solutions = $db->get_data_by_table("contest_problems",['contest_id'=>$contest_id, 'id'=>$problem_id]);
   $test_examples = $db->get_data_by_table_all('contest_tests', " where cn_problem_id=$problem_id LIMIT 2");
   $contest_problems = $db->get_data_by_table_all('contest_problems', " where contest_id=$contest_id ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SamCoding</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/dracula.min.css">
    <link rel="stylesheet" href="assets/css/styles-light.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <style>
        .loading-spinner {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin-right: 5px;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        #contestattemptsTableContainer.loading {
            opacity: 0.6;
            pointer-events: none;
        }
        .contest-problem-main-title {
            font-size: 1.25rem;
            font-weight: 700;
            line-height: 1.35;
            margin-bottom: 0.65rem;
            letter-spacing: 0;
            color: var(--text);
        }
        .contest-problem-desc {
            font-size: 0.98rem;
            line-height: 1.7;
            color: #1f2937;
        }
        .problem-section-title {
            margin-top: 1.35rem !important;
            margin-bottom: 0.35rem !important;
            font-size: 0.95rem !important;
            font-weight: 700;
            letter-spacing: 0.01em;
            text-transform: uppercase;
            color: #334155;
        }
        .problem-statement pre {
            font-size: 0.88rem;
            line-height: 1.45;
        }
        .contest-problems-page {
            --sidebar-width: 300px;
        }
        .contest-problems-page .problems-sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border-right: 1px solid #e2e8f0;
            box-shadow: 8px 0 24px rgba(15, 23, 42, 0.05);
        }
        .contest-problems-page .container {
            width: calc(100% - var(--sidebar-width));
            max-width: none;
            margin: 0 0 0 var(--sidebar-width);
            padding: 1.5rem;
        }
        .contest-problems-page .problem-layout {
            grid-template-columns: minmax(0, 1.1fr) minmax(360px, 0.9fr);
            gap: 1.5rem;
            align-items: start;
        }
        .contest-problems-page .problem-layout > * {
            min-width: 0;
        }
        .problem-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.05);
            padding: 0.85rem 0.9rem;
            margin-bottom: 0.6rem;
        }
        .problem-card:hover {
            transform: translateX(3px);
            border-color: #86efac;
            box-shadow: 0 6px 14px rgba(16, 185, 129, 0.14);
        }
        .problem-card.active {
            background: #ffffff;
            border-color: #22c55e;
            box-shadow: 0 8px 16px rgba(34, 197, 94, 0.16);
            color: #0f172a;
        }
        .problem-card.active .sidebar-problem-title,
        .problem-card.active .sidebar-problem-meta {
            color: #0f172a;
        }
        .problem-card.active .difficulty-badge {
            background: #e2e8f0 !important;
            color: #334155 !important;
        }
        .sidebar-problem-title {
            font-size: 0.96rem;
            font-weight: 650;
            color: #0f172a;
            margin-bottom: 0.45rem;
            line-height: 1.35;
        }
        .sidebar-problem-meta {
            display: flex;
            gap: 0.4rem;
            color: #64748b;
        }
        @media (max-width: 1280px) {
            .contest-problems-page {
                --sidebar-width: 270px;
            }
            .contest-problems-page .problem-layout {
                grid-template-columns: minmax(0, 1fr) minmax(320px, 0.95fr);
            }
        }
        @media (max-width: 1024px) {
            .contest-problems-page {
                --sidebar-width: 250px;
            }
            .contest-problems-page .container {
                padding: 1.2rem;
            }
            .contest-problems-page .problem-layout {
                grid-template-columns: 1fr;
            }
        }
        @media (max-width: 900px) {
            .contest-problem-main-title {
                font-size: 1.15rem;
            }
            .contest-problem-desc {
                font-size: 0.94rem;
            }
            .problem-section-title {
                font-size: 0.88rem !important;
            }
            .problem-statement pre {
                font-size: 0.82rem;
            }
        }
        @media (max-width: 768px) {
            .contest-problems-page {
                --sidebar-width: 250px;
            }
        }
        @media (max-width: 576px) {
            .contest-problems-page .container {
                width: 100%;
                margin-left: 0;
            }
        }
    </style>
</head>
<body class="contest-problems-page">
    <!-- Navbar -->
    <?php include_once 'includes/novbar.php';?>
    
    <!-- Problems Sidebar -->
    <aside class="problems-sidebar">
        <div class="sidebar-header">
        </div>
        
        <?php foreach ($contest_problems as $index => $prob): ?>
            <?php
                // Masala holatini aniqlash (solved, attempted, unsolved)
                $prob_attempts = $db->get_data_by_table('contest_reyting', ['user_id'=>$user_id, 'problem_id'=>$prob['id'], 'contest_id'=>$contest_id]);
                $status = 'unsolved';
                $statusIcon = '○';
                if (!empty($prob_attempts)) {
                    if (intval($prob_attempts['solved']) === 1) {
                        $status = 'solved';
                        $statusIcon = '✓';
                    } else {
                        $status = 'attempted';
                        $statusIcon = '◐';
                    }
                }
                
                $isActive = ($prob['id'] == $problem_id) ? 'active' : '';
            ?>
             
            <form action="" method="POST" class="problem-card-form">
                <input type="hidden" name="problemid" value="<?= $prob['id'] ?>">
                <input type="hidden" name="contestid" value="<?= $contest_id ?>">
                
                <button type="submit" class="problem-card <?= $isActive ?>">
                    <div class="problem-card-header">
                        <span class="problem-number"><?= chr(65 + $index)?></span>
                        <span class="problem-status status-<?= $status ?>"><?= $statusIcon ?></span>
                    </div>
                    <div class="sidebar-problem-title"><?= htmlspecialchars($prob['title']) ?></div>
                    <div class="sidebar-problem-meta">
                        <span class="difficulty-badge difficulty-<?= $prob['difficulty'] ?>">
                            <?= ucfirst($prob['difficulty']) ?>
                        </span>
                    </div>
                </button>
            </form>
        <?php endforeach; ?>
    </aside>
    
    <!-- Main Content -->
    <div class="container">
        <div style="margin-bottom: 1rem;" onclick="openContestDetail(<?=$contest_id?>)">
            <button class="btn btn-secondary">← Orqaga</button>
        </div>
        <div class="problem-layout">
            <!-- Problem Statement -->
            <div class="problem-statement">
                <h2 class="contest-problem-main-title"><?= htmlspecialchars($solutions['title']) ?></h2>
                <p class="contest-problem-desc"><?= nl2br(htmlspecialchars($solutions['descript'])) ?></p>
                <h3 class="problem-section-title">INPUT:</h3>
                <pre><?= htmlspecialchars($solutions['input_format']) ?></pre>

                <h3 class="problem-section-title">OUTPUT:</h3>
                <pre><?= htmlspecialchars($solutions['output_format']) ?></pre>

                <?php foreach ($test_examples as $index => $test): ?>
                <h3 class="problem-section-title">Example <?= $index + 1 ?>:</h3>
                <pre><strong>Input</strong>:
<?= htmlspecialchars($test['input']) ?>

<strong>Output</strong>:
<?= htmlspecialchars($test['output']) ?></pre>
                <?php endforeach; ?>
                <h3 class="problem-section-title">Constraints:</h3>
                <ul>
                    <li><?= htmlspecialchars($solutions['constraints']) ?></li>
                </ul>
                <div class="problem-meta-tags">
                    <span class="badge badge-<?= htmlspecialchars($solutions['difficulty']) ?>"><?= ucfirst(htmlspecialchars($solutions['difficulty'])) ?></span>
                    <span class="badge badge-<?= htmlspecialchars($solutions['category']) ?>"><?= ucfirst(htmlspecialchars($solutions['category'])) ?></span>
                </div>
                <?php if (!empty($solutions['izoh'])): ?>
                <div class="problem-note">
                    <strong>💡 Izoh:</strong> <?= nl2br(htmlspecialchars($solutions['izoh'])) ?>
                </div>
                <?php endif; ?>
            </div>
            <!-- Code Editor Section -->
            <div>
                <form id="attemptForm" onsubmit="submitAttempt(event)">
                    <input type="hidden" name="user_id" value="<?= $user_id; ?>">
                    <input type="hidden" name="problem_id" value="<?= $problem_id; ?>">
                    <input type="hidden" name="contest_id" value="<?= $contest_id; ?>">
                    <div class="code-section">
                        <div class="editor-header">
                            <div class="editor-language">
                                <label for="languageSelect">Dasturlash tili</label>
                                <select id="languageSelect" onchange="changeLanguage()" name="language" required>
                                    <option value="python">Python 3.10.0</option>
                                    <option value="python2">Python 2.7.18</option>
                                    <option value="java">Java 15.0.2</option>
                                    <option value="cpp">C++ (GCC 10.2.0)</option>
                                    <option value="c">C (GCC 10.2.0)</option>
                                    <option value="csharp">C# 6.12.0</option>
                                    <option value="javascript">JavaScript (Node.js 18.15.0)</option>
                                    <option value="typescript">TypeScript 5.0.3</option>
                                    <option value="php">PHP 8.2.3</option>
                                    <option value="go">Go 1.16.2</option>
                                    <option value="kotlin">Kotlin 1.8.20</option>
                                    <option value="rust">Rust 1.68.2</option>
                                    <option value="ruby">Ruby 3.0.1</option>
                                    <option value="swift">Swift 5.3.3</option>
                                    <option value="r">R (4.1.1)</option>
                                    <option value="scala">Scala 3.2.2</option>
                                </select>
                            </div>
                            <div class="editor-actions">
                                <button class="btn btn-success" id="submitBtn">Submit</button>
                            </div>
                        </div>
                        <textarea id="codeEditor" name="code"></textarea>
                    </div>
                </form>
                <!-- Submission History -->
                <div class="code-section" style="margin-top: 1rem;">
                    <h3 class="mb-1 attempts-title">Oxirgi urinishlar</h3>
                    <div id="contestattemptsTableContainer">
                        <?php 
                        // Bu yerda attempts-table.php include qilinadi
                        include_once 'cn-attempts-table.php'; 
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include_once 'includes/footer.php';?>
    
    <script src="assets/js/codeeditor.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/python/python.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/clike/clike.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/javascript/javascript.min.js"></script>
    <!-- SweetAlert2 (toastr uchun kerak) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

        function submitAttempt(event) {
            event.preventDefault();
            
            const user_id = document.querySelector("[name='user_id']").value;
            const problem_id = document.querySelector("[name='problem_id']").value;
            const contest_id = document.querySelector("[name='contest_id']").value;
            const language = document.querySelector("[name='language']").value;
            const code = editor.getValue();
            
            const submitBtn = document.getElementById('submitBtn');

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Sending...';
            }

            const formData = new FormData();
            formData.append("user_id", user_id);
            formData.append("problem_id", problem_id);
            formData.append("contest_id", contest_id);
            formData.append("language", language);
            formData.append("code", code);

            fetch("contest-problemadd.php", {
                method: "POST",
                body: formData
            })
            .then(response => {
                if (!response.ok) throw new Error("Serverdan noto'g'ri javob qaytdi");
                return response.json();
            })
            .then(data => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Submit';
                }

                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true
                });

                Toast.fire({
                    icon: data.success ? 'success' : 'error',
                    title: data.message
                });
                
                // Agar muvaffaqiyatli bo'lsa
                if(data.success && data.attempt_id){                    
                    // Judge jarayonini boshlaymiz
                    startJudgeProcess(data.attempt_id, language, problem_id, contest_id);
                }
            })
            .catch(error => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Submit';
                }

                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true
                });

                Toast.fire({
                    icon: 'error',
                    title: "❌ So'rovda xatolik: " + error.message
                });
            });
        }

        // Judge jarayonini boshlash
        function startJudgeProcess(attempt_id, language, problem_id, contest_id) {
            // 1. Avval attempts jadvalini yuklaymiz (yangi attempt ko'rinadi)
            reloadAttemptsTable(1, problem_id, contest_id, () => {
                // 2. Jadval yuklangandan keyin birinchi attemptni "Running..." ga o'zgartiramiz
                updateFirstAttemptToRunning(language);
            });
            
            // 3. Judge API ga so'rov yuboramiz
            const formData = new URLSearchParams();
            formData.append('attempt_id', attempt_id);
            
            fetch("cn_codecheck.php", {
                method: "POST",
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) throw new Error("Judge serverdan xatolik");
                return response.json();
            })
            .then(result => {                
                // 4. Judge tugagandan keyin attempts jadvalini qayta yuklaymiz
                if(result.success) {
                    const isAccepted = (result.status || '').toLowerCase() === 'accept';
                    if (isAccepted) {
                        setTimeout(() => {
                            window.location.reload();
                        }, 500);
                    } else {
                        // 500ms kutib jadvalini yangilaymiz (animatsiya uchun)
                        setTimeout(() => {
                            reloadAttemptsTable(1, problem_id, contest_id);
                        }, 500);
                    }
                } else {
                    console.error("❌ Judge xatolik:", result.message);
                    // Xatolik bo'lsa ham jadvalni yangilaymiz
                    reloadAttemptsTable(1, problem_id, contest_id);
                }
            })
            .catch(err => {
                console.error("❌ Judge xatolik:", err);
                
                setTimeout(() => {
                    reloadAttemptsTable(1, problem_id, contest_id);
                }, 500);
                
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });

                Toast.fire({
                    icon: 'error',
                    title: "❌ Judge jarayonida xatolik yuz berdi"
                });
            });
        }

        function updateFirstAttemptToRunning(language) {
            const listContainer = document.getElementById("attemptsListContainer");
            
            if(!listContainer) {
                console.error("❌ attemptsListContainer topilmadi");
                return;
            }
            
            const firstAttempt = listContainer.querySelector(".submission-item");
            
            if(!firstAttempt) {
                console.error("❌ Birinchi attempt topilmadi");
                return;
            }

            const attemptNumber = firstAttempt.querySelector(".attempt-number")?.textContent?.trim() || "1";
                        
            // Birinchi attemptni "Running..." ga o'zgartiramiz
            const currentTime = new Date().toLocaleString('en-GB', { 
                day: '2-digit', 
                month: '2-digit', 
                year: 'numeric', 
                hour: '2-digit', 
                minute: '2-digit' 
            }).replace(',', '');
            
            firstAttempt.innerHTML = `
                <div class="attempt-main">
                    <span class="attempt-number">${attemptNumber}</span>
                    <span class="status-badge status-error">
                        <span class="loading-spinner"></span> Running...
                    </span>
                </div>
                <div class="attempt-meta">
                    <span class="lang-badge">
                        ${language}
                    </span>
                    <span class="metric-value">
                        <span class="loading-dots">...</span> ms
                    </span>
                    <span class="metric-value">
                        <span class="loading-dots">...</span> KB
                    </span>
                    <span class="date-text">
                        ${currentTime}
                    </span>
                </div>
            `;
        }

        // Attempts jadvalini reload qilish funksiyasi
        function reloadAttemptsTable(page = 1, problem_id = null, contest_id = null, callback = null) {
            // Agar problem_id va contest_id berilmagan bo'lsa, formdan olamiz
            if(!problem_id) {
                problem_id = document.querySelector("[name='problem_id']").value;
            }
            if(!contest_id) {
                contest_id = document.querySelector("[name='contest_id']").value;
            }
                        
            // Loading holatini ko'rsatish
            const container = document.getElementById('contestattemptsTableContainer');
            if(container) {
                container.classList.add('loading');
            }
            
            // POST orqali yuborish
            const formData = new URLSearchParams();
            formData.append('problemid', problem_id);
            formData.append('contestid', contest_id);
            formData.append('page', page);
            
            fetch('cn-attempts-table.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: formData
            })
                .then(response => response.text())
                .then(html => {
                    if(container) {
                        container.innerHTML = html;
                        container.classList.remove('loading');                        
                        // Callback funksiyani chaqirish (agar mavjud bo'lsa)
                        if(callback && typeof callback === 'function') {
                            callback();
                        }
                    }
                })
                .catch(error => {
                    console.error('❌ Attempts jadvalini yuklashda xatolik:', error);
                    if(container) {
                        container.classList.remove('loading');
                    }
                });
        }


        // Pagination uchun sahifa yuklovchi funksiya
        function loadAttemptsPage(page) {
            const problem_id = document.querySelector("[name='problem_id']").value;
            const contest_id = document.querySelector("[name='contest_id']").value;
            reloadAttemptsTable(page, problem_id, contest_id);
        }
    </script>
</body>
</html>
