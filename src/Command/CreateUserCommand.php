<?php

namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateUserCommand extends Command
{
    protected static $defaultName = 'app:set-admin';
    protected static $defaultDescription = 'Grant admin role to given user';
    private UserRepository $userRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('email', InputArgument::REQUIRED, 'user email')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');

        if ($email) {
            $ask = $io->confirm("Are you sure you want to give ROLE_ADMIN to $email?", false);
            if ($ask) {
                $user = $this->userRepository->findOneByEmail($email);
                if ($user) {
                    $user->setRoles(array_merge($user->getRoles(), ['ROLE_ADMIN']));
                    $this->entityManager->persist($user);
                    $this->entityManager->flush();
                    $io->success('Admin granted.');
                    return Command::SUCCESS;
                } else {
                    $io->error('User not found.');
                }
            }
        }
        return Command::FAILURE;
    }
}
