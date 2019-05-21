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

namespace Snowdog\DevTest\Filesystem\SitemapUploader\Interfaces;

interface SitemapUploaderInterface
{
    /**
     * Validate temporary file
     *
     * @param array
     * @return bool
     */
    public function validate(array $file) : bool;

    /**
     * Get content of temp file
     *
     * @param array
     * @return null|string
     */
    public function getContent(array $file) : ?string;
}
