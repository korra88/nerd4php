<?php

namespace NERD;

/**
 * Interface for APIs at http://nerd.eurecom.fr
 *
 * @author emanuele
 */
class client {

    protected $api_key;

    const DOCUMENT_POST = 'http://nerd.eurecom.fr/api/document';
    const DOCUMENT_GET = 'http://nerd.eurecom.fr/document';
    const ANNOTATION_POST = 'http://nerd.eurecom.fr/api/annotation';
    const ENTITY_GET = 'http://nerd.eurecom.fr/api/entity';

    /**
     * Create a NERD api client.
     * Throws exception if built with empty key.
     * @param string $key   valid Api key
     * @throws Exception
     */
    public function __construct($key) {
        if (empty($key)) {
            error_log('Must provide an api key to use NERD services, visit http://nerd.eurecom.fr');
            throw new Exception('Missing API key.');
        } else {
            $this->api_key = $key;
        }
    }

    /**
     * Create a document with given text.
     * Returns created document's ID or false on failure.
     * @param string $text
     * @return mixed    int if document gets created, false otherwise
     */
    public function createDocumentFromString($text) {
        if (strlen($text) == 0) {
            return false;
        } else {
            $param = array("text" => $text, "key" => $this->api_key);

            $json_encoded = $this->api_request('POST', static::DOCUMENT_POST, $param);
            $json = json_decode($json_encoded, true);
            if (isset($json['idDocument'])) {
                return $json['idDocument'];
            } else {
                ob_start();
                echo "\n[" . date('Y-m-d H:i:s') . "] Error while using nerd API:\n";
                var_dump($json);
                $log = ob_end_clean();
                error_log($log);
                return false;
            }
        }
    }

    /**
     * Create a document from given URI.
     * Returns documents ID or false.
     * @param string $uri
     * @return mixed        int on success, false otherwise
     */
    public function createDocumentFromUri($uri) {
        if (filter_var($uri, FILTER_VALIDATE_URL) !== $uri) {
            return false;
        } else {
            $param = array("uri" => $uri, "key" => $this->api_key);

            $json_encoded = $this->api_request('POST', static::DOCUMENT_POST, $param);
            $json = json_decode($json_encoded, true);
            if (isset($json['idDocument'])) {
                return $json['idDocument'];
            } else {
                ob_start();
                echo "\n[" . date('Y-m-d H:i:s') . "] Error while using nerd API:\n";
                var_dump($json);
                $log = ob_end_clean();
                error_log($log);
                return false;
            }
        }
    }

    /**
     * Retrieve a document data, given it's ID.
     * @param integer $idDocument               Valid document ID
     * @return \NERD\schema\Document|boolean    Document, if successful, false otherwise.
     */
    public function getDocument($idDocument) {
        if (!is_numeric($idDocument)) {
            return false;
        } else {
            $json_encoded = $this->api_request('GET', static::DOCUMENT_GET . '/' . $idDocument);
            $json = json_decode($json_encoded, true);
            $document = new \NERD\schema\Document();
            foreach($json as $docProp => $value) {
                $document->$docProp = $value;
            }
            return $document;
        }
    }

    /**
     * Wrapper for CURL requests
     * @param type $method
     * @param type $url
     * @param type $data
     * @return boolean
     */
    protected function api_request($method, $url, $data = array()) {
        $http_code = 0;
        switch ($method) {
            case 'GET':
            case 'get':
                $response = $this->curl_get($url . (!empty($data) ? '?' . http_build_query($data) : ''), $http_code);
                break;
            case 'POST':
            case 'post':
                $response = $this->curl_post($url, $data, $http_code);
                break;
            default:
                return false;
        }
        if ($http_code !== 200) {
            // Error
            ob_start();
            echo "\n[" . date('Y-m-d H:i:s') . "] Error while using NERD APIs:\n" . $method . ' ' . $url . "\nParam:\n";
            var_dump($data);
            echo "\nResponse: $http_code\n";
            var_dump($response);
            $log = ob_get_clean();
            error_log($log);
            return $response;
        } else {
            return $response;
        }
    }

    /**
     * Used to perform POST request
     * @param type $URL
     * @param type $fields
     * @param type $http_code
     * @return boolean
     */
    protected function curl_post($URL, $fields, & $http_code = 0) {
        //url-ify the data for the POST
        foreach ($fields as $key => $value) {
            $fields_string .= $key . '=' . $value . '&';
        }
        rtrim($fields_string, '&');

        //set the url, number of POST vars, POST data
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $URL);
        curl_setopt($c, CURLOPT_POST, !empty($fields));
        curl_setopt($c, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($c, CURLOPT_HTTPHEADER, array(
            'Accept-Encoding: gzip, deflate',
        ));
        $contents = curl_exec($c);
        $http_code = curl_getinfo($c, CURLINFO_HTTP_CODE);
        curl_close($c);
        if ($contents) {
            return $contents;
        } else {
            return FALSE;
        }
    }

    /**
     * Used to perform GET request
     * @param type $URL
     * @param type $http_code
     * @return boolean
     */
    protected function curl_get($URL, & $http_code = 0) {
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_TIMEOUT_MS, 0);
        curl_setopt($c, CURLOPT_URL, $URL);
        curl_setopt($c, CURLOPT_HTTPHEADER, array(
            'Accept: application/json',
            'Accept-Encoding: gzip, deflate'
        ));
        $contents = curl_exec($c);
        $http_code = curl_getinfo($c, CURLINFO_HTTP_CODE);
        curl_close($c);
        if ($contents) {
            return $contents;
        } else {
            return FALSE;
        }
    }

}
