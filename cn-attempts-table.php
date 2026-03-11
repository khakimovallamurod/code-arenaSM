<?php
// cn-attempts-table.php
include_once 'config.php';
session_start();

if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
    exit;
}

$user_id = $_SESSION['id'];
$problem_id = intval($_POST['problemid']);
$contest_id = intval($_POST['contestid']);
$currentPage = isset($_POST['page']) ? intval($_POST['page']) : 1;

if ($currentPage < 1) $currentPage = 1;

$db = new Database();
$attempts = $db->get_contest_attempts_by_user($user_id, $problem_id, $contest_id);

$attemptsPerPage = 4;
$totalAttempts = count($attempts);
$totalPages = ceil($totalAttempts / $attemptsPerPage);

$startIndex = ($currentPage - 1) * $attemptsPerPage;
$visibleAttempts = array_slice($attempts, $startIndex, $attemptsPerPage);
?>

<?php if (empty($attempts)): ?>
    <div class="submission-item empty-attempts">
        <span><strong>Hali hech qanday urinish yo'q 😌</strong></span>
    </div>
<?php else: ?>
    <div id="attemptsListContainer">
    <?php foreach ($visibleAttempts as $index => $attempt): ?>
        <?php
            $status = $attempt['status'];
            $statusClass = 'status-badge ';
            $attemptNumber = $totalAttempts - ($startIndex + $index);

            if ($status === 'Accept') {
                $statusClass .= 'status-accepted';
            } elseif (strpos($status, 'Wrong Answer') !== false) {
                $statusClass .= 'status-wrong';
            } elseif (strpos($status, 'Runtime Error') !== false) {
                $statusClass .= 'status-wrong';
            } else {
                $statusClass .= 'status-error';
            }
        ?>
        <div class="submission-item" onclick="showCodeModal(<?= $attempt['attempt_id'] ?>)">
            <div class="attempt-main">
                <span class="attempt-number"><?= $attemptNumber ?></span>
                <span class="<?= $statusClass ?>">
                    <?php if ($status === 'Accept'): ?>
                        <?= htmlspecialchars($status); ?>
                    <?php else: ?>
                        <?= htmlspecialchars($status . " (test " . ($attempt['tests_passed']) . ")"); ?>
                    <?php endif; ?>
                </span>
            </div>
            <div class="attempt-meta">
                <span class="lang-badge">
                    <?= htmlspecialchars($attempt['language']); ?>
                </span>
                <span class="metric-value">
                    <?= intval($attempt['runTime']); ?> ms
                </span>
                <span class="metric-value">
                    <?= intval($attempt['memory'] / 1024); ?> KB
                </span>
                <span class="date-text">
                    <?= date('d.m.Y H:i', strtotime($attempt['created_at'])); ?>
                </span>
            </div>
        </div>
    <?php endforeach; ?>
    </div>

    <div id="paginationContainer" class="pagination-container attempts-pagination">
        <button type="button"
            onclick="if(<?= $currentPage ?> > 1){loadAttemptsPage(<?= $currentPage - 1; ?>)}"
            class="pagination-btn pagination-nav"
            <?= ($currentPage <= 1) ? 'disabled' : '' ?>>
            ← Previous
        </button>

        <div class="pagination-numbers">
            <?php
            $startPage = max(1, $currentPage - 2);
            $endPage = min($totalPages, $startPage + 4);
            if ($endPage - $startPage + 1 < 5 && $totalPages >= 5) {
                $startPage = max(1, $endPage - 4);
            }
            for ($i = $startPage; $i <= $endPage; $i++): ?>
                <button type="button"
                    onclick="loadAttemptsPage(<?= $i; ?>)"
                    class="pagination-btn <?= ($i === $currentPage) ? 'active' : ''; ?>">
                    <?= $i; ?>
                </button>
            <?php endfor; ?>
        </div>

        <button type="button"
            onclick="if(<?= $currentPage ?> < <?= $totalPages ?>){loadAttemptsPage(<?= $currentPage + 1; ?>)}"
            class="pagination-btn pagination-nav"
            <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>>
            Next →
        </button>
    </div>

    <div id="codeModal" class="code-modal-overlay" onclick="closeCodeModal(event)">
        <div class="code-modal-panel">
            <div class="code-modal-header">
                <h3>Kodni ko'rish</h3>
                <button type="button" class="code-modal-close" onclick="closeCodeModal()">
                    ×
                </button>
            </div>

            <pre class="code-modal-pre">
<code id="modalCodeContent"></code>
</pre>

            <div class="code-modal-footer">
                <button type="button" class="btn btn-primary" onclick="copyModalCode()">📋 Copy Code</button>
            </div>
        </div>
    </div>

    <script>
    async function showCodeModal(attemptId) {
        try {
            const response = await fetch('get_contest_attempt_code.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'attempt_id=' + attemptId
            });
            const code = await response.text();
            document.getElementById('modalCodeContent').textContent = code;
            document.getElementById('codeModal').style.display = 'block';
        } catch (error) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: "Kodni yuklashda xatolik yuz berdi",
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true
                });
            }
        }
    }

    function closeCodeModal(event) {
        if (event && event.target && event.target.id !== 'codeModal') return;
        document.getElementById('codeModal').style.display = 'none';
    }

    function copyModalCode() {
        const code = document.getElementById('modalCodeContent').textContent;

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(code)
                .then(() => {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: "Kod nusxalandi",
                            showConfirmButton: false,
                            timer: 2200,
                            timerProgressBar: true
                        });
                    }
                })
                .catch(() => fallbackCopy(code));
        } else {
            fallbackCopy(code);
        }
    }

    function fallbackCopy(text) {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.top = '-1000px';
        document.body.appendChild(textarea);
        textarea.select();
        try {
            document.execCommand('copy');
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: "Kod nusxalandi",
                    showConfirmButton: false,
                    timer: 2200,
                    timerProgressBar: true
                });
            }
        } catch (err) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: "Browser nusxa olishni qo‘llamaydi",
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true
                });
            }
        }
        document.body.removeChild(textarea);
    }
    </script>

<?php endif; ?>
