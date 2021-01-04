<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateUserCommand extends Command
{
    private $passwordEncoder;
    private $entityManager;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('user:create')
            ->setDescription('Create a user.')
            ->setDefinition(array(
                new InputArgument('username', InputArgument::REQUIRED, 'Username'),
                new InputArgument('password', InputArgument::REQUIRED, 'Mot de passe'),
                new InputOption('super-admin', null, InputOption::VALUE_NONE, 'Definir en tant que Super Admin (ROLE_SUPER_ADMIN)'),
            ))
            ->setHelp(<<<'EOT'
The <info>user:create</info> command creates a user:
  <info>php %command.full_name% romaric@netinfluence.ch</info>
This interactive shell will ask you for a password.
You can create a super admin via the super-admin flag:
  <info>php %command.full_name% admin --super-admin</info>
EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        $superadmin = $input->getOption('super-admin');
        $user = (new User())
            ->setUsername($username)
            ->setRoles($superadmin ? ['ROLE_SUPER_ADMIN'] : ['ROLE_USER']);
        $user->setEnabled(true);
        $password = $this->passwordEncoder->encodePassword($user, $password);
        $user->setPassword($password);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $output->writeln(sprintf('Created user <comment>%s</comment>', $username));
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $questions = array();
        if (!$input->getArgument('username')) {
            $question = new Question('Veuillez choisir un Username:');
            $question->setValidator(function ($password) {
                if (empty($password)) {
                    throw new \Exception('Username ne peut etre null');
                }
                return $password;
            });
            $question->setHidden(true);
            $questions['password'] = $question;
        }
        if (!$input->getArgument('password')) {
            $question = new Question('Veuillez choisir un Mot de passe:');
            $question->setValidator(function ($password) {
                if (empty($password)) {
                    throw new \Exception('Le mot de passe ne peut etre null');
                }
                return $password;
            });
            $question->setHidden(true);
            $questions['password'] = $question;
        }
        foreach ($questions as $name => $question) {
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument($name, $answer);
        }
    }
}
