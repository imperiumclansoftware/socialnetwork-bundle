<?php

namespace ICS\SocialnetworkBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use ICS\SocialnetworkBundle\Entity\Instagram\InstagramAccount;
use ICS\SocialnetworkBundle\Service\InstagramService;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class UpdateAccountCommand  extends Command
{
    protected static $defaultName = 'socialnetwork:account:update';

    private $io;
    private $em;
    private $instagramService;

    public function __construct(EntityManagerInterface $em, InstagramService $instagramService)
    {
        parent::__construct();
        $this->em=$em;
        $this->instagramService = $instagramService;
    }

    protected function configure()
    {
        $this
            ->addArgument('accountName', InputArgument::OPTIONAL, 'Account name to update')
            ->addOption('wait',"w",InputOption::VALUE_NEGATABLE,'Wait on error',null)
            ->setHelp('Update account');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
        

        $account = $input->getArgument('accountName');
        if($input->getOption('wait')==null)
        {
            $wait=false;
        }
        else
        {
            $wait=true;
        }

        $accountList=[];

        if($account == null)
        {
            $this->io->title('Update all accounts');
            $accountList = $this->em->getRepository(InstagramAccount::class)->findBy([],['updateDate' => 'ASC']);
        }
        else
        {
            $this->io->title('Update Account : '.$account);
            $accountList = $this->em->getRepository(InstagramAccount::class)->findBy(['name' => $account]);
        }
        
        $this->upadteAccount($accountList,$wait);

        return Command::SUCCESS;
    }

    private function upadteAccount($accountList,bool $wait=false)
    {
        $attemptInMinutes = 60;
        $stop=false;
        foreach($accountList as $account)
        {
            $this->io->info('Account : '.$account->getName());
            try
            {
                $this->io->text('Account : '.$account->getName().' updated ('.$this->instagramService->updateAccount($account).' updates)');
            }
            catch(Exception $ex)
            {
                $this->io->error('Error : '.$ex->getMessage());
                if($wait)
                {
                    $this->io->warning('Wait '.$attemptInMinutes.' minutes');
                    $this->io->progressStart($attemptInMinutes);
                    $i=0;
                    while($i < $attemptInMinutes)
                    {
                        $this->io->progressAdvance(1);
                        $i++;
                        sleep(60);
                    }
                    $this->io->progressFinish();
                    $this->io->info('Nouvelle tentative');
                    try
                    {
                        $this->io->text('Account : '.$account->getName().' updated ('.$this->instagramService->updateAccount($account).' updates)');
                    }
                    catch(Exception $ex)
                    {
                        continue;
                    }
                }
                else
                {
                    $stop=true;
                }
            }

            if($stop)
            {
                break;
            }

            sleep(3);
        }
    }
}
