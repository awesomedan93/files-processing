<?php

$files = glob('./static-html/blog/*/*.{html}', GLOB_BRACE);
$jsonString = [];
foreach($files as $file) {

    $fileHtmlContent = file_get_contents("$file");

    libxml_use_internal_errors(true); // Prevent HTML errors from displaying
    $dom = new \DOMDocument();
    $dom->loadHTML($fileHtmlContent);

    $xpath = new \DOMXPath($dom);
    $articles = $xpath->query('//article');
    $slugs = $xpath->query('//link[@rel="canonical"]//@href');

    $articleHtml = $dom->saveHTML($articles->item(0));
    $articleUrl = $dom->saveHTML($slugs->item(0));
    $articleUrl = substr($articleUrl,7,-2);
    $pathFragments = explode('/', $articleUrl);
    $end = end($pathFragments);
    //////////////////////////////// WRITE ARTICLE CONTENT TO A FILE
    $jsonString['static_pages']['blog_posts'][] = ['slug'=>$end,'content'=>$articleHtml];

}

$fp = fopen('posts.json', 'w');
fwrite($fp, json_encode($jsonString));
fclose($fp);
