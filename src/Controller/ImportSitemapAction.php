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

namespace Snowdog\DevTest\Controller;

use Snowdog\DevTest\Model\SitemapImportManager;
use Snowdog\DevTest\Model\UserManager;
use Snowdog\DevTest\Model\User;

class ImportSitemapAction
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var SitemapImportManager
     */
    private $sitemapImportManager;

    public function __construct(UserManager $userManager, SitemapImportManager $sitemapImportManager)
    {
        $this->userManager          = $userManager;
        $this->sitemapImportManager = $sitemapImportManager;

        if (isset($_SESSION['login'])) {
            $this->user = $userManager->getByLogin($_SESSION['login']);
        }
    }

    /**
     * @return void
     */
    public function execute() : void
    {
        if (!isset($_SESSION['login'])) {
            $this->back();
        }

        if ($this->user) {
            try {
                $imported = $this->sitemapImportManager->importSitemap($this->user, $_FILES['sitemap']);
                if ($imported) {
                    $_SESSION['flash'] = 'Sitemap imported!';
                } else {
                    $_SESSION['flash'] = 'Errors during import!';
                }
            } catch (\Exception $e) {
                $_SESSION['flash'] = $e->getMessage();
            }
        }

        $this->back();
    }

    /**
     * @return void
     */
    private function back() : void
    {
        header('Location: /');
    }
}
