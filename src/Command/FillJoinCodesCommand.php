<?php

namespace App\Command;

use App\Repository\ClubRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FillJoinCodesCommand extends Command
{
    protected static $defaultName = 'app:fill-join-codes';
    protected static $defaultDescription = 'Fill empty join codes';

    private $entityManager;
    private $clubRepository;

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
        ;
    }

    public function __construct(EntityManagerInterface $entityManager, ClubRepository $clubRepository)
    {
        $this->entityManager = $entityManager;
        $this->clubRepository = $clubRepository;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $clubs = $this->clubRepository->createQueryBuilder('c')->andWhere('c.joinCode IS NULL')->getQuery()->getResult();
        $io->info('Found '.count($clubs).' empty join codes.');

        foreach ($clubs as $club) {
            $club->regenerateJoinCode();
            $this->entityManager->persist($club);
        }
        $this->entityManager->flush();

        
        $io->success('Join codes successfully filled.');

        return Command::SUCCESS;
    }
}
