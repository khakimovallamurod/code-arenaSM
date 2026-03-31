<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SamCoding - Kirish va Ro'yxatdan o'tish</title>
    <link rel="stylesheet" href="../assets/css/login_style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="login-centered">
    <div class="auth-container">
        <!-- Background decoration -->
        <div class="auth-bg-decoration"></div>
        
        <div class="auth-wrapper">
            <!-- Logo & Title -->
            <div class="auth-header">
                <div class="auth-logo">🎓 SamCoding</div>
                <h1 class="auth-title">Xosh Kelibsiz</h1>
                <p class="auth-subtitle">Dasturlash ko'nikmalarini rivojlantirib boshlang</p>
            </div>

            <!-- Tab Navigation -->
            <div class="auth-tabs">
                <button class="auth-tab-btn active" data-tab="login">
                    <i class="fas fa-sign-in-alt"></i> Kirish
                </button>
                <button class="auth-tab-btn" data-tab="register">
                    <i class="fas fa-user-plus"></i> Ro'yxatdan o'tish
                </button>
            </div>

            <!-- Login Form -->
            <div class="auth-tab-content active" id="login">
                <form action="login-check.php" method="post" id="logform" class="auth-form">
                    <div class="form-group">
                        <label for="login-username">Foydalanuvchi nomi</label>
                        <div class="input-wrapper">
                            <i class="fas fa-user"></i>
                            <input 
                                type="text" 
                                id="login-username" 
                                name="username" 
                                required 
                                placeholder="Foydalanuvchi nomingizni kiriting"
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="login-password">Parol</label>
                        <div class="input-wrapper password-wrapper">
                            <i class="fas fa-lock"></i>
                            <input 
                                type="password" 
                                id="login-password" 
                                name="password" 
                                required 
                                placeholder="Parolingizni kiriting"
                            >
                            <button type="button" class="password-toggle" onclick="togglePassword('login-password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="auth-btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Tizimga kirish
                    </button>
                </form>
            </div>

            <!-- Register Form -->
            <div class="auth-tab-content" id="register">
                <form action="add_client.php" method="post" id="regform" class="auth-form">
                    <div class="form-group">
                        <label for="fullname">To'liq ismingiz</label>
                        <div class="input-wrapper">
                            <i class="fas fa-id-card"></i>
                            <input 
                                type="text" 
                                id="fullname" 
                                name="fullname" 
                                required 
                                placeholder="To'liq ismingizni kiriting"
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="reg-username">Foydalanuvchi nomi</label>
                        <div class="input-wrapper">
                            <i class="fas fa-user"></i>
                            <input 
                                type="text" 
                                id="reg-username" 
                                name="username" 
                                required 
                                placeholder="Noyob foydalanuvchi nomi tanlab oling"
                            >
                        </div>
                        <small class="username-feedback" id="helpblock" style="display: none;"></small>
                    </div>

                    <div class="form-row info-row">
                        <div class="form-group">
                            <label for="otm">Universitet/OTM</label>
                            <div class="input-wrapper">
                                <i class="fas fa-building"></i>
                                <input 
                                    type="text" 
                                    id="otm" 
                                    name="otm" 
                                    required 
                                    placeholder="OTM nomi"
                                >
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="course">Kurs</label>
                            <div class="input-wrapper">
                                <i class="fas fa-graduation-cap"></i>
                                <select id="course" name="course" required>
                                    <option value="">Kursni tanlang</option>
                                    <option value="1">1-kurs</option>
                                    <option value="2">2-kurs</option>
                                    <option value="3">3-kurs</option>
                                    <option value="4">4-kurs</option>
                                    <option value="5">5-kurs</option>
                                    <option value="6">6-kurs</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="phone">Telefon raqamingiz</label>
                        <div class="input-wrapper">
                            <i class="fas fa-phone"></i>
                            <input 
                                type="tel" 
                                id="phone" 
                                name="phone" 
                                required 
                                placeholder="+998 99 123 45 67"
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope"></i>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                required 
                                placeholder="example@mail.com"
                            >
                        </div>
                    </div>

                    <div class="form-row password-row">
                        <div class="form-group">
                            <label for="reg-password">Parol</label>
                            <div class="input-wrapper password-wrapper">
                                <i class="fas fa-lock"></i>
                                <input 
                                    type="password" 
                                    id="reg-password" 
                                    name="password" 
                                    required 
                                    placeholder="Parol kiriting"
                                >
                                <button type="button" class="password-toggle" onclick="togglePassword('reg-password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="reg-password-confirm">Parolni tasdiqlang</label>
                            <div class="input-wrapper password-wrapper">
                                <i class="fas fa-lock"></i>
                                <input 
                                    type="password" 
                                    id="reg-password-confirm" 
                                    name="password2" 
                                    required 
                                    placeholder="Qayta kiriting"
                                >
                                <button type="button" class="password-toggle" onclick="togglePassword('reg-password-confirm')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div id="mesg" class="error-message"></div>
                    </div>

                    <button type="submit" class="auth-btn btn-primary">
                        <i class="fas fa-user-plus"></i> Ro'yxatdan o'tish
                    </button>
                </form>
            </div>

            <!-- Footer -->
            <div class="auth-footer">
                <p>Ushbu saytni ishlatish orqali siz <a href="#">Foydalanish shartlari</a> ga rozilik bildirasiz</p>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function syncBodyLayout(activeTab) {
            document.body.classList.toggle('login-centered', activeTab === 'login');
        }

        // Tab switching
        document.querySelectorAll('.auth-tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const tabName = this.getAttribute('data-tab');
                
                // Remove active class from all tabs and contents
                document.querySelectorAll('.auth-tab-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.auth-tab-content').forEach(c => c.classList.remove('active'));
                
                // Add active class to clicked tab and corresponding content
                this.classList.add('active');
                document.getElementById(tabName).classList.add('active');
                syncBodyLayout(tabName);
            });
        });

        syncBodyLayout('login');

        // Toggle password visibility
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const btn = event.target.closest('.password-toggle');
            const icon = btn.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Form validation
        $('#logform').submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: "login-check.php",
                method: "POST",
                data: $('#logform').serialize(),
                success: function(data) {
                    let obj = jQuery.parseJSON(data);
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                    if (obj.error == 0) {
                        Toast.fire({
                            icon: 'success',
                            title: obj.message
                        });
                        setTimeout(function() {
                            window.location.href = '../index.php';
                        }, 2000);
                    } else {
                        $('#login-password').val('');
                        Toast.fire({
                            icon: 'error',
                            title: obj.message
                        });
                    }
                },
                error: function() {
                    alert("Internet bilan muammo. Qaytadan urinib ko'ring!");
                }
            });
        });

        // Username check
        $('#reg-username').on("keyup", function() {
            let l = $(this).val();
            $.ajax({
                url: "check_username.php",
                method: "POST",
                data: {username: l},
                success: function(data) {
                    let obj = jQuery.parseJSON(data);
                    if (obj.error == 0) {
                        $('#helpblock').css('display', 'block').html(obj.message);
                    } else {
                        $('#helpblock').css('display', 'none');
                    }
                },
                error: function() {
                    alert("Internet bilan muammo. Qaytadan urinib ko'ring!");
                }
            })
        });

        // Register form submission
        $('#regform').submit(function(e) {
            e.preventDefault();
            let p1 = $('#reg-password').val();
            let p2 = $('#reg-password-confirm').val();
            if (p1 != p2) {
                $('#mesg').html("Parollar mos kelmadi!").show();
                return false;
            }
            $.ajax({
                url: "add_client.php",
                method: "POST",
                data: $('#regform').serialize(),
                dataType: "json",
                success: function(obj) {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });

                    if (obj.error == 0) {
                        Toast.fire({
                            icon: 'success',
                            title: obj.message
                        });
                        setTimeout(function() {
                            window.location.href = '../index.php';
                        }, 2000);
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: obj.message
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                    alert("Muammo yuz berdi. Qaytadan urinib ko'ring!");
                }
            });
        });
    </script>
</body>
</html>
