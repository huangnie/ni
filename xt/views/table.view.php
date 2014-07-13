<?php require_once('header.php');?>
<link rel="stylesheet" type="text/css" href="<?php echo BASE_URI;?>/public/lib/css/nav_and_menu.css">
<link rel="stylesheet" type="text/css" href="<?php echo BASE_URI;?>/public/lib/css/data_table.css">
<header class="header">
    <section class="banner">
        <section class="logo"><img src="<?php echo BASE_URI;?>/public/lib/img/ico/cxcy.ico" width="50px" height="50px"></section>
        <article class="Web_title"> 创新创业中心 </article>
    </section>
</header>
<nav class="nav"> <?php echo $navHtml;?> </nav>
<article class="user"><?php echo $userHtml;?></article>
<article class="container">
    <section class="content r_4c_3">
        <menu class="content_left">
            <section class="menu r_4c_5"> <?php echo $menuHtml;?> </section>
        </menu>
        <article class="content_right">
            <section class="addition"> <?php if(isset($table) && isset($table['addition'])) echo $table['addition']; ?> </section>
            <section class="data_table">
                <table algin="center"  cellspacing=1 cellpadding=1 class="r_4c_3">
                    <?php
                        //标题行
                        if(isset($table) && isset($table['theader'])) echo $table['theader'];
                        //内容行
                        if(isset($table) && isset($table['tbody'])) echo $table['tbody'];
                    ?>
                </table>
            </section>
            <aside class="pageBar"> <?php  if(isset($table) && isset($table['pageBar'])) echo $table['pageBar'];?> </aside>
        </article>
    </section>
</article>
<?php require_once('footer.php');?>

