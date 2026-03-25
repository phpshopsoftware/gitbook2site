<?php

/**
 * GitBook2Site
 * @author PHPShop Software
 * @version 1.2
 * @copyright ИП Туренко Д.Л. 
 */
include "./config.php";
include "./lib/parsedown/Parsedown.php";

$sourse = $_CONFIG['sourse'];
$path = parse_url($_SERVER['REQUEST_URI'])['path'];


$Parsedown = new Parsedown();

if ($path == '/') {

    $filename = $sourse . '/README.md';

    if (!file_exists($sourse . '/README.md'))
        $filename = './README.md';
}
elseif ($path == '/about') {
    $sourse = '';
    $filename = 'README.md';
} else {

    if (file_exists($sourse . $path . '/README.md'))
        $filename = $sourse . $path . '/README.md';
    else
        $filename = $sourse . $path . '.md';
}
if (file_exists($filename)) {
    $content = file_get_contents($filename);
}

$parent_path = $sourse . pathinfo($_SERVER['REQUEST_URI'], PATHINFO_DIRNAME) . '/README.md';
$parent_path2 = $sourse . pathinfo($_SERVER['REQUEST_URI'], PATHINFO_DIRNAME) . pathinfo($_SERVER['REQUEST_URI'], PATHINFO_DIRNAME) . '.md';

if ((file_exists($parent_path) or file_exists($parent_path2))and pathinfo($_SERVER['REQUEST_URI'], PATHINFO_DIRNAME) != '/') {

    if (file_exists($parent_path)) {
        $parent = file_get_contents($parent_path);
        $breadcrumb_url = $path . '/../';
    } elseif (file_exists($parent_path2)) {
        $parent = file_get_contents($parent_path2);
        $breadcrumb_url = pathinfo($_SERVER['REQUEST_URI'], PATHINFO_DIRNAME) . '/';
    }

    // title
    if (preg_match_all('/\# (.+)/', $parent, $matchs)) {
        $breadcrumb_name = $matchs[1][0];
    }

    if ($path != '/') {
        $breadcrumb_parent = '<li itemprop="itemListElement" itemscope  itemtype="https://schema.org/ListItem">'
                . '<a itemprop="item" href="https://' . $_SERVER['SERVER_NAME'] . $breadcrumb_url . '"><span itemprop="name">' . str_replace(['# '], [''], $breadcrumb_name) . '</span></a><meta itemprop="position" content="2"/></li>';
    }
}

// title
if (preg_match_all('/\# (.+)/', $content, $matchs)) {

    $description = $title = $name = $matchs[1][0];

    if (!empty($breadcrumb_name)) {
        $title .= ' - ' . str_replace(['# '], [''], $breadcrumb_name);
        $description = $title;
    }
}

// navigation
if (preg_match_all('/\##(.+)/', $content, $matchs)) {

    if (is_array($matchs[1]) and count($matchs[1]) > 1)
        foreach ($matchs[1] as $nav) {

            if (!stristr($nav, '&#x20;')) {

                if (strstr($nav, '#'))
                    $navigation .= '<li role="presentation"><a href="#' . toLatin(str_replace(['#','*'], '', $nav)) . '" class="menu-2">' . str_replace(['#','*'], '', $nav) . '</a></li>';
                else
                    $navigation .= '<li role="presentation"><a href="#' . toLatin(str_replace(['#','*'], '', $nav)) . '" class="menu-1">' . str_replace(['#','*'], '', $nav) . '</a></li>';
            }
        }
}

if (getenv("COMSPEC"))
    $content = str_replace(['.gitbook/', $_CONFIG['site'], '.md'], [$sourse . '_gitbook/', 'http://' . $_SERVER['SERVER_NAME'], ''], $content);
else
    $content = str_replace(['.gitbook/', $_CONFIG['site'], '.md'], [$sourse . '.gitbook/', 'https://' . $_SERVER['SERVER_NAME'], ''], $content);


// banner
if (preg_match('/cover: (.+)\scoverY: (.+)/', $content, $matchs)) {
    $element = '<img src="' . $matchs[1] . '" class="img-responsive hidden-xs">';
    $banner = $matchs[1];
    $content = str_replace($matchs[0], $element, $content);
}

// image popup
if (preg_match_all('/\[\^[1-9]\]: (.+)/', $content, $matchs)) {
    if (is_array($matchs[0])) {
        $k = 1;
        foreach ($matchs[0] as $k => $text) {
            $content = str_replace([$matchs[0][$k], '[^' . $k . ']'], ['', ''], $content);
            $k++;
        }
    }
}

// image list
if (preg_match_all('/\!\[(.*?)\]\(<(.*?)>\)/', $content, $matchs)) {
    if (is_array($matchs[1]))
        foreach ($matchs[1] as $k => $text) {
            $element = '<p><img class="img-responsive" src="' . $matchs[2][$k] . '" alt="' . $text . '" title="' . $text . '"/></p>';
            $content = str_replace($matchs[0][$k], $element, $content);
        }
}

// description
if (preg_match('/---(.*?)---/s', $content, $matchs)) {
    $description = trim(str_replace(['description:', '>-'], [''], $matchs[1]));
    $element = '# ' . $title . PHP_EOL . $description;
    $content = str_replace($matchs[0], $element, $content);
}

$menu_content = @file_get_contents($sourse . '/SUMMARY.md');
$menu_content = str_replace(['.md', '(', 'Table of contents', 'README'], ['', '(/', '', ''], $menu_content);

$html = $Parsedown->text($content);

$menu = $Parsedown->text($menu_content);
$menu = $menu_mobile= str_replace(['<ul>'], ['<ul class="nav nav-pills nav-stacked">'], $menu);

// menu scroll  history
if (preg_match_all('/<a href="(.*?)">(.*?)<\/a>/', $menu, $matchs)) {

   if (is_array($matchs[1]))
        foreach ($matchs[1] as $k => $text) {
       
           if($path == $matchs[1][$k] and $path !='/')
               $menu_active = ' class="active" ';
           else $menu_active = null;

            $element = '<a href="' . $matchs[1][$k] . '" '. $menu_active.' id="'.str_replace(['/','-','.'],'',$matchs[1][$k]).'">' . $matchs[2][$k] . '</a>';

            $menu = str_replace($matchs[0][$k], $element, $menu);
        }
    
}

// hint
if (preg_match_all('/{% hint style="(.*?)" %}(\s*)(.*?)(\s*){% endhint %}/s', $html, $matchs)) {

    if (is_array($matchs[3]))
        foreach ($matchs[3] as $k => $text) {
            $element = '<div class="alert alert-' . $matchs[1][$k] . '">' . $text . '</div>';
            $html = str_replace($matchs[0][$k], $element, $html);
        }
}

// content-ref
if (preg_match_all('/{% content-ref url="(.*?)" %}(\s*)(.*?)(\s*){% endcontent-ref %}/', $html, $matchs)) {

    if (is_array($matchs[3]))
        foreach ($matchs[3] as $k => $text) {
            $element = '<a href="' . $matchs[1][$k] . '"><div class="panel panel-default"><div class="panel-body"><span class="glyphicon glyphicon-menu-right pull-right"></span><span class="glyphicon glyphicon-share-alt"></span>'.$text.'</div></div></a>';
            $html = str_replace($matchs[0][$k], $element, $html);
        }
}

// embed
if (preg_match_all('/{% embed url="<a href="(.*?)">(.*?)" %}/', $html, $matchs)) {

    if (is_array($matchs[1]))
        foreach ($matchs[1] as $k => $text) {

            // video
            if (in_array(pathinfo($text, PATHINFO_EXTENSION), ['mp4', 'mov']))
                $element = '<div class="panel panel-default"><div class="panel-body"><div class="embed-responsive embed-responsive-16by9"><video class="embed-responsive-item" src="' . $text . '" controls style="max-height:500px"></video></div></div></div>';
            // iframe
            else
                $element = '<div class="panel panel-default"><div class="panel-body"><a href="' . $text . '" target="_blank"><span class="glyphicon glyphicon-share-alt"></span>Перейти</a></div></div>';

            $html = str_replace($matchs[0][$k], $element, $html);
        }
}

// tabs
$ti = 1;
if (preg_match_all('/{% tabs %}(.*?){% endtabs %}/s', $html, $matchs_tabs)) {

    if (is_array($matchs_tabs[1])) {
        foreach ($matchs_tabs[1] as $kt => $tabs) {

            if (preg_match_all('/{% tab title="(.*?)" %}(.*?){% endtab %}/s', $tabs, $matchs)) {

                if (is_array($matchs[2])) {
                    $tablist = null;
                    $tabcontent = null;


                    foreach ($matchs[2] as $k => $text) {

                        if ($k == 0)
                            $class = 'active';
                        else
                            $class = null;

                        $tablist .= '<li role="presentation" class="' . $class . '"><a href="#tab_' . $ti . '" aria-controls="home" role="tab" data-toggle="tab">' . $matchs[1][$k] . '</a></li>';

                        $tabcontent .= '<div role="tabpanel" class="tab-pane ' . $class . '" id="tab_' . $ti . '">' . $Parsedown->text($text) . '</div>';
                        $GLOBALS['ti'] ++;
                    }


                    $element_tab[$kt] = '<div>

  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">' . $tablist . '</ul>

  <!-- Tab panes -->
  <div class="tab-content">' . $tabcontent . '</div>

        </div>';
                }
            }
        }
    }

    if (is_array($matchs_tabs[0])) {
        foreach ($matchs_tabs[0] as $k => $tabs) {
            $html = str_replace($matchs_tabs[0][$k], $element_tab[$k], $html);
        }
    }
}

$html = str_replace(['<img ', '<table>', '{% hint style="info" %}', '{% endhint %}', 'data-view="cards"','\\'], ['<img class="img-responsive img-rounded" ', '<table class="table table-striped table-responsive table-bordered">', '', '', 'class="table table-striped table-responsive table-bordered"','<br>'], $html);

// Подпапки
if (strlen($html) < 1000) {
    if ($dh = @opendir($sourse . $path)) {


        while (($file = readdir($dh)) !== false) {
            if ($file != "." and $file != ".." and $file != 'README.md') {

                if (file_exists($sourse . $path . $file . '/README.md')) {
                    $name = file_get_contents($sourse . $path . $file . '/README.md');

                    // title
                    if (preg_match_all('/\# (.+)/', $name, $matchs)) {

                        $name = $matchs[1][0];
                    }

                    $html .= '<div class="panel panel-default">
  <div class="panel-body">
    <a href="' . $path . $file . '"><span class="glyphicon glyphicon-menu-right pull-right"></span>' . str_replace(['#'], [''], $name) . '</a>
        
  </div>
</div>';
                } else {

                    if (file_exists($sourse . $path . '/' . $file))
                        $name = @file_get_contents($sourse . $path . '/' . $file);

                    // title
                    if (preg_match_all('/\# (.+)/', $name, $matchs)) {

                        $name = $matchs[1][0];


                        if (str_replace("/", "", $path) == str_replace(".md", "", $file))
                            $title = $name;
                    }

                    if (empty($name))
                        $name = str_replace(['.md'], [''], $file);

                    $html .= '<div class="panel panel-default">
  <div class="panel-body">
    <a href="' . $path . '/' . str_replace(['.md'], [''], $file) . '"><span class="glyphicon glyphicon-menu-right pull-right"></span>' . str_replace(['#'], [''], $name) . '</a>
        
  </div>
</div>';
                }
            }
        }

        closedir($dh);
    }
}

// Хлебны крошки
if (empty($breadcrumb_parent))
    $breadcrumb_position = 1;
else
    $breadcrumb_position = 2;

if (empty($html)) {
    header("HTTP/1.0 404 Not Found");
    header("Status: 404 Not Found");
    $html = $Parsedown->text($_CONFIG['404']);
    $name =  $title = '404 Not Found';
}


$breadcrumb = '<ol class="breadcrumb hidden-xs" itemscope itemtype="https://schema.org/BreadcrumbList">
   <li><a href="/"><span class="glyphicon glyphicon-home"></span></a></li>
   ' . $breadcrumb_parent . '
   <li class="active" itemprop="itemListElement" itemscope  itemtype="https://schema.org/ListItem"><span itemprop="name">' . $name . '</span>
       <meta itemprop="item" href="https://' . $_SERVER['SERVER_NAME'] . $path . '" />
       <meta itemprop="position" content="' . $breadcrumb_position . '"/>
   </li>
   </ol>';


// Шаблон дизайна
include_once './template/' . $_CONFIG['template'] . '/template.tpl.php';
