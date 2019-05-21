<?php
/**
 * Gate-Software
 *
 * @copyright Copyright © 2019 Gate-Software Sp. z o.o. www.gate-software.com. All rights reserved.
 * @author    Gate-Software Dev Team
 * @author    mateusz.gieron@gate-software.com
 *
 * @package   Snowdog\DevTest
 */

namespace Snowdog\DevTest\Processor\SitemapProcessor\Interfaces;

interface SitemapProcessorInterface
{
    /**
     * Parse xml
     *
     * @param string
     * @return null|array
     * @throws \ParseError
     */
    public function parse(string $file) : ?array;
}
