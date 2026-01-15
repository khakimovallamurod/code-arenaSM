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
    <script src="assets/js/change_style.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/dracula.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

</head>
<body>
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
                    <div class="problem-title"><?= htmlspecialchars($prob['title']) ?></div>
                    <div class="problem-meta">
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
                <h2 class="mb-2"><?=$solutions['title'] ?></h2>
                <p><?=$solutions['descript'] ?></p>  
                <h3 style="margin-top: 2rem;">INPUT:</h3>  
                <pre><?=$solutions['input_format'] ?></pre>

                <h3 style="margin-top: 2rem;">OUTPUT:</h3>  
                <pre><?=$solutions['output_format'] ?></pre>

                <?php foreach ($test_examples as $index => $test): ?>
                <h3 style="margin-top: 2rem;">Example <?=$index+1?>:</h3>
                <pre>
<strong>Input</strong>:</br><?=$test['input']?>
</br><strong>Output</strong>:</br><?=$test['output']?>
                </pre>
                <?php endforeach ; ?>
                <h3 style="margin-top: 2rem;">Constraints:</h3>
                <ul>
                    <li><?=$solutions['constraints'] ?></li>
                </ul>
                <div style="margin-top: 2rem;">
                    <span class="badge badge-<?=$solutions['difficulty'] ?>"><?=ucfirst($solutions['difficulty'])?></span>
                    <span class="badge badge-<?=$solutions['category']?>"><?= ucfirst($solutions['category']) ?></span>
                </div>
                <div style="margin-top: 2rem; padding: 1rem; background: var(--bg-tertiary); border-radius: 0.5rem;">
                    <strong>💡 Izoh:</strong><?=$solutions['izoh'] ?>
                </div>
            </div>
            <!-- Code Editor Section -->
            <div>
                <form id="attemptForm" onsubmit="submitAttempt(event)">
                    <input type="hidden" name="user_id" value="<?= $user_id; ?>">
                    <input type="hidden" name="problem_id" value="<?= $problem_id; ?>">
                    <input type="hidden" name="contest_id" value="<?= $contest_id; ?>">
                    <div class="code-section">
                        <div class="editor-header">
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
                            <div class="editor-actions">
                                <button class="btn btn-success" id="submitBtn">Submit</button>
                            </div>
                        </div>
                        <textarea id="codeEditor" name="code"></textarea>
                    </div>
                </form>
                <!-- Submission History -->
                <div class="code-section" style="margin-top: 1rem;">
                    <h3 class="mb-1">Oxirgi urinishlar</h3>
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
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
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
                    // 500ms kutib jadvalini yangilaymiz (animatsiya uchun)
                    setTimeout(() => {
                        reloadAttemptsTable(1, problem_id, contest_id);
                    }, 500);
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
                        
            // Birinchi attemptni "Running..." ga o'zgartiramiz
            const currentTime = new Date().toLocaleString('en-GB', { 
                day: '2-digit', 
                month: '2-digit', 
                year: 'numeric', 
                hour: '2-digit', 
                minute: '2-digit' 
            }).replace(',', '');
            
            firstAttempt.innerHTML = `
                <div style="display: flex; align-items: center; gap: 0.8rem;">
                    <span class="status-badge status-error" 
                        style="align-items: center; display: flex; justify-content: center; min-width: 100px;">
                        <span class="loading-spinner"></span> Running...
                    </span>
                </div>
                <div style="display: flex; align-items: center; justify-content: center; gap: 1.5rem;">
                    <span class="lang-badge" 
                        style="padding: 4px 10px; border-radius: 6px; font-size: 14px;">
                        ${language}
                    </span>
                    <span class="metric-value" style="font-weight: 500;">
                        <span class="loading-dots">...</span> ms
                    </span>
                    <span class="metric-value" style="font-weight: 500;">
                        <span class="loading-dots">...</span> KB
                    </span>
                    <span class="date-text" style="font-size: 14px;">
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