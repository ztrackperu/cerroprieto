<?php
class Configuracion extends Controller
{
    public function __construct()
    {
        session_start();
        if (empty($_SESSION['activo'])) {
            header("location: " . base_url);
        }
        parent::__construct();
    }
    public function index()
    {
		$id_user = $_SESSION['id_usuario'];
        $perm = $this->model->verificarPermisos($id_user, "Configuracion");
        if (!$perm && $id_user != 1) {
            $this->views->getView($this, "permisos");
            exit;
        }
        $data = $this->model->selectConfiguracion();
        $this->views->getView($this, "index", $data);
    }
    public function actualizar()
    {
		$id_user = $_SESSION['id_usuario'];
        $perm = $this->model->verificarPermisos($id_user, "Configuracion");
        if (!$perm && $id_user != 1) {
            $this->views->getView($this, "permisos");
            exit;
        }
        $id = strClean($_POST['id']);
        $nombre = strClean($_POST['nombre']);
        $telefono = strClean($_POST['telefono']);
        $direccion = strClean($_POST['direccion']);
        $correo = strClean($_POST['correo']);
        $img = $_FILES['imagen'];
        $tmpName = $img['tmp_name'];
        if (empty($id) || empty($nombre) || empty($telefono) || empty($direccion) || empty($correo)) {
            $msg = array('msg' => 'Todo los campos son requeridos', 'icono' => 'warning');
        } else {
            $name = "logo.png";
            $destino = 'Assets/img/logo.png';
            $data = $this->model->actualizarConfig($nombre, $telefono, $direccion, $correo, $name, $id);
            if ($data == "modificado") {
                $msg = array('msg' => 'Datos de la empresa modificado', 'icono' => 'success');
                if (!empty($img['name'])) {
                    $extension = pathinfo($img['name'], PATHINFO_EXTENSION);
                    $formatos_permitidos =  array('png', 'jpeg', 'jpg');
                    $extension = pathinfo($img['name'], PATHINFO_EXTENSION);
                    if (!in_array($extension, $formatos_permitidos)) {
                        $msg = array('msg' => 'Archivo no permitido', 'icono' => 'warning');
                    }else{
                        move_uploaded_file($tmpName, $destino);
                    }
                }
            }
        }
        
        echo json_encode($msg, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function admin()
    {
        $data['libros'] = $this->model->selectDatos('libro');
        $data['materias'] = $this->model->selectDatos('materia');
        $data['estudiantes'] = $this->model->selectDatos('estudiante');
        $data['autor'] = $this->model->selectDatos('autor');
        $data['editorial'] = $this->model->selectDatos('editorial');
        $data['prestamos'] = $this->model->selectDatos('prestamo');
        $data['usuarios'] = $this->model->selectDatos('usuarios');
        $this->views->getView($this, "home", $data);
    }
    public function error()
    {
        $this->views->getView($this, "error");
    }
    public function vacio()
    {
        $this->views->getView($this, "vacio");
    }


}
