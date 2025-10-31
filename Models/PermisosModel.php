<?php
class PermisosModel extends Query{
    private $Permiso, $nombre, $clave, $id, $estado;
    public function __construct()
    {
        parent::__construct();
    }
    public function getPermisos()
    {
        $sql = "SELECT * FROM permisos";
        $data = $this->selectAll($sql);
        return $data;
    }



    public function getUsuario($usuario, $clave)
    {
        $sql = "SELECT * FROM usuarios WHERE usuario = '$usuario' AND clave = '$clave' AND estado = 1"; 
        $data = $this->select($sql);
        return $data;
    }
    public function getUsuarios()
    {
        $sql = "SELECT * FROM usuarios";
        $data = $this->selectAll($sql);
        return $data;
    }
    public function registrarPermiso($Permiso)
    {
        $this->Permiso = $Permiso;
        $vericar = "SELECT * FROM Permisos WHERE nombre = '$this->Permiso'";
        $existe = $this->select($vericar);
        if (empty($existe)) {
            # code...
            $vericar = "SELECT id FROM Permisos ORDER BY id DESC LIMIT 1";
            $existe = $this->select($vericar);
            $comodin =$existe['id']+1;
            $sql = "INSERT INTO Permisos(nombre, id, tipo) VALUES (?,?,?)";
            $datos = array($this->Permiso, $comodin, $comodin);
            $data = $this->save($sql, $datos);
            if ($data == 1) {
                $res = "ok";
            }else{
                $res = "error";
            }
        }else{
            $res = "existe";
        }
        return $res;
    }
    public function modificarPermiso($Permiso,$id)
    {
        $this->Permiso = $Permiso;
        $this->id = $id;
        $sql = "UPDATE permisos SET  nombre = ? WHERE id = ?";
        $datos = array($this->Permiso, $this->id);
        $data = $this->save($sql, $datos);
        if ($data == 1) {
            $res = "modificado";
        } else {
            $res = "error";
        }
        return $res;
    }
    public function editarPermiso($id)
    {
        $sql = "SELECT * FROM Permisos WHERE id = $id";
        $data = $this->select($sql);
        return $data;
    }
    public function accionPermiso($estado, $id)
    {
        $this->id = $id;
        $this->estado = $estado;
        $sql = "UPDATE permisos SET estado = ? WHERE id = ?";
        $datos = array($this->estado, $this->id);
        $data = $this->save($sql, $datos);
        return $data;
    }

    public function getDetallePermisos($id)
    {
        $sql = "SELECT * FROM detalle_permisos WHERE id_usuario = $id";
        $data = $this->selectAll($sql);
        return $data;
    }
    public function deletePermisos($id)
    {
        $sql = "DELETE FROM detalle_permisos WHERE id_usuario = ?";
        $datos = array($id);
        $data = $this->save($sql, $datos);
        return $data;
    }
    public function actualizarPermisos($usuario, $permiso)
    {
        $sql = "INSERT INTO detalle_permisos(id_usuario, id_permiso) VALUES (?,?)";
            $datos = array($usuario, $permiso);
            $data = $this->save($sql, $datos);
            if ($data == 1) {
                $res = "ok";
            } else {
                $res = "error";
            }
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
    }
    public function actualizarPass($clave, $id)
    {
        $sql = "UPDATE usuarios SET clave = ? WHERE id = ?";
        $datos = array($clave, $id);
        $data = $this->save($sql, $datos);
        if ($data == 1) {
            $res = "modificado";
        } else {
            $res = "error";
        }
        return $res;
    }
}
?>