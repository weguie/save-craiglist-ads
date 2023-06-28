<!-- This is the form in which you put the URL -->
<form name="form" action="index.php" method="get">
    <label>URL</label>
    <input type="text" name="url">
</form>

<?php
function research($searched, $title)
{
    $fichier = file_get_contents("./".$title."/craiglist.txt");
    $ex = explode($searched, $fichier);
    $info = '';
    $i = 0;
    foreach ($ex as $link) {
        $i++;
        if ($i > 1) {
            $link = preg_replace("#(?:'|\")(https?://[\w./?&%:;=-]+)(?:'|\")(.+)#is", "$1", $link);
            $info .= $link . "\n";
        }
    }
    echo "<p>".$info."</p>";
}

// This function is for the title of the ad
function getPics($searched, $title)
{
    $pics = '';
    $fichier = file_get_contents("./".$title."/craiglist.txt");
    $ex = explode($searched, $fichier);

    $n = 7;
    $i = 0;
    $j = $i -1;
    foreach ($ex as $link) {
        $i++;
        
        if ($i > 1 && $i < $n) {
            $link = preg_replace("#(?:'|\")(https?://[\w./?&%:;=-]+)(?:'|\")(.+)#is", "$1", $link);
            $pics .= $link;
            $j = $j+1;
            $ch = curl_init($link);
            $fp = fopen("./".$title.'/images/link'.$j.'.jpg', 'wb');
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_exec($ch);
            curl_close($ch);
            fclose($fp);

            print("<img src=$link><br>");
        }
    }

}

if (isset($_GET['url'])) {
    $url = $_GET['url'];
    $pics = '';

    $searchTitle = "<title>";
    $searchInfo = "<p class=\"attrgroup\">";
    //Thumbnail pics :
    // $searchPics = "img src=";
    $searchPics2 = "url\":";

    //Trim the URL to gather informations
    $trimmed = trim($url, "https://.html");
    $title = explode("/", $trimmed);
    $titleRepPic = "./".$title[3]."/images";
    
    if (is_dir("./".$title[3])) {
        echo "<script>alert(\"You already downloaded this URL.\")</script>";
    }else {
        mkdir("./".$title[3]);
        mkdir($titleRepPic);
        echo 'You successfully downloaded this URL. <br>';
    }

    $ch = curl_init($url);
    $fp = fopen("./".$title[3]."/craiglist.txt", "w");

    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_exec($ch);
    if (curl_error($ch)) {
        fwrite($fp, curl_error($ch));
    }
    curl_close($ch);
    fclose($fp);

    //Thumbnail pics :
    // getPics($searchPics, $title[3]);
    research($searchTitle, $title[3]);
    echo "<br>";
    research($searchInfo, $title[3]);
    getPics($searchPics2, $title[3]);
    

    $explode = file_get_contents("./".$title[3]."/craiglist.txt");
    $section = explode("<section id=\"postingbody\">", $explode);

    $i = 0;
    foreach ($section as $information) {
        $i++;
        if ($i > 1) {
            $information = substr($information, 0, strpos($information, "</section>"));
            print($information);
        }
    }
}

?>