<?php

include "./config.php";
$sourse = $_CONFIG['sourse'];
$words = urldecode($_REQUEST['words']);
include "./lib/parsedown/Parsedown.php";
$Parsedown = new Parsedown();

function search($sourse, $words) {
    global $result, $Parsedown,$_CONFIG;

    if ($dh = opendir($sourse)) {
        while (($file = readdir($dh)) !== false) {
            if ($file != "." and $file != "..") {

                if (is_file($sourse . $file)) {

                    $fp = fopen($sourse . $file, "r");
                    $fstat = fstat($fp);
                    $fread = fread($fp, $fstat['size']);

                    $Content = strip_tags($fread);
                    fclose($fp);
                    $patern = "/" . $words . "/i";
                    $description = $title = '';


                    if (preg_match($patern, $Content, $match)) {

                        // title
                        if (preg_match_all('/\# (.+)/', $Content, $matchs)) {
                            $title = $matchs[1][0];
                        }

                        // description
                        if (preg_match('/---(.*?)---/s', $Content, $matchs)) {
                            $description = trim(str_replace(['description:', '>-'], [''], $matchs[1]));
                        }

                        // banner
                        if (preg_match('/cover: (.+)\scoverY: (.+)/', $Content, $matchs)) {
                            $description = str_replace($matchs[0], '<span class="hidden">', $description);
                        }

                        $Content = strpos(strip_tags($Parsedown->text($Content)), $words);
                        $found_str = strpos($Content, $words);
                        //$found = strip_tags($Parsedown->text($found_str));
                        $found = substr($Content, $found_str - 150, 150);

                        //print_r($match);

                        $filename = explode(".", $file);

                        //$meta = $this->getMeta($fread);
                        if (empty($title))
                            $title = $filename[0];



                        if (count($result) < 10)
                            $result[] = [
                                'title' => $title,
                                'description' => $description,
                                //'text' => $found,
                                'file' => str_replace([$_CONFIG['sourse'], '.md'], ['/', ''], $sourse . $file)
                            ];
                    }
                }
            }
        }

        closedir($dh);
    }
}

if (!empty($words)) {
    if ($dh = opendir($sourse)) {
        while (($file = readdir($dh)) !== false) {
            if ($file != "." and $file != ".." and $file != "_gitbook") {

                if (is_dir($sourse . $file)) {

                    search($sourse . $file . '/', $words);
                }
            }
        }

        closedir($dh);
    }


    if (is_array($result)) {
        header("Content-Type: application/json");
        echo json_encode($result);
    }
}