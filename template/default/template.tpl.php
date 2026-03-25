<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo $title; ?> - <?php echo $_CONFIG['brand']; ?></title>
        <meta name="description" content="<?php echo trim(strip_tags($description)); ?>">
        <meta name="Generator" content="Gitbook2Site">
        <meta name="Copyright" content="PHPShop">
        <meta name="Author" content="PHPShop">

        <meta property="og:title" content="<?php echo $title; ?> | <?php echo $_CONFIG['brand']; ?>">
        <meta property="og:url" content="https://<?php echo $_SERVER['SERVER_NAME'] . parse_url($_SERVER['REQUEST_URI'])['path']; ?>">
        <meta property="og:image" content="https://<?php echo $_SERVER['SERVER_NAME'] ?>/help-main/.gitbook/assets/main_flow.png">
        <meta property="og:type" content="website">
        <meta property="og:description" content="<?php echo trim(strip_tags($description)); ?>">

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
        <link rel="stylesheet" href="/template/<?php echo $_CONFIG['template']; ?>/style.css">
        <link rel="icon" href="/logo.png">
        <link rel="canonical" href="https://<?php echo $_SERVER['SERVER_NAME'] . parse_url($_SERVER['REQUEST_URI'])['path']; ?>">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/mono-blue.min.css">
        
    </head>
    <body role="document" data-spy="scroll" data-target="#content-menu">
        <script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
        
        <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/php.min.js"></script>
        
        <script src="/template/<?php echo $_CONFIG['template']; ?>/js.js"></script> 

        <nav class="navbar navbar-default visible-xs">
            <div class="container-fluid">

                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="/"> <?php echo $_CONFIG['brand']; ?></a>

                    <button type="button" class="btn btn-default navbar-btn pull-right search-mobil-activ" style="margin-right: 10px"><span class="glyphicon glyphicon-search"></span></button>
                </div>

                <form class="navbar-form navbar-right hide search-mobil-display">
                    <div class="form-group">
                        <input class="form-control search-mobil" placeholder="Я ищу..." type="search" data-trigger="manual" data-container="body" data-toggle="popover" data-placement="bottom" data-html="true"  data-content="">
                    </div>
                </form>


                <div class="collapse navbar-collapse" id="navbar">
                    <ul class="nav navbar-nav" id="mobmenu">
                        <?php echo $menu_mobile; ?>
                        </li>
                    </ul>

                </div>
            </div>
        </nav>
        <header class="container hidden-xs"> 

            <nav class="navbar navbar-default navbar-fixed-top">
                <div class="container-fluid">
                    <div class="row hidden-xs">
                        <div class="col-md-3" style="margin:15px">
                            <a href="/" class="logo" ><img src="/logo.png" style="margin-right:10px"><?php echo $_CONFIG['brand']; ?></a>
                        </div>

                        <div class="col-md-4">
                            <div class="" style="margin:15px;">
                                <input class="form-control search" placeholder="Я ищу..." type="search" data-trigger="manual" data-container="body" data-toggle="popover" data-placement="bottom" data-html="true"  data-content="">
                            </div>
                        </div>
                        <div class="col-md-3">

                        </div>

                    </div>

                </div>
            </nav>
        </header>

        <div class="container-fluid" id="main">

            <div class="row">
                <div class="col-md-3 hidden-xs hide-scrollbar" id="menu">
                    <div>

                        <?php echo $menu; ?>
                        <a id="test"></a>

                    </div>

                </div>
                <div class="col-md-7 col-xs-12" id="content-main">


                    <?php echo $breadcrumb . $html; ?>
                </div>
                <div class="col-md-2 hidden-xs" id="content-menu"><div>
                        <ul data-spy="affix" data-offset-top="10" data-offset-bottom="100" class="nav nav-pills nav-stacked">
                            <?php echo $navigation; ?>
                        </ul>

                    </div>
                </div>

            </div>

        </div>

    </body>
</html>