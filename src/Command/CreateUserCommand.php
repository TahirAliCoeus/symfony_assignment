<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateUserCommand extends Command
{
    protected static $defaultName = "create-user";
    private $managerRegistry ;
    private $validator;

    public function __construct(ManagerRegistry $managerRegistry,ValidatorInterface $validator)
    {
        parent::__construct();
        $this->managerRegistry = $managerRegistry;
        $this->validator = $validator;

    }

    public function configure()
    {

//        $this->addArgument("name",InputArgument::REQUIRED,'Name of user');
//        $this->addArgument("email",InputArgument::REQUIRED,'Email of user');
//        $this->addArgument("users",InputArgument::REQUIRED,'How many users do you want to create?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $nameQuestion = new Question("Enter name: ");
        $emailQuestion = new Question("Enter email: ");
        $numberOfUsersQuestion = new Question("How many users do you want create?");

        $numberOfUsersToCreate = $helper->ask($input,$output,$numberOfUsersQuestion);
        $entityManager = $this->managerRegistry->getManager();
        for ($counter = 1 ; $counter <= $numberOfUsersToCreate ; $counter++)
        {
            $name = $helper->ask($input,$output,$nameQuestion);
            $email = $helper->ask($input,$output,$emailQuestion);
            $errors = $this->createUser($name,$email,$entityManager);

            if($errors)
            {
                $output->writeln($errors);
                return COMMAND::FAILURE;
            }
        }
            $entityManager->flush();

        $output->writeln(($numberOfUsersToCreate == 0 ? "No" : $numberOfUsersToCreate) . " user created");

        return COMMAND::SUCCESS;
    }

    private function createUser($name,$email,$entityManager)
    {
        $user = new User();
        $user->setName($name);
        $user->setEmail($email);
        $user->setUpdatedAt(new \DateTime("now"));

       $errors =  $this->validator->validate($user);

        if(count($errors))
        {
            return (string) $errors;
        }
        $entityManager->persist($user);
        return null;
    }
}