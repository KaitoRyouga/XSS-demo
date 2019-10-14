<!DOCTYPE html>
<html lang="en">

<head>
  <link rel="stylesheet" type="text/css" href="style_Login.css">
  <meta charset="UTF-8">
  <title>Login</title>
</head>
<body>
  <div class="wrapper fadeInDown">
    <div id="formContent">
      <!-- Tabs Titles -->
      <h2 class="active"> Sign In </h2>
      <h2 class="inactive underlineHover">Sign Up </h2>

      <?php
        require_once 'class/user.php';
        require_once 'config2.php';

        $user = new user();

        if (!empty($_POST["submit"])) {
          $username = $_POST["username"];
          $password = $_POST["login"];
          if ($user->login($username, $password)) {
            $user->msg = "Login successfully";
            $user->print_msg();
            $user->assign_session($username);
            echo "<SCRIPT type='text/javascript'>
                    window.location.replace(\"./index.php\");
                  </SCRIPT>";
          }else{
            $user->msg = "Login faill";
            $user->print_msg();
          }
        }
      ?>

      <!-- Icon -->
      <div class="fadeIn first">
        <img style="width: 30px; height: 30px;" src="images/user.svg" id="icon" alt="User Icon" />
      </div>

      <!-- Login Form -->
      <form id="main-form" method="POST" action="#">
        <input type="text" class="fadeIn second" name="username" placeholder="Username">
        <input type="text" class="fadeIn third" name="login" placeholder="Password">
<input type="submit" name="submit" class="fadeIn fourth" value="Log In">
       
      </form>

      <!-- Remind Passowrd -->
      
      <div id="formFooter">
        <a class="underlineHover" href="#">Forgot Password?</a>
      </div>

    </div>
  </div>
</body>

</html>