<?php
namespace ICS\SocialnetworkBundle\Twig;

use Twig\TwigFilter;
use Twig\Extension\AbstractExtension;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class SocialExtension extends AbstractExtension
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
            new TwigFilter('typeName', [$this, 'typeName'], [
                'is_safe' => ['html'],
            ]),
        ];
    }

    public function typeName(string $typeName)
    {
        return $typeName::getTypeName();
    }
}