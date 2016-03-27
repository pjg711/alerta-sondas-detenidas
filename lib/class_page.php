<?php
class PAGE
{
    public function header()
    {
        echo "<html>"
        . "     <head>"
        . "         <meta name=\"google-site-verification\" content=\"4ICx_x8s8IurLmbe5UCa5hd97QlC3F2zfv8AyZvbLts\" />"
        . "         <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">"
        . "         <meta http-equiv=\"cache-control\" content=\"max-age=0\" />"
        . "         <meta http-equiv=\"cache-control\" content=\"no-cache\" />"
        . "         <meta http-equiv=\"expires\" content=\"0\" />"
        . "         <meta http-equiv=\"expires\" content=\"Tue, 01 Jan 1980 1:00:00 GMT\" />"
        . "         <meta http-equiv=\"pragma\" content=\"no-cache\" />"
        . "         <meta name=\"author\" content=\"Pablo Julián García\">"
        . "         <meta name=\"keywords\" content=\"Seedmech alerta sondas\">"
        . "         <meta name=\"DESCRIPTION\" content=\"Sistema para sondas de humedad de suelo\">\n"
        . "         <title>".TITULO."</title>"
        . "         <!-- estilos propios -->\n"
        . "         <link rel=\"stylesheet\" type=\"text/css\" href=\"./css/estilos.css\"/>\n"
        . "         <script src=\"./lib/funciones.js\"></script>\n"
        . "         <!-- fontawesome -->"
        . "         <link rel=\"stylesheet\" href=\"./lib/font-awesome-4.5.0/css/font-awesome.min.css\">\n"
        . "         <!-- fin fontawesome -->"
        . "         <!-- bootstrap -->"
        . "         <link rel=\"stylesheet\" type=\"text/css\" href=\"./lib/bootstrap/dist/css/bootstrap.css\"/>\n"
        . "         <script src=\"./lib/jquery/dist/jquery.js\"></script>\n"
        . "         <script src=\"./lib/bootstrap/dist/js/bootstrap.js\"></script>\n"
        . "         <!-- fin bootstrap -->"
        . "         <!-- toastr -->"                
        . "         <link rel=\"stylesheet\" type=\"text/css\" href=\"./lib/toastr/build/toastr.min.css\"/>\n"
        . "         <script type=\"text/javascript\" src=\"./lib/toastr/build/toastr.min.js\"></script>\n"
        . "         <!-- fin toastr -->"
        . "     </head>"
        . "     <body>";
    }
    public function footer()
    {
        echo "
        <div class=\"navbar navbar-fixed-bottom\"
            <div class=\"row\" style=\"width: 100%; height: 50px; border:1px solid gray; background-color: #dadada; color: #0086b3; vertical-align: middle;\">
                <div class=\"col-md-4 text-right\"><a href=\"http://www.seedmech.com\"><img src=\"./img/seedmech.png\"></a></div>
                <div class=\"col-md-8 text-left\">".PIE."</div>
            </div>
        </div>";
    }
}