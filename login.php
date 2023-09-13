<!DOCTYPE html>
<html lang="en">
  <head>
  <link href="https://fonts.googleapis.com/css?family=Roboto:300,400&display=swap" rel="stylesheet">

<link rel="stylesheet" href="./assets/style/fonts/icomoon/style.css">

<link rel="stylesheet" href="./assets/style/css/owl.carousel.min.css">

<!-- Bootstrap CSS -->
<link rel="stylesheet" href="./assets/style/css/bootstrap.min.css">

<!-- Style -->
<link rel="stylesheet" href="./assets/style/css/style.css">
  </head>
<?php 
session_start();
include('./db_connect.php');
  ob_start();
  // if(!isset($_SESSION['system'])){

    $system = $conn->query("SELECT * FROM system_settings")->fetch_array();
    foreach($system as $k => $v){
      $_SESSION['system'][$k] = $v;
    }
  // }
  ob_end_flush();
?>
<?php 
if(isset($_SESSION['login_id']))
header("location:index.php?page=home");

?>
<?php include 'header.php' ?>
<div class="content">
    <div class="container">
      <div class="row">
        
        <div class="col-md-6  order-md-2" style="justify-content: center; display: flex; align-items: center;">
          <div class="row justify-content-center">
            <div class="col-md-8">
              <div class="mb-4">
                  <center><a href="#" class="text-white"><b style="color: cornflowerblue; font-size: 30px;"><?php echo $_SESSION['system']['name'] ?> - Admin</b></a></center>
            </div>
            <form action="" id="login-form">
              <div class="form-group first">
                <input type="email" class="form-control" name="email" required placeholder="Email">

              </div>
              <div class="form-group last mb-4">
                <input type="password" class="form-control"  name="password" required placeholder="Mật khẩu">
                
              </div>
              
              <div class="d-flex mb-5 align-items-center">
                <label class="control control--checkbox mb-0"><span class="caption">Lưu đăng nhập</span>
                  <input type="checkbox" checked="checked"/>
                  <div class="control__indicator"></div>
                </label>
              </div>

              <input type="submit" value="Đăng nhập" class="btn text-white btn-block btn-primary">

              
            </form>
            </div>
          </div>
          
        </div>
        <div class="col-md-6 contents" style="justify-content: center; display: flex; align-items: center;">
          <img src="./assets/style/images/3961373.jpg" alt="Image" class="img-fluid">
        </div>
        
      </div>
    </div>
  </div>
<!-- /.login-box -->
<script>
  $(document).ready(function(){
    $('#login-form').submit(function(e){
    e.preventDefault()
    start_load()
    if($(this).find('.alert-danger').length > 0 )
      $(this).find('.alert-danger').remove();
    $.ajax({
      url:'ajax.php?action=login',
      method:'POST',
      data:$(this).serialize(),
      error:err=>{
        console.log(err)
        end_load();

      },
      success:function(resp){
        if(resp == 1){
          location.href ='index.php?page=home';
        }else{
          $('#login-form').prepend('<div class="alert alert-danger">Tên đăng nhập hoặc tài khoản của bạn không chính xác.</div>')
          end_load();
        }
      }
    })
  })
  })
</script>
<?php include 'footer.php' ?>
<script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
