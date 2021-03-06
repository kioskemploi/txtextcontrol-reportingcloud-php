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

/**
 * Class ReportingCloud
 *
 * @package TxTextControl\ReportingCloud
 * @author  Jonathan Maron (@JonathanMaron)
 */
class ReportingCloud extends AbstractReportingCloud
{
    use BuildTrait;
    use DeleteTrait;
    use GetTrait;
    use PostTrait;
    use PutTrait;
    use UtilityTrait;

    /**
     * Constructor Method
     * -----------------------------------------------------------------------------------------------------------------
     */

    /**
     * ReportingCloud constructor
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $methods = [
            'api_key'  => 'setApiKey',
            'base_uri' => 'setBaseUri',
            'debug'    => 'setDebug',
            'password' => 'setPassword',
            'test'     => 'setTest',
            'timeout'  => 'setTimeout',
            'username' => 'setUsername',
            'version'  => 'setVersion',
        ];

        foreach ($methods as $key => $method) {
            if (array_key_exists($key, $options)) {
                $this->$method($options[$key]);
            }
        }
    }
}
