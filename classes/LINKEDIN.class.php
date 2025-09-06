<?php



class LINKEDIN

{

    private static $appId = 'appId';

    private static $appPass = 'appPass';



    public static function isEnabled()

    {

        return MYSQL::selectOneValue("SELECT `service` FROM api_token WHERE `service` = 'LinkedInPageURN'");

    }



    public static function saveUserToken($code, $state)

    {

        if ('89298737378822' == $state) {

            $data = ['grant_type' => 'authorization_code',

                'code' => $code,

                'redirect_uri' => UTILS::getFunction('isHttps').'://'.UTILS::getFunction('StaticUrl').'/Administration/Settings',

                'client_id' => self::$appId,

                'client_secret' => self::$appPass, ];

            $sURL = 'https://www.linkedin.com/oauth/v2/accessToken';

            $aHTTP = [

                'http' => [

                    'method' => 'POST',

                    'content' => http_build_query($data),

                    'header' => "Content-type: application/x-www-form-urlencoded\r\n",

                ],

            ];

            $context = stream_context_create($aHTTP);

            $result = file_get_contents($sURL, false, $context);



            $res = json_decode($result);

            $token = $res->access_token;

            if ($token) {

                MYSQL::query("DELETE FROM api_token WHERE `service` = 'LinkedInUserToken'");

                MYSQL::query("INSERT INTO api_token (`service`, token) VALUES ('LinkedInUserToken', '{$token}')");

            }

        }

    }



    public static function savePageURN($urn)

    {

        MYSQL::query("DELETE FROM api_token WHERE `service` = 'LinkedInPageURN'");

        MYSQL::query("INSERT INTO api_token (`service`, token) VALUES ('LinkedInPageURN', '{$urn}')");

    }



    public static function getListPages()

    {

        $pages = [];

        $res = self::get('organizationAcls', ['q' => 'roleAssignee', 'projection' => '(elements*(*,organization~(localizedName)))']);

        if ($res) {

            foreach ($res->elements as $page) {

                $pages[] = ['name' => $page->{'organization~'}->localizedName, 'id' => $page->organization];

            }

        }



        $resMe = self::get('me');

        if ($resMe) {

            $pages[] = ['name' => "{$resMe->localizedLastName} {$resMe->localizedFirstName}", 'id' => "urn:li:person:{$resMe->id}"];

        }



        return $pages;

    }



    public static function postMessage($message)

    {

        if (strpos(self::pageUrn(), 'urn:li:person:') !== false) {

            return self::post('ugcPosts', json_encode(

                [

                    'author' => self::pageUrn(),

                    'lifecycleState' => 'PUBLISHED',

                    'specificContent' => [

                        'com.linkedin.ugc.ShareContent' => [

                            'shareCommentary' => [

                                'text' => self::cleanMessage($message)

                            ],

                            'shareMediaCategory' => 'NONE'

                        ]

                    ],

                    'visibility' => [

                        'com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC'

                    ]

                ]

            ));

        }

        return self::post('shares', json_encode(

            [

                'owner' => self::pageUrn(),

                'distribution' => ['linkedInDistributionTarget' => ['visibleToGuest' => true]],

                'text' => ['text' => self::cleanMessage($message)],

            ]

        ));

    }



    public static function postArticles($title, $image, $url)

    {

        if (strpos(self::pageUrn(), 'urn:li:person:') !== false) {

            return self::post('ugcPosts', json_encode(

                [

                    "author" => self::pageUrn(),

                    "lifecycleState" => "PUBLISHED",

                    "specificContent" => [

                        "com.linkedin.ugc.ShareContent" => [

                            "shareCommentary" => [

                                "text" => ""

                            ],

                            "shareMediaCategory" => "ARTICLE",

                            "media" => [

                                [

                                    "status" => "READY",

                                    "originalUrl" => $url,

                                ]

                            ]

                        ]

                    ],

                    "visibility" => [

                        "com.linkedin.ugc.MemberNetworkVisibility" => "CONNECTIONS"

                    ]

                ]

            ));

        }

        return self::post('shares', json_encode(

            [

                'owner' => self::pageUrn(),

                'distribution' => ['linkedInDistributionTarget' => ['visibleToGuest' => true]],

                'content' => [

                    'title' => $title,

                    'contentEntities' => [[

                        'entityLocation' => $url,

                        'thumbnails' => [

                            [

                                'resolvedUrl' => $image,

                            ],

                        ],

                    ]],

                ],

            ]

        ));

    }



    private static function cleanMessage($message)

    {

        return htmlspecialchars_decode(strip_tags(str_replace(["<br>", "</p>"], "\n", html_entity_decode(htmlspecialchars_decode($message)))));

    }



    private static function userToken()

    {

        return MYSQL::selectOneValue("SELECT token FROM api_token WHERE `service` = 'LinkedInUserToken'");

    }



    private static function pageUrn()

    {

        return MYSQL::selectOneValue("SELECT token FROM api_token WHERE `service` = 'LinkedInPageURN'");

    }



    private static function post($url, $data)

    {

        $token = self::userToken();

        $sURL = "https://api.linkedin.com/v2/{$url}";

        $aHTTP = [

            'http' => [

                'method' => 'POST',

                'content' => $data,

                'header' => "Content-Type: application/json\r\n".

                            'Authorization: Bearer '.$token."\r\n",

            ],

        ];

        $context = stream_context_create($aHTTP);

        $result = file_get_contents($sURL, false, $context);



        return json_decode($result);

    }



    private static function get($url, $data = [])

    {

        $token = self::userToken();

        $sURL = "https://api.linkedin.com/v2/{$url}?".http_build_query($data);

        $aHTTP = [

            'http' => [

                'method' => 'GET',

                'header' => "Content-Type: application/x-www-form-urlencoded\r\n".

                'Authorization: Bearer '.$token."\r\n",

            ],

        ];

        $context = stream_context_create($aHTTP);

        $result = file_get_contents($sURL, false, $context);



        return json_decode($result);

    }

}

