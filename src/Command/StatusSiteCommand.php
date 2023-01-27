<?php

namespace App\Command;

use App\Repository\StatusRepository;
use App\Entity\Status;
use App\Repository\SitesRepository;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use Psr\Log\LoggerInterface;

#[AsCommand('app:status:add', 'Get status of all sites')]
class StatusSiteCommand extends Command
{
    public function __construct(
        private StatusRepository $statusRepository,
        private SitesRepository $sitesRepository,
        private HttpClientInterface $clientHttp,
        private Status $statusEntity,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger
    ) {
        parent::__construct();
    }

    protected function configure()
    {

    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

            $sites = $this->sitesRepository->findAll();
            foreach ($sites as $site) {
                $siteAddress = $site->getSiteAdress();
                $status = new Status();
                $status->setSite($site);
                $status->setTimeStamp(new \DateTime());
                try {
                    $response = $this->clientHttp->request(
                        'GET',
                        $siteAddress
                    );
                    $statusCode = $response->getStatusCode();
                    $httpInfo = $response->getInfo();

                    $status->setStatus($statusCode);
                    $status->setLatency($httpInfo["total_time"]);

                }catch (\Exception $e) {
                    $status->setStatus(0);
                    $status->setLatency(0);
                }
                $this->entityManager->persist($status);
                $this->entityManager->flush();
                $this->logger->notice(sprintf('Check Status site "%s" ', $siteAddress));

            }




        return Command::SUCCESS;
    }

}
