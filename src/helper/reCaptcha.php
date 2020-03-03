<?php

namespace Leochenftw\Util;
use SilverStripe\Core\Environment;
use GuzzleHttp\Client;
use SilverStripe\Core\Config\Config;
use Leochenftw\Debugger;

class reCaptcha {
    public static function verify($response)
    {
        $secret_key =   Environment::getEnv('RECAPTCHA_SECRET_KEY');

        $data       =   [
            'form_params'   =>  [
                'secret'    =>  $secret_key,
                'response'  =>  $response
            ]
        ];

        $client =   new Client(['base_uri' => 'https://www.google.com/']);

        try {

            $response   =   $client->request('POST', 'recaptcha/api/siteverify', $data);
            $result     =   json_decode($response->getBody());

            return $result->success;

        } catch (Exception $e) {

        }

        return  false;
    }
}
