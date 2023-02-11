<?php

/**
 * KrmPesan PHP SDK.
 *
 * @version     3.0.0
 *
 * @see         https://github.com/KrmPesan/SDK-PHP
 *
 * @author      KrmPesan <support@krmpesan.com>
 * @copyright   2023 KrmPesan
 */

namespace KrmPesan;

use Exception;

/**
 * KrmPesan Client Class For Handle REST API Request.
 *
 * @see https://docs.krmpesan.com/
 */

class ClientV3
{

    /**
     * Default Curl Timeout.
     *
     * @var int
     *
     * @see https://www.php.net/manual/en/function.curl-setopt
     */
    protected $timeout;

    /**
     * Default API Url.
     *
     * @var string
     */
    protected $apiUrl = 'https://api.krmpesan.dev';

    /**
     * API Token.
     *
     * @var string
     */
    protected $token;

    /**
     * API Token.
     *
     * @var string
     */
    protected $deviceId;

    /**
     * Custom Request Header.
     *
     * @var array
     */
    protected $customHeader;

    /**
     * Construct Function.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        // Set Token
        $this->token = $data['idToken'];
        $this->token = $data['refreshToken'];

        // Set DeviceId
        if (!isset($data['deviceId']) or empty($data['deviceId'])) {
            throw new Exception('DeviceId is required.');
        } else {
            $this->deviceId = $data['deviceId'];
        }


        // Set Token
        if (!isset($data['refreshToken']) or empty($data['refreshToken'])) {
            throw new Exception('Token is required.');
        } else {
            if (isset($data['idToken'])) {
                $this->token = $data['idToken'];
            } else {
                $response = $this->refreshToken($data['refreshToken'], $data['deviceId']);
                $getToken = json_decode($response, true);
                $this->token = $getToken["AccessToken"];
            }
        }

        // Set Custom Header
        $this->customHeader = $data['headers'] ?? null;
    }

    /**
     * Curl Post or Get Function.
     *
     * @param string $type
     * @param string $url
     * @param array  $form
     *
     * @return void
     */
    private function action($type, $url, $form = null)
    {
        // setup url
        $buildUrl = $this->apiUrl . '/' . $url;

        // set default header
        $headers = [];
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: Bearer ' . $this->token;

        // use custom header if not null
        if ($this->customHeader) {
            $headers = $this->customHeader;
        }

        if (
            isset($form['image']) xor
            isset($form['document'])
        ) {
            // update header type
            $headers[] = 'Content-Type: multipart/form-data';

            // update form data
            $fileData = $form['image'] ?? $form['document'];

            // parse file and get file information
            // https://www.php.net/manual/en/function.realpath
            $file = realpath($fileData);
            // https://www.php.net/manual/en/function.basename
            $filename = basename($file);
            // https://www.php.net/manual/en/function.mime-content-type
            $filemime = mime_content_type($file);

            if (isset($form['image'])) {
                $form['image'] = curl_file_create($file, $filemime, $filename);
            } else {
                $form['document'] = curl_file_create($file, $filemime, $filename);
            }
        } else {
            $headers[] = 'Content-Type: application/json';
        }

        // build curl instance
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $buildUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYSTATUS, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // check action type
        if ($type == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $form);
        } elseif ($type == 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $form);
        } elseif ($type == 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        } else {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        }

        // running curl
        $result = curl_exec($ch);

        // throw error
        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }

        // stop connection
        curl_close($ch);

        // return result request
        return $result;
    }

    public function refreshToken($refreshToken, $deviceId)
    {
        $url =  'tokens?refresh_token=' . $refreshToken . '&device_key=' . $deviceId;
        return $this->action('GET', $url);
    }

    public function sendMessageTemplate($to, $templateName, $templateLanguage, $parameters)
    {
        // build form
        $form = json_encode([
            'phone'             => $to,
            'template_name'     => $templateName,
            'template_language' => $templateLanguage,
            'template'          => (object) $parameters,
        ]);
        return $this->action('POST', 'messages', $form);
    }

    public function sendReply($to, $parameters)
    {
        // build form
        $form = json_encode([
            'phone' => $to,
            'reply' => (object) $parameters,
        ]);
        return $this->action('POST', 'messages', $form);
    }
}
