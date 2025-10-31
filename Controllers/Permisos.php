<?php
class Permisos extends Controller{
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
        $perm = $this->model->verificarPermisos($id_user, "Permisos");
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
        $data = $this->model->getPermisos(); 
        for ($i=0; $i < count($data); $i++) {  
            if ($data[$i]['estado'] == 1) {
                if ($data[$i]['id'] != 1) {
                    $data[$i]['estado'] = '<span class="badge badge-success">Activo</span>';
                    $data[$i]['acciones'] = '<div>
                    <button class="btn btn-primary" type="button" onclick="btnEditarPermiso(' . $data[$i]['id'] . ');">E<i class="fa fa-pencil-square-o"></i></button>
                    <button class="btn btn-danger" type="button" onclick="btnEliminarPermiso(' . $data[$i]['id'] . ');">D<i class="fa fa-trash-o"></i></button>
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
                <button class="btn btn-success" type="button" onclick="btnReingresarPermiso(' . $data[$i]['id'] . ');"><i class="fa fa-reply-all">R</i></button>
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
        if (empty($usuario) || empty($clave)) {
            $msg = array('msg' => 'Todo los campos son requeridos', 'icono' => 'warning');
        }else{
            $hash = hash("SHA256", $clave);
            //$hash = $clave;
            $data = $this->model->getUsuario($usuario, $hash);
            if ($data) {
                $_SESSION['id_usuario'] = $data['id'];
                $_SESSION['usuario'] = $data['usuario'];
                $_SESSION['nombre'] = $data['nombre'];
                $_SESSION['activo'] = true;
                $msg = array('msg' => 'Bienvenido a ZGROUP!', 'icono' => 'success');
            }else{
                $msg = array('msg' => 'Usuario o contraseña incorrecta', 'icono' => 'warning');
            }
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function registrar()
    {
        $Permiso = strClean($_POST['Permiso']);
        $id = strClean($_POST['id']);

 
            if ($id == "") {
                $data = $this->model->registrarPermiso($Permiso);
                if ($data == "ok") {
                    $msg = array('msg' => 'Permiso registrado', 'icono' => 'success');
                } else if ($data == "existe") {
                    $msg = array('msg' => 'El Permiso ya existe', 'icono' => 'warning');
                } else {
                    $msg = array('msg' => 'Error al registrar', 'icono' => 'error');
                }
            }else{
                $data = $this->model->modificarPermiso($Permiso,$id);
                if ($data == "modificado") {
                    $msg = array('msg' => 'Permiso modificado', 'icono' => 'success');
                }else {
                    $msg = array('msg' => 'Error al modificar', 'icono' => 'error');
                }
            }
        
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);

        die();
    }
    public function editar( $id)
    {
        $data = $this->model->editarPermiso($id);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function eliminar( $id)
    {
        $data = $this->model->accionPermiso(0, $id);
        if ($data == 1) {
            $msg = array('msg' => 'Permiso dado de baja', 'icono' => 'success');
        }else{
            $msg = array('msg' => 'Error al eliminar', 'icono' => 'error');
        }
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function reingresar( $id)
    {
        $data = $this->model->accionPermiso(1, $id);
        if ($data == 1) {
            $msg = array('msg' => 'Permiso restaurado', 'icono' => 'success');
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