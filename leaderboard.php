<?php
   include_once 'config.php';
   session_start();
   $db = new Database();
   $reytings = $db->get_reyting_by_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SamCoding</title>
    <link rel="stylesheet" href="assets/css/styles-light.css">
    <style>
        .leaderboard-search-form {
            margin-bottom: 1rem;
            display: flex;
            justify-content: flex-end;
        }
        .leaderboard-search-form .search-box {
            width: min(360px, 100%);
            min-width: 0;
        }
        .leaderboard-search-form .search-box input {
            width: 100%;
        }
        @media (max-width: 768px) {
            .leaderboard-search-form {
                justify-content: stretch;
            }
            .leaderboard-search-form .search-box {
                width: 100%;
            }
        }
    </style>

</head>
<body>
    <!-- Navbar -->
    <?php include_once 'includes/novbar.php';?>
    
    <!-- Main Content -->
    <div class="container">
        <?php
        $query = isset($_GET['q']) ? trim($_GET['q']) : '';
        $toLower = function ($value) {
            $value = (string)$value;
            if (function_exists('mb_strtolower')) {
                return mb_strtolower($value, 'UTF-8');
            }
            return strtolower($value);
        };

        if ($query !== '') {
            $queryLower = $toLower($query);
            $reytings = array_values(array_filter($reytings, function ($row) use ($queryLower, $toLower) {
                $user = $toLower($row['user'] ?? '');
                $username = $toLower($row['username'] ?? '');
                $course = $toLower($row['course'] ?? '');
                return strpos($user, $queryLower) !== false
                    || strpos($username, $queryLower) !== false
                    || strpos($course, $queryLower) !== false;
            }));
        }

        $reytingsPerPage = 10;
        $totalReytings = count($reytings);
        $totalPages = ceil($totalReytings / $reytingsPerPage);

        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($currentPage < 1) $currentPage = 1;
        if ($totalPages > 0 && $currentPage > $totalPages) $currentPage = $totalPages;
        if ($totalPages === 0) $currentPage = 1;

        $startIndex = ($currentPage - 1) * $reytingsPerPage;
        $visibleReytings = array_slice($reytings, $startIndex, $reytingsPerPage);
        ?>
 
        <h2 class="mb-1">To'liq reytinglar</h2>

        <form method="GET" class="leaderboard-search-form" id="leaderboardSearchForm">
            <input type="hidden" name="page" value="1">
            <div class="search-box">
                <input
                    type="text"
                    name="q"
                    id="leaderboardSearchInput"
                    value="<?= htmlspecialchars($query) ?>"
                    placeholder="Ism, username yoki kurs bo'yicha qidiring..."
                >
            </div>
        </form>

        <!-- 🏆 Top 3 blok -->
        <?php if ($currentPage == 1 && $query === ''): ?>
        <div class="card-grid" style="margin-bottom: 3rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
            <!-- 1-o‘rin -->
            <?php if (isset($reytings[0])): ?>
            <div class="card text-center" style="background: linear-gradient(135deg, rgba(255, 215, 0, 0.1), rgba(255, 215, 0, 0.05)); border-color: #ffd700;">
                <div style="font-size: 3rem;">🥇</div>
                <div class="profile-avatar-large" style="margin: 0 auto 1rem; width: 80px; height: 80px; font-size: 2rem;">
                    <?= strtoupper(substr($reytings[0]['user'], 0, 2)) ?>
                </div>
                <h3><?= htmlspecialchars($reytings[0]['user']) ?></h3>
                <div style="display: flex; gap: 20px; align-items: center; justify-content: center;">
                    <div>
                        <div class="stat-value"><?= intval($reytings[0]['total_score']) ?></div>
                        <div class="text-secondary">Ball</div>
                    </div>
                    <div>
                        <div class="stat-value"><?= intval($reytings[0]['solved']) ?></div>
                        <div class="text-secondary">Yechilgan</div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- 2-o‘rin -->
            <?php if (isset($reytings[1])): ?>
            <div class="card text-center" style="background: linear-gradient(135deg, rgba(192, 192, 192, 0.1), rgba(192, 192, 192, 0.05)); border-color: #c0c0c0;">
                <div style="font-size: 3rem;">🥈</div>
                <div class="profile-avatar-large" style="margin: 0 auto 1rem; width: 80px; height: 80px; font-size: 2rem;">
                    <?= strtoupper(substr($reytings[1]['user'], 0, 2)) ?>
                </div>
                <h3><?= htmlspecialchars($reytings[1]['user']) ?></h3>
                <div style="display: flex; gap: 20px; align-items: center; justify-content: center;">
                    <div>
                        <div class="stat-value"><?= intval($reytings[1]['total_score']) ?></div>
                        <div class="text-secondary">Ball</div>
                    </div>
                    <div>
                        <div class="stat-value"><?= intval($reytings[1]['solved']) ?></div>
                        <div class="text-secondary">Yechilgan</div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- 3-o‘rin -->
            <?php if (isset($reytings[2])): ?>
            <div class="card text-center" style="background: linear-gradient(135deg, rgba(205, 127, 50, 0.1), rgba(205, 127, 50, 0.05)); border-color: #cd7f32;">
                <div style="font-size: 3rem;">🥉</div>
                <div class="profile-avatar-large" style="margin: 0 auto 1rem; width: 80px; height: 80px; font-size: 2rem;">
                    <?= strtoupper(substr($reytings[2]['user'], 0, 2)) ?>
                </div>
                <h3><?= htmlspecialchars($reytings[2]['user']) ?></h3>
                <div style="display: flex; gap: 20px; align-items: center; justify-content: center;">
                    <div>
                        <div class="stat-value"><?= intval($reytings[2]['total_score']) ?></div>
                        <div class="text-secondary">Ball</div>
                    </div>
                    <div>
                        <div class="stat-value"><?= intval($reytings[2]['solved']) ?></div>
                        <div class="text-secondary">Yechilgan</div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- 📊 Reyting jadvali -->
        <div class="table-container">
            <table id="leaderboard" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th style="width: 100px;">#</th>
                        <th>FIO</th>
                        <th>Username</th>
                        <th>Kurs</th>
                        <th style="width: 150px;">Ball</th>
                        <th style="width: 150px;">Yechilgan</th>
                        <th style="width: 150px;">Urinishlar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (empty($visibleReytings)):
                    ?>
                    <tr>
                        <td colspan="7" style="text-align:center; padding: 1rem;">Natija topilmadi</td>
                    </tr>
                    <?php
                    else:
                    foreach ($visibleReytings as $index => $reyting): 
                        $rank = $startIndex + $index + 1;
                        if ($rank <= 3 && $currentPage == 1 && $query === '') continue;
                    ?>
                    <tr>
                        <td><span class="rank"><?= $rank ?></span></td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div class="user-avatar" style="width: 35px; height: 35px; font-size: 0.9rem;">
                                    <?= strtoupper(substr($reyting['user'], 0, 2)) ?>
                                </div>
                                <strong><?= htmlspecialchars($reyting['user']) ?></strong>
                            </div>
                        </td>
                        <td><strong style="color: var(--primary);"><?= htmlspecialchars($reyting['username'] ?? '-') ?></strong></td>
                        <td><?= htmlspecialchars($reyting['course'] ?? '-') ?></td>
                        <td><?= intval($reyting['total_score']) ?></td>
                        <td><?= intval($reyting['solved']) ?></td>
                        <td><?= intval($reyting['attempts']) ?></td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>


        <div class="pagination-container" style="margin-top: 1rem; margin-bottom: 1rem;">
            <?php if ($currentPage > 1): ?>
                <a href="?<?= http_build_query(['page' => $currentPage - 1, 'q' => $query]); ?>" class="pagination-btn pagination-nav">← Previous</a>
            <?php else: ?>
                <button class="pagination-btn pagination-nav" disabled>← Previous</button>
            <?php endif; ?>

            <div class="pagination-numbers">
            <?php
            $startPage = max(1, $currentPage - 4);
            $endPage = min($totalPages, $startPage + 9);
            for ($i = $startPage; $i <= $endPage; $i++): ?>
                <a href="?<?= http_build_query(['page' => $i, 'q' => $query]); ?>" class="pagination-btn <?= ($i === $currentPage) ? 'active' : ''; ?>">
                    <?= $i; ?>
                </a>
            <?php endfor; ?>
            </div>

            <!-- Next tugmasi -->
            <?php if ($currentPage < $totalPages): ?>
                <a href="?<?= http_build_query(['page' => $currentPage + 1, 'q' => $query]); ?>" class="pagination-btn pagination-nav">Next →</a>
            <?php else: ?>
                <button class="pagination-btn pagination-nav" disabled>Next →</button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <?php include_once 'includes/footer.php';?>
    <script src="assets/js/change_style.js"></script>
    <script>
        (function () {
            const form = document.getElementById('leaderboardSearchForm');
            const input = document.getElementById('leaderboardSearchInput');
            if (!form || !input) return;

            let timer = null;
            input.addEventListener('input', function () {
                clearTimeout(timer);
                timer = setTimeout(function () {
                    form.submit();
                }, 350);
            });
        })();
    </script>
    
</body>
</html>
