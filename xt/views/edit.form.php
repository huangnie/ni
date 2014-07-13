<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="<?php echo BASE_URI;?>/public/lib/css/data_info.css">
<article class="box">
    <section class="data_info">

        <?php if(!isset($editForm)) echo '操作有误';
        else{  ?>
            <form class="jsubmitForm" target="edit_container"  action="<?php echo $uri;?>" method="post">

                <?php echo $editForm;  ?>

                <ul>
                    <li class="label">&nbsp;<!-- 提示 --></li>
                    <li class="edit">
                        <input type="submit"  class="btn_confirm" value="提 交"/>
                        <input type="reset" class="btn_cancel" value="清 空"/>
                    </li>
                </ul>
                <ul>
                    <li class="inform">&nbsp;</li>
                </ul>
            </form>
            <iframe name="edit_container" class="hide"></iframe>
        <?php } ?>

    </section>
</article>