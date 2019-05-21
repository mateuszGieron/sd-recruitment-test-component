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

namespace Snowdog\DevTest\Model;

use Snowdog\DevTest\Core\Database;
use Snowdog\DevTest\Model\WebsiteManager;
use Snowdog\DevTest\Filesystem\SitemapUploader\SitemapUploader;
use Snowdog\DevTest\Processor\SitemapProcessor\SitemapProcessor;
use Snowdog\DevTest\Model\PageManager;
use Snowdog\DevTest\Model\User;

class SitemapImportManager
{
    /**
     * @var Database|\PDO
     */
    private $database;

    /**
     * @var WebsiteManager
     */
    private $websiteManager;

    /**
     * @var SitemapUploader
     */
    private $sitemapUploader;

    /**
     * @var SitemapProcessor
     */
    private $sitemapProcessor;

    /**
     * @var PageManager
     */
    private $pageManager;

    public function __construct(
        Database $database,
        WebsiteManager $websiteManager,
        SitemapUploader $sitemapUploader,
        SitemapProcessor $sitemapProcessor,
        PageManager $pageManager
    ) {
        $this->database         = $database;
        $this->websiteManager   = $websiteManager;
        $this->sitemapUploader  = $sitemapUploader;
        $this->sitemapProcessor = $sitemapProcessor;
        $this->pageManager      = $pageManager;
    }

    /**
     * Validate, parse and import sitemap
     *
     * @param User  $user
     * @param array $file
     *
     * @return bool
     */
    public function importSitemap(User $user, array $file) : bool
    {
        if ($user) {
            $sitemap = [];
            if ($this->sitemapUploader->validate($file)) {
                $sitemapContent = $this->sitemapUploader->getContent($file);
                $sitemap = $this->sitemapProcessor->parse($sitemapContent);
            }

            $errors = 0;
            if (!empty($sitemap)) {
                foreach ($sitemap as $website => $pages) {
                    $errors = $this->saveWebsite($user, $website, $pages);
                }
            }

            return ! (bool)$errors;
        }

        return false;
    }

    /**
     * Save website and pages
     *
     * @param User $user
     * @param string $website
     * @param array  $page
     *
     * @return bool
     */
    private function saveWebsite(User $user, string $website, array $pages) : int
    {
        $errors = 0;
        if (!empty($website) && !empty($pages)) {
            $websiteId = $this->existsWebsite($website);
            if (empty($websiteId)) {
                $websiteId = $this->websiteManager->create($user, $website, $website);
            }

            if (!empty($websiteId)) {
                foreach ($pages as $page) {
                    $this->pageManager->create($this->websiteManager->getById($websiteId), $page);
                }
            } else {
                $errors++;
            }
        } else {
            $errors++;
        }

        return $errors;
    }

    /**
     * Check if website exists by hostname
     *
     * @param string $website
     *
     * @return bool
     */
    public function existsWebsite(string $hostname) : int
    {
        /** @var \PDOStatement $query */
        $query = $this->database->prepare('SELECT website_id FROM websites WHERE hostname = :hostname');
        $query->bindParam(':hostname', $hostname, \PDO::PARAM_STR);
        $query->execute();
        $website = (int) $query->fetch(\PDO::FETCH_COLUMN);
        return $website;
    }
}
