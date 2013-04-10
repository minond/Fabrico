<?php

namespace Fabrico\Response\Http;

use Fabrico\Output\Http\Output;
use Fabrico\Response\Response as ResponseBase;

/**
 * responds to a browser
 */
class Response implements ResponseBase
{
    /**
     * checked before sending headers
     * @var boolean
     */
    private $content_sent = false;

    /**
     * @var Output
     */
    private $output;

    /**
     * headers
     * @var array
     */
    private $headers = [];

    /**
     * output setter
     * @param Output $output
     */
    public function setOutput(Output $output)
    {
        $this->output = $output;
        $this->setHeaders($output->getHeaders());
    }

    /**
     * output getter
     * @return Output
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * add a header
     * @param string $header
     * @param string $value
     * @param boolean $overwrite
     */
    public function setHeader($header, $value, $overwrite = false)
    {
        if ($overwrite || !$this->hasHeader($header)) {
            $this->headers[ $header ] = $value;
        }
    }

    /**
     * sets multiple headers at once
     * @param array $headers
     * @param boolean $overwrite
     */
    public function setHeaders($headers, $overwrite = false)
    {
        foreach ($headers as $header => $value) {
            $this->setHeader($header, $value, $overwrite);
        }
    }

    /**
     * get a header's value
     * @param string $header
     * @return string
     */
    public function getHeader($header)
    {
        return $this->hasHeader($header) ?
            $this->headers[ $header ] : null;
    }

    /**
     * checks if a header has been test
     * @param string $header
     * @return boolean
     */
    public function hasHeader($header)
    {
        return array_key_exists($header, $this->headers);
    }

    /**
     * removes a header
     * @param string $header
     */
    public function removeHeader($header)
    {
        unset($this->headers[ $header ]);
    }

    /**
     * @return boolean
     */
    public function ready()
    {
        return isset($this->output);
    }

    /**
     * sends headers to brownser
     * @codeCoverageIgnoreEnd
     * @throws \Exception
     */
    public function sendHeaders()
    {
        if ($this->content_sent) {
            throw new \Exception('Content already sent');
        }

        // TODO: implement test
        foreach ($this->headers as $header => $val) {
            header(strlen($val) ? "{$header}: {$val}" : $header);
        }
    }

    /**
     * outputs the content
     */
    public function sendContent()
    {
        // then output
        $this->output->output();
        $this->content_sent = true;
    }

    /**
     * sends the headers and content
     * @codeCoverageIgnoreEnd
     */
    public function send()
    {
        $this->sendHeaders();
        $this->sendContent();
    }
}
