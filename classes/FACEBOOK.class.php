<?php



class FACEBOOK {

  private static $appId = "appId";

  private static $appPass = "appPass";



  private static function cleanMessage($message) {

    return htmlspecialchars_decode(strip_tags(str_replace(["<br>", "</p>"], "\n", html_entity_decode(htmlspecialchars_decode($message)))));

  }



  private static function userToken() {

    return MYSQL::selectOneValue("SELECT token FROM api_token WHERE `service` = 'FBUserToken'");

  }



  private static function pageToken() {

    return MYSQL::selectOneValue("SELECT token FROM api_token WHERE `service` = 'FBPageToken'");

  }



  private static function pageId() {

    return MYSQL::selectOneValue("SELECT content FROM api_token WHERE `service` = 'FBPageToken'");

  }



  private static function post($url, $data) {

    $sURL = "https://graph.facebook.com/v10.0/{$url}";

    $aHTTP = array(

      'http' =>

        array(

        'method'  => 'POST',

        'header'  => 'Content-type: application/x-www-form-urlencoded\r\n',

        'content' => http_build_query($data, '', '&')

      )

    );

    $context = stream_context_create($aHTTP);

    $result = file_get_contents($sURL, false, $context);

    return json_decode($result);

  }



  private static function postData($url, $postdata, $files = null)

  {

    $sURL = "https://graph.facebook.com/v10.0/{$url}";

    $data = "";

      $boundary = "---------------------".substr(md5(rand(0,32000)), 0, 10);



      //Collect Postdata

      foreach($postdata as $key => $val)

      {

          $data .= "--$boundary\n";

          $data .= "Content-Disposition: form-data; name=\"".$key."\"\n\n".$val."\n";

      }



      $data .= "--$boundary\n";



      //Collect Filedata

      foreach($files as $key => $file)

      {

          $fileContents = file_get_contents($file['tmp_name']);



          $data .= "Content-Disposition: form-data; name=\"{$key}\"; filename=\"{$file['name']}\"\n";

          $data .= "Content-Type: image/jpeg\n";

          $data .= "Content-Transfer-Encoding: binary\n\n";

          $data .= $fileContents."\n";

          $data .= "--$boundary--\n";

      }



      $params = array('http' => array(

            'method' => 'POST',

            'header' => 'Content-Type: multipart/form-data; boundary='.$boundary,

            'content' => $data

          ));

    $ctx = stream_context_create($params);

    $result = file_get_contents($sURL, false, $ctx);

    return json_decode($result);

  }



  private static function get($url, $data) {

    $sURL = "https://graph.facebook.com/v10.0/{$url}?".http_build_query($data, '', '&');

    $aHTTP = array(

      'http' =>

        array(

        'method'  => 'GET'

      )

    );

    $context = stream_context_create($aHTTP);

    $result = file_get_contents($sURL, false, $context);

    return json_decode($result);

  }



  public static function isEnabled() {

    return MYSQL::selectOneValue("SELECT service FROM api_token WHERE `service` = 'FBPageToken'");

  }



  public static function getLongLiveUserToken($token) {

    $longToken = self::post("oauth/access_token", array("grant_type" => "fb_exchange_token", "client_id" => self::$appId, "client_secret" => self::$appPass, "fb_exchange_token" => $token));

    if ($longToken && $longToken->access_token) {

      MYSQL::query("DELETE FROM api_token WHERE `service` = 'FBUserToken'");

      MYSQL::query("INSERT INTO api_token (`service`, token) VALUES ('FBUserToken', '{$longToken->access_token}')");

    }

    return $longToken;

  }



  public static function getPageToken($pageId) {

    $pageToken = self::get($pageId, array("fields" => "access_token,name", "access_token" => self::userToken()));

    if ($pageToken && $pageToken->access_token) {

      MYSQL::query("DELETE FROM api_token WHERE `service` = 'FBPageToken'");

      MYSQL::query("INSERT INTO api_token (`service`, token, content) VALUES ('FBPageToken', '{$pageToken->access_token}', '{$pageId}')");

    }

    return $pageToken;

  }



  public static function getListPages() {

    $res = self::get("me/accounts", array('access_token' => self::userToken()));

    $pages = [];

    foreach ($res->data as $page) {

      $pages[] = array('name' => $page->name, 'id' => $page->id);

    }

    return $pages;

  }



  public static function postArticles($url) {

    return self::post(self::pageId() . "/feed", array('access_token' => self::pageToken(), 'link' => $url));

  }



  public static function postMessage($message) {

    return self::post(self::pageId() . "/feed", array('access_token' => self::pageToken(), 'message' => self::cleanMessage($message)));

  }



  public static function postVideo($video, $titre, $description) {

    return self::postData(self::pageId() . "/videos", array('access_token' => self::pageToken(), 'titre' => self::cleanMessage($titre), 'description' => self::cleanMessage($description)), $video);

  }

}

