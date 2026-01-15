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

<?php if(empty($attempts)): ?>
    <div class="submission-item">
        <span><strong>Hali hech qanday urinish yo'q 😌</strong></span>
    </div>
<?php else: ?>
    <div id="attemptsListContainer">
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
            style="display: flex; justify-content: space-between; align-items: center; padding: 10px 15px; border-bottom: 1px solid #eee; cursor:pointer;"
            onclick="showCodeModal(<?= $attempt['attempt_id'] ?>,'<?= htmlspecialchars($attempt['language'], ENT_QUOTES) ?>')">
            
            <div style="display: flex; align-items: center; gap: 0.8rem; min-width: 50px;">
                <span style="font-weight: bold; color: #333; font-size: 14px;">
                    #<?= htmlspecialchars($attempt['attempt_id']); ?>
                </span>
            </div>
            
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
                <span class="lang-badge" style="padding: 4px 10px; border-radius: 6px; font-size: 14px;">
                    <?= htmlspecialchars($attempt['language']); ?>
                </span>
                <span style="font-weight: 500;">
                    <?= intval($attempt['runTime']); ?> ms
                </span>
                <span style="font-weight: 500;">
                    <?= intval($attempt['memory']/1024); ?> KB
                </span>
                <span style="font-size: 14px;">
                    <?= date('d.m.Y H:i', strtotime($attempt['created_at'])); ?>
                </span>
            </div>
        </div>
    <?php endforeach; ?>
    </div>

    <div id="paginationContainer" style="display: flex; justify-content: center; gap: 0.5rem; margin-top: 2rem; margin-bottom: 3rem;">
        <?php if ($currentPage > 1): ?>
            <button onclick="loadAttemptsPage(<?= $currentPage - 1; ?>)" class="btn btn-secondary">← Previous</button>
        <?php else: ?>
            <button class="btn btn-secondary" disabled>← Previous</button>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <button onclick="loadAttemptsPage(<?= $i; ?>)" 
                class="btn <?= ($i === $currentPage) ? 'btn-primary' : 'btn-secondary'; ?>">
                <?= $i; ?>
            </button>
        <?php endfor; ?>

        <?php if ($currentPage < $totalPages): ?>
            <button onclick="loadAttemptsPage(<?= $currentPage + 1; ?>)" class="btn btn-secondary">Next →</button>
        <?php else: ?>
            <button class="btn btn-secondary" disabled>Next →</button>
        <?php endif; ?>
    </div>
    
    <!-- Modal -->
    <div id="codeModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
        <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); background:white; padding:20px; border-radius:8px; width:80%; max-width:800px; max-height:80vh;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
                <h3 style="margin:0;">Kodni ko'rish</h3>
                <button onclick="document.getElementById('codeModal').style.display='none'" 
                    style="background:none; border:none; font-size:20px; cursor:pointer;">×</button>
            </div>

            <pre style="background:#f5f5f5; padding:15px; border-radius:4px; max-height:60vh; overflow:auto;">
<code id="modalCodeContent"></code>
</pre>

            <div style="margin-top:15px; text-align:right;">
                <button onclick="copyModalCode()" style="padding:8px 16px; background:#007bff; color:white; border:none; border-radius:4px; cursor:pointer;">📋 Copy Code</button>
            </div>
        </div>
    </div>
    
    <script>
    async function showCodeModal(attemptId, language) {
        try {
            const response = await fetch('get_contest_attempt_code.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'attempt_id=' + attemptId
            });
            
            const code = await response.text();
            document.getElementById('modalCodeContent').textContent = code;
            document.getElementById('codeModal').style.display = 'block';
        } catch (error) {
            alert('Kodni yuklashda xatolik!');
        }
    }
    
    function copyModalCode() {
        const codeElement = document.getElementById('modalCodeContent');
        const code = codeElement.textContent;

        if (navigator.clipboard && navigator.clipboard.writeText) {
            // Modern browser
            navigator.clipboard.writeText(code)
                .then(() => alert('Kod nusxalandi!'))
                .catch(() => fallbackCopy(code));
        } else {
            // Fallback
            fallbackCopy(code);
        }
    }

    function fallbackCopy(text) {
        let textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.top = '-1000px'; 
        document.body.appendChild(textarea);
        textarea.select();

        try {
            document.execCommand('copy');
            
        } catch (err) {
            alert('Nusxa olishni browser qo‘llamaydi');
        }
        document.body.removeChild(textarea);
    }

    </script>
<?php endif; ?>
