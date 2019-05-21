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

namespace Snowdog\DevTest\Processor\SitemapProcessor;

use Snowdog\DevTest\Processor\SitemapProcessor\Interfaces\SitemapProcessorInterface;

class SitemapProcessor implements SitemapProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function parse(string $xml): ?array
    {
        $parsed = [];
        $xmlObject = $this->getXMLObject($xml);

        foreach ($xmlObject->url as $url) {
            if (!empty($url->loc)) {
                $parsedUrl = parse_url($url->loc);
                if (!empty($parsedUrl['host'] && isset($parsedUrl['path']) && !empty($parsedUrl['path']))) {
                    $parsed[($parsedUrl['scheme'] ? $parsedUrl['scheme'] . '://' : '') . $parsedUrl['host']][] =
                        ltrim($parsedUrl['path'], '/');
                }
            }
        }

        return $parsed;
    }

    /**
     * Get xml object
     *
     * @param string $xml
     *
     * @return \SimpleXMLElement
     */
    private function getXMLObject(string $xml) :\SimpleXMLElement
    {
        return new \SimpleXMLElement($xml, LIBXML_NOCDATA);
    }
}
