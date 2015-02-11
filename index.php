<?php
  /**
   * Turn on all error reporting.
   */
  error_reporting(E_ALL);
  ini_set('display_errors', 1);
  $start = microtime(true);

  header('Content-type: application/json');

  $ch = curl_init(); 

  $headers  = array(
    "Accept: text/html, application/xhtml+xml, */*",
    "Accept-Language: en-US,en;q=0.8,zh-Hans-CN;q=0.5,zh-Hans;q=0.3"
  );

  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_URL, $_REQUEST["u"]); 
  curl_setopt($ch, CURLOPT_POST, false);
  curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
  curl_setopt($ch, CURLOPT_TIMEOUT, 5000); 
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
  curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.3; WOW64; Trident/7.0; rv:11.0) like Gecko"); 

  $html = curl_exec($ch);
  $dom = new DOMDocument();
  @$dom->loadHTML($html);
  $xpath = new DomXPath($dom);

  // curl_info
  if(isset($_REQUEST["curl_info"])) {
    $curlInfo = curl_getinfo($ch); 
  }
  curl_close($ch);

  $queries = ["//head/title"];
  if(isset($_REQUEST["x"])) {
    $queries = $_REQUEST["x"];
  }

  function processNodes($nodes) {
    $json = [];
    foreach($nodes as $node) {
      $el = [];
      $el["name"] = $node->nodeName;
      // Get all attributes
      if($node->hasAttributes()) {
        foreach($node->attributes as $attribute) {
          $el["attributes"] = [];
          array_push($el["attributes"], [$attribute->nodeName=>$attribute->textContent]);
        }
      }
      
      $textContent = $node->textContent;
      // Get the text content of this node only and not of the children.
      if($node->hasChildNodes()) {
        $el["children"] = [];
        foreach( $node->childNodes as $child) {
          if( $child->nodeType === XML_TEXT_NODE) {
            $textContent = $child->textContent;
            break;
          }
        }
        array_push($el["children"], processNodes($node->childNodes));
      }
      $el["textContent"] = $textContent;
      array_push($json, $el);
    }
    return $json;
  }
  
  foreach($queries as $query) {
    // Create the placeholder so we have an array even when the query doesn't resolve.
    $nodes = $xpath->query($query);
    $json = processNodes($nodes);
  }
  
  if(sizeof($json) == 0) {
    foreach($queries as $key=>$query) {
      $json[$key] = [
        "attributes" => [],
        "textContent" => ""
      ];
    }
  }
  
  if(isset($curlInfo)) {
    $json["curl_info"] = $curlInfo;
  }
  $json["exec_time"] = (microtime(true) - $start);
  echo json_encode($json, JSON_PRETTY_PRINT);
  