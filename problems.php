<?php
    include_once 'config.php';
    session_start();
    if (!isset($_SESSION['id']) || empty($_SESSION['id']) ) {
        header("Location: auth/login.php");
        exit;
    }
   $user_id = $_SESSION['id'];
   $db = new Database();
   $problems = $db->get_all_problems_by_status($user_id);
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
        <h1 class="mb-2">Masalalar toplami</h1>
        <!-- Filters -->
        <div class="filters">
            <div class="search-box">
                <input type="text" placeholder="Search problems..." id="searchInput" onkeyup="filterProblems()">
            </div>
            <select id="difficultyFilter" onchange="filterProblems()">
                <option value="">Barcha qiyinchilikdagilar</option>
                <option value="beginner">Beginner</option>
                <option value="easy">Easy</option>
                <option value="medium">Medium</option>
                <option value="hard">Hard</option>
                <option value="expert">Expert</option>
            </select>
            <select id="tagFilter" onchange="filterProblems()">
                <option value="">Barcha turdagi</option>
                <option value="array">Array</option>
                <option value="string">String</option>
                <option value="math">Math</option>
                <option value="dp">Dynamic Programming</option>
                <option value="graph">Graph</option>
                <option value="tree">Tree</option>
                <option value="list">List</option>
                <option value="stack">Stack</option>
                <option value="queue">Queue</option>
                <option value="sorting">Sorting</option>
                <option value="ga">Graph Algorithms</option>
            </select>
        </div>


        <!-- Problems Table -->
        <?php
        $problemsPerPage = 10;  // har sahifada 10 ta masala
        $totalProblems = count($problems);
        $totalPages = ceil($totalProblems / $problemsPerPage);

        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($currentPage < 1) $currentPage = 1;
        if ($currentPage > $totalPages) $currentPage = $totalPages;

        // Ko‘rinadigan masalalar
        $startIndex = ($currentPage - 1) * $problemsPerPage;
        $visibleProblems = array_slice($problems, $startIndex, $problemsPerPage);
        ?>

        <div class="table-container">
            <table id="problemsTable">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th style="width: 80px;">Status</th>
                        <th>Masala</th>
                        <th style="width: 150px;">Qiyinchiligi</th>
                        <th>Masala turi</th>
                        <th style="width: 120px;">Urinishlar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($visibleProblems as $problem): ?>
                    <tr onclick="window.location='problem-detail.php?id=<?= (int)$problem['id'] ?>'">
                        <td><strong><?= str_pad(htmlspecialchars($problem['id']), 6, '0', STR_PAD_LEFT) ?></strong></td>
                        <td style="font-size: 1.5rem; color: <?= $problem['solved'] ? 'var(--success)' : 'inherit' ?>">
                            <?= $problem['solved'] ? '✓' : '—' ?>
                        </td>
                        <td><strong><?= htmlspecialchars($problem['title']) ?></strong></td>
                        <td><span class="badge badge-<?= $problem['difficulty'] ?>"><?= ucfirst($problem['difficulty']) ?></span></td>
                        <td><span class="badge badge-<?= $problem['category'] ?>"><?= ucfirst($problem['category']) ?></span></td>
                        <td><strong><?= htmlspecialchars($problem['attempts']) ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div style="display: flex; justify-content: center; gap: 0.5rem; margin-top: 2rem; margin-bottom: 3rem;">
            <!-- Previous tugmasi -->
            <?php if ($currentPage > 1): ?>
                <a href="?page=<?= $currentPage - 1 ?>" class="btn btn-secondary">← Previous</a>
            <?php else: ?>
                <button class="btn btn-secondary" disabled>← Previous</button>
            <?php endif; ?>

            <!-- Sahifa raqamlari -->
            <?php
            $startPage = max(1, $currentPage - 4);
            $endPage = min($totalPages, $startPage + 9); // 10 ta ko‘rsatadi
            for ($i = $startPage; $i <= $endPage; $i++): ?>
                <a href="?page=<?= $i ?>" class="btn <?= ($i === $currentPage) ? 'btn-primary' : 'btn-secondary' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <!-- Next tugmasi -->
            <?php if ($currentPage < $totalPages): ?>
                <a href="?page=<?= $currentPage + 1 ?>" class="btn btn-secondary">Next →</a>
            <?php else: ?>
                <button class="btn btn-secondary" disabled>Next →</button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <?php include_once 'includes/footer.php';?>
    <script src="assets/js/change_style.js"></script>   
    <script>
        function filterProblems() {
            const searchValue = document.getElementById("searchInput").value.toLowerCase();
            const difficultyValue = document.getElementById("difficultyFilter").value.toLowerCase();
            const tagValue = document.getElementById("tagFilter").value.toLowerCase();

            const rows = document.querySelectorAll("#problemsTable tbody tr");

            rows.forEach(row => {
                const title = row.cells[2].textContent.toLowerCase();
                const difficulty = row.cells[3].textContent.toLowerCase();
                const tag = row.cells[4].textContent.toLowerCase();

                const matchesSearch = title.includes(searchValue);
                const matchesDifficulty = difficultyValue === "" || difficulty.includes(difficultyValue);
                const matchesTag = tagValue === "" || tag.includes(tagValue);

                if (matchesSearch && matchesDifficulty && matchesTag) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        }
        </script>



</body>
</html>