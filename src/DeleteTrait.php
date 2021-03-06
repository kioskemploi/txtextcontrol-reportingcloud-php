<?php

/**
 * ReportingCloud PHP Wrapper
 *
 * PHP wrapper for ReportingCloud Web API. Authored and supported by Text Control GmbH.
 *
 * @link      http://www.reporting.cloud to learn more about ReportingCloud
 * @link      https://github.com/TextControl/txtextcontrol-reportingcloud-php for the canonical source repository
 * @license   https://raw.githubusercontent.com/TextControl/txtextcontrol-reportingcloud-php/master/LICENSE.md
 * @copyright © 2018 Text Control GmbH
 */

namespace TxTextControl\ReportingCloud;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use TxTextControl\ReportingCloud\Exception\InvalidArgumentException;
use TxTextControl\ReportingCloud\Validator\StaticValidator;

/**
 * Trait DeleteTrait
 *
 * @package TxTextControl\ReportingCloud
 * @author  Jonathan Maron (@JonathanMaron)
 */
trait DeleteTrait
{
    /**
     * Abstract Methods
     * -----------------------------------------------------------------------------------------------------------------
     */

    /**
     * Construct URI with version number
     *
     * @param string $uri URI
     *
     * @return string
     */
    abstract protected function uri($uri);

    /**
     * Request the URI with options
     *
     * @param string $method  HTTP method
     * @param string $uri     URI
     * @param array  $options Options
     *
     * @return mixed|null|\Psr\Http\Message\ResponseInterface
     *
     * @throws RuntimeException
     */
    abstract protected function request($method, $uri, $options);

    /**
     * DELETE Methods
     * -----------------------------------------------------------------------------------------------------------------
     */

    /**
     * Delete an API key
     *
     * @param string $key API key
     *
     * @return bool
     */
    public function deleteApiKey($key)
    {
        $ret = false;

        StaticValidator::execute($key, 'ApiKey');

        $options = [
            RequestOptions::QUERY => [
                'key' => $key,
            ],
        ];

        $response = $this->request('DELETE', $this->uri('/account/apikey'), $options);

        if ($response instanceof Response && 200 === $response->getStatusCode()) {
            $ret = true;
        }

        return $ret;
    }

    /**
     * Delete a template in template storage
     *
     * @param string $templateName Template name
     *
     * @throws InvalidArgumentException
     *
     * @return bool
     */
    public function deleteTemplate($templateName)
    {
        $ret = false;

        StaticValidator::execute($templateName, 'TemplateName');

        $options = [
            RequestOptions::QUERY => [
                'templateName' => $templateName,
            ],
        ];

        $response = $this->request('DELETE', $this->uri('/templates/delete'), $options);

        if ($response instanceof Response && 204 === $response->getStatusCode()) {
            $ret = true;
        }

        return $ret;
    }
}
