<?php
class Usuarios extends Controller{
    public function __construct() {
        session_start();
        parent::__construct();
    }
    public function test(){
        echo "oli";
    }
    public function index()
    {
        if (empty($_SESSION['activo'])) {
            header("location: " . base_url);
        }
        $id_user = $_SESSION['id_usuario'];
        $perm = $this->model->verificarPermisos($id_user, "Usuarios");
        if (!$perm && $id_user != 1) {
            $this->views->getView($this, "permisos");
            exit;
        }
        $this->views->getView($this, "index");
    }
    public function listar()
    {
        if (empty($_SESSION['activo'])) {
            header("location: " . base_url);
        }
        $data = $this->model->getUsuarios();
        for ($i=0; $i < count($data); $i++) {  
            if ($data[$i]['estado'] == 1) {
                if ($data[$i]['id'] != 1) {
                    $data[$i]['estado'] = '<span class="badge badge-success">Activo</span>';
                    $data[$i]['acciones'] = '<div>
                    <button class="btn btn-dark" onclick="btnRolesUser(' . $data[$i]['id'] . ')"><i class="fa fa-key">A</i></button>
                    <button class="btn btn-primary" type="button" onclick="btnEditarUser(' . $data[$i]['id'] . ');">E<i class="fa fa-pencil-square-o"></i></button>
                    <button class="btn btn-danger" type="button" onclick="btnEliminarUser(' . $data[$i]['id'] . ');">D<i class="fa fa-trash-o"></i></button>
                    <div/>';
                }else{
                    $data[$i]['estado'] = '<span class="badge badge-success">Activo</span>';
                    $data[$i]['acciones'] = '<div class"text-center">
                    <span class="badge-primary p-1 rounded">Super Administrador</span>
                    </div>'; 
                }
            }else {
                $data[$i]['estado'] = '<span class="badge badge-danger">Inactivo</span>';
                $data[$i]['acciones'] = '<div>
                <button class="btn btn-success" type="button" onclick="btnReingresarUser(' . $data[$i]['id'] . ');"><i class="fa fa-reply-all">R</i></button>
                <div/>';
            }
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function validar_ex()
    {
        if (empty($_SESSION['activo'])) {
            header("location: " . base_url);
        }
        $data = $this->model->getUsuarios();
        for ($i=0; $i < count($data); $i++) {  
            if ($data[$i]['estado'] == 1) {
                if ($data[$i]['id'] != 1) {
                    $data[$i]['estado'] = '<span class="badge badge-success">Activo</span>';
                    $data[$i]['acciones'] = '<div>
                    <button class="btn btn-dark" onclick="btnRolesUser(' . $data[$i]['id'] . ')"><i class="fa fa-key">A</i></button>
                    <button class="btn btn-primary" type="button" onclick="btnEditarUser(' . $data[$i]['id'] . ');">E<i class="fa fa-pencil-square-o"></i></button>
                    <button class="btn btn-danger" type="button" onclick="btnEliminarUser(' . $data[$i]['id'] . ');">D<i class="fa fa-trash-o"></i></button>
                    <div/>';
                }else{
                    $data[$i]['estado'] = '<span class="badge badge-success">Activo</span>';
                    $data[$i]['acciones'] = '<div class"text-center">
                    <span class="badge-primary p-1 rounded">Super Administrador</span>
                    </div>'; 
                }
            }else {
                $data[$i]['estado'] = '<span class="badge badge-danger">Inactivo</span>';
                $data[$i]['acciones'] = '<div>
                <button class="btn btn-success" type="button" onclick="btnReingresarUser(' . $data[$i]['id'] . ');"><i class="fa fa-reply-all">R</i></button>
                <div/>';
            }
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function validar()
    {
        $usuario = strClean($_POST['usuario']);
        $clave = strClean($_POST['clave']);
        $utc = strClean($_POST['utc']);
        if (empty($usuario) || empty($clave)) {
            $msg = array('msg' => 'Todo los campos son requeridos', 'icono' => 'warning');
        }else{
            #echo "ten en cuenta que el usuario es ";
            #echo $usuario ;
            //aqui solicitamos el hash del user para compararlo
            $data = $this->model->getHash_api($usuario); 
            #print_r( $data);
            #print("aqui estamos en el controlador");

            $resultadoPass = json_decode($data);
            $resultadoPass = $resultadoPass->data->password;
            #comparar data obtenida con conversion de datos ingreaado por el usuario
            if (password_verify($clave,$resultadoPass)) {
                //consultar datos del usuario de forma total para obtener la data 
                $data = $this->model->getUser_api($usuario);
                $resultadoUser = json_decode($data);
                $resultadoUser = $resultadoUser->data;
                // especificamos la session del usuario 
                $_SESSION['nombres_ztrack'] = $resultadoUser->nombres;
                $_SESSION['apellidos_ztrack'] = $resultadoUser->apellidos;
                $_SESSION['usuario_ztrack'] = $resultadoUser->usuario;
                $_SESSION['correo_ztrack'] = $resultadoUser->correo;
                $_SESSION['permiso_ztrack'] = $resultadoUser->permiso;
                $_SESSION['id_ztrack'] = $resultadoUser->id;
                $_SESSION['activo_ztrack'] = true;
                //aqui debemos especificar  la empresa_id referencial 
                $_SESSION['empresa_id'] = isset($resultadoUser->empresa_id) ? $resultadoUser->empresa_id : 0 ;
                // establecer parametros de los equipos asignados a telemetria para actualizacion automatica
                $ExtraerDatos = $this->model->ExtraerDatos_api($_SESSION['empresa_id']);
                $resultadoData = json_decode($ExtraerDatos);
                $_SESSION['data'] = $resultadoData->data;
                $_SESSION['utc'] = $utc;
                //$_SESSION['data'] = $this->model->ExtraerDatos($_SESSION['empresa_id']);

                $msg = array('msg' => 'Welcome  '.$_SESSION['nombres_ztrack'].' to ZTRACK !', 'icono' => 'success');

                
                #$msg = array('msg' => 'Welcome  test to ZTRACK !', 'icono' => 'success');
            
                
            }else{
                $msg = array('msg' => 'User or password wrong', 'icono' => 'warning');
            }
                
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function registrar()
    {
        $usuario = strClean($_POST['usuario']);
        $nombre = strClean($_POST['nombre']);
        $clave = strClean($_POST['clave']);
        $confirmar = strClean($_POST['confirmar']);
        $id = strClean($_POST['id']);
        $hash = hash("SHA256", $clave);
        if (empty($usuario) || empty($nombre)) {
            $msg = array('msg' => 'All fields are required', 'icono' => 'warning');
        }else{
            if ($id == "") {
                if (!empty($clave) && !empty($confirmar)) {
                    if ($clave != $confirmar) {
                        $msg = array('msg' => 'Your password is needed!!', 'icono' => 'warning');
                    } else {
                        $data = $this->model->registrarUsuario($usuario, $nombre, $hash);
                        if ($data == "ok") {
                            $msg = array('msg' => 'User has been registered', 'icono' => 'success');
                        } else if ($data == "existe") {
                            $msg = array('msg' => 'User already exists', 'icono' => 'warning');
                        } else {
                            $msg = array('msg' => 'Failed to register', 'icono' => 'error');
                        }
                    }
                }
            }else{
                $data = $this->model->modificarUsuario($usuario, $nombre, $id);
                if ($data == "modificado") {
                    $msg = array('msg' => 'User has been modified', 'icono' => 'success');
                }else {
                    $msg = array('msg' => 'Error when trying to modify', 'icono' => 'error');
                }
            }
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function editar( $id)
    {
        $data = $this->model->editarUser($id);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function eliminar( $id)
    {
        $data = $this->model->accionUser(0, $id);
        if ($data == 1) {
            $msg = array('msg' => 'Usuario dado de baja', 'icono' => 'success');
        }else{
            $msg = array('msg' => 'Error al eliminar', 'icono' => 'error');
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function reingresar( $id)
    {
        $data = $this->model->accionUser(1, $id);
        if ($data == 1) {
            $msg = array('msg' => 'Usuario restaurado', 'icono' => 'success');
        } else {
            $msg = array('msg' => 'Error al restaurar', 'icono' => 'error');
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function permisos($id)
    {
        // toma el id del ususuario de la session iniciada
        $id_user = $_SESSION['id_usuario'];
        // pedimos los permisos del usuario capturado
        $perm = $this->model->verificarPermisos($id_user, "roles");
        // si no exitste resultados e la consulta y no es usuario tipo 1 , entonces se envia directamente qe no puede asignar permisos 
        if (!$perm && $id_user != 1) {
            echo '<div class="card">
                    <div class="card-body text-center">
                        <span class="badge badge-danger">No tienes permisos </span>
                    </div>
                </div>';
            exit;
        }
        // trae las lista de todos los permisos disponibles 
        $data = $this->model->getPermisos();
        // trae el detalle de los permisos asosiados con el usuario
        $asignados = $this->model->getDetallePermisos($id);
        $datos = array();
        // aqui agregamos los permisos que tiene registrado el usuario
        foreach ($asignados as $asignado) {
            $datos[$asignado['id_permiso']] = true;
        }
        echo '<div class="row">
        <input type="hidden" name="id_usuario" value="' . $id . '">';
        // aqui creamos la interfaz necesaria para los checbox segun la cantidad de permisos 
        foreach ($data as $row) {
            echo '<div class="d-inline mx-3 text-center">
                    <hr>
                    <label for="" class="font-weight-bold text-capitalize">' . $row['nombre'] . '</label>
                        <div class="center">
                            <input type="checkbox" name="permisos[]" value="' . $row['id'] . '" ';
            // qui validamos si existe e permio y le hacemos checked de existir el permiso 
            if (isset($datos[$row['id']])) {
                echo "checked";
            }
            echo '>
                            <span class="span">On</span>
                            <span class="span">Off</span>
                        </div>
                </div>';
        }
        // terminamos por agregar el boton actualizar , que enviara todas las opcionesque hayamos seleccionado al 
        // evento registrar permiso
        echo '</div>
        <button class="btn btn-primary mt-3 btn-block" type="button" onclick="registrarPermisos(event);">Actualizar</button>';
        die();
    }
    public function registrarPermisos()
    {
        $id_user = strClean($_POST['id_usuario']);
        $permisos = $_POST['permisos'];
        $this->model->deletePermisos($id_user);
        if ($permisos != "") {
            foreach ($permisos as $permiso) {
                $this->model->actualizarPermisos($id_user, $permiso);
            }
        }
        echo json_encode("ok");
        die();
    }
    public function cambiarPas()
    {
        if ($_POST) {
            $id = $_SESSION['id_usuario'];
            $clave = strClean($_POST['clave_actual']);
            $user = $this->model->editarUser($id);
            if (hash("SHA256", $clave) == $user['clave']) {
                $hash = hash("SHA256", strClean($_POST['clave_nueva']));
                $data = $this->model->actualizarPass($hash, $id);
                if ($data == "modificado") {
                    $msg = array('msg' => 'Contraseña modificado', 'icono' => 'success');
                } else {
                    $msg = array('msg' => 'Error al modificar', 'icono' => 'warning');
                }
            } else {
                $msg = array('msg' => 'Contraseña actual incorrecta', 'icono' => 'warning');
            }
            echo json_encode($msg, JSON_UNESCAPED_UNICODE);
            die();
        }
    }
    public function salir()
    {
        session_destroy();
        header("location: ".base_url."/LoginPage");
    }
}