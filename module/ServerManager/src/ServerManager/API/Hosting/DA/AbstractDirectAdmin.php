<?php
# vim: set expandtab tabstop=4 shiftwidth=4 fdm=marker:

# +---------------------------------------------------+
# | This source file is not copyrighted nor licensed. |
# +---------------------------------------------------+
# | Author:  Eric Yao <Eric@AncientDeveloper.com>     |
# +---------------------------------------------------+

namespace ServerManager\API\Hosting\DA;

use ServerManager\API\Hosting\AbstractHost;

abstract class AbstractDirectAdmin extends AbstractHost
{
    private $da = array();
    public $error;

    /**
     * @param $username
     * @param $password
     * @param $domain
     * @param int $port
     * @param string $scheme
     */
    public function __construct($username, $password, $domain, $port = 2222, $scheme = 'http')
    {
        $this->da['user'] = $username;
        $this->da['pass'] = $password;
        $this->da['host'] = $domain;
        $this->da['port'] = $port;
        $this->da['scheme'] = $scheme;

        if (!$this->da['port']) {
            $this->da['port'] = ($this->da['scheme'] == 'https') ? 443 : 80;
        }
    }

    /**
     * @param $argument
     * @return Result|string
     */
    public function retrieve($argument)
    {
        $error = array();
        $body = '';
        if (is_array($argument) && count($argument)) {

            if (is_string($argument['command'])) {
                $command = $argument['command'];
            } else {
                return 'command not specified';
            }

            switch (strcasecmp($argument['method'], 'POST')) {
                case 0:
                    $post = 1;
                    $method = 'POST';
                    break;
                default:
                    $post = 0;
                    $method = 'GET';
            }

            $data = '';
            if (is_array($argument['data']) && count($argument['data'])) {

                $pair = '';
                foreach ($argument['data'] as $index => $value) {
                    $pair .= $index . '=' . urlencode($value) . '&';
                }

                $data = rtrim($pair, '&');
                $content_length = ($post) ? strlen($data) : 0;

            } else {
                $content_length = 0;
            }

            $prefix = ($this->da['scheme'] == 'https') ? 'ssl://' : NULL;

            if ($fp = fsockopen($prefix . $this->da['host'], $this->da['port'], $error['number'], $error['string'], 10)) {

                $http_header = array(
                    $method . ' /' . $command . ((!$post) ? '?' . $data : NULL) . ' HTTP/1.0',
                    'Authorization: Basic ' . base64_encode($this->da['user'] . ':' . $this->da['pass']),
                    'Host: ' . $this->da['host'],
                    'Content-Type: application/x-www-form-urlencoded',
                    'Content-Length: ' . $content_length,
                    'Connection: close'
                );

                $request = implode("\r\n", $http_header) . "\r\n\r\n";
                fwrite($fp, $request . (($post) ? $data : NULL));

                $returned = '';
                while ($line = @fread($fp, 1024)) {
                    $returned .= $line;
                }

                fclose($fp);

                $h = strpos($returned, "\r\n\r\n");
                $head['all'] = substr($returned, 0, $h);
                $head['part'] = explode("\r\n", $head['all']);

                foreach ($head['part'] as $response) {
                    if (preg_match('/^Location:\s+/i', $response)) {
                        header($response);
                        exit;
                    }
                }

                $body = substr($returned, $h + 4); # \r\n\r\n = 4
            }
        }
        $this->error = new Error($error);
        $body = rtrim((string)$body);
        parse_str($body, $result);
        return new Result($result);
    }

    protected function getUsername(){
        return $this->da['user'];
    }
}