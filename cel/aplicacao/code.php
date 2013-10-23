<?php
session_start();

include ("functionsProject/check_proj_perm.php");

if (isset($_GET['id_projeto'])) {
    $id_projeto = $_GET['id_projeto'];
}
else
    include("funcoes_genericas.php");
include_once("bd.inc");

chkUser("index.php");
?>  

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"> 

<?php
// cocnect SGBD 
$r = bd_connect() or die("Erro ao conectar ao SGBD");

// The variable $ id_projeto, if set, corresponds to the id of the project
// Should be shown. If she is not setada then, by default,
// Do not show any project (wait the User choose a project).
// How to pass eh done using JavaScript (in heading.php), we must check
// If this id really corresponds to a project that has the User Access(Security).

if (isset($id_projeto)) {
    check_proj_perm($_SESSION['id_usuario_corrente'], $id_projeto) or
            die("Permissao negada");
    $q = "SELECT nome FROM projeto WHERE id_projeto =" . (int) $_GET[$id_projeto];
    $qrr = mysql_query($q) or die("Erro ao enviar a query");
    $result = mysql_fetch_array($qrr);
    $nome_projeto = $result['nome'];
}
?> 
else {


<script language="javascript1.3">

    top.frames['menu'].document.writeln('<font color="red">Nenhum projeto selecionado</font>');

</script> 

<?php
exit();
?>  

<html> 
    <head> 

        <script type="text/javascript">
            // Framebuster script to relocate browser when MSIE bookmarks this 
            // page instead of the parent frameset.  Set variable relocateURL to 
            // the index document of your website (relative URLs are ok): 
            /*var relocateURL = "/"; 
             
        </script> 

        <script type="text/javascript" src="mtmcode.js">
        </script> 

        <script type="text/javascript">
        // Morten's JavaScript Tree Menu 
        // version 2.3.2, dated 2002-02-24 
        // http://www.treemenu.com/ 
             
        // Copyright (c) 2001-2002, Morten Wang & contributors 
        // All rights reserved. 
             
        // This software is released under the BSD License which should accompany 
        // it in the file "COPYING".  If you do not have this file you can access 
        // the license through the WWW at http://www.treemenu.com/license.txt 
             
        // Nearly all user-configurable options are set to their default values. 
        // Have a look at the section "Setting options" in the installation guide 
        // for description of each option and their possible values. 
             
        MTMDefaultTarget = "text";
        MTMenuText = "<?= $project_name = 0 ?>";
             
        /****************************************************************************** 
        * User-configurable list of icons.                                            * 
        ******************************************************************************/

       var MTMIconList = null;
       MTMIconList = new IconList();
       MTMIconList.addIcon(new MTMIcon("menu_link_external.gif", "http://", "pre"));
       MTMIconList.addIcon(new MTMIcon("menu_link_pdf.gif", ".pdf", "post"));

       /****************************************************************************** 
        * User-configurable menu.                                                     * 
        ******************************************************************************/

       var menu = null;
       menu = new MTMenu();
       menu.addItem("Cen�rios");
       // + submenu 
       var mc = null;
       mc = new MTMenu();

<?php
$selection = "SELECT id_cenario, titulo  
FROM cenario  
WHERE id_projeto = $project_id  
ORDER BY titulo";

$qrr = mysql_query($selection) or die("Erro ao enviar a query de selecao");

//We must remove all HTML tags from the title of the scenario. possibly
//There will be links tags (<a> </ a>). If not put away, there will be error
//Show it in the menu. This search & replace anything that removes
//Is the way <qualquer_coisa_aqui>. It can even remove portions
//That are not HTML tags.

$search = "'<[\/\!]*?[^<>]*?>'si";
$replace = "";
while ($row = mysql_fetch_row($qrr)) {    // para cada cenario do projeto 
    $row[1] = preg_replace($search, $replace, $row[1]);
    ?>

           mc.addItem("<?= $row[1] ?>", "main.php?id=<?= $row[0] ?>&t=c");

           // + submenu 
           var mcs_<?= $row[0] ?> = null;
           mcs_<?= $row[0] ?> = new MTMenu();
           mcs_<?= $row[0] ?>.addItem("Sub-cen�rios", "", null, "Cen�rios que este cen�rio referencia");
           // + submenu 
           var mcsrc_<?= $row[0] ?> = null;
           mcsrc_<?= $row[0] ?> = new MTMenu();

    <?php
    $selection = "SELECT c.id_cenario_to, cen.titulo FROM centocen c, cenario cen WHERE c.id_cenario_from = " . $row[0];
    $selection = $selection . " AND c.id_cenario_to = cen.id_cenario";
    $qrr_2 = mysql_query($selection) or die("Erro ao enviar a query de selecao");
    while ($row_2 = mysql_fetch_row($qrr_2)) {
        $row_2[1] = preg_replace($search, $replace, $row_2[1]);
        ?>

               mcsrc_<?= $row[0] ?>.addItem("<?= $row_2[1] ?>", "main.php?id=<?= $row_2[0] ?>&t=c&cc=<?= $row[0] ?>");

        <?php
    }
    ?>

           // - submenu 
           mcs_<?= $row[0] ?>.makeLastSubmenu(mcsrc_<?= $row[0] ?>);

           // - submenu 
           mc.makeLastSubmenu(mcs_<?= $row[0] ?>);

    <?php
}
?>

       // - submenu 
       menu.makeLastSubmenu(mc);
       menu.addItem("L�xico");
       // + submenu 
       var ml = null;
       ml = new MTMenu();

<?php
$selection = "SELECT id_lexico, nome  
FROM lexico  
WHERE id_projeto = $project_id  
ORDER BY nome";

$qrr = mysql_query($selection) or die("Erro ao enviar a query de selecao");
while ($row = mysql_fetch_row($qrr)) {   // para cada lexico do projeto 
    ?>

           ml.addItem("<?= $row[1] ?>", "main.php?id=<?= $row[0] ?>&t=l");
           // + submenu 
           var mls_<?= $row[0] ?> = null;
           mls_<?= $row[0] ?> = new MTMenu();
           // mls_<?= $row[0] ?>.addItem("L�xico", "", null, "Termos do l�xico que este termo referencia"); 
           // + submenu 
           // var mlsrl_<?= $row[0] ?> = null; 
           // mlsrl_<?= $row[0] ?> = new MTMenu(); 

    <?php
    $selection = "SELECT l.id_lexico_to, lex.nome FROM lextolex l, lexico lex WHERE l.id_lexico_from = " . $row[0];
    $selection = $selection . " AND l.id_lexico_to = lex.id_lexico";
    $qrr_2 = mysql_query($selection) or die("Erro ao enviar a query de selecao");
    while ($row_2 = mysql_fetch_row($qrr_2)) {
        ?>

               // mlsrl_<?= $row[0] ?>.addItem("<?= $row_2[1] ?>", "main.php?id=<?= $row_2[0] ?>&t=l&ll=<?= $row[0] ?>"); 
               mls_<?= $row[0] ?>.addItem("<?= $row_2[1] ?>", "main.php?id=<?= $row_2[0] ?>&t=l&ll=<?= $row[0] ?>");

        <?php
    }
    ?>


           // mls_<?= $row[0] ?>.makeLastSubmenu(mlsrl_<?= $row[0] ?>); 
           ml.makeLastSubmenu(mls_<?= $row[0] ?>);

    <?php
}
?>



       menu.makeLastSubmenu(ml);



       menu.addItem("Ontologia");
       var mo = null;
       mo = new MTMenu();


       menu.makeLastSubmenu(mo);


       // CONCEPT 

       mo.addItem("Conceitos");
       var moc = null;
       moc = new MTMenu();

<?php
$selection = "SELECT id_conceito, nome  
FROM conceito 
WHERE id_projeto = $project_id  
ORDER BY nome";

$qrr = mysql_query($selection) or die("Erro ao enviar a query de selecao");
while ($row = mysql_fetch_row($qrr)) {  // para cada conceito do projeto 
    print "moc.addItem(\"$row[1]\", \"main.php?id=$row[0]&t=oc\");";
}
?>

       // --submenu 
       mo.makeLastSubmenu(moc);




       // RELA��ES 
       // ++ submenu 

       mo.addItem("Rela��es");
       var mor = null;
       mor = new MTMenu();

<?php
$selection = "SELECT   id_relacao, nome 
FROM     relacao r 
WHERE    id_projeto = $project_id  
ORDER BY nome";

$qrr = mysql_query($selection) or die("Erro ao enviar a query de selecao");
while ($row = mysql_fetch_row($qrr)) {   // para cada rela��o do projeto 
    print "mor.addItem(\"$row[1]\", \"main.php?id=$row[0]&t=or\");";
}
?>


       mo.makeLastSubmenu(mor);

       mo.addItem("Axiomas");
       var moa = null;
       moa = new MTMenu();

<?php
$selection = "SELECT   id_axioma, axioma 
FROM     axioma 
WHERE    id_projeto = $project_id  
ORDER BY axioma";

$qrr = mysql_query($selection) or die("Erro ao enviar a query de selecao");

while ($row = mysql_fetch_row($qrr)) {  // para cada axioma do projeto 
    $axi = explode(" disjoint ", $row[1]);
    print "moa.addItem(\"$axi[0]\", \"main.php?id=$row[0]&t=oa\");";
}
?>

       // --submenu    
       mo.makeLastSubmenu(moa);



        </script> 
    </head> 
    <body onload="MTMStartMenu(true)" bgcolor="#000033" text="#ffffcc" link="yellow" vlink="lime" alink="red"> 
    </body> 
</html> 
