<?php
   include_once 'config.php';
   session_start();
   if (!isset($_SESSION['id']) || empty($_SESSION['id'])) {
       header("Location: auth/login.php");
       exit;
   }
   $current_username = $_SESSION['username'] ?? '';
   $db = new Database();
   $reytings = $db->get_reyting_by_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SamCoding - Reyting</title>
    <link rel="stylesheet" href="assets/css/styles-light.css">
    <style>
        .current-user-row {
            background: rgba(16, 185, 129, 0.08) !important;
            border-left: 3px solid #10b981;
        }
        .current-user-row td { font-weight: 600; }
        .you-badge {
            display: inline-flex;
            align-items: center;
            background: #10b981;
            color: white;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 0.15rem 0.5rem;
            border-radius: 999px;
            margin-left: 0.5rem;
            letter-spacing: 0.05em;
            vertical-align: middle;
        }
        .leaderboard-search-form {
            margin-bottom: 1rem;
            display: flex;
            justify-content: flex-end;
        }
        .leaderboard-search-form .search-box { width: min(360px, 100%); min-width: 0; }
        .leaderboard-search-form .search-box input { width: 100%; }
        @media (max-width: 768px) {
            .leaderboard-search-form { justify-content: stretch; }
            .leaderboard-search-form .search-box { width: 100%; }
        }
    </style>
</head>
<body>
    <?php include_once 'includes/novbar.php'; ?>

    <div class="container">
        <?php
        $query = isset($_GET['q']) ? trim($_GET['q']) : '';
        $toLower = function ($value) {
            $value = (string)$value;
            return function_exists('mb_strtolower') ? mb_strtolower($value, 'UTF-8') : strtolower($value);
        };

        if ($query !== '') {
            $queryLower = $toLower($query);
            $reytings = array_values(array_filter($reytings, function ($row) use ($queryLower, $toLower) {
                return strpos($toLower($row['user'] ?? ''), $queryLower) !== false
                    || strpos($toLower($row['username'] ?? ''), $queryLower) !== false
                    || strpos($toLower($row['course'] ?? ''), $queryLower) !== false;
            }));
        }

        $reytingsPerPage = 10;
        $totalReytings   = count($reytings);
        $totalPages      = ceil($totalReytings / $reytingsPerPage);
        $currentPage     = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($currentPage < 1) $currentPage = 1;
        if ($totalPages > 0 && $currentPage > $totalPages) $currentPage = $totalPages;
        if ($totalPages === 0) $currentPage = 1;
        $startIndex    = ($currentPage - 1) * $reytingsPerPage;
        $visibleReytings = array_slice($reytings, $startIndex, $reytingsPerPage);
        ?>

        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;flex-wrap:wrap;gap:0.75rem;">
            <h2 style="margin:0;">&#127942; To'liq reytinglar</h2>
            <span style="font-size:0.85rem;color:var(--text-secondary);background:var(--bg-tertiary);padding:0.3rem 0.85rem;border-radius:999px;font-weight:600;">
                <?= count($reytings) ?> ta ishtirokchi
            </span>
        </div>

        <form method="GET" class="leaderboard-search-form" id="leaderboardSearchForm">
            <input type="hidden" name="page" value="1">
            <div class="search-box">
                <input type="text" name="q" id="leaderboardSearchInput"
                    value="<?= htmlspecialchars($query) ?>"
                    placeholder="Ism, username yoki kurs bo'yicha qidiring...">
            </div>
        </form>

        <!-- Top 3 podium -->
        <?php if ($currentPage == 1 && $query === ''): ?>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;margin-bottom:2rem;">
        <?php
            $medals  = array('&#129351;', '&#129352;', '&#129353;');
            $colors  = array('#f59e0b',   '#94a3b8',   '#b45309');
            $labels  = array("1-o'rin",   "2-o'rin",   "3-o'rin");
            for ($pi = 0; $pi < 3; $pi++):
                if (!isset($reytings[$pi])) continue;
                $r     = $reytings[$pi];
                $isMe  = ($r['username'] ?? '') === $current_username;
                $score = intval($r['total_score']);
                $sv    = intval($r['solved']);
                $init  = htmlspecialchars(strtoupper(substr($r['user'], 0, 2)));
                $nm    = htmlspecialchars($r['user']);
                $borderStyle = 'border:2px solid ' . $colors[$pi] . ';';
                $shadowStyle = $isMe ? 'box-shadow:0 0 0 3px rgba(16,185,129,0.4);' : '';
        ?>
            <div class="card text-center" style="<?= $borderStyle . $shadowStyle ?>cursor:default;">
                <div style="font-size:2.2rem;margin-bottom:0.4rem;"><?= $medals[$pi] ?></div>
                <div style="width:56px;height:56px;border-radius:50%;background:<?= $colors[$pi] ?>;color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.3rem;font-weight:800;margin:0 auto 0.6rem;">
                    <?= $init ?>
                </div>
                <h3 style="margin:0 0 0.2rem;font-size:0.9rem;">
                    <?= $nm ?>
                    <?php if ($isMe): ?><span class="you-badge">Siz</span><?php endif; ?>
                </h3>
                <div style="font-size:0.75rem;color:var(--text-secondary);margin-bottom:0.6rem;"><?= $labels[$pi] ?></div>
                <div style="display:flex;gap:1.2rem;justify-content:center;">
                    <div>
                        <div style="font-size:1.2rem;font-weight:800;color:<?= $colors[$pi] ?>;"><?= $score ?></div>
                        <div style="font-size:0.72rem;color:var(--text-secondary);">Ball</div>
                    </div>
                    <div>
                        <div style="font-size:1.2rem;font-weight:800;"><?= $sv ?></div>
                        <div style="font-size:0.72rem;color:var(--text-secondary);">Yechilgan</div>
                    </div>
                </div>
            </div>
        <?php endfor; ?>
        </div>
        <?php endif; ?>

        <!-- Reyting jadvali -->
        <div class="table-container">
            <table id="leaderboard" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th style="width:100px;">#</th>
                        <th>FIO</th>
                        <th>Username</th>
                        <th>Kurs</th>
                        <th style="width:150px;">Ball</th>
                        <th style="width:150px;">Yechilgan</th>
                        <th style="width:150px;">Urinishlar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($visibleReytings)): ?>
                    <tr>
                        <td colspan="7" style="text-align:center;padding:1rem;">Natija topilmadi</td>
                    </tr>
                    <?php else:
                    foreach ($visibleReytings as $index => $reyting):
                        $rank = $startIndex + $index + 1;
                        if ($rank <= 3 && $currentPage == 1 && $query === '') continue;
                        $isMe = ($reyting['username'] ?? '') === $current_username;
                    ?>
                    <tr <?= $isMe ? 'class="current-user-row"' : '' ?>>
                        <td><span class="rank"><?= $rank ?></span></td>
                        <td>
                            <div style="display:flex;align-items:center;gap:0.75rem;">
                                <div class="user-avatar" style="width:35px;height:35px;font-size:0.9rem;<?= $isMe ? 'background:#10b981;color:white;' : '' ?>">
                                    <?= htmlspecialchars(strtoupper(substr($reyting['user'], 0, 2))) ?>
                                </div>
                                <strong><?= htmlspecialchars($reyting['user']) ?></strong>
                                <?php if ($isMe): ?><span class="you-badge">Siz</span><?php endif; ?>
                            </div>
                        </td>
                        <td><strong style="color:var(--primary);"><?= htmlspecialchars($reyting['username'] ?? '-') ?></strong></td>
                        <td><?= htmlspecialchars($reyting['course'] ?? '-') ?></td>
                        <td><strong><?= intval($reyting['total_score']) ?></strong></td>
                        <td><?= intval($reyting['solved']) ?></td>
                        <td><?= intval($reyting['attempts']) ?></td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

        <div class="pagination-container" style="margin-top:1rem;margin-bottom:1rem;">
            <?php if ($currentPage > 1): ?>
                <a href="?<?= http_build_query(array('page' => $currentPage - 1, 'q' => $query)) ?>" class="pagination-btn pagination-nav">&#8592; Previous</a>
            <?php else: ?>
                <button class="pagination-btn pagination-nav" disabled>&#8592; Previous</button>
            <?php endif; ?>

            <div class="pagination-numbers">
            <?php
            $startPage = max(1, $currentPage - 4);
            $endPage   = min($totalPages, $startPage + 9);
            for ($i = $startPage; $i <= $endPage; $i++): ?>
                <a href="?<?= http_build_query(array('page' => $i, 'q' => $query)) ?>" class="pagination-btn <?= ($i === $currentPage) ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
            </div>

            <?php if ($currentPage < $totalPages): ?>
                <a href="?<?= http_build_query(array('page' => $currentPage + 1, 'q' => $query)) ?>" class="pagination-btn pagination-nav">Next &#8594;</a>
            <?php else: ?>
                <button class="pagination-btn pagination-nav" disabled>Next &#8594;</button>
            <?php endif; ?>
        </div>
    </div>

    <?php include_once 'includes/footer.php'; ?>
    <script>
        (function () {
            var form  = document.getElementById('leaderboardSearchForm');
            var input = document.getElementById('leaderboardSearchInput');
            if (!form || !input) return;
            var timer = null;
            input.addEventListener('input', function () {
                clearTimeout(timer);
                timer = setTimeout(function () { form.submit(); }, 350);
            });
        })();
    </script>
</body>
</html>
