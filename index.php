<?php

/**
 * GitBook2Site
 * @author PHPShop Software
 * @version 1.0
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

    $file = file($filename);
}

if (file_exists($filename))
    $content = file_get_contents($filename);

if (file_exists($sourse . $path . '/../README.md')) {
    $parent = file_get_contents($sourse . $path . '/../README.md');

    // title
    if (preg_match_all('/\# (.+)/', $parent, $matchs)) {

        $breadcrumb_name = $matchs[1][0];
    }

    if ($path != '/')
        $breadcrumb_parent = '<li><a href="' . $path . '/../">' . str_replace(['# '], [''], $breadcrumb_name) . '</a></li>';
}

// title
if (preg_match_all('/\# (.+)/', $content, $matchs)) {

    $description = $title = $matchs[1][0];

    if (is_array($matchs[1]) and count($matchs[1]) > 1)
        foreach ($matchs[1] as $nav) {
            if (strstr($nav, '*'))
                $navigation .= '<li role="presentation"><a href="#' . toLatin($nav) . '" class="menu-2">' . str_replace('*', '', $nav) . '</a></li>';
            else
                $navigation .= '<li role="presentation"><a href="#' . toLatin($nav) . '" class="menu-1">' . $nav . '</a></li>';
        }
}

if (getenv("COMSPEC"))
    $content = str_replace(['.gitbook/', $_CONFIG['site'], '.md'], [$sourse . '_gitbook/', 'http://' . $_SERVER['SERVER_NAME'], ''], $content);
else
    $content = str_replace(['.gitbook/', $_CONFIG['site'], '.md'], [$sourse . '.gitbook/', 'https://' . $_SERVER['SERVER_NAME'], ''], $content);

// banner
if (preg_match('/cover: (.+)\scoverY: (.+)/', $content, $matchs)) {
    $element = '<img src="' . $matchs[1] . '" class="img-responsive hidden-xs">';
    $content = str_replace($matchs[0], $element, $content);
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
$menu = str_replace(['<ul>'], ['<ul class="nav nav-pills nav-stacked">'], $menu);

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
            $element = '<a href="' . $matchs[1][$k] . '"><div class="panel panel-default"><div class="panel-body"><span class="glyphicon glyphicon-menu-right pull-right"></span>' . str_replace('.md', '', $text) . '</div></div></a>';
            $html = str_replace($matchs[0][$k], $element, $html);
        }
}

// tabs
if (preg_match_all('/{% tabs %}(.*?){% endtabs %}/s', $html, $matchs_tabs)) {

    if (is_array($matchs_tabs[1])) {
        foreach ($matchs_tabs[1] as $tabs) {

            if (preg_match_all('/{% tab title="(.*?)" %}(.*?){% endtab %}/s', $tabs, $matchs)) {

                if (is_array($matchs[2]))
                    foreach ($matchs[2] as $k => $text) {

                        if ($k == 0)
                            $class = 'active';
                        else
                            $class = null;

                        $tablist .= '<li role="presentation" class="' . $class . '"><a href="#tab_' . $k . '" aria-controls="home" role="tab" data-toggle="tab">' . $matchs[1][$k] . '</a></li>';

                        $tabcontent .= '<div role="tabpanel" class="tab-pane ' . $class . '" id="tab_' . $k . '">' . $Parsedown->text($text) . '</div>';
                    }

                $element = '<div>

  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">' . $tablist . '</ul>

  <!-- Tab panes -->
  <div class="tab-content">' . $tabcontent . '</div>

        </div>';
            }
        }
    }

    if (is_array($matchs_tabs[0])) {
        foreach ($matchs_tabs[0] as $k => $tabs) {
            $html = str_replace($matchs_tabs[0][$k], $element, $html);
        }
    }
}

$html = str_replace(['<img ', '<table>', '{% hint style="info" %}', '{% endhint %}'], ['<img class="img-responsive" ', '<table class="table table-striped table-responsive table-bordered">', '', ''], $html);

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
$breadcrumb = '<ol class="breadcrumb hidden-xs">
   <li><a href="/"><span class="glyphicon glyphicon-home"></span></a></li>
   ' . $breadcrumb_parent . '
   <li class="active">' . $title . '</li>
   </ol>';

// Шаблон дизайна
include_once './template/' . $_CONFIG['template'] . '/template.tpl.php';
