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
    protected $apiUrl = 'https://api.krmpesan.app';

    /**
     * API Token.
     *
     * @var string
     */
    protected $token;

    /**
     * Refresh Token.
     *
     * @var string
     */
    protected $refreshToken;

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
        // Set Token (optional)
        $this->token = $data['idToken'];

        // Set DeviceId
        if (!isset($data['deviceId']) or empty($data['deviceId'])) {
            throw new Exception('DeviceId is required.');
        } else {
            $this->deviceId = $data['deviceId'];
        }

        // Set Refrest Token
        if (!isset($data['refreshToken']) or empty($data['refreshToken'])) {
            throw new Exception('Token is required.');
        } else {
            $this->refreshToken = $data['refreshToken'];
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
        $buildUrl = $this->apiUrl.'/'.$url;

        // set default header
        $headers = [];
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: Bearer '.$this->token;

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

    /**
     * Refresh Token.
     *
     * @return void
     */
    public function refreshToken()
    {
        $url = 'tokens?refresh_token='.$this->refreshToken.'&device_key='.$this->deviceId;
        $response = $this->action('GET', $url);
        $data = json_decode($response, true);
        $this->token = $data['IdToken'];

        return $response;
    }


    /**
     * Create Template Message
     * 
     * @param string $name
     * @param string $category UTILLITY, AUTHENTICATION, MARKETING
     * @param string $description
     */
    public function createTemplate($name, $category, $description)
    {
        // build form
        $form = json_encode([
            'name' => $name,
            'category' => $category,
            'description' => $description
        ]);

        return $this->action('POST', 'messages/template', $form);
    }

    /**
     * Create Template Language
     * 
     * @param string $slug
     * @param string $lang en, id
     * @param string $message
     * @param array $fields ["fields-1", "fields-2", "fields-3"]
     * @param array $header [
     *      "type" => "image|document",
     *      "url" => "https://example.com/image.png"
     * ]
     * @param string $footer
     * @param array $button
     */
    public function createTemplateLang($slug, $lang, $message, $fields, $header = null, $footer = null, $button = null)
    {
        // build form
        $form = json_encode([
            'slug' => $slug,
            'language' => $lang,
            'message' => $message,
            'fields' => $fields,
            'header' => isset($header) ? $header : null,
            'footer' => isset($footer) ? $footer : null,
            'button' => isset($button) ? $button : null
        ]);

        return $this->action('POST', 'messages/template/lang', $form);
    }

    /**
     * Send Message Text.
     *
     * @param string|int $to
     * @param string     $templateLanguage
     * @param string     $templateName
     * @param array      $body
     *
     * @return void
     */
    public function sendMessageTemplateText($to, $templateName, $templateLanguage, $body)
    {
        // build form
        $form = json_encode([
            'phone'             => $to,
            'template_name'     => $templateName,
            'template_language' => $templateLanguage,
            'template'          => (object) [
                'body' => $body,
            ],
        ]);

        return $this->action('POST', 'messages', $form);
    }

    /**
     * Send Message Image.
     *
     * @param string|int $to
     * @param string     $templateLanguage
     * @param string     $templateName
     * @param array      $body
     * @param string     $image
     *
     * @return void
     */
    public function sendMessageTemplateImage($to, $templateName, $templateLanguage, $body, $image)
    {
        // build form
        $form = json_encode([
            'phone'             => $to,
            'template_name'     => $templateName,
            'template_language' => $templateLanguage,
            'template'          => (object) [
                'body'   => $body,
                'header' => [
                    'type' => 'image',
                    'url'  => $image,
                ],
            ],
        ]);

        return $this->action('POST', 'messages', $form);
    }

    /**
     * Send Message Document.
     *
     * @param string|int $to
     * @param string     $templateLanguage
     * @param string     $templateName
     * @param array      $body
     * @param string     $document
     *
     * @return void
     */
    public function sendMessageTemplateDocument($to, $templateName, $templateLanguage, $body, $document)
    {
        // build form
        $form = json_encode([
            'phone'             => $to,
            'template_name'     => $templateName,
            'template_language' => $templateLanguage,
            'template'          => (object) [
                'body'   => $body,
                'header' => [
                    'type' => 'document',
                    'url'  => $document,
                ],
            ],
        ]);

        return $this->action('POST', 'messages', $form);
    }

    /**
     * Send Message Button.
     *
     * @param string|int $to
     * @param string     $templateLanguage
     * @param string     $templateName
     * @param array      $body
     * @param string     $button
     *
     * @return void
     */
    public function sendMessageTemplateButton($to, $templateName, $templateLanguage, $body, $button)
    {
        // build form
        $form = json_encode([
            'phone'             => $to,
            'template_name'     => $templateName,
            'template_language' => $templateLanguage,
            'template'          => (object) [
                'body'    => $body,
                'buttons' => [
                    'url'  => $button,
                ],
            ],
        ]);

        return $this->action('POST', 'messages', $form);
    }

    /**
     * Send Reply Text.
     *
     * @param string|int $to
     * @param string     $text
     *
     * @return void
     */
    public function sendReplyText($to, $text)
    {
        // build form
        $form = json_encode([
            'phone' => $to,
            'reply' => (object) [
                'type' => 'text',
                'text' => $text,
            ],
        ]);

        return $this->action('POST', 'messages', $form);
    }

    /**
     * Send Reply Image.
     *
     * @param string|int $to
     * @param string     $image
     * @param string     $caption
     *
     * @return void
     */
    public function sendReplyImage($to, $image, $caption = '')
    {
        // build form
        $form = json_encode([
            'phone' => $to,
            'reply' => (object) [
                'type'    => 'image',
                'image'   => $image,
                'caption' => $caption,
            ],
        ]);

        return $this->action('POST', 'messages', $form);
    }

    /**
     * Send Reply document.
     *
     * @param string|int $to
     * @param string     $document
     *
     * @return void
     */
    public function sendReplyDocument($to, $document)
    {
        // build form
        $form = json_encode([
            'phone' => $to,
            'reply' => (object) [
                'type'       => 'image',
                'document'   => $document,
            ],
        ]);

        return $this->action('POST', 'messages', $form);
    }
}
