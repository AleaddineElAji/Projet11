<?php

namespace App\Controller;


use App\Entity\Admin;
use App\Form\RegistrationType;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class SecurityController extends AbstractController
{
    /**
     * @Route("/register", name="security_register")
     */
    public function registration(Request $request, ManagerRegistry $doctrine, UserPasswordEncoderInterface $encoder ){
        $manager = $doctrine->getManager();
        $admin = new Admin();

        $form = $this->createForm(RegistrationType::class, $admin);
        

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $admin->setCreatedAt(new DateTimeImmutable());
        
            $hash = $encoder->encodePassword($admin, $admin->getPassword());
            $admin->setPassword($hash);
            $manager->persist($admin);
            $manager->flush();
            return $this->redirectToRoute('security_login');
        }
        
        return $this->render('security/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/login", name="security_login")
     */
    public function login(){
        
        return $this->render('security/login.html.twig');
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout(){

    }
}
