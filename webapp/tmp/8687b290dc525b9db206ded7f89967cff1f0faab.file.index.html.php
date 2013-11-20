<?php /* Smarty version Smarty-3.0.8, created on 2013-11-20 09:53:56
         compiled from "/Users/kelaocai/tongshibang/tsb/webapp/tpl/main/index.html" */ ?>
<?php /*%%SmartyHeaderCode:426624687528c1634c6ac36-81883030%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8687b290dc525b9db206ded7f89967cff1f0faab' => 
    array (
      0 => '/Users/kelaocai/tongshibang/tsb/webapp/tpl/main/index.html',
      1 => 1384875062,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '426624687528c1634c6ac36-81883030',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<!DOCTYPE html >
<html lang="zh-CN">
    <head>
        <title>webapp</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0 maximum-scale=1.0, user-scalable=no" >
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!-- Bootstrap -->
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link href="css/mycss.css" rel="stylesheet">

        <script src="http://cdn.bootcss.com/jquery/2.0.3/jquery.min.js"></script>
        <script src="js/flipsnap.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
    </head>
    <body style="background: url('img/bg.gif')" >

        <nav class="navbar navbar-default" role="navigation">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <span><a class="navbar-brand" href="#">读美文<small style="font-size: 9px;">&nbsp;去你的大鸭梨 每天5篇 爱藏不藏</small></a></span>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li class="active">
                        <a href="#">今日美文</a>
                    </li>
                    <li>
                        <a href="#" >我的收藏</a>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">名家名作 <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="#">席慕容</a>
                            </li>
                            <li>
                                <a href="#">舒婷</a>
                            </li>
                            <li>
                                <a href="#">毕淑敏</a>
                            </li>
                            <li>
                                <a href="#">林语堂</a>
                            </li>
                            <li class="divider"></li>

                        </ul>
                    </li>
                </ul>

                <ul class="nav navbar-nav navbar-right">

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">分类浏览 <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="#">抒情</a>
                            </li>
                            <li>
                                <a href="#">忧伤</a>
                            </li>
                            <li>
                                <a href="#">励志</a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="#">离别</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div><!-- /.navbar-collapse -->
        </nav>

        <div class="container">

            <div class="row " >
                <div class="col-xs-12 col-md-4">
                    <div class="thumbnail mybg"  >
                        <div class="viewport">
                            <div class="flipsnap">
                                <div class="picitem"><img src="img/mw.png" class="img-responsive img-rounded ">
                                </div>
                                <div class="picitem"><img src="img/mw.png" class="img-responsive img-rounded ">
                                </div>
                            </div>
                        </div>
                        <div class="caption">

                            <div class="attribution" style="padding: 5px 15px;margin:auto -13px;margin-top:-10px;">
                                <div class="row">
                                    <div class="col-xs-8">
                                        <h3>无怨的青春</h3>
                                        <p>
                                            作者：席慕容
                                        </p>
                                    </div>
                                    <div class="col-xs-4" >
                                        <h5 id="btn_tip">PLAY</h5>
                                        <p>
                                            <i class="glyphicon glyphicon-play" style="font-size: 28px;" id="btn_play" ></i>
                                        </p>
                                        <p>
                                            <i class="glyphicon glyphicon-pause" style="font-size: 28px; display: none" id="btn_pause" ></i>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <p style="margin-top: 5px;">
                                在年轻的时候，如果你爱上一个人，
                                <br>
                                请你，请你一定要温柔的对待他。
                                <br>
                                不管你们相爱的时间有多长或多短，
                                <br>
                                若你们能始终温柔的对待，那么，
                                <br>
                                所有的时刻都将是一种无瑕的美丽。
                                <br>
                                若不得不分离，也要好好的说声再见，
                                <br>
                                也要在心里存着感谢，
                                <br>
                                感谢他给了你一份记忆。
                                <br>
                                长大了以后，你才会知道，
                                <br>
                                在蓦然回首的刹那，
                                <br>
                                没有怨恨的青春才会无遗憾，
                                <br>
                                如山冈上那轮静静的满月。
                            </p>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-xs-12 "><img src="img/logo.png"  class="pull-left" style="width: 55%;height:55%"/><small class="pull-right">最闹腾的白骨精社区</small>
            </div>
        </div>

        <audio id="myaudio">
            <source src="audio/one.mp3" type="audio/mp3">
            Your browser does not support the audio tag.
        </audio>

        <br />
        <script type="text/javascript">
            $(document).ready(function() {
                var flipsnap = Flipsnap('.flipsnap');
                var myaudio = document.getElementById("myaudio");

                flipsnap.element.addEventListener('fspointmove', function() {
                    myaudio.pause();
                    myaudio.currentTime = 0.0;
                    $("#btn_play").show();
                    $("#btn_pause").hide();
                    $("#btn_tip").html('PLAY');
                }, false);

                $("#btn_play").click(function() {

                    myaudio.play();
                    $(this).hide();
                    $("#btn_pause").show();
                    $("#btn_tip").html('PAUSE');
                });

                $("#btn_pause").click(function() {
                    var myaudio = document.getElementById("myaudio");
                    myaudio.pause();
                    $(this).hide();
                    $("#btn_play").show();
                    $("#btn_tip").html('PLAY');
                });
            });
        </script>

    </body>
</html>

