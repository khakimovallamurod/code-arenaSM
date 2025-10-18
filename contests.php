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
        <!-- Contest Header -->
        <div class="contest-header">
            <h1>Tuzatish ishlari olib borilmoqda</h1>
            <p class="text-secondary" style="margin-bottom: 1rem;">Tez kunda ishga tushadi</p>
            <div class="timer" id="contestTimer">02:34:15</div>
            <p class="text-secondary"></p>
            <div style="margin-top: 1.5rem;">
                <span class="badge badge-tag" style="font-size: 1rem; padding: 0.5rem 1rem;">2025</span>
            </div>
        </div>
        
    </div>

    <!-- Footer -->
    <?php include_once 'includes/footer.php';?>
    <script src="assets/js/change_style.js"></script>
    
    <script>
        // Contest Timer
        function startContestTimer() {
            let totalSeconds = 2 * 3600 + 34 * 60 + 15; // 2:34:15
            
            setInterval(() => {
                if (totalSeconds > 0) {
                    totalSeconds--;
                    const hours = Math.floor(totalSeconds / 3600);
                    const minutes = Math.floor((totalSeconds % 3600) / 60);
                    const seconds = totalSeconds % 60;
                    
                    const timerElement = document.getElementById('contestTimer');
                    if (timerElement) {
                        timerElement.textContent = 
                            `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                    }
                } else {
                    document.getElementById('contestTimer').textContent = 'Contest Ended';
                    document.getElementById('contestTimer').style.color = 'var(--danger)';
                }
            }, 1000);
        }

        // Start timer when page loads
        document.addEventListener('DOMContentLoaded', startContestTimer);
    </script>
</body>
</html>