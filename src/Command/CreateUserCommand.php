<?php

namespace App\Command;
use App\Service\UserService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class CreateUserCommand extends Command
{
    protected static $defaultName = "create-user";
    private UserService $userService ;

    public function __construct(UserService $userService)
    {
        parent::__construct();
        $this->userService = $userService;

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper("question");

        $nameQuestion = new Question("Enter name: ");
        $emailQuestion = new Question("Enter email: ");
        $numberOfUsersQuestion = new Question("How many users do you want create?");

        $numberOfUsersToCreate = $helper->ask($input,$output,$numberOfUsersQuestion);
        for ($counter = 1 ; $counter <= $numberOfUsersToCreate ; $counter++)
        {
            $name = $helper->ask($input,$output,$nameQuestion);
            $email = $helper->ask($input,$output,$emailQuestion);
            $errors = $this->userService->addUser($name,$email,new \DateTime("now"));

            if(!is_null($errors))
            {
                $output->writeln($errors);
                return COMMAND::FAILURE;
            }
        }
        $output->writeln(($numberOfUsersToCreate == 0 ? "No" : $numberOfUsersToCreate) . " user created");
        return COMMAND::SUCCESS;
    }
}