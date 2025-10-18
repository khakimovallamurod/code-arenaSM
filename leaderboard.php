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
</head>
<body>
    <!-- Navbar -->
    <?php include_once 'includes/novbar.php';?>
    
    <!-- Main Content -->
    <div class="container">
        <!-- Filters -->
        <div class="filters" style="margin-bottom: 2rem;">
            <select id="categoryFilter">
                <option value="overall">Overall Score</option>
                <option value="problems">Problems Solved</option>
                <option value="contests">Contest Wins</option>
            </select>
        </div>

        <!-- Top 3 Spotlight -->
        <!-- <div class="card-grid" style="margin-bottom: 3rem; grid-template-columns: repeat(3, 1fr);">
            <div class="card text-center" style="background: linear-gradient(135deg, rgba(255, 215, 0, 0.1), rgba(255, 215, 0, 0.05)); border-color: #ffd700;">
                <div style="font-size: 3rem; margin-bottom: 0.5rem;">🥇</div>
                <div class="profile-avatar-large" style="margin: 0 auto 1rem; width: 80px; height: 80px; font-size: 2rem;">AK</div>
                <h3>AlgoKing</h3>
                <div class="stat-value" style="margin-top: 0.5rem;">8,945</div>
                <div class="text-secondary">Total Score</div>
                <div style="margin-top: 1rem;">
                    <span class="badge badge-tag">456 Problems</span>
                    <span class="badge badge-tag">89 Contests</span>
                </div>
            </div>
            <div class="card text-center" style="background: linear-gradient(135deg, rgba(192, 192, 192, 0.1), rgba(192, 192, 192, 0.05)); border-color: #c0c0c0;">
                <div style="font-size: 3rem; margin-bottom: 0.5rem;">🥈</div>
                <div class="profile-avatar-large" style="margin: 0 auto 1rem; width: 80px; height: 80px; font-size: 2rem;">CN</div>
                <h3>CodeNinja</h3>
                <div class="stat-value" style="margin-top: 0.5rem;">8,723</div>
                <div class="text-secondary">Total Score</div>
                <div style="margin-top: 1rem;">
                    <span class="badge badge-tag">432 Problems</span>
                    <span class="badge badge-tag">85 Contests</span>
                </div>
            </div>
            <div class="card text-center" style="background: linear-gradient(135deg, rgba(205, 127, 50, 0.1), rgba(205, 127, 50, 0.05)); border-color: #cd7f32;">
                <div style="font-size: 3rem; margin-bottom: 0.5rem;">🥉</div>
                <div class="profile-avatar-large" style="margin: 0 auto 1rem; width: 80px; height: 80px; font-size: 2rem;">PM</div>
                <h3>ProMaster</h3>
                <div class="stat-value" style="margin-top: 0.5rem;">8,456</div>
                <div class="text-secondary">Total Score</div>
                <div style="margin-top: 1rem;">
                    <span class="badge badge-tag">410 Problems</span>
                    <span class="badge badge-tag">82 Contests</span>
                </div>
            </div>
        </div> -->

        <!-- Full Leaderboard Table -->
        <?php
        $reytingsPerPage = 10; 
        $totalReytings = count($reytings);
        $totalPages = ceil($totalReytings / $reytingsPerPage);

        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($currentPage < 1) $currentPage = 1;
        if ($currentPage > $totalPages) $currentPage = $totalPages;

        $startIndex = ($currentPage - 1) * $reytingsPerPage;
        $visibleReytings = array_slice($reytings, $startIndex, $reytingsPerPage);
        ?>

        <h2 class="mb-1">To'liq reytinglar</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 100px;">Rank</th>
                        <th>Fullname</th>
                        <th>Username</th>
                        <th>Course</th>
                        <th style="width: 150px;">Ball</th>
                        <th style="width: 150px;">Yechilgan</th>
                        <th style="width: 150px;">Urinishlar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $shown = count($visibleReytings);
                    foreach ($visibleReytings as $index => $reyting): ?>
                        <tr>
                            <td><span class="rank"><?= $startIndex + $index + 1 ?></span></td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div class="user-avatar" style="width: 35px; height: 35px; font-size: 0.9rem;">
                                        <?= strtoupper(substr($reyting['user'],0,2)) ?>
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
                    <?php endforeach; ?>

                    <?php for($i = $shown; $i < $reytingsPerPage; $i++): ?>
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>

        <div style="display: flex; justify-content: center; gap: 0.5rem; margin-top: 1rem; margin-bottom: 1rem;">
            <?php if ($currentPage > 1): ?>
                <a href="?id=<?= $problem_id ?>&page=<?= $currentPage - 1; ?>" class="btn btn-secondary">← Previous</a>
            <?php else: ?>
                <button class="btn btn-secondary" disabled>← Previous</button>
            <?php endif; ?>

            <!-- Sahifa raqamlari (10 ta ko‘rinadi) -->
            <?php
            $startPage = max(1, $currentPage - 4);
            $endPage = min($totalPages, $startPage + 9);
            for ($i = $startPage; $i <= $endPage; $i++): ?>
                <a href="?id=<?= $problem_id ?>&page=<?= $i; ?>" class="btn <?= ($i === $currentPage) ? 'btn-primary' : 'btn-secondary'; ?>">
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

    <!-- Footer -->
    <?php include_once 'includes/footer.php';?>
    <script src="assets/js/change_style.js"></script>
</body>
</html>