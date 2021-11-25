<?php
require_once 'app/Login/models/app_autoload.php';
class controller
{
    public static function Login()
    {
        //require_once 'app/Login/views/assets/header.html';
        require_once 'app/Login/views/modules/login.phtml';

        if (isset($_POST['btnIniciarSession'])) {

            $user = $_POST['user'];
            $pass = md5($_POST['pass']);

            $validacion1 = mysqli_num_rows(crud::Read(query::ValidacionLoginMaster($user, $pass)));
            $validacion2 = mysqli_num_rows(crud::Read(query::ValidacionLoginAdmin($user, $pass)));
            $validacion3 = mysqli_num_rows(crud::Read(query::ValidacionLoginAsistant($user, $pass)));

            if ($validacion1) {
                $_SESSION['Master'] = $user;
                echo "<script> 
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                      toast.addEventListener('mouseenter', Swal.stopTimer)
                      toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                  })
                  
                  Toast.fire({
                    icon: 'success',
                    title: 'Te has logueado con Exito',
                    text: 'Ingresando al sistema',
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown'
                      },
                      hideClass: {
                        popup: 'animate__animated animate__fadeOutUp'
                      }
                    
                  })
                    setTimeout(function(){
                        window.location.href = '?c=Home&a=Home';
                    }, 3000);
                </script>";
            } elseif ($validacion2) {
                $_SESSION['Admin'] = $user;
                header('Location:./');
            } elseif ($validacion3) {
                $_SESSION['Asistant'] = $user;
                header('Location:./');
            } else {

                //echo '<center class="text-danger"><b>Usuario o Contraseña invalido</b></center>';
                echo "<script> 
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                      toast.addEventListener('mouseenter', Swal.stopTimer)
                      toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                  })
                  
                  Toast.fire({
                    icon: 'error',
                    title: 'Verifica los datos introducidos',
                    text: 'Usuario o Contraseña invalido',
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown'
                      },
                      hideClass: {
                        popup: 'animate__animated animate__fadeOutUp'
                      }
                    
                  })
                  
                </script>";
                echo '</body>';
                echo '</html>';
            }
        }
        //require_once 'app/Login/views/template/footer.html';
    }
}
