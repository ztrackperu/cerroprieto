<?php
class RecetaModel extends Query{
    protected $id, $nombre, $telefono, $direccion, $correo, $img;
    public function __construct()
    {
        parent::__construct();
    }
    public function selectConfiguracion()
    {
        $sql = "SELECT * FROM configuracion";
        $res = $this->select($sql);
        return $res;
    }

    public function verificarPermisos($id_user, $permiso)
    {

        $tiene = false;
        $sql = "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'";
        $existe = $this->select($sql);
        if ($existe != null || $existe != "") {
            $tiene = true;
        }
        return $tiene;
        //alterar estructura para trabajar con apis       
    }
    
}