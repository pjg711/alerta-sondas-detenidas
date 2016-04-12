<?php
if(Login::getLoginSession())
{
    echo "pase por stations<br>";
    if(!isset($_POST['action'])) exit;
    $userid=req('userid');
    switch($_POST['action'])
    {
        case 'export_data':
        
            break;
    }
}