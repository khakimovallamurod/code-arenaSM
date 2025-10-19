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
   $attempts = $db->get_attempts_by_user($user_id, $problem_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SamCoding</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/dracula.min.css">
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
                    <?php if(empty($attempts)){ ?>
                        <div class="submission-item">
                            <span><strong>Hali hech qanday urinish yo'q 😌</strong></span>
                        </div>
                    <?php } ?>
                    <?php
                    $attemptsPerPage = 4;

                    $problem_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
                    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    if ($currentPage < 1) $currentPage = 1;

                    $totalAttempts = count($attempts);
                    $totalPages = ceil($totalAttempts / $attemptsPerPage);

                    $startIndex = ($currentPage - 1) * $attemptsPerPage;

                    $visibleAttempts = array_slice($attempts, $startIndex, $attemptsPerPage);
                    ?>

                    <!-- Attemptlar ro‘yxati -->
                    <div>
                    <?php foreach ($visibleAttempts as $attempt): ?>
                        <?php 
                            $status = $attempt['status'];
                            $statusClass = 'status-badge ';
                            
                            if($status === 'Accept') {
                                $statusClass .= 'status-accepted';
                            } elseif(strpos($status, 'Wrong Answer') !== false) {
                                $statusClass .= 'status-wrong';
                            } elseif(strpos($status, 'Runtime Error') !== false) {
                                $statusClass .= 'status-wrong';
                            } else {
                                $statusClass .= 'status-error';
                            }
                        ?>
                        <div class="submission-item" 
                            style="display: flex; justify-content: space-between; align-items: center; padding: 10px 15px; border-bottom: 1px solid #eee;">
                            <div style="display: flex; align-items: center; gap: 0.8rem;">
                                <span class="<?= $statusClass ?>" 
                                    style="align-items: center; display: flex; justify-content: center; min-width: 100px;">
                                    <?php if ($status === 'Accept'): ?>
                                        <?= htmlspecialchars($status); ?>
                                    <?php else: ?>
                                        <?= htmlspecialchars($status . " (test " . ($attempt['tests_passed']) . ")"); ?>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <div style="display: flex; align-items: center; justify-content: center; gap: 1.5rem;">
                                <span class="lang-badge" 
                                    style="padding: 4px 10px; border-radius: 6px; font-size: 14px;">
                                    <?= htmlspecialchars($attempt['language']); ?>
                                </span>
                                <span class="metric-value" style="font-weight: 500;">
                                    <?= intval($attempt['runTime']); ?> ms
                                </span>
                                <span class="metric-value" style="font-weight: 500;">
                                    <?= intval($attempt['memory']/1024); ?> KB
                                </span>
                                <span class="date-text" style="font-size: 14px;">
                                    <?= date('d.m.Y H:i', strtotime($attempt['created_at'])); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <div style="display: flex; justify-content: center; gap: 0.5rem; margin-top: 2rem; margin-bottom: 3rem;">
                        <!-- Previous tugmasi -->
                        <?php if ($currentPage > 1): ?>
                            <a href="?id=<?= $problem_id ?>&page=<?= $currentPage - 1; ?>" class="btn btn-secondary">← Previous</a>
                        <?php else: ?>
                            <button class="btn btn-secondary" disabled>← Previous</button>
                        <?php endif; ?>

                        <!-- Sahifa raqamlari -->
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?id=<?= $problem_id ?>&page=<?= $i; ?>" 
                            class="btn <?= ($i === $currentPage) ? 'btn-primary' : 'btn-secondary'; ?>">
                            <?= $i; ?>
                            </a>
                        <?php endfor; ?>

                        <!-- Next tugmasi -->
                        <?php if ($currentPage < $totalPages): ?>
                            <a href="?id=<?= $problem_id ?>&page=<?= $currentPage + 1; ?>" class="btn btn-secondary">Next →</a>
                        <?php else: ?>
                            <button class="btn btn-secondary" disabled>Next →</button>
                        <?php endif; ?>
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
            
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Sending...';
            }

            const formData = new FormData();
            formData.append("user_id", user_id);
            formData.append("problem_id", problem_id);
            formData.append("language", language);
            formData.append("code", code);

            fetch("codecheck.php", {
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
                setTimeout(() => {
                    location.reload();
                }, 2000);
            })
            .catch(error => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Submit';
                }
                // Xatolik holatida ham Toast chiqadi
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true
                });

                Toast.fire({
                    icon: 'error',
                    title: "❌ So‘rovda xatolik: " + error.message
                });
            });
        }
    </script>
</body>
</html>