<?php
declare(strict_types=1);

/**
 * ReportingCloud PHP Wrapper
 *
 * PHP wrapper for ReportingCloud Web API. Authored and supported by Text Control GmbH.
 *
 * @link      https://www.reporting.cloud to learn more about ReportingCloud
 * @link      https://github.com/TextControl/txtextcontrol-reportingcloud-php for the canonical source repository
 * @license   https://raw.githubusercontent.com/TextControl/txtextcontrol-reportingcloud-php/master/LICENSE.md
 * @copyright © 2019 Text Control GmbH
 */

namespace TxTextControl\ReportingCloud\PropertyMap;

/**
 * Abstract property map
 *
 * @package TxTextControl\ReportingCloud
 * @author  Jonathan Maron (@JonathanMaron)
 */
abstract class AbstractPropertyMap implements PropertyMapInterface
{
    /**
     * Assoc array of properties
     * camelCase properties => Lower case underscore array keys
     *
     * @var array
     */
    protected $map = [];

    /**
     * Return the property map
     *
     * @return array
     */
    public function getMap(): array
    {
        return $this->map;
    }

    /**
     * Set the property map
     *
     * @param array $map Assoc array of property data
     *
     * @return AbstractPropertyMap
     */
    public function setMap(array $map): AbstractPropertyMap
    {
        $this->map = $map;

        return $this;
    }
}
