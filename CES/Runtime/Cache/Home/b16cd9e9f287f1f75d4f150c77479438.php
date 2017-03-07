<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">

<head>
    <title>发送测试界面</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
    <meta name="viewport" content="width=device-width,inital-scale=1.0,maximum-scale=1.0,user-scalable=no;">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="description" content="">
    <script src="/CES/Public/js/jquery-3.1.1.min.js" type="text/javascript"></script>
    <!--<script src="../../../../Public/js/jquery-3.1.1.min.js" type="text/javascript"></script>-->
</head>
<script type="text/javascript">
    function sendMessage() {

        var openid = $('#openid').text();
        var initial = $('#initial').val();

        if(initial==''){
            alert('initial不能为空！');
            exit();
        }

//        alert("----" + openid + "----" + initial);

        $.ajax({
            type: "POST", //用POST方式传输
            url: "<?php echo U('Home/GroupSend/sendTextArray');?>", //目标地址.
            dataType: "JSON", //数据格式:JSON
            data: {content:openid,initial:initial},
            success: function (result) {
                if (result.status == 'success') {
                    alert(result.hint);
                } else {
                    alert(result.hint);
                }
            }
        });
    }

    function sendMessageAll() {

        var arrInitial = new Array();

        var openid = $('#openid').text();
        var initial1 = $('#initial1').val();
        var initial2 = $('#initial2').val();
//        var initial3 = $('#initial3').val();

        arrInitial.push(initial1);
        arrInitial.push(initial2);
//        arrInitial.push(initial3);

        if(initial1==''||initial2==''){
            alert('initial不能为空！');
            exit();
        }

//        alert("----" + openid + "----" + initial);

        $.ajax({
            type: "POST", //用POST方式传输
            url: "<?php echo U('Home/GroupSend/sendTextArray');?>", //目标地址.
            dataType: "JSON", //数据格式:JSON
            data: {content:openid,initial:arrInitial},
            success: function (result) {
                if (result.status == 'success') {
                    alert(result.hint);
                } else {
                    alert(result.hint);
                }
            }
        });
    }
</script>
<body>

<span id="openid" style="display:none"><?php echo ($openid); ?></span>
<input id="initial" type="text" name="initial" placeholder="输入initial" ／>
<input id="initial1" type="text" name="initial1" placeholder="输入多initial" ／>
<input id="initial2" type="text" name="initial2" placeholder="输入多initial" ／>
<!--<input id="initial3" type="text" name="initial3" placeholder="输入多initial" ／>-->
<input type="button" value="单个发送" name="submit" onclick="sendMessage();"/>
<input type="button" value="多个发送" name="submit" onclick="sendMessageAll();"/>

</body>
</html>