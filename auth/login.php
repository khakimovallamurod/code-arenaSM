<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LoginForm</title>
    <link rel="stylesheet" href="../assets/css/login_style.css">
</head>
<body>
    <div id="container" class="container">
		<!-- FORM SECTION -->
		<div class="row">
			<!-- SIGN UP -->
			<div class="col align-items-center flex-col sign-up">
                <div class="form-wrapper align-items-center">
                    <form action="add_client.php" method="post" id="regform" class="form sign-up">

                        <!-- Fullname -->
                        <div class="input-group">
                            <i class='bx bxs-user'></i>
                            <input type="text" name="fullname" id="fullname" required placeholder="Enter your fullname">
                        </div>

                        <!-- Username -->
                        <div class="input-group">
                            <i class='bx bxs-user'></i>
                            <input type="text" name="username" id="lgn" required placeholder="Enter your username">
                        </div>
                        <p class="usernamealert" id="helpblock" style="color: red; font-size: 14px;"></p>

                        <!-- OTM -->
                        <div class="input-group">
                            <i class='bx bxs-school'></i>
                            <input type="text" name="otm" id="otm" required placeholder="Enter your University (OTM)">
                        </div>

                        <!-- Course -->
                        <div class="input-group">
                            <i class='bx bxs-graduation'></i>
                            <input type="number" name="course" id="course" min="1" max="6" required placeholder="Enter your course">
                        </div>

                        <!-- Phone -->
                        <div class="input-group">
                            <i class='bx bxs-phone'></i>
                            <input type="tel" name="phone" id="phone" required placeholder="Enter your phone">
                        </div>

                        <!-- Email -->
                        <div class="input-group">
                            <i class='bx bx-mail-send'></i>
                            <input type="email" name="email" id="email" required placeholder="Enter your email">
                        </div>

                        <!-- Password -->
                        <div class="input-group">
                            <i class='bx bxs-lock-alt'></i>
                            <input type="password" name="password" id="pas1" required placeholder="Create password">
                        </div>

                        <!-- Confirm Password -->
                        <div class="input-group">
                            <i class='bx bxs-lock-alt'></i>
                            <input type="password" name="password2" id="pas2" required placeholder="Confirm password">
                        </div>
                        <p id="mesg" class="error-msg" style="color: red; font-size: 14px;"></p>

                        <button type="submit">
                            Ro'yxatdan o'tish
                        </button>
                        <p>
                            <span>Allaqachon ro'yxatdan o'tganmisiz?</span>
                            <b onclick="toggle()" class="pointer">Tizimga kiring</b>
                        </p>
                    </form>
                </div>
            </div>

			<!-- END SIGN UP -->
			<!-- SIGN IN -->
			<div class="col align-items-center flex-col sign-in">
                <div class="form-wrapper align-items-center">
                    <form action="login-check.php" method="post" id="logform" class="form sign-in">
                        <div class="input-group">
                            <i class='bx bxs-user'></i>
                            <input type="text" name="username" required placeholder="Enter your username">
                        </div>
                        <div class="input-group">
                            <i class='bx bxs-lock-alt'></i>
                            <input type="password" name="password" id="pass" class="pass-key" required placeholder="Enter your password">
                        </div>
                        <button type="submit">
                            Kirish
                        </button>
                        <p>
                            <span>Ro'yxatdan o'tmaganmisiz?</span>
                            <b onclick="toggle()" class="pointer">Ro'yxatdan o'ting</b>
                        </p>
                    </form>
                </div>
            </div>

			<!-- END SIGN IN -->
		</div>
		<!-- END FORM SECTION -->
		<!-- CONTENT SECTION -->
		<div class="row content-row">
			<!-- SIGN IN CONTENT -->
			<div class="col align-items-center flex-col">
				<div class="text sign-in">
					<h2>
						Xush Kelibsiz
					</h2>
	
				</div>
				<div class="img sign-in">
		
				</div>
			</div>
			<!-- END SIGN IN CONTENT -->
			<!-- SIGN UP CONTENT -->
			<div class="col align-items-center flex-col">
				<div class="img sign-up">
				
				</div>
				<div class="text sign-up">
					<h2>
						Bizga qo'shiling!
					</h2>
	
				</div>
			</div>
			<!-- END SIGN UP CONTENT -->
		</div>
		<!-- END CONTENT SECTION -->
	</div>
    <script src="../assets/js/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/sweetalert.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let container = document.getElementById('container')

        toggle = () => {
            container.classList.toggle('sign-in')
            container.classList.toggle('sign-up')
        }

        setTimeout(() => {
            container.classList.add('sign-in')
        }, 200)
    </script>
   
    <script type="text/javascript">
        $('#logform').submit(function(e){
        e.preventDefault();
        $.ajax({
            url:"login-check.php",
            method:"POST",
            data:$('#logform').serialize(),
            success:function(data){
            let obj = jQuery.parseJSON(data);
            const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
            if(obj.error == 0){
                Toast.fire({
                    icon: 'success',
                    title: obj.message
                });
                setTimeout(function(){
                    window.location.href = '../index.php';
                }, 2000);
            } else {
                $('#pass').val('');
                Toast.fire({
                    icon: 'error', 
                    title: obj.message
                });
            }
            },
            error:function(){
            alert("There is a problem with your internet connection. Please try again!");
            }
        });
        })
    </script>
    <script type="text/javascript">
        // Handle floating labels for inputs
        function handleFloatingLabels() {
        $('input, select').each(function() {
            if ($(this).val()) {
            $(this).closest('.field').addClass('has-value');
            } else {
            $(this).closest('.field').removeClass('has-value');
            }
        });
        }

        $(document).ready(function() {
        handleFloatingLabels();
        });

        $('input, select').on('input change', function() {
        handleFloatingLabels();
        });

        $('#lgn').on("keyup", function(){
        let l = $(this).val();
        $.ajax({
            url:"check_username.php",
            method:"POST", 
            data:{ username:l },
            success:function(data){
            let obj = jQuery.parseJSON(data);          
            if(obj.error==0){
                $('#helpblock').css('display','block').html(obj.message);
            }else{
                $('#helpblock').css('display','none');
            }
            },
            error:function(){
            alert("There is a problem with your internet connection. Please try again!");
            }
        })
        });

        $('#regform').submit(function(e){
        e.preventDefault();
        let p1 = $('#pas1').val();
        let p2 = $('#pas2').val();
        if(p1 != p2){
            $('#mesg').html('The passwords did not match!').show();
            return false;
        }
        $.ajax({
            url: "add_client.php",
            method: "POST",
            data: $('#regform').serialize(),
            dataType: "json", 
            success: function(obj){  
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });

                if(obj.error == 0){
                    Toast.fire({
                        icon: 'success',
                        title: obj.message
                    });
                    setTimeout(function(){
                        window.location.href = '../index.php';
                    }, 2000);
                } else {
                    Toast.fire({
                        icon: 'error', 
                        title: obj.message
                    });
                }
            },
            error: function(xhr, status, error){
                console.log(xhr.responseText);
                alert("There is a problem. Please try again!");
            }
        });

        });
    </script>
</body>
</html>