<?php
namespace ICS\SocialnetworkBundle\Form\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use ICS\SocialnetworkBundle\Entity\SocialAccount;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SocialNetworkType extends AbstractType
{
    private $socialNetworkTypes = [];
    private $entities;

    public function __construct(EntityManagerInterface $em)
    {
        $metadatas = $em->getMetadataFactory()->getAllMetadata();

        foreach($metadatas as $metadata)
        {
            if(is_subclass_of($metadata->getName(),SocialAccount::class))
            {
                $this->socialNetworkTypes[]=$metadata->getName();
            }
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->entities = $builder->getData();
        
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['types'] = $this->socialNetworkTypes;
        $view->vars['entities'] = $this->entities;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Collection::class
        ]);
    }

    public function getBlockPrefix()
    {
        return 'socialnetwork';
    }

    public function getParent()
    {
        return CollectionType::class;
    }

}