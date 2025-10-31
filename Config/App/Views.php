<?php
class Views{

    public function getView($controlador, $vista, $data="")
    {
        $validacionHistorial = $controlador;
        $controlador = get_class($controlador);
        //$controlador = $controlador;
        if ($controlador == "Home") {
            $vista = "Views/".$vista.".php";
        }
        // validacion necesaria para acumular todo en historial
        else if($validacionHistorial=="Historial"){
            $vista = "Views/Historial/".$vista.".php";
        }
        
        else{
            $vista = "Views/".$controlador."/".$vista.".php";
        }
        require $vista;
    }
}


?>