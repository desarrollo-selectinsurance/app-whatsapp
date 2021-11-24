<?php
require_once 'app/master/models/app_autoload.php';

//Funciones para requerir encabezado, pie de pagina y menu
function higher()
{
    require_once 'app/master/views/template/header.html';
}
/* function Nav()
{
    //require_once 'app/master/views/assets/menu.phtml';
    require_once 'app/master/views/assets/contentheader.phtml';
} */

function lower()
{
    require_once 'app/master/views/assets/contentfooter.phtml';
    require_once 'app/master/views/template/footer.html';
}


class controller
{
    //Filtrando Sala desde Nav
    public static function FiltrandoSalaNav()
    {
        $filtrarNav = '';
        $user = $_SESSION['Master'];
        if (!isset($_POST['filtrarNav'])) {

            //Recibiendo Salas de chat abiertas desde la app de whatsapp
            $AwebT = mysqli_fetch_assoc(crud::Read(query::ReadAwebT($user)));
            if (isset($AwebT['Instance']) && isset($AwebT['Token'])) {
                $ChatApi = new ChatApi($AwebT['Instance'], $AwebT['Token']);
                $json = $ChatApi->Dialogs();
                echo $json;

                //Insercion a la base de Datos de los dialogs consultados a la APi de Whatsapp
                $array = array();
                $array = json_decode($json, true);
                foreach ($array as $key => $value) {
                    $j = count($value);
                    $i = 0;
                    $name = array();
                    while ($i < $j) {
                        $name[$i]['name'] = str_replace(' ', '', $value[$i]['name']);
                        crud::Create(query::CreateDialogs($value[$i]['id'], $name[$i]['name'], $value[$i]['image'], $value[$i]['last_time']));
                        crud::Update(query::UpdateImageDialogs($value[$i]['id'], $value[$i]['image']));
                        $i++;
                    }
                }
            } else {
                $name = array();
                $name = [
                    'name' => 'No existe Token'
                ];
                print json_encode($name);
            }
        } else {
            $filtrarNav = $_POST['filtrarNav'];

            //Salas de chat almacenadas en base de datos
            $consulta = crud::Read(query::ReadDialogsByName($filtrarNav));
            $i = 0;
            $Array = array();
            while ($rows = mysqli_fetch_assoc($consulta)) {
                $Array[$i]['id'] = $rows['id'];
                $Array[$i]['name'] = $rows['name'];
                $Array[$i]['image'] = $rows['image'];
                $Array[$i]['last_time'] = $rows['last_time'];
                $Array[$i]['abierto'] = $rows['abierto'];
                $Array[$i]['seguimiento'] = $rows['seguimiento'];
                $Array[$i]['Asignador'] = $rows['Asignador'];
                $Array[$i]['idAgentes'] = $rows['idAgentes'];
                $i++;
            }
            print  json_encode($Array, JSON_PRETTY_PRINT);
        }
    }


    //DashBoard
    public static function Inicio()
    {
        if (isset($_SESSION['Master'])) {


            //Logica para cerrar chat
            if (isset($_POST['btnCerrarChatConMensaje'])) {
                $user = $_SESSION['Master'];


                //Envio de mensaje pregrabado
                $resultados = crud::Read(query::ReadMensajeDespedidaChat($user));
                $mensajeDespedida = mysqli_fetch_assoc($resultados);
                if (isset($mensajeDespedida)) {

                    $UrlToken = mysqli_fetch_assoc(crud::Read(query::ReadAwebT($user)));
                    $Api = new ChatApi($UrlToken['Instance'], $UrlToken['Token']);
                    $message = $mensajeDespedida['cuerpo'];
                    $Phone = $_POST['btnCerrarChatConMensaje'];
                    $Api->SendMenssage($Phone, $message);
                }




                //Cerrando chat abierto
                $id = $_POST['btnCerrarChatConMensaje'] . '@c.us';
                crud::Update(query::UpdateDialogsCerrarChat($id));
            } elseif (isset($_POST['btnCerrarChat'])) {

                $id = $_POST['btnCerrarChat'] . '@c.us';
                crud::Update(query::UpdateDialogsCerrarChat($id));
            }

            higher();
            /* Nav(); */

            require_once 'app/master/views/dashboard/dashboard.phtml';
            lower();
        } else {
            header('Location:?controller=Login');
        }
    }

    //Validacion cuando ingresan al login logeados
    public static function Login()
    {
        header('Location:?controller=Inicio');
    }

    //Cerrar Session
    public static function Cerrar()
    {
        session_destroy();
        header('Location:./');
    }

    //Reiniciando instancia para actualizar fotos y estados de whatsapp
    public static function ReiniciarEstancia()
    {
        $user = $_SESSION['Master'];
        $consultaAWToken = mysqli_fetch_assoc(crud::Read(query::ReadAwebT($user)));
        $api = new ChatApi($consultaAWToken['Instance'], $consultaAWToken['Token']);
        $respuesta = $api->RebootInstance();
        print($respuesta);
    }

    //configuracion
    public static function Preferences()
    {
        higher();
        
        $Resultado = crud::Read(query::ReadAgentes());
        require_once 'app/master/views/preferencias/preferences.phtml';
        lower();
    }
    public static function Settings()
    {
        higher();

        require_once 'app/master/views/settings/settings.phtml';
        lower();
    }

    public static function Profile()
    {
        higher();
        
        require_once 'app/master/views/profile/profile.phtml';
        lower();
    }
    public static function SalasDeChat()
    {
        higher();
        
        require_once 'app/master/views/salasdechat/salasdechat.phtml';
        lower();
    }

    //Json que se muestra en el dataTable para consultar Agente
    public static function Datatable()
    {
        $ReadAgente = crud::Read(query::ReadAgentes());
        while ($Resultado = mysqli_fetch_assoc($ReadAgente)) {
            $rows["data"][] = $Resultado;
        }
        echo json_encode($rows);
    }

    //Insertando Datos por metodo post usando Ajax de jquery
    public static function AgregarAgente()
    {
        $creador = $_SESSION['Master'];
        $user = $_POST['user'];
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $documento = $_POST['documento'];

        $admin = '';
        if (isset($_POST['admin'])) {
            $admin = boolval($_POST['admin']);
        }
        $master = '';
        if (isset($_POST['master'])) {
            $master = boolval($_POST['master']);
        }

        $telefono = $_POST['telefono'];
        $direccion = $_POST['direccion'];
        $correo = $_POST['correo'];
        $password = md5($_POST['password']);
        $ConfirmacionPassword = md5($_POST['ConfirmacionPassword']);

        //Validacion de pass identica
        if ($password === $ConfirmacionPassword) {


            //Validacion de usuario master o no
            if ($master === TRUE) {
                crud::Create(query::CreateUsuario(
                    $user,
                    $password,
                    TRUE,
                    TRUE
                ));

                crud::Create(query::CreateAgente(
                    $nombre,
                    $apellido,
                    $documento,
                    $telefono,
                    $direccion,
                    $correo,
                    $creador,
                    $user
                ));
            } elseif ($admin == TRUE && $master == FALSE) {
                crud::Create(query::CreateUsuario(
                    $user,
                    $password,
                    1,
                    0
                ));

                crud::Create(query::CreateAgente(
                    $nombre,
                    $apellido,
                    $documento,
                    $telefono,
                    $direccion,
                    $correo,
                    $creador,
                    $user
                ));
            } else {
                crud::Create(query::CreateUsuario(
                    $user,
                    $password,
                    0,
                    0
                ));

                crud::Create(query::CreateAgente(
                    $nombre,
                    $apellido,
                    $documento,
                    $telefono,
                    $direccion,
                    $correo,
                    $creador,
                    $user
                ));
            }

            echo 'Agente Registrado Correctamente';
            
        } else {
            echo 'Las contraseñas no coinciden';
        }
    }

    //Cambiando Contraseña de los Agentes usando Ajax por metodo post
    public static function CambiarContrasena()
    {
        if (isset($_POST['btnCambiarContrasenaAgente'])) {
            $User = $_POST['UsuarioAgenteCambioContrasena'];
            $NuevaContrasena = $_POST['NuevaContrasena'];
            $confirmarNuevaContrasena = $_POST['ConfirmarNuevaContrasena'];
            if ($NuevaContrasena === $confirmarNuevaContrasena) {
                crud::Update(query::UpdatePassword($User, md5($NuevaContrasena)));
                header('Location:./?controller=Preferences');
            }
        }
    }



    //TODO LO RELACIONADO A LOS CHAT
    // add Sala de chat individual
    public static function ModulosChat()
    {
        higher();
        //Nav();
        require_once 'app/master/views/ModulosChat/modulosChat.phtml';
        lower();
    }

    //Abrir Sala de chat individual
    public static function AbrirSalaChat()
    {
        //Condicion para obligar a tener si o si una sala de chat
        if (!empty($_POST['btnAbrirChat']) || isset($_POST['btnAddSalaChat'])) {

            //Condicion para Agregar nuevo chat o no
            if (!empty($_POST['NumeroCliente'])) {
                $user = $_SESSION['Master'];
                $id = $_POST['codigopais'] . $_POST['NumeroCliente'] . '@c.us';
                $SalaChat = $_POST['codigopais'] . $_POST['NumeroCliente'];
            } else {
                $user = $_SESSION['Master'];
                $id = $_POST['btnAbrirChat'];
                $SalaChat = str_replace('@c.us', '', $_POST['btnAbrirChat']);
            }

            //Imagen Guardada
            $resultado = crud::Read(query::ReadImageDialogs($id));
            $image = mysqli_fetch_assoc($resultado);

            //ChatAbiertos
            if (isset($_POST['btnAbrirChat'])) {
                crud::Update(query::UpdateDialogsAbrirChat($_POST['btnAbrirChat']));
            }

            //Mostrando mensaje de despedida en el modal de cerrar chat
            $consulta = mysqli_fetch_assoc(crud::Read(query::ReadMensajeDespedidaChat($user)));

            higher();
            Nav();
            require_once 'app/master/views/chat/chat.phtml';
            lower();
        } else {
            header('Location:./');
        }
    }

    //Mostrar Mensajes de chat individual
    public static function MostrarMensajesChat()
    {
        if (!empty($_POST['chatId'])) {
            $user = $_SESSION['Master'];
            $id =  $_POST['chatId'];
            //$id =  '573166857000@c.us';
            $url = mysqli_fetch_assoc(crud::Read(query::ReadAwebT($user)));
            $api = new ChatApi($url['Instance'], $url['Token']);
            $data = $api->messages();
            //var_dump($data['messages']);

            //cambiando ciclo foreach por ciclo while para hacer insercion a la base de datos usando 
            //la cantidad de indices que tiene el array
            $contador = count($data['messages']);
            $i = 0;
            $sender = array();
            while ($i < $contador) {
                $author = trim($data['messages'][$i]['author']);
                $chatId = trim($data['messages'][$i]['chatId']);
                if ($author == $chatId) {
                    $sender[$i] = $author;
                } else {
                    $sender[$i] = $_SESSION['Master'];
                }

                $resultado[$i] = crud::Create(query::CreateAlmacenarMensajes(
                    $data['messages'][$i]['id'],
                    $data['messages'][$i]['body'],
                    $data['messages'][$i]['fromMe'],
                    $data['messages'][$i]['self'],
                    $data['messages'][$i]['isForwarded'],
                    $data['messages'][$i]['author'],
                    $data['messages'][$i]['time'],
                    $data['messages'][$i]['chatId'],
                    $data['messages'][$i]['messageNumber'],
                    $data['messages'][$i]['type'],
                    $data['messages'][$i]['senderName'],
                    $data['messages'][$i]['quotedMsgBody'],
                    $data['messages'][$i]['quotedMsgId'],
                    $data['messages'][$i]['quotedMsgType'],
                    $data['messages'][$i]['metadata'],
                    $data['messages'][$i]['ack'],
                    $data['messages'][$i]['chatName'],
                    $sender[$i]
                ));
                $i++;
            }

            $consulta = crud::Read(query::ReadMensajesChat($id));
            $i = 0;
            $Array = array();
            while ($row = mysqli_fetch_assoc($consulta)) {

                $Array[$i]['id']              =   $row['id'];
                $Array[$i]['body']            =   $row['body'];
                $Array[$i]['fromMe']          =   $row['fromMe'];
                $Array[$i]['isForwarded']     =   $row['isForwarded'];
                $Array[$i]['author']          =   $row['author'];
                $Array[$i]['time']            =   $row['time'];
                $Array[$i]['chatId']          =   $row['chatId'];
                $Array[$i]['messageNumber']   =   $row['messageNumber'];
                $Array[$i]['type']            =   $row['type'];
                $Array[$i]['senderName']      =   $row['senderName'];
                $Array[$i]['quotedMsgBody']   =   $row['quotedMsgBody'];
                $Array[$i]['quotedMsgId']     =   $row['quotedMsgId'];
                $Array[$i]['quotedMsgType']   =   $row['quotedMsgType'];
                $Array[$i]['metadata']        =   $row['metadata'];
                $Array[$i]['ack']             =   $row['ack'];
                $Array[$i]['chatName']        =   $row['chatName'];
                $Array[$i]['FechaHora']       =   $row['FechaHora'];
                $Array[$i]['sender']          =   str_replace('@c.us', '', $row['sender']);
                $i++;
            }
            print json_encode($Array, JSON_PRETTY_PRINT);
        }
    }

    //Mostrar si el cliente esta conectado o no
    public static function MostrarEstadoConectado()
    {
        if (!empty($_POST['chatId'])) {
            $phone = $_POST['chatId'];
            $user = $_SESSION['Master'];

            $AccesWebToken = mysqli_fetch_assoc(crud::Read(query::ReadAwebT($user)));
            $ChatApi = new ChatApi($AccesWebToken['Instance'], $AccesWebToken['Token']);

            print $ChatApi->userStatus($phone)['status'];
        }
    }

    //Mostrando mensaje Escribiendo a el cliente
    public static function MostrarEscribiendoaCliente()
    {
        $user = $_SESSION['Master'];
        $UrlToken = mysqli_fetch_assoc(crud::Read(query::ReadAwebT($user)));
        $Api = new ChatApi($UrlToken['Instance'], $UrlToken['Token']);
        $Phone = $_POST['chatId'];
        $message = $_POST['txtCuerpoMensage'];
        echo $Api->typing($Phone);
    }

    //Enviar Mensajes de chat individual
    public static function EnviarMensajesChat()
    {

        $user = $_SESSION['Master'];
        $UrlToken = mysqli_fetch_assoc(crud::Read(query::ReadAwebT($user)));
        $Api = new ChatApi($UrlToken['Instance'], $UrlToken['Token']);
        $Phone = $_POST['chatId'];
        $message = $_POST['txtCuerpoMensage'];
        echo $Api->SendMenssage($Phone, $message);
        //echo $Phone.' '.$message;
    }

    //Cantidad Salas de Chat
    public static function CantidadSalasChat()
    {
        $consulta = crud::Read(query::ReadDialogs());
        $i = 0;
        $Array = array();
        while ($row = mysqli_fetch_array($consulta)) {
            $Array[$i]['id'] = $row['id'];
            $Array[$i]['name'] = $row['name'];
            $Array[$i]['image'] = $row['image'];
            $Array[$i]['last_name'] = '';

            if (isset($row['last_name'])) {
                $Array[$i]['last_name'] = $row['last_name'];
            }

            $i++;
        }
        $conteo = count($Array);
        echo $conteo;
    }

    //Mostrar Mensaje de Despedida
    public static function PlantillasMensajes()
    {
        higher();
        $usuario = $_SESSION['Master'];
        require_once 'app/master/views/plantillasmensajes/plantillasmensajes.phtml';

        lower();
    }

    //Ingreso de Mensaje de Despedida
    public static function CreateMensajeDespedida()
    {
        $cuerpo =  $_POST['txtMensajeDespedida'];
        $usuario = $_SESSION['Master'];
        $success = crud::Create(query::CreateMensajeDespedida($cuerpo, $usuario));
        if ($success != null) {
            echo 'Registro Exitoso';
        } else {
            echo 'Error';
        }
    }

    //Mostrar Mensaje de Despedida
    public static function MostrandoMensajeDespedida()
    {
        $consulta = crud::Read(query::ReadMensajeDespedida());
        $i = 0;
        $Array = array();
        while ($resultados = mysqli_fetch_assoc($consulta)) {
            $Array[$i]['id'] = $resultados['id'];
            $Array[$i]['cuerpo'] = $resultados['cuerpo'];
            $Array[$i]['usuario'] = $resultados['usuario'];
            $Array[$i]['fecha'] = $resultados['fecha'];
            $i++;
        }

        print json_encode($Array, JSON_PRETTY_PRINT);
    }

    //Eliminar Mensaje de Despedida
    public static function DeleteMensajeDespedida()
    {
        $Array = $_POST['EliminarMensajeDespedida'];
        foreach ($Array as $indice) {
            $id =  $indice;
        }
        crud::Delete(query::DeleteMensajeDespedida($id));
        echo 'Mensaje Eliminado con Exito';
    }
    ///////////////////////////////////////





    //ACA COMIENZA TODO LO DEL ACCES WEB TOKEN
    //form para insertar accesweb token
    public static function AccesWebToken()
    {
        higher();
        
        require_once 'app/master/views/acceswebtokens/acceswebtokens.phtml';

        lower();
    }

    //ingreso de AccesWebToken por ajax por metodo post
    public static function InsertAccesWebToken()
    {
        $user = $_SESSION['Master'];
        $instance = trim($_POST['instancia']);
        $token = trim($_POST['token']);
        crud::Read(query::CreateAwebT($instance, $token, $user));
        echo 'Registro Exitoso';
    }

    //Mostrando AccesWebToken por ajax en la tabla
    public static function ReadAccesWebToken()
    {
        $user = $_SESSION['Master'];
        $Consulta = crud::Read(query::ReadAwebT($user));
        $i = 0;
        $Array = array();
        while ($rows = mysqli_fetch_assoc($Consulta)) {

            $Array[$i]['idToken'] = $rows['idToken'];
            $Array[$i]['Instance'] = $rows['Instance'];
            $Array[$i]['Token'] = $rows['Token'];
            $i++;
        }
        $json = json_encode($Array, JSON_PRETTY_PRINT);
        print $json;
    }
    //////////////////////////////////////////





    //TODO LO RELACIONADO CON LA TRANSFERENCIA DE SALAS DE CHAT
    //Modulo Transferir chat
    public static function TransferirChat()
    {
        $id = str_replace('@c.us', '', $_GET['Id']);
        higher();
        Nav();


        require_once 'app/master/views/TransferenciaChat/TransferenciaChat.phtml';
        lower();
    }

    //Consultando Datos del Modulo Transferir
    public static function ConsultandoUsuarioATransferir()
    {
        $Consulta = crud::Read(query::ReadAgentes());
        while ($Resultado = mysqli_fetch_assoc($Consulta)) {
            $rows["data"][] = $Resultado;
        }
        echo json_encode($rows);
    }

    //Transfiriendo Sala Chat a un Agente
    public static function UpdateDialogs()
    {
        if (isset($_POST['SeleccionSalaChat'])) {
            $idAgente = $_POST['IdAgenteTransferir'];
            $id = $_POST['SeleccionSalaChat'] . '@c.us';
            crud::Update(query::UpdateDialogs($idAgente, $id));
            echo 'Transferencia Exitosa';
        } else {
            echo 'No se pudo Transferir';
        }
    }
    /////////////////////////////////////////////





    //TODO LO RELACIONADO A LOS CONTEOS DEL CHAT
    //Mostrando Cantidad chat abiertos
    public static function MostrandoChatAbiertos()
    {
        $consulta = '';
        $consulta = crud::Read(query::ReadChatAbiertos());
        $i = 0;
        $Array = array();
        while ($row = mysqli_fetch_array($consulta)) {
            $Array[$i]['abierto'] = $row['abierto'];
            $i++;
        }
        $conteo = count($Array);
        echo $conteo;
    }

    //Mostrar Tabla Dialogs totales
    public static function MostrarTablaChatAcumulado()
    {
        $datos = '';
        if (isset($_POST['FiltroTablaTotal'])) {
            $datos = $_POST['FiltroTablaTotal'];
        }
        //echo $datos;
        if (!empty($datos)) {
            $consulta = crud::Read(query::ReadDialogsFiltrando($datos));
            $i = 0;
            $Array = array();
            while ($row = mysqli_fetch_assoc($consulta)) {
                $Array[$i]['id'] = $row['id'];
                $Array[$i]['name'] = $row['name'];
                $Array[$i]['image'] = $row['image'];
                $Array[$i]['Asignador'] = $row['Asignador'];
                $Array[$i]['idAgentes'] = $row['idAgentes'];
                $i++;
            }
        } else if (empty($datos)) {
            $consulta = crud::Read(query::ReadDialogs());
            $i = 0;
            $Array = array();
            while ($row = mysqli_fetch_assoc($consulta)) {
                $Array[$i]['id'] = $row['id'];
                $Array[$i]['name'] = $row['name'];
                $Array[$i]['image'] = $row['image'];
                $Array[$i]['Asignador'] = $row['Asignador'];
                $Array[$i]['idAgentes'] = $row['idAgentes'];
                $i++;
            }
        }
        print json_encode($Array, JSON_PRETTY_PRINT);
    }

    //Mostrar Tabla Dialogs Abiertos
    public static function MostrarTablaChatAbiertos()
    {
        //echo $_POST['FiltroTablaAbiertos'];
        $datos = '';
        if (isset($_POST['FiltroTablaAbiertos'])) {
            $datos = $_POST['FiltroTablaAbiertos'];
        }
        $consulta = crud::Read(query::ReadDialogsFiltrandoAbiertos($datos));
        $i = 0;

        $Array = array();
        while ($row = mysqli_fetch_assoc($consulta)) {
            $Array[$i]['id'] = $row['id'];
            $Array[$i]['name'] = $row['name'];
            $Array[$i]['image'] = $row['image'];
            $Array[$i]['last_time'] = $row['last_time'];
            $Array[$i]['abierto'] = $row['abierto'];
            $Array[$i]['Asignador'] = $row['Asignador'];
            $Array[$i]['idAgentes'] = $row['idAgentes'];
            $i++;
        }
        print json_encode($Array, JSON_PRETTY_PRINT);
    }

    //Mostrar tabla Dialogs Cerrados
    public static function MostrarTablaChatCerrados()
    {

        $datos = '';
        if (isset($_POST['FiltroTablaCerrados'])) {
            $datos = $_POST['FiltroTablaCerrados'];
        }

        $consulta = crud::Read(query::ReadDialogsFiltrandoCerrados($datos));
        $i = 0;
        $Array = array();
        while ($row = mysqli_fetch_assoc($consulta)) {
            $Array[$i]['id'] = $row['id'];
            $Array[$i]['name'] = $row['name'];
            $Array[$i]['image'] = $row['image'];
            $Array[$i]['last_time'] = $row['last_time'];
            $Array[$i]['abierto'] = $row['abierto'];
            $Array[$i]['Asignador'] = $row['Asignador'];
            $Array[$i]['idAgentes'] = $row['idAgentes'];
            $i++;
        }
        print json_encode($Array, JSON_PRETTY_PRINT);
    }

    //Mostrar tabla Dialogs Asignados
    public static function MostrarTablaChatAsignados()
    {
        $datos = '';
        if (isset($_POST['FiltroTablaAsignados'])) {
            $datos = $_POST['FiltroTablaAsignados'];
        }
        $consulta = crud::Read(query::ReadDialogsFiltrandoAsignados($datos));
        $i = 0;
        $Array = array();
        while ($row = mysqli_fetch_assoc($consulta)) {
            $Array[$i]['id'] = $row['id'];
            $Array[$i]['name'] = $row['name'];
            $Array[$i]['image'] = $row['image'];
            $Array[$i]['last_time'] = $row['last_time'];
            $Array[$i]['abierto'] = $row['abierto'];
            $Array[$i]['Asignador'] = $row['Asignador'];
            $Array[$i]['idAgentes'] = $row['idAgentes'];
            $i++;
        }
        print json_encode($Array, JSON_PRETTY_PRINT);
    }

    //Mostrar Sala Chat por Id desde Modal
    public static function ConsultandoSalaDesdeModalTotal()
    {
        if (!empty($_POST['btnIdConsultarSala'])) {
            $id = $_POST['btnIdConsultarSala'];

            //sacando el indice usando foreach
            foreach ($id as $indice) {
                $id =  $indice;
            }



            $user = $_SESSION['Master'];
            $id = $indice;
            $SalaChat = str_replace('@c.us', '', $indice);

            //Imagen Guardada
            $resultado = crud::Read(query::ReadImageDialogs($id));
            $image = mysqli_fetch_assoc($resultado);

            //Mostrando mensaje de despedida en el modal de cerrar chat
            $consulta = mysqli_fetch_assoc(crud::Read(query::ReadMensajeDespedidaChat($user)));


            higher();
            Nav();
            require_once 'app/master/views/chat/chat.phtml';
            lower();
        } else {
            header('Location:Inicio');
        }
    }

    //Mostrando Cantidad Chat Asignado a Agentes
    public static function MostrandoChatAsignados()
    {
        $consulta = crud::Read(query::ReadChatAsignados());
        $row = mysqli_fetch_assoc($consulta);
        echo $row['count(idAgentes)'];
    }

    //Mostrando Cantidad Chat Cerrados
    public static function MostrandoChatCerrados()
    {
        $Consulta = crud::Read(query::ReadConteoChatCerrados());
        $resultado = mysqli_fetch_assoc($Consulta);
        echo $resultado['v_conteo'];
    }



    //Tabla para mostrar cantidad de chat asignados a cada agente
    public static function TablaChatAsignadoAgente()
    {
        $consulta = crud::Read(query::ReadChatAsignadosAgentes());

        $i = 0;
        while ($row = mysqli_fetch_assoc($consulta)) {

            //Consultando Agente
            $ArrayAgentes[$i]['id'] = $row['id'];
            $ArrayAgentes[$i]['usuario'] = $row['usuario'];
            $ArrayAgentes[$i]['nombre'] = $row['nombre'];
            $ArrayAgentes[$i]['apellido'] = $row['apellido'];


            //Logica para sacar la consulta con la funcion count de mysql
            $ConteoChat = crud::Read(query::ReadConteoChatPendientes($row['usuario']));
            $conteo = mysqli_fetch_assoc($ConteoChat);
            $ArrayAgentes[$i]['ChatAbiertos'] = $conteo['v_cantidadChatAbiertos'];
            $ArrayAgentes[$i]['ChatPendiente'] = $conteo['v_cantidadChatPendiente'];
            $i++;
        }
        //var_dump($ArrayAgentes);
        $json = json_encode($ArrayAgentes, JSON_PRETTY_PRINT);
        print $json;
    }
    //////////////////////////////////////////




    //TODO LO RELACIONADO CON LOS CHATS ABIERTOS DESDE DASHBOARD
    //Modulo mostrar dialogs Asignados Agente Seleccionado desde Dashboard
    public static function MostrandoDialogAsignadoAgente()
    {
        if (!empty($_POST['idAgente'])) {
            higher();
            
            foreach ($_POST['idAgente'] as $Array) {
                $idAgente = $Array;
            }
            require_once 'app/master/views/conversacion/conversacion.phtml';
            lower();
        } else {
            header('Location:Inicio');
        }
    }

    //Modulo mostrar conversacion de dialog seleccionado desde conversaciones
    public static function MostrarConversacionDialogAsignadoAgente()
    {
        $id = $_POST['idRadio'];
        foreach ($id as $indice) {
            $chatId = $indice;
        }


        $user = $_SESSION['Master'];
        $url = mysqli_fetch_assoc(crud::Read(query::ReadAwebT($user)));
        $api = new ChatApi($url['Instance'], $url['Token']);
        $data = $api->messages();
        //var_dump($data);

        //cambiando ciclo foreach por ciclo while para hacer insercion a la base de datos usando 
        //la cantidad de indices que tiene el array
        $contador = count($data['messages']);
        $i = 0;
        while ($i < $contador) {
            if ($data['messages'][$i]['author'] === $data['messages'][$i]['chatId']) {
                $sender[$i] = $data['messages'][$i]['author'];
            } elseif ($data['messages'][$i]['author'] != $data['messages'][$i]['chatId']) {
                $sender[$i] = $_SESSION['Master'];
            }
            crud::Create(query::CreateAlmacenarMensajes(
                $data['messages'][$i]['id'],
                $data['messages'][$i]['body'],
                $data['messages'][$i]['fromMe'],
                $data['messages'][$i]['self'],
                $data['messages'][$i]['isForwarded'],
                $data['messages'][$i]['author'],
                $data['messages'][$i]['time'],
                $data['messages'][$i]['chatId'],
                $data['messages'][$i]['messageNumber'],
                $data['messages'][$i]['type'],
                $data['messages'][$i]['senderName'],
                $data['messages'][$i]['quotedMsgBody'],
                $data['messages'][$i]['quotedMsgId'],
                $data['messages'][$i]['quotedMsgType'],
                $data['messages'][$i]['metadata'],
                $data['messages'][$i]['ack'],
                $data['messages'][$i]['chatName'],
                $sender[$i]
            ));
            $i++;
        }




        $consulta = crud::Read(query::ReadMensajesChat($chatId));
        $i = 0;
        $Array = array();
        while ($row = mysqli_fetch_assoc($consulta)) {

            $Array[$i]['id']              =   $row['id'];
            $Array[$i]['body']            =   $row['body'];
            $Array[$i]['fromMe']          =   $row['fromMe'];
            $Array[$i]['isForwarded']     =   $row['isForwarded'];
            $Array[$i]['author']          =   $row['author'];
            $Array[$i]['time']            =   $row['time'];
            $Array[$i]['chatId']          =   $row['chatId'];
            $Array[$i]['messageNumber']   =   $row['messageNumber'];
            $Array[$i]['type']            =   $row['type'];
            $Array[$i]['senderName']      =   $row['senderName'];
            $Array[$i]['quotedMsgBody']   =   $row['quotedMsgBody'];
            $Array[$i]['quotedMsgId']     =   $row['quotedMsgId'];
            $Array[$i]['quotedMsgType']   =   $row['quotedMsgType'];
            $Array[$i]['metadata']        =   $row['metadata'];
            $Array[$i]['ack']             =   $row['ack'];
            $Array[$i]['chatName']        =   $row['chatName'];
            $Array[$i]['FechaHora']       =   $row['FechaHora'];
            $Array[$i]['sender']          =   str_replace('@c.us', '', $row['sender']);
            $i++;
        }

        print json_encode($Array, JSON_PRETTY_PRINT);
    }

    //Consultando los datos recibidos por el input de dialogs mostrados en la tabla
    public static function FiltrarDatosTabla()
    {
        $valor = '';
        $id = '';
        if (isset($_POST['SearchDialogs']) && isset($_POST['idAgente'])) {
            $valor = $_POST['SearchDialogs'];
            $id = $_POST['idAgente'];
        }
        if (!empty($valor)) {
            $resultado = crud::Read(query::ReadFiltrarSala($valor, $id));
            $i = 0;
            $Array = array();
            while ($row = mysqli_fetch_assoc($resultado)) {
                $Array[$i]['id']        =   $row['id'];
                $Array[$i]['name']      =   $row['name'];
                $Array[$i]['image']     =   $row['image'];
                $Array[$i]['last_time'] =   $row['last_time'];
                $Array[$i]['abierto']   =   $row['abierto'];
                $Array[$i]['Asignador'] =   $row['Asignador'];
                $Array[$i]['idAgentes'] =   $row['idAgentes'];
                $i++;
            }
        } elseif (empty($valor)) {
            $resultado = crud::Read(query::ReadDialogsAgente($id));
            $i = 0;
            $Array = array();
            while ($row = mysqli_fetch_assoc($resultado)) {
                $Array[$i]['id']        =   $row['id'];
                $Array[$i]['name']      =   $row['name'];
                $Array[$i]['image']     =   $row['image'];
                $Array[$i]['last_time'] =   $row['last_time'];
                $Array[$i]['abierto']   =   $row['abierto'];
                $Array[$i]['Asignador'] =   $row['Asignador'];
                $Array[$i]['idAgentes'] =   $row['idAgentes'];
                $i++;
            }
        }
        print json_encode($Array, JSON_PRETTY_PRINT);
    }

    //Consulta para dirigir al DAtatable de mostrar conversaciones
    public static function MostrarConversacionesConsulta()
    {
        if (!empty($_POST['id'])) {
            $id = $_POST['id'];
            $Resultados = crud::Read(query::ReadChatAgente($id));
            $i = 0;
            while ($conversacion = mysqli_fetch_assoc($Resultados)) {
                $rows[$i]['chatId']         =    $conversacion['chatId'];
                $rows[$i]['sender']         =    $conversacion['sender'];
                $rows[$i]['messageNumber']  =    $conversacion['messageNumber'];
                $rows[$i]['body']           =    $conversacion['body'];
                $i++;
            }

            print json_encode($rows, JSON_PRETTY_PRINT);

            /*   $id = $_POST['id'];
            $Resultados = crud::Read(query::ReadChatAgente($id));
            $conversacion = mysqli_fetch_assoc($Resultados);
            print json_encode($conversacion, JSON_PRETTY_PRINT);
            $i = 0;
            while ($conversacion = mysqli_fetch_assoc($Resultados)) {
                $rows["data"][] = $conversacion;
            }
            
            print json_encode($rows, JSON_PRETTY_PRINT); */
        } else {
            header('Location:Inicio');
        }
    }
    ///////////////////////////////////////////
}
