<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Clientprint{
function clientprint(){
    require_once(str_replace("\\","/",APPPATH).'../../WebClientPrint'.EXT); //Por si estamos ejecutando este script en un servidor Windows
}
}