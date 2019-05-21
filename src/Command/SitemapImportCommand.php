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

namespace Snowdog\DevTest\Command;

use Snowdog\DevTest\Model\SitemapImportManager;
use Symfony\Component\Console\Output\OutputInterface;
use Snowdog\DevTest\Model\UserManager;

class SitemapImportCommand
{
    /**
     * @var SitemapImportManager
     */
    private $sitemapImportHelper;

    /**
     * @var UserManager
     */
    private $userManager;

    public function __construct(SitemapImportManager $sitemapImportHelper, UserManager $userManager)
    {
        $this->sitemapImportHelper = $sitemapImportHelper;
        $this->userManager = $userManager;
    }

    public function __invoke($sitemapPath, $userLogin, OutputInterface $output)
    {
        if ($sitemapPath && file_exists($sitemapPath) && $userLogin) {
            try {
                $user = $this->userManager->getByLogin($userLogin);
                if (!$user) {
                    throw new \Exception('User not exists!');
                }

                $file = [
                    'error'    => UPLOAD_ERR_OK,
                    'type'     => mime_content_type($sitemapPath),
                    'tmp_name' => $sitemapPath
                ];

                $imported = $this->sitemapImportHelper->importSitemap($user, $file);
                if ($imported) {
                    $output->writeln('<info>Sitemap imported!</info>');
                } else {
                    $output->writeln('<error>Errors during import!</error>');
                }
            } catch (\Exception $e) {
                $output->writeln('<error>' . $e->getMessage() . '</error>');
            }
        } else {
            $output->writeln('<error>Sitemap filepath or user login not set or file not exists!</error>');
            $output->writeln('<info>Try again! Never give up! Failure is not an option!</info>');
        }
    }
}
