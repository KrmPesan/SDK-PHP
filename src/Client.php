<?php

namespace KrmPesan;

use Exception;

class Client
{
    
    /**
     * Curl Instance
     */
    protected $ch;

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
     * Construct Function
     */
    public function __construct(array $data)
    {
        $this->ch = curl_init();

        // Region Available
        $regionPanel = [
            'region01' => 'https://region01.krmpesan.com'
        ];

        // Select Region
        $this->apiUrl   = $regionPanel[$data['region']];
        
        // Custom Set Timeout
        $this->timeout  = $data['timeout'] ?? 30;
    }

    /**
     * Curl Post or Get Function
     */
    private function action($type, $url, $form, $customHeader)
    {
        try {
            $buildUrl = $this->apiUrl . '/' . $url;

            curl_setopt($this->ch, CURLOPT_URL, $buildUrl);
            curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYSTATUS, false);
            curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->timeout);
        

            if ($type == 'POST') {
                curl_setopt($this->ch, CURLOPT_POST, true);
                curl_setopt($this->ch, CURLOPT_POSTFIELDS, $form);
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function deviceInfo()
    {
        return 'ok';
    }
}
