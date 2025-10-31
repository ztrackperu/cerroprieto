<?php
class AdminPageModel extends Query{
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
    }

    public function validarCamposCorreoYClave($id_user) {
        $sql = "SELECT * FROM usuarios WHERE id = $id_user AND (email = '' OR pass_email = '')";
        $res = $this->select($sql);
        if ($res != null) {
            return true; //  // El usuario tiene los campos correo_usuario y clave_correo vacios
        }else {
        return false; // El usuario tiene los campos correo_usuario o clave_correo llenos
        }
    }

    public function insertarRespuesta($id, $correo_usuario, $clave_correo, $usuario_activo)
    {
        $query = "UPDATE INTO usuarios(email, pass_email) VALUES (?,?)";
        $datos = array($id, $correo_usuario, $clave_correo, $usuario_activo);
        $data = $this->save($query, $datos);
        if ($data == 1) {
            $res = "ok";
            $nuevo_estado = 0;
            $estado_anterior = 1; // Reemplaza 'nuevo_estado' con el valor deseado del nuevo estado
            $query_actualizar_estado = "UPDATE formulario SET estado = ? WHERE id = ?";
            $datos_actualizar_estado = array($nuevo_estado, $id); // Reemplaza 'alguna_condicion' con la condición adecuada para actualizar el estado en la otra tabla
            $data_actualizar_estado = $this->save($query_actualizar_estado, $datos_actualizar_estado);

            if ($data_actualizar_estado != 1) {
                // Hubo un error al actualizar el estado en la otra tabla
                $res = "error al actualizar estado en otra_tabla";
            }
        } else {
            $res = "error";
        }

        return $res;
    }
    #ConsultarUltimaTrama

    public function ConsultarUltimaTrama($imei)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, url_nueva."/TermoKing/ConsultarUltimaTrama/".$imei);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);   
        return $res;
    }

    public function ListaDispositivoEmpresa($id)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, urlapiMysql."/contenedores/ListaDispositivoEmpresa/".$id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);   
        return $res;
    }
    public function VerificarLive($data)
    {
        $ch = curl_init();
        $data =json_encode($data);
        curl_setopt($ch, CURLOPT_URL, urlapiMysql."/contenedores/VerificarLive/");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);   
        return $res;
    }
    public function ListaComandos(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, urlapiMongo2."/Comandos/JhonVena/866782048942516");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);   
        return $res;
    }
    public function generarComandos($cantidad){
        $cards = array();
        /*
        for($i = 1; $i <= $cantidad; $i++){
            $cards[] = array(
                "id" => $i,
                "comando" => 'Humedad',
                "estatus" => 'Solicitado',
                "hora_solicitud" => '2022-01-01 12:00:00',
                "hora_ejecucion" => '2022-01-01 12:03:00',
                "hora_validacion" => '2022-01-01 12:05:00',
            );
        }*/
        
        for($i = 1; $i <= $cantidad; $i++){
            if($i % 2 == 0){
                $hsoli = '2022-01-01 12:00:00';
                $hej = '2022-01-01 12:03:00';
                $hval = '2022-01-01 12:05:00';
                $com = 'Humedad';
                $validar = 'ok';
            }
            else{
                $hsoli = null;
                $hej = null;
                $hval = null;
                $com = 'Ethyleno';
                $validar = 'pendiente';

            }
            $cards[] = array(
                "id" => $i,
                "comando" => $com,
                "estatus" => 'Solicitado',
                "validacion" => $validar,
                "hora_solicitud" => $hsoli,
                "hora_ejecucion" => $hej,
                "hora_validacion" => $hval,
                
            );
        }
        return json_encode($cards);
    }

}
