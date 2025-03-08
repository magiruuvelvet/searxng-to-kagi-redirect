<?php

// Redirect search queries targetted at the local SearXNG instance to Kagi.com
// Preserves the original search query habits by translating SearXNG specific syntax in the query string
// before redirecting to Kagi.com

require_once "utils.php";

// parse the SearXNG query and translate it to a Kagi search query
$res = search_pair::create_search_pair($_GET["q"]);
$res->filter_bangs();

header("Location: https://kagi.com/search?q=" . urlencode($res));

// DEBUGGING
// echo "'{$res->bang}' :: '{$res->query}'<br>";
// echo "'{$res}'";
