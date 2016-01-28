<?php
error_reporting(E_ALL);
ini_set("display_errors",1);
//
$ftp_server = "190.228.29.71";
$ftp_user = "monsanto.seedmech.com.ar";
$ftp_password = "monsanto";
$directorio="/";
//
$conn_id = ftp_connect($ftp_server) or die("Couldn't connect to $ftp_server"); 
if (ftp_login($conn_id, $ftp_user, $ftp_password))
{
    //$buff = ftp_rawlist($conn_id, '/');
    if($listado=listDetailed($conn_id,$directorio))
    {
        analizo_sondas($listado);
        ftp_close($conn_id);
    }
    // close the connection
}

function listDetailed($resource, $directory = '.') 
{ 
    if (is_array($children = @ftp_rawlist($resource, $directory))) 
    { 
        $items = array(); 
        foreach ($children as $child) 
        { 
            $chunks = preg_split("/\s+/", $child); 
            list($item['rights'], $item['number'], $item['user'], $item['group'], $item['size'], $item['month'], $item['day'], $item['time']) = $chunks; 
            $item['type'] = $chunks[0]{0} === 'd' ? 'directory' : 'file'; 
            array_splice($chunks, 0, 8); 
            $items[implode(" ", $chunks)] = $item; 
        } 
        return $items; 
    } 
    // Throw exception or return false < up to you 
}
/*
 *     [TDF04-1A2F4D03-151122-081000.esp] => Array
        (
            [time] => 10:02
            [day] => 22
            [month] => Nov
            [size] => 732
            [group] => ftp
            [user] => ftp
            [number] => 1
            [rights] => -rw-rw-r--
            [type] => file
        )
 */
function analizo_sondas($sondas)
{
    //$q_sondas_cantidad=array();
    foreach($sondas as $key => $sonda)
    {
        if(substr($key,-4)==".esp")
        {
            $partes=explode("-",$key);
            if(count($partes)==4)
            {   
                // es sonda
                $agrego=array("archivo"=>$key,"sonda"=>$partes[0],"fecha"=>$partes[2]);
                $sonda=array_merge($sonda,$agrego);
                $q_sondas[$partes[0]][]=$sonda;
                if(!isset($q_sondas_cantidad[$partes[0]])) $q_sondas_cantidad[$partes[0]]=0;
                $q_sondas_cantidad[$partes[0]]++;
            }
        }
    }
    echo "<table>";
    echo "<tr>";
    echo "<td colspan=\"2\">";
    echo "Cantidad de sondas:".count($q_sondas_cantidad);
    echo "</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<th>Sonda</th>";
    echo "<th>Nro. archivos</th>";
    echo "<th>Ultima fecha</th>";
    echo "<th>Mas info</th>";
    echo "</tr>";
    foreach($q_sondas_cantidad as $key => $cantidad)
    {
        // fuera de fecha
        if(fecha_vencida($q_sondas[$key][count($q_sondas[$key])-1]))
        {
            echo "<tr bgcolor=\"#D49590\">";
        }else
        {
            echo "<tr bgcolor=\"#A6D490\">";
        }
        echo "<td align=\"center\">".$key."</td>";
        echo "<td align=\"center\">".$cantidad."</td>";
        echo "<td align=\"center\">".$q_sondas[$key][count($q_sondas[$key])-1]['fecha']."</td>";
        echo "<td>";
        echo "<pre>";
        print_r($q_sondas[$key][count($q_sondas[$key])-1]);
        echo "</pre>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
    return true;
}
function fecha_vencida($dato)
{
    $diferencia_dias=2;    
    $ahora=mktime(0,0,0,date("n"),date("j"),date("Y"));
    if(strlen($dato['fecha'])==6)
    {
        $anio=intval(substr($dato['fecha'],0,2))+2000;
        $mes=substr($dato['fecha'],2,2);
        $dia=substr($dato['fecha'],4,2);
        $fdato=mktime(0,0,0,$mes,$dia,$anio);
        
        $fecha_ahora=new DateTime(date("Y")."-".date("m")."-".date("d"));
        $fecha_dato=new DateTime($anio."-".$mes."-".$dia);
        $dife=$fecha_ahora->diff($fecha_dato);
        if($dife->days>$diferencia_dias)
        {
            return true;
        }
        return false;
        /*
        echo "<br>-----------------------------------------<br>";
        echo $dife->format('%R')."<br>";
        echo $dife->days."<br>";
        //$dife=$ahora-$fecha_dato
        echo "ahora->".$ahora."<br>";
        //echo "dato-->".$fecha_dato."<br>";
        echo "anio-->".$anio."<br>";
        echo "mes--->".$mes."<br>";
        echo "dia--->".$dia."<br>";
        echo "dato-->";
        echo "<pre>";
        print_r($dato);
        echo "</pre>";
         * 
         */
    }
}