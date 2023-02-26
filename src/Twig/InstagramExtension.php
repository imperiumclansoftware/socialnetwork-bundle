<?php
namespace ICS\SocialnetworkBundle\Twig;

use ICS\SocialnetworkBundle\Entity\Instagram\InstagramMedia;
use ICS\SocialnetworkBundle\Entity\Instagram\Medias\InstagramPicture;
use ICS\SocialnetworkBundle\Entity\Instagram\Medias\InstagramSidecar;
use ICS\SocialnetworkBundle\Entity\Instagram\Medias\InstagramVideo;
use Twig\TwigFunction;
use Twig\TwigFilter;
use Twig\Extension\AbstractExtension;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class InstagramExtension extends AbstractExtension
{
    private $translator;
    private $environement;

    public function __construct(TranslatorInterface $translator, Environment $twig)
    {
        $this->translator = $translator;    
        $this->environement = $twig;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('HumanRead', [$this, 'humanRead'], [
                'is_safe' => ['html'],
            ]),
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('getInstagramMedia', [$this, 'getInstagramMedia'], [
                'is_safe' => ['html'],
            ]),
        ];
    }

    public function getInstagramMedia(InstagramMedia $value)
    {
        if($value instanceof InstagramPicture)
        {
            return $this->environement->render('@Socialnetwork/instagram/part/picture.html.twig',[
                'media' => $value
            ]);
        }
        else if($value instanceof InstagramSidecar)
        {
            return $this->environement->render('@Socialnetwork/instagram/part/sidecar.html.twig',[
                'media' => $value
            ]);
        }
        else if($value instanceof InstagramVideo)
        {
            return $this->environement->render('@Socialnetwork/instagram/part/video.html.twig',[
                'media' => $value
            ]);
        }

        return "Unknow Instagram Media !";

    }

    public function humanRead(int $number)
    {
        $size = ['k','M','P'];
        
        $i=0;
        
        while(($number=$number/1000) >= 1000)
        {
            $i++;
        }

        return round($number,2).' '.$size[$i];
    }

}