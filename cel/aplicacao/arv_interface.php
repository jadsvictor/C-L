<?php
include("auxiliar_bd.php");
include_once("bd.inc");

session_start();

$connect_database = bd_connect();
?>    

<html> 
    <head> 

        <script language="javascript" src="mtmcode.js">
        </script> 

        <script language="javascript">

            MTMDefaultTarget = "text";
            MTMenuText = "Ontologia";

            function MTMenu() {
                this.items = new Array();
                this.MTMAddItem = MTMAddItem;
                this.addItem = MTMAddItem;
                this.makeLastSubmenu = MTMakeLastSubmenu;
            }

            var MTMIconList = null;
            MTMIconList = new IconList();
            MTMIconList.addIcon(new MTMIcon("menu_link_external.gif", "http://", "pre"));
            MTMIconList.addIcon(new MTMIcon("menu_link_pdf.gif", ".pdf", "post"));

            var menu = null;
            menu = new MTMenu();

<?php


if (isset($_SESSION['lista_de_conceitos']))
    $arv = $_SESSION['lista_de_conceitos'];
else
    $arv = array();

//conceitos
foreach ($arv as $conc) {
    echo "\nmenu.addItem(\"$conc->nome\");\n";
    echo " var mC = null;\n";
    echo " mC = new MTMenu();\n";
    echo "menu.makeLastSubmenu(mC);\n";
    
    //Verbos 
    foreach ($conc->relacoes as $relacao) {
        echo " mC.addItem(\"$relacao->verbo\",\"\");\n";
        echo " var mV = new MTMenu();\n";

        //Predicados 
        foreach ($relacao->predicados as $predicado) {
            echo " mV.addItem(\"$predicado\",\"blank.html\",\"enganaarvore\");\n";
        }

        echo " mC.makeLastSubmenu(mV);\n";
    }
}

mysql_close($connect_database);
?>

        </script>

    </head>
    <body onload="MTMStartMenu(true);" bgcolor="#FFFFFF" text="#ffffcc" link="yellow" vlink="lime" alink="red">

<?php
print "<font color=black>";
print_r($arv);
print "</font>";
?>
    </body>
</html>