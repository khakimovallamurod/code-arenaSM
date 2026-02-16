<?php
    include_once 'config.php';
    session_start();
    if (!isset($_SESSION['id']) || empty($_SESSION['id']) ) {
        header("Location: auth/login.php");
        exit;
    }
   $user_id = $_SESSION['id'];
   $problem_id = intval($_GET['id']);
   $db = new Database();
   $solutions = $db->get_problem_by_id("problems",$problem_id);
   $test_examples = $db->get_data_by_table_all('tests', " where problem_id=$problem_id LIMIT 2");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SamCoding</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/dracula.min.css">
    <link rel="stylesheet" href="assets/css/styles-light.css">
    
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
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php include_once 'includes/novbar.php';?>
    
    <!-- Main Content -->
    <div class="container">
        <h1 class="mb-2"><?=$solutions['title'] ?></h1>
        <div class="problem-layout">
            <!-- Problem Statement -->
            <div class="problem-statement">
                <h2>Muammo bayoni</h2>
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
                    
                    <!-- IMPORTANT: Bu container ichiga attempts-table.php yuklanadi -->
                    <div id="attemptsTableContainer">
                        <?php 
                        // Bu yerda attempts-table.php include qilinadi
                        // attempts-table.php ichida attemptsListContainer va paginationContainer bo'ladi
                        include_once 'attempts-table.php'; 
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include_once 'includes/footer.php';?>
    
    <script src="assets/js/change_style.js"></script>
    <script src="assets/js/codeeditor.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/python/python.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/clike/clike.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/javascript/javascript.min.js"></script>
    <!-- SweetAlert2 (toastr uchun kerak) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function submitAttempt(event) {
            event.preventDefault();
            
            const user_id = document.querySelector("[name='user_id']").value;
            const problem_id = document.querySelector("[name='problem_id']").value;
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
            formData.append("language", language);
            formData.append("code", code);

            fetch("problem-insert.php", {
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
                
                if(data.success && data.attempt_id){
                    
                    // Judge jarayonini boshlaymiz
                    startJudgeProcess(data.attempt_id, language);
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
        function startJudgeProcess(attempt_id, language) {
            
            reloadAttemptsTable(1, () => {
                updateFirstAttemptToRunning(language);
            });
            
            // 3. Judge API ga so'rov yuboramiz
            const formData = new URLSearchParams();
            formData.append('attempt_id', attempt_id);
            
            fetch("codecheck.php", {
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
                
                if(result.success) {
                    
                    setTimeout(() => {
                        reloadAttemptsTable(1);
                    }, 500);
                } else {
                    console.error("❌ Judge xatolik:", result.message);
                    // Xatolik bo'lsa ham jadvalni yangilaymiz
                    reloadAttemptsTable(1);
                }
            })
            .catch(err => {
                console.error("❌ Judge xatolik:", err);
                
                // Xatolik bo'lsa ham jadvalni yangilaymiz
                setTimeout(() => {
                    reloadAttemptsTable(1);
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

        // Birinchi attemptni "Running..." holatiga o'zgartirish
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
        function reloadAttemptsTable(page = 1, callback = null) {
            const problem_id = document.querySelector("[name='problem_id']").value;
            
            // Loading holatini ko'rsatish
            const container = document.getElementById('attemptsTableContainer');
            if(container) {
                container.classList.add('loading');
            }
            
            fetch(`attempts-table.php?id=${problem_id}&page=${page}`)
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
            reloadAttemptsTable(page);
        }
    </script>
</body>
</html>