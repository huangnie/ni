(function() {
    if (! /*@cc_on!@*/ 0) return;
    var e = "abbr, article, aside, audio, canvas, datalist, details, dialog, eventsource, figure, footer, header, hgroup, mark, menu, meter, nav, output, progress, section, time, video".split(', ');
    var i= e.length;
    while (i--){
        document.createElement(e[i])
    }
})()

$(document).ready(function () {

    $("ul.loginUserCenter").click(function() {
        $(this).find("ul.loginUserInfo").slideDown('fast').show();
        $(this).hover(function(){ $(this).find("ul.loginUserInfo").slideUp('slow'); });
    });

    $('.datePicker').datepicker({
        changeMonth: true,
        changeYear: true
    });

    $('.operate').click(function (e) {
        e.preventDefault();
        var url   = $(this).attr("href");
        $.ajax({
            type:'get',
            url:url,
            dataType:'html',
            success:function(data){
                data=data.match('{.*tip.*}');
                var json=eval('('+data.toString()+')');
                alert(json.tip+": "+json.content);
                window.location.replace(window.location.href);
            },
            error:function(){
                alert("操作失败！");
            }
        });
    });

    $('.searchBtn').click(function(e){
        e.preventDefault();
        if(document.getElementById('fieldIndex').value == -1){
            alert('请选择搜索对象');
            return false;
        }
        var hasStartTime = document.getElementById('timeStart').value.match('[0-9]{2}/[0-9]{2}/[0-9]{4}') ;
        var hasEndTime   = document.getElementById('timeEnd').value.match('[0-9]{2}/[0-9]{2}/[0-9]{4}') ;
        var noValue      = document.getElementById('fieldValue').value=='';
        if((hasEndTime && !hasStartTime) || (!hasEndTime && hasStartTime) ){
             alert('时间范围不明');
             return false;
        }else if(!hasStartTime && !hasEndTime && noValue) {
            alert('搜索内容不能为空');
            return false;
        }
        $('#searchForm').submit();
    });

    $('.gdlg').click(function (e) {
        e.preventDefault();
        var url   = $(this).attr("href");
        var title = $(this).attr("title");
        height='auto';
        width  = $(this).clientWidth;
        jdialog('GET',title,url,width , height);
    });

    $('.pdlg').click(function (e) {
        e.preventDefault();
        var url   = $(this).attr("href");
        var title = $(this).attr("title");
        height=$(this).offsetHeight;
        width  = 'auto';
        jdialog('POST',title,url,width,height);
    });

});

function jdialog(method,title,url,width,height){
    $.ajax({
        type:method,
        url:url,
        dataType:'html',
        success:function(data){
            var xPosition=document.body.clientWidth/2-width/2+200;
            var yPosition=150;
            var $dialog = $('<div></div>')
                .html(data)
                .dialog({
                    autoOpen: false,
                    modal: true,
                    height: height,
                    width: width,
                    title: title,
                    position:[xPosition,yPosition],
                    show: {
                        effect: 'fade',
                        duration: 200
                    },
                    close:function(){
                        $( this ).dialog( "close" );
                        window.location.replace(window.location.href);
                    }
                });
//          $(".ui-dialog-titlebar-close").hide();

            $('input').change(function (e) {
                $('.inform').html('');
                isSave=false;
            });
            $('textarea').change(function (e) {
                $('.inform').html('');
                isSave=false;
            });
            $('select').change(function (e) {
                $('.inform').html('');
            });

            $('input').mouseover(function (e) {
                $(this).focus();
            });
            $('textarea').mouseover(function (e) {
                $(this).focus();
            });
            $('select').mouseover(function (e) {
                $(this).focus();
            });

            $('input[type=reset]').click(function () {
                $('.inform').html('');
                isSave=false;
            });

            $('.inform').html('');

            $('.jsubmitForm').submit(function (e) {
                e.preventDefault();
                if($('.inform').html()!='') {
                    alert('无须重复同一操作');
                    return false;
                }
                var options={
                    cache:true,
                    url:$(this).attr('action'),
                    type:$(this).attr('method'),
                    data:$(this).serialize(),
                    success:function(data ,status){
                        data=data.match('{.*tip.*}');
                        if(data){
                            var json=eval("("+data+")");
//                            var state=json.state;       //state==1 成功，state==0 失败
                            if(json.tip=="url"){
                                window.location.href=json.content;
                                return false;
                            }else{
                                var inform=json.tip+": "+json.content;
                                $('.inform').html(inform);
                                return true;
                            }
                        }else{
                            alert('服务端响应出错');
                            return false;
                        }
                    },
                    error:function(){
                        $('.inform').html("提交失败！");
                        return false;
                    }
                };
                $.ajax(options);
                return false;
            });
            $dialog.dialog('open');
        },
        error:function(){
            $('.inform').html("操作失败，请检查网络状态！");
        }
    });
}
