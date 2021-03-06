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
                    <div class="col-lg-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <div class="panel-title">
                                    <em class="fa fa-list">
                                    </em>
                                    <?php echo ($pageName); ?>&nbsp;&nbsp;
                                </div>
                            </div>

                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-4 col-xs-6">
                                                <div class="panel">
                                                    <div class="panel-body" style="background:#eee;">
                                                        <h4 class="pull-left">
                                                            ???????????? (???)
                                                        </h4>
                                                        <h4 class="pull-right text-danger">
                                                            ???<?php echo ($tj["today"]); ?> ???
                                                        </h4>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-xs-6">
                                                <div class="panel">
                                                    <div class="panel-body" style="background:#eee;">
                                                        <h4 class="pull-left">
                                                            ?????????????????????
                                                        </h4>
                                                        <h4 class="pull-right text-primary">
                                                            ???<?php echo ($tj["yes"]); ?> ???
                                                        </h4>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-xs-6">
                                                <div class="panel">
                                                    <div class="panel-body" style="background:#eee;">
                                                        <h4 class="pull-left">
                                                            ???????????????
                                                        </h4>
                                                        <h4 class="pull-right text-info">
                                                            ???<?php echo ($tj["all"]); ?> ???
                                                        </h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <form id="J_Date" method="get" class="form-inline m-b-xs" action="">
                                    <div class="form-group">
                                        <input class="form-control" name="ordernum" placeholder="?????????" value="<?php echo ($_REQUEST['ordernum']); ?>" size="15" type="text">
                                    </div>
                                    <div class="form-group">
                                        <select name="status" class="layui-btn-small ajax-action form-control"  >
                                            <option value="">????????????</option>
                                            <option value="0" <?php if($_REQUEST['status']== '0'): ?>selected="selected"<?php endif; ?>>?????????</option>
                                            <option value="1" <?php if($_REQUEST['status']== '1'): ?>selected="selected"<?php endif; ?>>?????????</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <select name="jkstyle" class="layui-btn-small ajax-action form-control"  >
                                            <option value="">????????????</option>
                                            <?php if(is_array($jiekou)): $i = 0; $__LIST__ = $jiekou;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$n): $mod = ($i % 2 );++$i;?><option value="<?php echo ($n["jkstyle"]); ?>" <?php if($_REQUEST['jkstyle']== $n.jkstyle): ?>selected="selected"<?php endif; ?>><?php echo ($n["jkname"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                        </select>
                                    </div>
                                    &nbsp;&nbsp;
                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                            <input size="16" class="form-control startTime" name="start" readonly="" placeholder="????????????" class="form_datetime form-control" value="<?php echo ($_REQUEST['start']); ?>" type="text">
                                        </div>
                                        -
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                            <input size="16" class="form-control endTime" name="end" readonly="" placeholder="????????????" class="form_datetime form-control" value="<?php echo ($_REQUEST['end']); ?>" type="text">
                                        </div>
                                    </div>
                                    &nbsp;&nbsp;
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-search"></i>
                                        &nbsp;????????????
                                    </button>
                                </form>

                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th align="center" >ID</th>
                                                <th align="center">??????id</th>
                                                <th align="center">?????????</th>
                                                <th align="center">???????????????</th>
                                                <th align="center">????????????</th>
                                                <th align="center">????????????</th>
                                                <th align="center">??????</th>
                                                <th align="center">????????????</th>
                                                <th align="center">??????</th>
                                                <th align="center">??????</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if($list): if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$n): $mod = ($i % 2 );++$i;?><tr>
                                                <td align="center"><?php echo ($n["ddid"]); ?></td>
                                                <td align="center"><a href="<?php echo U('Index/Home/dingdancf',array('ddid'=>$n['ddid'],'p'=>$_GET['p']));?>))#}"><?php echo ($n["ddid"]); ?></a></td>
                                                <td align="center"><?php echo ($n["ordernum"]); ?></td>
                                                <td align="center"><?php echo ($n["preordernum"]); ?></td>
                                                <td align="center"><?php echo ($n["totalmoney"]); ?></td>
                                                <td align="center"><?php echo ($n["havemoney"]); ?></td>
                                                <td align="center"><?php echo ($n["status"]); ?></td>
                                                <td align="center"><?php echo ($n["paytime"]); ?></td>
                                                <td align="center"><?php echo ($n["jkstyle"]); ?></td>
                                                <td align="center"><?php echo ($n["tzzt"]); ?>
                                                    <?php if($n["tz"] == '1'): ?><a href="<?php echo U('Index/Home/dingdancf',array('id'=>$n['ddid'],'p'=>$_GET['p']));?>" class="layui-btn layui-btn-normal layui-btn-mini">??????</a><?php endif; ?>
                                                </td>
                                            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                                            <?php else: ?>
                                            <tr>
                                                <td colspan="10" align="center">????????????</td>
                                            </tr><?php endif; ?>
                                        </tbody>
                                    </table>
                                    <div id="wypage"><?php echo ($page); ?></div>
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