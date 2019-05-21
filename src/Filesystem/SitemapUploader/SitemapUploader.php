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

namespace Snowdog\DevTest\Filesystem\SitemapUploader;

use Snowdog\DevTest\Filesystem\SitemapUploader\Interfaces\SitemapUploaderInterface;

class SitemapUploader implements SitemapUploaderInterface
{
    /**
     * @var array
     */
    protected $allowedMime = ['text/xml','application/xml'];

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    public function getContent(array $file) : ?string
    {
        if (empty($file['tmp_name'])) {
            throw new \RuntimeException('Temporary file not exists!');
        }

        $content = file_get_contents($file['tmp_name']);

        return $content;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    public function validate(array $file) : bool
    {
        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new \RuntimeException('No file sent.');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new \RuntimeException('Exceeded filesize limit.');
            default:
                throw new \RuntimeException('Unknown errors.');
        }

        if (!in_array($file['type'], $this->allowedMime)) {
            throw new \RuntimeException('Invalid file type!');
        }

        return true;
    }
}
