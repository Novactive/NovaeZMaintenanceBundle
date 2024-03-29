<?php

/**
 * NovaeZMaintenanceBundle.
 *
 * @package   Novactive\NovaeZMaintenanceBundle
 *
 * @author    Novactive
 * @copyright 2020 Novactive
 * @license   https://github.com/Novactive/NovaeZMaintenanceBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\NovaeZMaintenanceBundle\Command;

use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessAware;
use Novactive\NovaeZMaintenanceBundle\Helper\FileHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class MaintenanceCommand extends Command implements SiteAccessAware
{
    /**
     * @var FileHelper
     */
    protected $fileHelper;

    /**
     * @var SiteAccess|null
     */
    private $siteAccess;

    public function __construct(FileHelper $fileHelper)
    {
        parent::__construct();
        $this->fileHelper = $fileHelper;
    }

    /**
     * @required
     */
    public function setSiteAccess(SiteAccess $siteAccess = null): void
    {
        $this->siteAccess = $siteAccess;
    }

    protected function configure(): void
    {
        parent::configure();
        $this->setName('novamaintenance:set');
        $this->addOption('lock', null, InputOption::VALUE_NONE, 'Enable Maintenance');
        $this->addOption('unlock', null, InputOption::VALUE_NONE, 'Disable Maintenance');
    }

    private function unlock(string $siteaccess): string
    {
        return $this->fileHelper->maintenanceUnLock($siteaccess) ?
            'Maintenance unlocked' : 'Maintenance already unlocked';
    }

    private function lock(string $siteaccess): string
    {
        return $this->fileHelper->maintenanceLock($siteaccess) ? 'Maintenance locked' : 'Maintenance already locked';
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (null === $this->siteAccess) {
            return Command::FAILURE;
        }
        $siteaccessName = $this->siteAccess->name;
        if (true === $input->getOption('lock')) {
            $output->writeln('<info>'.$this->lock($siteaccessName).'</info>');
        } elseif (true === $input->getOption('unlock')) {
            $output->writeln('<info>'.$this->unlock($siteaccessName).'</info>');
        }

        return Command::SUCCESS;
    }
}
