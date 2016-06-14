<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class nusoap{
function nusoap(){
    require_once(str_replace("\\","/",APPPATH).'libraries/nusoap/nusoap'.EXT); //Por si estamos ejecutando este script en un servidor Windows
}
}
?>