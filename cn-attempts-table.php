<?php
// cn-attempts-table.php
include_once 'config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
                <div class="code-modal-title-wrap">
                    <span class="code-modal-icon">💻</span>
                    <h3>Kodni ko'rish</h3>
                </div>
                <button type="button" class="code-modal-close" onclick="closeCodeModal()" title="Yopish">×</button>
            </div>

            <pre class="code-modal-pre">
<code id="modalCodeContent"></code>
</pre>

            <div class="code-modal-footer">
                <button type="button" class="code-modal-action code-modal-action-primary" onclick="copyModalCode()">
                    <span>📋</span>
                    <span>Copy Code</span>
                </button>
                <button type="button" class="code-modal-action code-modal-action-secondary" onclick="closeCodeModal()">Yopish</button>
            </div>
        </div>
    </div>

    <style>
    .code-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.68);
        z-index: 9000;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    .code-modal-overlay.show {
        display: flex;
    }
    #codeModal.code-modal-overlay.show {
        display: flex;
    }
    .code-modal-panel {
        width: 100%;
        max-width: 720px;
        max-height: 90vh;
        position: relative;
        top: auto;
        left: auto;
        transform: none;
        margin: 0 auto;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        background: #ffffff;
        border: 1px solid #dbe3ee;
        border-radius: 16px;
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.28);
    }
    .code-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.25rem;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }
    .code-modal-title-wrap {
        display: flex;
        align-items: center;
        gap: 0.65rem;
    }
    .code-modal-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: linear-gradient(135deg, #10b981, #059669);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 32px;
    }
    .code-modal-header h3 {
        margin: 0;
        font-size: 1rem;
        color: #0f172a;
    }
    .code-modal-close {
        width: 34px;
        height: 34px;
        border: none;
        border-radius: 9px;
        background: #e2e8f0;
        color: #475569;
        font-size: 1.25rem;
        line-height: 1;
        cursor: pointer;
    }
    .code-modal-close:hover {
        background: #fee2e2;
        color: #dc2626;
    }
    .code-modal-pre {
        margin: 0;
        padding: 1.25rem;
        overflow: auto;
        flex: 1;
        background: #111827;
        color: #e5eefc;
        font-family: 'JetBrains Mono', 'Fira Code', 'Consolas', monospace;
        font-size: 0.84rem;
        line-height: 1.6;
        white-space: pre;
    }
    .code-modal-footer {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 1.25rem;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
    }
    .code-modal-action {
        appearance: none;
        border: 1px solid transparent;
        border-radius: 10px;
        padding: 0.72rem 1rem;
        font-size: 0.9rem;
        font-weight: 600;
        line-height: 1;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        transition: background-color 0.18s ease, border-color 0.18s ease, color 0.18s ease, transform 0.18s ease;
    }
    .code-modal-action:hover {
        transform: translateY(-1px);
    }
    .code-modal-action-primary {
        background: #2563eb;
        border-color: #2563eb;
        color: #ffffff;
    }
    .code-modal-action-primary:hover {
        background: #1d4ed8;
        border-color: #1d4ed8;
    }
    .code-modal-action-secondary {
        background: #ffffff;
        border-color: #cbd5e1;
        color: #334155;
    }
    .code-modal-action-secondary:hover {
        background: #f8fafc;
        border-color: #94a3b8;
    }
    @media (max-width: 640px) {
        .code-modal-footer {
            flex-direction: column;
            align-items: stretch;
        }
        .code-modal-action {
            width: 100%;
        }
    }
    </style>

    <script>
    async function showCodeModal(attemptId) {
        try {
            const overlay = document.getElementById('codeModal');
            overlay.classList.add('show');
            document.getElementById('modalCodeContent').textContent = 'Yuklanmoqda...';

            const response = await fetch('get_contest_attempt_code.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'attempt_id=' + attemptId
            });
            const code = await response.text();
            document.getElementById('modalCodeContent').textContent = code;
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
        document.getElementById('codeModal').classList.remove('show');
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeCodeModal();
        }
    });

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
