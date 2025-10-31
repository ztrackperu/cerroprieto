<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo base_url.'Assets'; ?>/css/styleLogin.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo base_url; ?>Assets/css/fonts/remixicon.css" />
    <!--<link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>-->
    <!--<link href='<?php echo base_url.'Assets'; ?>/css/boxicons.min.css' rel='stylesheet'>-->
    <title>Bienvenido a mi Formulario</title>
</head>
<body>
    <div class="container-form sign-up">
        <div class="welcome">
            <div class="message">
                <h2>Welcome to ZTRACK</h2>
                <p>If you already have an account please log in here</p>
                <button class="sign-up-btn">Log In</button>
            </div>
        </div>
        <form class="formularioInformacion">
            <h2 class="create-account">We hear you!</h2>
            <div class="iconos">
                <div class="border-icon-instagram">
                    <a href="#" target="_blank"><i class="ri-instagram-fill"></i></a>
                </div>
                <div class="border-icon-youtube">
                    <a href="https://www.youtube.com/@zgroupsac/videos" target="_blank"><i class="ri-youtube-fill"></i></a>
                </div>
                <div class="border-icon-facebook">
                    <a href="https://www.facebook.com/ZgroupSac/?locale=es_LA" target="_blank"><i class="ri-facebook-circle-fill"></i></a>
                </div>
            </div>
            <p class="cuenta-gratis">Request information from Ztrack</p>
            <input type="text" placeholder="Name">
            <input type="email" placeholder="Email">
            <input type="password" placeholder="Information requested">
            <button type="button" class="btn-solicitar-info">Request</button>
        </form>
    </div>
    <div class="container-form sign-in">
        <form class="formulario" id="frmLogin" onsubmit="frmLogin(event);">
            <div class="logo">
            <br><br>
            <img class="formulario_logo "src="<?php echo base_url.'Assets'; ?>/img/logo.png" alt="logo" width="240" height="60">
            <p>By ZGROUP</p>
            </div>
            <div class="iconos">
                <div class="border-icon-instagram">
                    <a href="#"><i class="ri-instagram-fill"></i></a>
                </div>
                <div class="border-icon-youtube">
                    <a href="https://www.youtube.com/@zgroupsac/videos" target="_blank"><i class="ri-youtube-fill"></i></a>
                </div>
                <div class="border-icon-facebook">
                    <a href="https://www.facebook.com/ZgroupSac/?locale=es_LA" target="_blank"><i class="ri-facebook-circle-fill"></i></a>
                </div>
            </div>
            <br><br>
            <input type="text" placeholder="Username" id="usuario" name="usuario" autofocus required>
            <input type="password" placeholder="Password" id="clave" name="clave" required>
            <input type="hidden"  id="utc" name="utc" >

            <button class="btn-solicitar" type="submit">Enter</button>
            <p class="cuenta-gratis">Don't have an account yet?</p>
        </form>
        <div class="welcome-back">
            <div class="message">
                <h2>Welcome to ZTRACK!</h2>
                <p>If you need information about our service, please register here</p>
                <button class="sign-in-btn">Request</button>
                
            </div>
        </div>
    </div>
    <script src="<?php echo base_url; ?>Assets/js/jquery.min.js"></script>
  <!--<script src="<?php echo base_url; ?>Assets/admin/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>-->
  <script src="<?php echo base_url; ?>Assets/js/sweetalert2.all.min.js"></script>
  <script>
        const base_url = '<?php echo base_url; ?>';
       console.log(base_url);
    </script>
      <script src="<?php echo base_url.'Assets'; ?>/js/LoginPage.js "></script>
    <script src="<?php echo base_url.'Assets'; ?>/js/scriptLogin.js"></script>
</body>
</html>