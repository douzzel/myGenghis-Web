<?php

// require "vendor/autoload.php";



use Abraham\TwitterOAuth\TwitterOAuth;



define('CONSUMER_KEY', getenv('key'));

define('CONSUMER_SECRET', getenv('secret'));

define('OAUTH_CALLBACK', getenv('OAUTH_CALLBACK'));







// define('CONSUMER_KEY', 'key');

// define('CONSUMER_SECRET', 'secret');

// define('ACCESS_TOKEN', 'token');

// define('ACCESS_TOKEN_SECRET', 'token_secret');

$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);

$request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => OAUTH_CALLBACK));

class TWITTER

{



    public static function cleanMessage($message)

    {

        return strip_tags(str_replace(["<br>", "</p>"], "\n", html_entity_decode($message)));

    }



    public static function isEnabled()

    {



        try {

            $tw = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);

            $tw->setDecodeJsonAsArray(true);

            $response = $tw->get('account/verify_credentials');

            if (isset($response['errors'])) {

                return false;

            }

        } catch (Exception $e) {

            echo "$e" . "ERROR!";

        }

        return true;

    }



    public static function postMessage($msg)

    {

        $tw = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);

        $tw->setDecodeJsonAsArray(true);

        $msg = self::cleanMessage($msg);

        $tw->post('statuses/update', array('status' => $msg));

        return $tw;

    }

}

