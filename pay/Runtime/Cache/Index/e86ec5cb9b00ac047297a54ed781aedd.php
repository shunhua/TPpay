<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=gb2312">
        <meta charset="gb2312">
        <title><?php echo ($pageName); ?> - <?php echo ($sitename); ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,Chrome=1">
        <meta name="renderer" content="webkit">
        <link href="/Public/plugin/bootstrap/bootstrap.css" type="text/css" rel="stylesheet">
        <link href="/Public/home/font-awesome.css" type="text/css" rel="stylesheet">
        <link href="/Public/home/style.css" type="text/css" rel="stylesheet">
        <script type="text/javascript" src="/Public/plugin/jquery-1.8.0.min.js"></script>
        <script type="text/javascript" src="/Public/plugin/layer/layer.js"></script>
        <script type="text/javascript"src="/Public/plugin/laydate/laydate.js"></script>
        <script type="text/javascript" src="/Public/home/app.js"></script>
    </head>

<body class="">
    <div style="position:absolute;left:40%">
        <div class="woody-prompt">
            <div class="prompt-error alert alert-danger">
            </div>
        </div>
    </div>

    <div class="pace pace-inactive">
        <div class="pace-progress" data-progress-text="100%" data-progress="99" style="transform: translate3d(100%, 0px, 0px);">
            <div class="pace-progress-inner">
            </div>
        </div>
        <div class="pace-activity">
        </div>
    </div>
    <div id="wrapper">
        <nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu" style="display: block;">
            <li class="nav-header">
                <div class="dropdown profile-element text-center">
                    <span style="font-size:26px;color:#fff;font-weight:bold;">
                        <?php echo ($sitename); ?>
                    </span>
                </div>
            </li>
            <?php if($user["ifagent"] == '1' && $config["ifagent"] == '0'): ?><li class="active">
                <a href="#">
                    <span class="nav-label">
                        <i class="fa fa-home">
                        </i>
                        ????????????
                    </span></a>
            </li>
            <li class="">
                <a href="<?php echo U('Index/Home/dl');?>">
                    <span class="nav-label">
                        <i class="fa fa-newspaper-o">
                        </i>
                        ????????????
                    </span></a>
            </li>
            <li class="">
                <a href="<?php echo U('Index/Home/dluser');?>">
                    <span class="nav-label">
                        <i class="glyphicon glyphicon-user">
                        </i>
                        ????????????
                    </span></a>
            </li>
            <li class="">
                <a href="<?php echo U('Index/Home/dldingdan');?>">
                    <span class="nav-label">
                        <i class="fa fa-line-chart">
                        </i>
                        ????????????
                    </span></a>
            </li>
            <li class="">
                <a href="<?php echo U('Index/Home/dlfandian');?>">
                    <span class="nav-label">
                        <i class="fa fa-map-signs">
                        </i>
                        ????????????
                    </span></a>
            </li><?php endif; ?>

            <li class="active">
                <a href="#">
                    <span class="nav-label">
                        <i class="fa fa-home">
                        </i>
                        ????????????
                    </span></a>
            </li>
            <li class="">
                <a href="<?php echo U('Index/Home/index');?>">
                    <span class="nav-label">
                        <i class="fa fa-home">
                        </i>
                        ????????????
                    </span></a>
            </li>
            <li>
                <a href="<?php echo U('Index/Home/pass');?>">
                    <span class="nav-label">
                        <i class="fa fa-newspaper-o">
                        </i>
                        ????????????
                    </span></a>
            </li>
            <li>
                <a href="<?php echo U('Index/Home/dingdan');?>">
                    <span class="nav-label">
                        <i class="fa fa-line-chart">
                        </i>
                        ????????????
                    </span>
                </a>
            </li><li>
                <a href="<?php echo U('Index/Home/yhk');?>">
                    <span class="nav-label">
                        <i class="fa fa-server"></i>
                        ????????????
                    </span>
                </a>
            </li>
            <li>
                <a href="<?php echo U('Index/Home/tx');?>">
                    <span class="nav-label">
                        <i class="fa fa-calendar-check-o">
                        </i>
                        ????????????
                    </span>
                </a>
            </li>
            <li>
                <a href="<?php echo U('Index/Home/txjl');?>">
                    <span class="nav-label">
                        <i class="fa fa-calculator">
                        </i>
                        ????????????
                    </span></a>
            </li>
            <li>
                <a href="<?php echo U('Index/Home/fl');?>">
                    <span class="nav-label">
                        <i class="fa fa-map-signs">
                        </i>
                        ????????????
                    </span>
                </a>
            </li>
            <li>
                <a href="<?php echo U('Index/Home/api');?>">
                    <span class="nav-label">
                        <i class="fa fa-wrench">
                        </i>
                        ????????????API
                    </span>
                </a>
            </li>
            <li>
                <a href="<?php echo U('Index/Home/loginout');?>">
                    <span class="nav-label">
                        <i class="fa fa-building-o">
                        </i>
                        ????????????
                    </span>
                </a>
            </li>
        </ul>
    </div>
</nav>
        <div id="page-wrapper" class="gray-bg">
            <div class="row border-bottom">
    <nav class="navbar navbar-static-top white-bg" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
        </div>
        <ul class="nav navbar-top-links navbar-right">
            <li class="dropdown">
                <a href="<?php echo U('Index/Home/pass');?>" class="dropdown-toggle" data-toggle="dropdown">
                    <span class="glyphicon glyphicon-user">
                    </span>
                    &nbsp;<?php echo ($user["username"]); ?>&nbsp;
                </a>
            </li>
            <li>
                <a href="<?php echo U('Index/Home/loginout');?>">
                    <i class="fa fa-sign-out">
                    </i>
                    ????????????
                </a>
            </li>
        </ul>
    </nav>
</div>

            <div class="row wrapper wrapper-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-3 col-xs-6">
                                <div class="panel">
                                    <div class="panel-body" style="background:#fff;">
                                        <h4 class="pull-left">
                                            ????????????
                                        </h4>
                                        <h4 class="pull-right text-danger">
                                            ???<?php echo ($user["money"]); ?> ???
                                        </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-xs-6">
                                <div class="panel">
                                    <div class="panel-body" style="background:#fff;">
                                        <h4 class="pull-left">
                                            ????????????
                                        </h4>
                                        <h4 class="pull-right text-primary">
                                            ???<?php echo ($tj["payhave"]); ?> ???
                                        </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-xs-6">
                                <div class="panel">
                                    <div class="panel-body" style="background:#fff;">
                                        <h4 class="pull-left">
                                            ????????????
                                        </h4>
                                        <h4 class="pull-right text-info">
                                            ???<?php echo ($user["tx"]); ?> ???
                                        </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-xs-6">
                                <div class="panel">
                                    <div class="panel-body" style="background:#fff;">
                                        <h4 class="pull-left">
                                            ???????????????
                                        </h4>
                                        <h4 class="pull-right text-info">
                                            ???<?php echo ($tj["dingdanall"]); ?> ???
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 col-xs-6">
                                <div class="panel">
                                    <div class="panel-body" style="background:#fff;">
                                        <h4 class="pull-left">
                                            ???????????? (???)
                                        </h4>
                                        <h4 class="pull-right text-danger">
                                            ???<?php echo ($tj["dingdantoday"]); ?> ???
                                        </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-xs-6">
                                <div class="panel">
                                    <div class="panel-body" style="background:#fff;">
                                        <h4 class="pull-left">
                                            ?????????????????????
                                        </h4>
                                        <h4 class="pull-right text-primary">
                                            ???<?php echo ($tj["dingdanyes"]); ?> ???
                                        </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-xs-6">
                                <div class="panel">
                                    <div class="panel-body" style="background:#fff;">
                                        <h4 class="pull-left">
                                            ?????????????????????
                                        </h4>
                                        <h4 class="pull-right text-info">
                                            <?php echo ($tj["dingdantodaycount"]); ?> ???
                                        </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-xs-6">
                                <div class="panel">
                                    <div class="panel-body" style="background:#fff;">
                                        <h4 class="pull-left">
                                            ?????????????????????
                                        </h4>
                                        <h4 class="pull-right text-info">
                                            <?php echo ($tj["dingdanyescount"]); ?> ???
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <em class="fa fa-bell-o fa-fw">
                                            </em>
                                            ????????????
                                        </h4>
                                    </div>
                                    <div class="panel-body">
                                        <div class="list-group">
                                            <p>??????ID???<?php echo ($user["userid"]); ?></p>
                                            <p class="margin-small-top clearfix">
                                                <span class="fl">???????????????<em class="text-blue-deep"><?php echo ($user["miyao"]); ?></em></span>
                                            </p><p>???????????????http://<?php echo ($_SERVER['HTTP_HOST']); ?>/Pay/</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <em class="fa fa-bell-o fa-fw">
                                            </em>
                                            ????????????
                                        </h4>
                                    </div>
                                    <div class="panel-body">
                                        <div class="list-group">
                                            <?php echo ($config["notice"]); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <em class="fa fa-bell-o fa-fw">
                                            </em>
                                            ????????????
                                        </h4>
                                    </div>
                                    <div class="panel-body">
                                        <div class="list-group">
                                            <p>?????????<?php echo ($config["phone"]); ?></p>
                                            <p>QQ???<a href="http://wpa.qq.com/msgrd?v=3&amp;uin=<?php echo ($config["qq"]); ?>&amp;Menu=yes" target='_blank'><i class="iconfont icon-qq"><?php echo ($config["qq"]); ?></i></a></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="footer">
    &copy;&nbsp;2017&nbsp;
    <?php echo ($config["sitename"]); echo (C("FX_VERSION")); ?>&nbsp;Powered by ??????
</div>

<!-- END modal-->
</body>
</html>