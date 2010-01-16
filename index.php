<?php
/**
 * (c) 2007 botmonster
 * http://blog.botmonster.com
 */

// enter your feed name
$feedURL = isset($_GET['feedURL']) ? $_GET['feedURL'] : '';

function get_cached_xml($feedURL, $ttl = 600){

    $cache = function_exists('xcache_set');
    
    if($cache){
        $cachedFeed = xcache_get($feedURL);
        if ($cachedFeed != null) 
            return simplexml_load_string($cachedFeed);
    }
    
    $feed = @file_get_contents($feedURL);
    if(!$feed)
        return false;
    if($cache)
        xcache_set($feedURL, $feed, $ttl);
    
    return simplexml_load_string($feed);
}
$xml = get_cached_xml($feedURL);

$feedTitle = $xml !== false ? $xml->title : 'Error';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
         "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $feedTitle; ?></title>
<meta name="viewport" content="width=320; initial-scale=1.0; maximum-scale=1.0; user-scalable=1;"/>
<style type="text/css" media="screen">@import "iui/iuix.css";</style>
<style type="text/css">
div.feed h1{
	font-size: 18px;
}
div.feed h2{
	font-size: 10px;
}
</style>
<script type="application/x-javascript" src="iui/iuix.js"></script>

</head>

<body>
    <div class="toolbar">
        <h1 id="pageTitle"><?php echo $feedTitle; ?></h1>
        	
        </h1>
        <a id="backButton" class="button" href="#"></a>
    </div>
    <?php
    if($xml === false){ 
     echo '<div id="home" selected="true">Error retrieving feed: '.$feedURL.'</div>';
    }else{
        echo '<ul id="home" selected="true">';
        foreach($xml->entry as $entry){
          echo '<li><a href="#'.$entry->id.'">'.($entry->title == '' ? substr(strip_tags($entry->content), 0, 30) : $entry->title).
               '<br/><span style="font-size: 10px;">'.$entry->author->name.' @ '.date('r',strtotime($entry->published)).'</span></a></li>'."\n";
        }
        echo '<li><a href="http://botmonster.com/iFeed/?about">powered by iFeedBlogger 0.1</a></li></ul>';
        foreach($xml->entry as $entry){
              echo '<div class="feed" id="'.$entry->id.'"><h1>'.$entry->title.'</h1><h2>'.$entry->author->name.' @ '.date('r',strtotime($entry->published)).'</h2><p>'.
                   $entry->content.'</p>';
              foreach($entry->link as $link){
                  if($link['rel'] == 'replies')
                      echo '<p><a href="'.$link['href'].'">'.$link['title']."</a></p>";
              }
              echo "</div>";
        }
    }
    ?>
</body>
</html>
