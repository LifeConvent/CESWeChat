<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <!-- BEGIN META -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=0">
    <meta name="description" content="">
    <meta name="author" content="Custom Theme">
    <!-- END META -->

    <title>微信绑定提示</title>

    <!-- BEGIN STYLESHEET -->
    <link href="/CES/Public/css/bootstrap.min.css" rel="stylesheet"><!-- BOOTSTRAP CSS -->
    <link href="/CES/Public/css/bootstrap-reset.css" rel="stylesheet"><!-- BOOTSTRAP CSS -->
    <link href="/CES/Public/assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <!-- FONT AWESOME ICON STYLESHEET -->
    <link href="/CES/Public/css/style.css" rel="stylesheet"><!-- THEME BASIC CSS -->
    <link href="/CES/Public/css/style-responsive.css" rel="stylesheet"><!-- THEME BASIC RESPONSIVE  CSS -->
    <!-- END STYLESHEET -->
</head>
<body class="body-404">
<div class="container">
    <!-- BEGIN MAIN CONTENT -->
    <section class="error-wrapper">
        <h2 style="margin-top: 50%;">绑定已完成</h2>

        <h3 style="margin-top: 2%;"><?php echo ($stu_name); ?>您好,您已成功绑定本系统</h3>

        <p class="page-404" style="margin-top: 10%;">绑定信息已成功提交，感谢您绑定本系统.</p>
    </section>
    <!-- END MAIN CONTENT -->
</div>
</body>
</html>