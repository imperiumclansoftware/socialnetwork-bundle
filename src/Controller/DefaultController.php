<?php

    namespace ICS\SocialnetworkBundle\Controller;
    
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    
    
    class DefaultController extends AbstractController
    {
        /**
         * @Route("/", name="ics-socialnetwork-homepage")
         */
        public function index()
        {
            return $this->render("@Socialnetwork\default\default.html.twig",[

            ]);
        }
    }