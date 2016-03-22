<?php
class PAGINA
{
    public function encabezado()
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
        . "         <title>".TITULO."</title>";
        // cargo las librerias
        echo "      <link rel=\"stylesheet\" type=\"text/css\" href=\"./css/estilos.css\"/>"
        . "         <script src=\"./lib/funciones.js\"></script>"
        . "         <!-- bootstrap -->"
        . "         <link rel=\"stylesheet\" type=\"text/css\" href=\"./lib/bootstrap/dist/css/bootstrap.css\"/>"
        . "         <script src=\"./lib/jquery/dist/jquery.js\"></script>"
        . "         <!-- fin bootstrap -->"
        . "         <!-- fontawesome -->"
        . "         <link rel=\"stylesheet\" href=\"./lib/font-awesome-4.5.0/css/font-awesome.min.css\">"
        . "         <!-- fin fontawesome -->"
        . "         <!-- toastr -->"
        . "         <link rel=\"stylesheet\" type=\"text/css\" href=\"./lib/toastr/build/toastr.min.css\" />"
        . "         <script type=\"text/javascript\" src=\"./lib/toastr/build/toastr.min.js\"></script>"
        . "         <!-- fin toastr -->"                
        //. "         <!-- bootstrap-select -->"
        //. "         <link rel=\"stylesheet\" href=\"./lib/bootstrap-select/dist/css/bootstrap-select.min.css\">"
        //. "         <script src=\"./lib/bootstrap-select/dist/js/bootstrap-select.js\">"
        //. "         <!-- fin bootstrap-select -->"
        . "     </head>"
        . "     <body>";
    }
    public function pie()
    {
        echo "      <div class=\"pie\">"
        . "             Seedmech SRL"
        . "         </div>"
        . "     </body>"
        . "   </html>";
    }
}