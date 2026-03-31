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
    <title>SamCoding — Masalalar</title>
    <link rel="stylesheet" href="assets/css/styles-light.css?v=<?php echo time(); ?>">
    <style>
        .problems-hero {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .problems-hero h1 { margin: 0; }
        .problems-count {
            font-size: 0.88rem;
            color: var(--text-secondary);
            background: var(--bg-tertiary);
            padding: 0.35rem 0.85rem;
            border-radius: 999px;
            font-weight: 600;
        }
        .status-icon-solved { color: #10b981; font-size: 1.1rem; font-weight: 700; }
        .status-icon-none   { color: #cbd5e1; font-size: 1.1rem; }
        #problemsTable tbody tr {
            transition: background 0.15s;
        }
        #problemsTable tbody tr:hover {
            background: #f0fdf4;
        }
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--text-secondary);
        }
        .empty-state svg { margin: 0 auto 1rem; display: block; opacity: 0.35; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php include_once 'includes/novbar.php';?>
    <!-- Main Content -->
    <div class="container">
        <div class="problems-hero">
            <h1>Masalalar to'plami</h1>
            <span class="problems-count"><?= count($problems) ?> ta masala</span>
        </div>
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
                        <td data-label="ID"><strong><?= str_pad(htmlspecialchars($problem['id']), 6, '0', STR_PAD_LEFT) ?></strong></td>
                        <td data-label="Status">
                            <?php if ((int)$problem['solved'] === 1): ?>
                                <span class="status-icon-solved" title="Yechilgan">✓</span>
                            <?php else: ?>
                                <span class="status-icon-none" title="Yechilmagan">—</span>
                            <?php endif; ?>
                        </td>
                        <td data-label="Masala"><strong><?= htmlspecialchars($problem['title']) ?></strong></td>
                        <td data-label="Qiyinchiligi"><span class="badge badge-<?= $problem['difficulty'] ?>"><?= ucfirst($problem['difficulty']) ?></span></td>
                        <td data-label="Masala turi"><span class="badge badge-<?= $problem['category'] ?>"><?= ucfirst($problem['category']) ?></span></td>
                        <td data-label="Urinishlar"><strong><?= htmlspecialchars($problem['attempts']) ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination-container">
            <!-- Previous tugmasi -->
            <a href="javascript:void(0)" class="pagination-btn pagination-nav prev" onclick="if(currentPage > 1) { currentPage--; renderProblems(); }">← Previous</a>

            <!-- Sahifa raqamlari -->
            <div class="pagination-numbers">
                <?php
                $startPage = max(1, $currentPage - 2);
                $endPage = min($totalPages, $startPage + 4);
                if ($endPage - $startPage + 1 < 5 && $totalPages >= 5) {
                    $startPage = max(1, $endPage - 4);
                }
                for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <a href="?page=<?= $i ?>" class="pagination-btn <?= ($i === $currentPage) ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>

            <!-- Next tugmasi -->
            <a href="javascript:void(0)" class="pagination-btn pagination-nav next" onclick="if(currentPage < <?= $totalPages ?>) { currentPage++; renderProblems(); }">Next →</a>
        </div>
    </div>

    <!-- Footer -->
    <?php include_once 'includes/footer.php';?>
    <script>
        // Barcha masalalarni JavaScript obekti sifatida saqlaylik
        const allProblems = <?php echo json_encode($problems); ?>.map(problem => ({
            ...problem,
            solved: Number(problem.solved) === 1 ? 1 : 0
        }));
        
        function filterProblems() {
            const searchValue = document.getElementById("searchInput").value.toLowerCase();
            const difficultyValue = document.getElementById("difficultyFilter").value.toLowerCase();
            const tagValue = document.getElementById("tagFilter").value.toLowerCase();

            // Barcha masalalardan filterla
            filteredProblems = allProblems.filter(problem => {
                const title = problem.title.toLowerCase();
                const difficulty = problem.difficulty.toLowerCase();
                const category = problem.category.toLowerCase();

                const matchesSearch = title.includes(searchValue);
                const matchesDifficulty = difficultyValue === "" || difficulty.includes(difficultyValue);
                const matchesTag = tagValue === "" || category.includes(tagValue);

                return matchesSearch && matchesDifficulty && matchesTag;
            });

            // Reset to first page when filtering
            currentPage = 1;
            
            renderProblems();
        }
        
        function renderProblems() {
            const tbody = document.querySelector("#problemsTable tbody");
            tbody.innerHTML = "";

            if (filteredProblems.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6"><div class="empty-state">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                <p style="font-size:1rem;font-weight:600;margin-bottom:0.25rem;">Hech qanday masala topilmadi</p>
                <p style="font-size:0.875rem;">Filtirlarni o'zgartirib ko'ring</p>
            </div></td></tr>`;
                document.querySelector('.pagination-container').style.display = 'none';
                return;
            }

            // Calculate pagination
            const totalPages = Math.ceil(filteredProblems.length / problemsPerPage);
            const startIndex = (currentPage - 1) * problemsPerPage;
            const endIndex = Math.min(startIndex + problemsPerPage, filteredProblems.length);
            const visibleProblems = filteredProblems.slice(startIndex, endIndex);

            // Show/hide pagination
            if (totalPages > 1) {
                document.querySelector('.pagination-container').style.display = 'flex';
                renderPagination(totalPages);
            } else {
                document.querySelector('.pagination-container').style.display = 'none';
            }

            visibleProblems.forEach(problem => {
                const row = document.createElement("tr");
                row.style.cursor = "pointer";
                row.onclick = function() {
                    window.location = 'problem-detail.php?id=' + problem.id;
                };
                
                const isSolved = Number(problem.solved) === 1;
                const statusIcon = isSolved
                    ? `<span class="status-icon-solved" title="Yechilgan">✓</span>`
                    : `<span class="status-icon-none" title="Yechilmagan">—</span>`;

                row.innerHTML = `
                    <td data-label="ID"><strong>${String(problem.id).padStart(6, '0')}</strong></td>
                    <td data-label="Status">${statusIcon}</td>
                    <td data-label="Masala"><strong>${problem.title}</strong></td>
                    <td data-label="Qiyinchiligi"><span class="badge badge-${problem.difficulty}">${problem.difficulty.charAt(0).toUpperCase() + problem.difficulty.slice(1)}</span></td>
                    <td data-label="Masala turi"><span class="badge badge-${problem.category}">${problem.category.charAt(0).toUpperCase() + problem.category.slice(1)}</span></td>
                    <td data-label="Urinishlar"><strong>${problem.attempts}</strong></td>
                `;
                tbody.appendChild(row);
            });
        }
        
        function renderPagination(totalPages) {
            const paginationNumbers = document.querySelector('.pagination-numbers');
            paginationNumbers.innerHTML = '';
            
            const startPage = Math.max(1, currentPage - 2);
            const endPage = Math.min(totalPages, startPage + 4);
            
            for (let i = startPage; i <= endPage; i++) {
                const pageBtn = document.createElement('a');
                pageBtn.href = 'javascript:void(0)';
                pageBtn.className = `pagination-btn ${i === currentPage ? 'active' : ''}`;
                pageBtn.textContent = i;
                pageBtn.onclick = function() {
                    currentPage = i;
                    renderProblems();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                };
                paginationNumbers.appendChild(pageBtn);
            }
        }
        
        // Initialize with all problems
        let currentPage = 1;
        const problemsPerPage = 10;
        let filteredProblems = [...allProblems];
        renderProblems();
        </script>



</body>
</html>
