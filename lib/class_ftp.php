<?php
class FTP
{
    private $conn_id;
    private $usuario_activo;
    private $ftp_server;
    private $ftp_user;
    private $ftp_password;
    private $directorio_remoto;
    private $tipo_usuario;
    private $mails;
    
    private $listado=array();
    
    public function get_listado()
    {
        return $this->listado;
    }
    function __construct($ftp_server="",$ftp_user="",$ftp_password="",$directorio_remoto="")
    {
        if($ftp_server=="")
        {
            return false;
        }
        $this->ftp_server=$ftp_server;
        $this->ftp_user=$ftp_user;
        $this->ftp_password=$ftp_password;
        $this->directorio_remoto=$directorio_remoto;
        //
        //echo "\n";
        //echo "servidor-->".$this->ftp_server."\n";
        //echo "usuario--->".$this->ftp_user."\n";
        //echo "password-->".$this->ftp_password."\n";
        $this->conn_id = ftp_connect($this->ftp_server,21) or die("No es posible la conexión con el servidor $this->ftp_server");
        //echo "conn result: ".$this->conn_id."\n";
        if(ftp_login($this->conn_id, $this->ftp_user, $this->ftp_password))
        {
            if($this->listado=$this->lista_detallada($this->conn_id,$this->directorio_remoto))
            {
                ftp_close($this->conn_id);
                return true;
            }
        }
        return false;
    }
    private function lista_detallada($resource, $directorio = '.')
    {
        if(is_array($children = @ftp_rawlist($resource, $directorio))) 
        { 
            $items = array(); 
            foreach($children as $child) 
            { 
                $chunks = preg_split("/\s+/", $child); 
                list($item['rights'], $item['number'], $item['user'], $item['group'], $item['size'], $item['month'], $item['day'], $item['time']) = $chunks; 
                $item['type'] = $chunks[0]{0} === 'd' ? 'directory' : 'file'; 
                array_splice($chunks, 0, 8); 
                $items[implode(" ", $chunks)] = $item; 
            } 
            return $items; 
        } 
        return false;
    }
    public function conexion_exitosa()
    {
        $this->conn_id = ftp_connect($this->ftp_server,21) or die("No es posible la conexión con el servidor $this->ftp_server");
        if(ftp_login($this->conn_id, $this->ftp_user, $this->ftp_password))
        {
            return true;
        }
        return false;
    }
}