<?php

namespace KrmPesan;

class Client
{
    /**
     * Curl Timeout
     *
     * @see https://www.php.net/manual/en/function.curl-setopt
     */
    protected $timeout;

    /**
     * Default URL
     */
    protected $apiUrl;

    /**
     * Token Data
     */
    protected $token;

    /**
     * Region Available
     *
     * @var array
     */
    protected $regionPanel = [
        '01'    => 'https://region01.krmpesan.com',
        'test'  => 'http://krmpesan.test'
    ];

    /**
     * Custom Request Header
     *
     * @var [type]
     */
    protected $customHeader;

    /**
     * Construct Function
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        // Select Region
        $this->apiUrl   = $this->regionPanel[$data['region']];
        
        // Custom Set Timeout
        $this->timeout  = $data['timeout'] ?? 30;

        // Set Token
        $this->token = $data['token'];

        // Set Custom Header
        $this->customHeader = $data['headers'] ?? null;
    }

    /**
     * Curl Post or Get Function
     *
     * @param string $type
     * @param string $url
     * @param array $form
     * @param array $customHeader
     *
     * @return void
     */
    private function action($type, $url, $form = null)
    {
        // setup url
        $buildUrl = $this->apiUrl . '/' . $url;

        // build curl instance
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $buildUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYSTATUS, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);

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

        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Accept: application/json';
        $headers[] = 'Authorization: Bearer '. $this->token;

        // use custom header if not null
        if ($this->customHeader) {
            $headers = $this->customHeader;
        }

        // set curl header
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // running curl
        $result = curl_exec($ch);

        // throw error
        if (curl_errno($ch)) {
            throw curl_error($ch);
        }

        // stop connection
        curl_close($ch);

        // return result request
        return $result;
    }

    /**
     * Device Information
     *
     * @return void
     */
    public function deviceInfo()
    {
        return $this->action('GET', 'api/v2/device');
    }

    /**
     * Restart Device Connection
     *
     * @return void
     */
    public function deviceRestart()
    {
        return $this->action('GET', 'api/v2/device/restart');
    }

    /**
     * Update Device Data
     *
     * @param array $form
     * @return void
     */
    public function deviceUpdate(array $form)
    {
        return $this->action('PUT', 'api/v2/device');
    }

    /**
     * Get All Message
     *
     * @param int $pages
     * @return void
     */
    public function getMessage($page = null)
    {
        $url = 'api/v2/message';

        if ($page) {
            $url = 'api/v2/message?page=' . $page;
        }
        
        return $this->action('GET', $url);
    }

    /**
     * Get All Message Inbox
     *
     * @param int $page
     * @return void
     */
    public function getMessageInbox($page = null)
    {
        $url = 'api/v2/message/inbox';

        if ($page) {
            $url = 'api/v2/message/inbox?page=' . $page;
        }
        
        return $this->action('GET', $url);
    }

    /**
     * Get All Message Outbox
     *
     * @param int $page
     * @return void
     */
    public function getMessageOutbox($page = null)
    {
        $url = 'api/v2/message/outbox';

        if ($page) {
            $url = 'api/v2/message/outbox?page=' . $page;
        }
        
        return $this->action('GET', $url);
    }

    /**
     * Get All Message Forward
     *
     * @param int $page
     * @return void
     */
    public function getMessageForward($page = null)
    {
        $url = 'api/v2/message/forward';

        if ($page) {
            $url = 'api/v2/message/forward?page=' . $page;
        }
        
        return $this->action('GET', $url);
    }

    /**
     * Get Message by ID
     *
     * @param uuid $uuid
     * @return void
     */
    public function getMessageId($uuid)
    {
        return $this->action('GET', 'api/v2/message/' . $uuid);
    }

    /**
     * Send Message Text
     *
     * @param number $to
     * @param text $message
     * @return void
     */
    public function sendMessageText($to, $message)
    {
        // build form
        $form = json_encode([
            'phone' => $to,
            'message' => $message
        ]);

        return $this->action('POST', 'api/v2/message/send-text', $form);
    }
}
