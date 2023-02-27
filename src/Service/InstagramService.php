<?php
namespace ICS\SocialnetworkBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use ICS\MediaBundle\Entity\MediaFile;
use ICS\MediaBundle\Repository\MediaFileRepository;
use ICS\MediaBundle\Service\MediaService;
use ICS\SocialnetworkBundle\Entity\Instagram\InstagramAccount;
use ICS\SocialnetworkBundle\Entity\Instagram\InstagramMedia;
use ICS\SocialnetworkBundle\Entity\Instagram\Medias\InstagramPicture;
use ICS\SocialnetworkBundle\Entity\Instagram\Medias\InstagramSidecar;
use ICS\SocialnetworkBundle\Entity\Instagram\Medias\InstagramVideo;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpKernel\KernelInterface;

class InstagramService 
{
    private $browser;
    private $basePath;
    private $mediaService;
    private $mediasParameters;
    private $em;
    
    public function __construct(KernelInterface $kernel,EntityManagerInterface $em, MediaService $mediaService, ParameterBagInterface $parameterBagInterface)
    {
        $this->browser = new HttpBrowser(HttpClient::create());
        $this->basePath = $kernel->getProjectDir().'/public';
        $this->mediaService = $mediaService;
        $this->mediasParameters = $parameterBagInterface->get('medias');
        $this->em=$em;
    }

    public function getAccount(string $name, bool $preview = false)
    {
        $this->browser->request('GET',"https://www.instagram.com/".$name."?__a=1&__d=dis",[
            'Accept' => 'application/json'
        ]);

        $result=$this->browser->getResponse();
        $jsonInfos = json_decode($result->getContent());
        
        if(isset($jsonInfos->status) && $jsonInfos->status == 'fail')
        {
            throw new Exception("Instagram response failed. Instagram said : ".$jsonInfos->message);
        }

        $user = InstagramAccount::fromWeb($jsonInfos->graphql->user);
        if(!$preview)
        {
            $user->downloadSource($this);
        }
        
        return $user;
    }

    public function getMedia(InstagramAccount $account, string $url,$filename=null,$directory=null)
    {
        $media = null;
        
        $filePath = "social/instagram/".$account->getName();
        if($directory != null)
        {
            $filePath = $filePath.'/'.$directory;
        }
        $finalPath = $this->basePath.'/'.$this->mediasParameters['path'].'/'.$filePath;
        
        if(!file_exists($finalPath.'/'.$filename) or $filename =='profile.jpg')
        {
            $browser=new HttpBrowser();
            $browser->request('GET',$url);
            $content = $browser->getResponse()->getContent();
            
            
            if(!file_exists($finalPath))
            {
                mkdir($finalPath,0777,true);
            }

            if($filename == null)
            {
                $filename=$account->getName().'_'.date('YmdHis').'.jpg';
            }

            file_put_contents($finalPath.'/'.$filename,$content);

            if($finalPath.'/'.$filename != '')
            {
                $type=$this->mediaService->getMediaType($finalPath.'/'.$filename);
                $media = new $type($finalPath.'/'.$filename,$filePath);
            }
        }
        else
        {
            $type=$this->mediaService->getMediaType($finalPath.'/'.$filename);
            $media=$this->em->getRepository($type)->findOneBy(['path' => $finalPath.'/'.$filename]);
        }
        
        
        return $media;
    }

    public function updateAccount(InstagramAccount $account)
    {
        $nbUpdate = 0;

        $updatedAccount = $this->getAccount($account->getName());

        //? Update profile picture
        if($updatedAccount->getProfilePicture() != '')
        {
            // ? Remove Old profile picture
            $oldpicture=$account->getProfilePicture();
            if($oldpicture != null)
            {
                $account->setProfilePicture(null);
                $this->em->persist($account);
                $this->em->remove($oldpicture);
                $this->em->flush();
            }
            
            // ?D ownload new pofile picture
            $newProfile = $updatedAccount->getProfilePicture();
            $account->setProfilePicture($newProfile);
            $nbUpdate++;
        }
        
        //? Update fullname
        $account->setFullname($updatedAccount->getFullname());
        $nbUpdate++;
        //? Update biography
        $account->setBiography($updatedAccount->getBiography());
        $nbUpdate++;
        //? Update followers count
        $account->setFollowers($updatedAccount->getFollowers());
        $nbUpdate++;
        //? Updated Verified status
        $account->setVerified($updatedAccount->getVerified());
        $nbUpdate++;
        //? Update timeline
        foreach($updatedAccount->getTimeline() as $media)
        {
            if(!$this->accountHaveMedia($account,$media))
            {
                $media->setAccount($account);
                $media->downloadSource($this,$account);
                $this->em->persist($media);
                $nbUpdate++;
            }
        }

        $this->em->persist($account);
        $this->em->flush();

        return $nbUpdate;
    }

    private function accountHaveMedia(InstagramAccount $account,InstagramMedia $media): bool
    {

        foreach($account->getTimeline() as $med)
        {
            if($med->getId() == $media->getId())
            {
                if(is_a($med,InstagramVideo::class) or is_a($med,InstagramSidecar::class))
                {
                    if(
                        (is_a($med,InstagramVideo::class) and $med->getVideo() ==null) or
                        (is_a($med,InstagramSidecar::class) and count($med->getElements()) == 0) or
                        (is_a($med,InstagramPicture::class) and $med->getPicture() ==null)
                    )
                    {
                        $this->em->remove($med);
                        return false;
                    }
                }
                return true;
            }
        }

        return false;
    }

}