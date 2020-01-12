<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use App\Service\Twig;

class StatisticsCommand extends Command
{
    protected static $defaultName = 'app:statistics';

    private $em, $twig, $mailer;


    public function __construct(EntityManagerInterface $entityManager, Twig $twig, \Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->em = $entityManager;
        parent::__construct();
    }
    
    protected function configure()
    {
        $this
            ->addArgument('email', InputArgument::OPTIONAL, 'Email address to send statistics')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        if($email){
            $popular = $this->em->getRepository('App\Entity\News')->getMostPopular();
            $output->writeln([
                'Top articles',
                '============',
                '',
            ]);
            foreach($popular as $article){
                $output->writeln($article->getTitle(). " ".$article->getVisitors());
            }    
    
            $template = $this->twig->load('templates/emails/statistics.html.twig');

            $message = (new \Swift_Message('statistics'))
                ->setFrom('kkaimar@hotmail.com')
                ->setTo($email)
                ->setBody($template->render(['statistics' => $popular]),
                'text/html'
            );

            $this->mailer->send($message);

            $output->writeln([
                '',
                'This statistics is send to '.$email
            ]);
        }else{
            $output->write('You have to add email to command example: php bin/console statistics your@email.com');
        }
        return 0;
    }
}
