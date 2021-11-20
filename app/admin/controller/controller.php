<?php

require_once 'app/admin/models/app_autoload.php';

//Funciones para requerir encabezado, pie de pagina y menu
function higher()
{
    require_once 'app/admin/views/assets/header.html';
}

function Nav()
{
    /*     $user = $_SESSION['Admin'];
    //Recibiendo Salas de chat abiertas desde la app de whatsapp
    $AwebT = mysqli_fetch_assoc(crud::Read(query::ReadAwebT($user)));

    if (isset($AwebT['Instance']) && isset($AwebT['Token'])) {
        $ChatApi = new ChatApi($AwebT['Instance'], $AwebT['Token']);
        $array = $ChatApi->Dialogs();

        //logica para sacar cantidad de indices y recorrer el array con la cantidad de indices
        foreach ($array as $key => $value) {
            $j = count($value);
            $i = 0;

            while ($i < $j) {
                $value[$i]['user'] = $user;
                crud::Create(query::CreateDialogs($value[$i]['id'], $value[$i]['name'], $value[$i]['image'], $value[$i]['last_time'], $value[$i]['user']));
                $i++;
            }
        }

        //Salas de chat almacenadas en base de datos
        $consulta = crud::Read(query::ReadDialogs($user));
    } else {
        $consulta = '<center>No existe Token</center>';
        echo $consulta;
    } */

    require_once 'app/admin/views/assets/menu.phtml';
}

function lower()
{
    require_once 'app/admin/views/assets/footer.html';
}


class controller
{

    //Filtrando Sala desde Nav
    public static function FiltrandoSalaNav()
    {
        $filtrarNav = '';
        $user = $_SESSION['Admin'];
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
                        $value[$i]['user'] = $user;
                        $name[$i]['name'] = str_replace(' ', '', $value[$i]['name']);
                        crud::Create(query::CreateDialogs($value[$i]['id'], $name[$i]['name'], $value[$i]['image'], $value[$i]['last_time'], $value[$i]['user']));
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
            $consulta = crud::Read(query::ReadDialogsByName($filtrarNav, $user));
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
        if (isset($_SESSION['Admin'])) {


            //Logica para cerrar chat
            if (isset($_POST['btnCerrarChatConMensaje'])) {
                $user = $_SESSION['Admin'];


                //Envio de mensaje pregrabado
                $resultados = crud::Read(query::ReadMensajeDespedidaChat($user));
                $mensajeDespedida = mysqli_fetch_assoc($resultados);

                $UrlToken = mysqli_fetch_assoc(crud::Read(query::ReadAwebT($user)));
                $Api = new ChatApi($UrlToken['Instance'], $UrlToken['Token']);
                $message = $mensajeDespedida['cuerpo'];
                $Phone = $_POST['btnCerrarChatConMensaje'];
                $Api->SendMenssage($Phone, $message);





                //Cerrando chat abierto
                $id = $_POST['btnCerrarChatConMensaje'] . '@c.us';
                crud::Update(query::UpdateDialogsCerrarChat($id));
            } elseif (isset($_POST['btnCerrarChat'])) {

                $id = $_POST['btnCerrarChat'] . '@c.us';
                crud::Update(query::UpdateDialogsCerrarChat($id));
            }

            higher();
            Nav();

            require_once 'app/admin/views/modules/dashboard/dashboard.phtml';
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

    //Preferencias
    public static function Preferences()
    {
        higher();
        Nav();
        $user = $_SESSION['Admin'];
        $Resultado = crud::Read(query::ReadAgentes($user));
        require_once 'app/admin/views/modules/Preferencias/preferences.phtml';
        lower();
    }

    //Json que se muestra en el dataTable para consultar Agente
    public static function Datatable()
    {
        $creador = $_SESSION['Admin'];
        $ReadAgente = crud::Read(query::ReadAgentes($creador));
        while ($Resultado = mysqli_fetch_assoc($ReadAgente)) {
            $rows["data"][] = $Resultado;
        }
        echo json_encode($rows);
    }

    //Insertando Datos por metodo post usando Ajax de jquery
    public static function AgregarAgente()
    {
        $creador = $_SESSION['Admin'];
        $user = $_POST['user'];
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $documento = $_POST['documento'];
        $telefono = $_POST['telefono'];
        $direccion = $_POST['direccion'];
        $correo = $_POST['correo'];
        $password = md5($_POST['password']);
        $ConfirmacionPassword = md5($_POST['ConfirmacionPassword']);

        //Validacion de pass identica
        if ($password === $ConfirmacionPassword) {
            //Validacion de usuario master o no
            crud::Create(query::CreateUsuario(
                $user,
                $password,
                FALSE,
                FALSE
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






    //TODO LO RELACIONADO CON EL ACCES WEB TOKEN
    //form para insertar accesweb token
    public static function formAccesWebToken()
    {
        higher();
        Nav();
        require_once 'app/admin/views/modules/config/config.html';

        lower();
    }

    //ingreso de AccesWebToken por ajax por metodo post
    public static function InsertAccesWebToken()
    {
        $user = $_SESSION['Admin'];
        $instance = trim($_POST['instancia']);
        $token = trim($_POST['token']);
        crud::Read(query::CreateAwebT($instance, $token, $user));
        echo 'Registro Exitoso';
    }

    //Mostrando AccesWebToken por ajax en la tabla
    public static function ReadAccesWebToken()
    {
        $user = $_SESSION['Admin'];
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
    /////////////////////////////////////





    //tODO LO RELACIONADO CON LOS CONTEOS DE CHAT
    //Cantidad Salas de Chat
    public static function CantidadSalasChat()
    {
        $user = $_SESSION['Admin'];
        $consulta = crud::Read(query::ReadDialogs($user));
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


    //Mostrar Tabla Dialogs totales
    public static function MostrarTablaChatAcumulado()
    {
        $user = $_SESSION['Admin'];
        $datos = '';
        if (isset($_POST['FiltroTablaTotal'])) {
            $datos = $_POST['FiltroTablaTotal'];
        }
        //echo $datos;
        if (!empty($datos)) {
            $consulta = crud::Read(query::ReadDialogsFiltrando($datos, $user));
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
            $consulta = crud::Read(query::ReadDialogs($user));
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
        $user = $_SESSION['Admin'];
        //echo $_POST['FiltroTablaAbiertos'];
        $datos = '';
        if (isset($_POST['FiltroTablaAbiertos'])) {
            $datos = $_POST['FiltroTablaAbiertos'];
        }
        $consulta = crud::Read(query::ReadDialogsFiltrandoAbiertos($datos, $user));
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
        $user = $_SESSION['Admin'];

        $datos = '';
        if (isset($_POST['FiltroTablaCerrados'])) {
            $datos = $_POST['FiltroTablaCerrados'];
        }

        $consulta = crud::Read(query::ReadDialogsFiltrandoCerrados($datos, $user));
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
        $user = $_SESSION['Admin'];
        $datos = '';
        if (isset($_POST['FiltroTablaAsignados'])) {
            $datos = $_POST['FiltroTablaAsignados'];
        }
        $consulta = crud::Read(query::ReadDialogsFiltrandoAsignados($datos, $user));
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


    public static function MostrandoChatAbiertos()
    {
        $user = $_SESSION['Admin'];
        $Resultados = crud::Read(query::ReadChatAbiertos($user));
        $Array = mysqli_fetch_assoc($Resultados);
        print $Array['v_conteo'];
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



            $user = $_SESSION['Admin'];
            $id = $indice;
            $SalaChat = str_replace('@c.us', '', $indice);

            //Imagen Guardada
            $resultado = crud::Read(query::ReadImageDialogs($id));
            $image = mysqli_fetch_assoc($resultado);

            //Mostrando mensaje de despedida en el modal de cerrar chat
            $consulta = mysqli_fetch_assoc(crud::Read(query::ReadMensajeDespedidaChat($user)));


            higher();
            Nav();
            require_once 'app/admin/views/modules/chat/chat.phtml';
            lower();
        } else {
            header('Location:Inicio');
        }
    }








    //Mostrar si el cliente esta conectado o no
    public static function MostrarEstadoConectado()
    {
        if (!empty($_POST['chatId'])) {
            $phone = $_POST['chatId'];
            $user = $_SESSION['Admin'];

            $AccesWebToken = mysqli_fetch_assoc(crud::Read(query::ReadAwebT($user)));
            $ChatApi = new ChatApi($AccesWebToken['Instance'], $AccesWebToken['Token']);

            print $ChatApi->userStatus($phone)['status'];
        }
    }

    //Mostrando Cantidad Chat Cerrados
    public static function MostrandoChatCerrados()
    {
        $user = $_SESSION['Admin'];
        $Consulta = crud::Read(query::ReadConteoChatCerrados($user));
        if ($Consulta == false) {
            print 0;
        } else {
            $Array = mysqli_fetch_assoc($Consulta);
            print $Array['v_conteo'];
        }
    }

    //Mostrando Cantidad Chat Asignado a Agentes
    public static function MostrandoChatAsignados()
    {
        $user = $_SESSION['Admin'];
        $consulta = crud::Read(query::ReadChatAsignados($user));
        $row = mysqli_fetch_assoc($consulta);
        print $row['count(idAgentes)'];
    }

    //Tabla para mostrar cantidad de chat asignados a cada agente
    public static function TablaChatAsignadoAgente()
    {
        $user = $_SESSION['Admin'];
        $consulta = crud::Read(query::ReadChatAsignadosAgentes($user));

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
    ///////////////////////////////////////






    //TODO LO RELACIONADO CON EL CHAT
    // add Sala de chat individual
    public static function AddSala()
    {
        higher();
        Nav();
        require_once 'app/admin/views/modules/addChat/addChat.phtml';
        lower();
    }

    //Sala de chat individual
    public static function AbrirSalaChat()
    {
        //Condicion para obligar a tener si o si una sala de chat
        if (!empty($_POST['btnAbrirChat'])) {
            $user = $_SESSION['Admin'];
            $id = $_POST['btnAbrirChat'];
            $SalaChat = str_replace('@c.us', '', $_POST['btnAbrirChat']);

            //Imagen Guardada
            $resultado = crud::Read(query::ReadImageDialogs($id));
            $image = mysqli_fetch_assoc($resultado);


            //ChatAbiertos
            crud::Update(query::UpdateDialogsAbrirChat($_POST['btnAbrirChat']));


            //Mostrando mensaje de despedida en el modal de cerrar chat
            $consulta = mysqli_fetch_assoc(crud::Read(query::ReadMensajeDespedidaChat($user)));

            higher();
            Nav();
            require_once 'app/admin/views/modules/chat/chat.phtml';
            lower();
        } else {
            header('Location:./');
        }
    }

    //Mostrar Mensajes de chat individual
    public static function MostrarMensajesChat()
    {
        //if (!empty($_POST['chatId'])) {
        $user = $_SESSION['Admin'];
        //$id =  $_POST['chatId'];
        $id =  '573166857000@c.us';
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
                $sender[$i] = $_SESSION['Admin'];
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
        //}
    }

    //Enviar Mensajes de chat individual
    public static function EnviarMensajesChat()
    {

        $user = $_SESSION['Admin'];
        $UrlToken = mysqli_fetch_assoc(crud::Read(query::ReadAwebT($user)));
        $Api = new ChatApi($UrlToken['Instance'], $UrlToken['Token']);
        $Phone = $_POST['chatId'];
        $message = $_POST['txtCuerpoMensage'];
        echo $Api->SendMenssage($Phone, $message);
        //echo $Phone.' '.$message;
    }
    ///////////////////////////////////


    //TODO LO RELACIONADO CON  MOSTRAR CONVERSACIONES EN TABLAS
    //Modulo mostrar Conversacion Agente Seleccionado desde Dashboard
    public static function MostrandoConversacionAgente()
    {
        if (!empty($_POST['idAgente'])) {
            higher();
            Nav();
            foreach ($_POST['idAgente'] as $Array) {
                $idAgente = $Array;
            }
            require_once 'app/admin/views/modules/conversacion/conversacion.phtml';
            lower();
        } else {
            header('Location:Inicio');
        }
    }


    //logica para mostrar la cantidad de salas de chat asignadas al Agente o al asistente
    public static function ReadDialogsAsignadosAgente()
    {
        $id = $_POST['id'];
        $consulta = crud::Read(query::ReadDialogsAgente($id));

        $i = 0;
        $array = array();
        while ($row = mysqli_fetch_assoc($consulta)) {
            $array[$i]['id']        =  str_replace('@c.us', '', $row['id']);
            $array[$i]['name']      =   $row['name'];
            $array[$i]['image']     =   $row['image'];
            $array[$i]['last_time'] =   $row['last_time'];
            $array[$i]['abierto']   =   $row['abierto'];
            $array[$i]['idAgentes'] =   $row['idAgentes'];
            $i++;
        }
        print json_encode($array, JSON_PRETTY_PRINT);
    }

    ////////////////////////////////////////




    //TODO LO RELACIONADO CON LA TRANSFERENCIA DE SALAS DE CHAT
    //Modulo Transferir chat
    public static function TransferirChat()
    {
        $id = str_replace('@c.us', '', $_GET['Id']);
        higher();
        Nav();


        require_once 'app/admin/views/modules/TransferenciaChat/TransferenciaChat.phtml';
        lower();
    }


    //Consultando Datos del Modulo Transferir
    public static function ConsultandoUsuarioATransferir()
    {
        $user = $_SESSION['Admin'];
        $Consulta = crud::Read(query::ReadAgentes($user));
        while ($Resultado = mysqli_fetch_assoc($Consulta)) {
            $rows["data"][] = $Resultado;
        }
        echo json_encode($rows);
    }

    //Transfiriendo Sala Chat a un Agente
    public static function UpdateDialogs()
    {
        if (isset($_POST['SeleccionSalaChat'])) {
            $user = $_SESSION['Admin'];
            $idAgente = $_POST['IdAgenteTransferir'];
            $id = $_POST['SeleccionSalaChat'] . '@c.us';
            crud::Update(query::UpdateDialogs($idAgente, $id, $user));
            echo 'Transferencia Exitosa';
        } else {
            echo 'No se pudo Transferir';
        }
    }
    /////////////////////////////////////////////




    //TODO LO RELACIONADO A LOS MENSAJES DE DESPEDIDA
    //Mostrar Mensaje de Despedida
    public static function MensajeDeDespedida()
    {
        higher();
        Nav();
        $usuario = $_SESSION['Admin'];
        require_once 'app/master/views/modules/mensajefinal/mensajedespedida.phtml';

        lower();
    }

    //Ingreso de Mensaje de Despedida
    public static function CreateMensajeDespedida()
    {
        $cuerpo =  $_POST['txtMensajeDespedida'];
        $usuario = $_SESSION['Admin'];
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
        $user = $_SESSION['Admin'];
        $consulta = crud::Read(query::ReadMensajeDespedida($user));

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
        echo 'Prueba';
    }
    ////////////////////////////////////////////







    //TODO LO RELACIONADO CON LOS CHATS ABIERTOS DESDE DASHBOARD
    //Modulo mostrar dialogs Asignados Agente Seleccionado desde Dashboard
    public static function MostrandoDialogAsignadoAgente()
    {
        if (!empty($_POST['idAgente'])) {
            higher();
            Nav();
            var_dump($_POST['idAgente']);
            foreach ($_POST['idAgente'] as $Array) {
                $idAgente = $Array;
            }
            require_once 'app/admin/views/modules/conversacion/conversacion.phtml';
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
