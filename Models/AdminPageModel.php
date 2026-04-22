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
        if (!defined('url_nueva') || url_nueva === null || !is_string(url_nueva) || trim(url_nueva) === '') {
            return json_encode(array('data' => (object) array()));
        }
        if ($imei === null || (is_string($imei) && trim($imei) === '')) {
            return json_encode(array('data' => (object) array()));
        }
        $imei = is_string($imei) ? trim($imei) : (string) $imei;
        if ($imei === '') {
            return json_encode(array('data' => (object) array()));
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, rtrim(url_nueva, '/') . "/TermoKing/ConsultarUltimaTrama/" . rawurlencode($imei));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
        curl_setopt($ch, CURLOPT_TIMEOUT, 25);
        $res = curl_exec($ch);
        curl_close($ch);
        if ($res === false || $res === null || $res === '') {
            return json_encode(array('data' => (object) array()));
        }
        $test = json_decode($res);
        if (!is_object($test) || !property_exists($test, 'data')) {
            return json_encode(array('data' => (object) array()));
        }
        return $res;
    }

    public function ListaDispositivoEmpresa($id)
    {
        if (!defined('urlapiMysql') || urlapiMysql === null || !is_string(urlapiMysql) || trim(urlapiMysql) === '') {
            return json_encode(array('data' => array()));
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, rtrim(urlapiMysql, '/') . "/contenedores/ListaDispositivoEmpresa/" . (int) $id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $res = curl_exec($ch);
        curl_close($ch);
        if ($res === false || $res === null || $res === '') {
            return json_encode(array('data' => array()));
        }
        $test = json_decode($res);
        if (!is_object($test) || !property_exists($test, 'data')) {
            return json_encode(array('data' => array()));
        }
        return $res;
    }
    public function VerificarLive($data)
    {
        if (!defined('urlapiMysql') || urlapiMysql === null || !is_string(urlapiMysql) || trim(urlapiMysql) === '') {
            return json_encode(array('data' => array()));
        }
        $ch = curl_init();
        $payload = json_encode($data);
        curl_setopt($ch, CURLOPT_URL, rtrim(urlapiMysql, '/') . "/contenedores/VerificarLive/");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $res = curl_exec($ch);
        curl_close($ch);
        if ($res === false || $res === null || $res === '') {
            return json_encode(array('data' => array()));
        }
        $test = json_decode($res);
        if (!is_object($test) || !property_exists($test, 'data')) {
            return json_encode(array('data' => array()));
        }
        return $res;
    }
    /**
     * Lista de comandos vía API Mongo2. Si la URL no está definida, curl falla o la respuesta
     * no es un JSON con data.lista (p. ej. "Endpoint no encontrado"), se devuelve estructura vacía.
     */
    public function ListaComandos()
    {
        $vacio = array(
            'data' => array(
                'lista' => array(),
                'contador' => 0,
            ),
        );
        $jsonVacio = json_encode($vacio, JSON_UNESCAPED_UNICODE);

        if (!defined('urlapiMongo2') || urlapiMongo2 === null || !is_string(urlapiMongo2) || trim(urlapiMongo2) === '') {
            return $jsonVacio;
        }

        $url = rtrim(urlapiMongo2, '/') . '/Comandos/JhonVena/866782048942516';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        $res = curl_exec($ch);
        $err = curl_errno($ch);
        curl_close($ch);

        if ($err !== 0 || $res === false || $res === null || $res === '') {
            return $jsonVacio;
        }

        $dec = json_decode($res, true);
        if (!is_array($dec)) {
            return $jsonVacio;
        }

        $msg = null;
        if (isset($dec['message']) && is_string($dec['message'])) {
            $msg = $dec['message'];
        } elseif (isset($dec['msg']) && is_string($dec['msg'])) {
            $msg = $dec['msg'];
        }
        if ($msg !== null && (stripos($msg, 'no encontrad') !== false || stripos($msg, 'endpoint') !== false)) {
            return $jsonVacio;
        }

        if (!isset($dec['data']) || !is_array($dec['data'])) {
            return $jsonVacio;
        }
        if (!isset($dec['data']['lista']) || !is_array($dec['data']['lista'])) {
            $dec['data']['lista'] = array();
        }
        if (!isset($dec['data']['contador'])) {
            $dec['data']['contador'] = count($dec['data']['lista']);
        }

        return json_encode($dec, JSON_UNESCAPED_UNICODE);
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
