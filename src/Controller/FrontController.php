<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;
use App\Form\FrontContactFormType;

class FrontController extends AbstractController
{
    /**
     * @Route("/", name="main_page")
     */
    public function index()
    {
        return $this->render('front/index.html.twig');
    }

    /**
     * @Route("/services", name="services")
     */
    public function services()
    {
        return $this->render('front/services.html.twig');
    }

    /**
     * @Route("/about", name="about")
     */
    public function about()
    {
        return $this->render('front/about.html.twig');
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $helper)
    {
        return $this->render('front/login.html.twig',  [
            'error' => $helper->getLastAuthenticationError()
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(): void
    {
        throw new \Exception('This should never be reached!');
    }

    /**
     * @Route("/contact", name="contact", methods={"GET","POST"})
     */
    public function contact(Request $request, MailerInterface $mailer)
    {

        $form = $this->createForm(FrontContactFormType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            $formData = $form->getData();

            $email = (new Email())
            ->from($formData['email'])
            ->to('susie.sle@djbagsofun.co.uk')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject($formData['subject'])
            ->text('It should show as html')
            ->html(
                '<p>'.$formData['name'].'</p>'.
                '<p>'.$formData['number'].'</p>'.
                '<p>'.$formData['content'].'</p>'
            );

            /** @var Symfony\Component\Mailer\SentMessage $sentEmail */
            $sentEmail = $mailer->send($email);
            // $messageId = $sentEmail->getMessageId();

            // ...

            // $entityManager = $this->getDoctrine()->getManager();
            // $entityManager->persist($user);
            // $entityManager->flush();

            // return $this->redirectToRoute('new_family', ['id' => $id]);
        }
            return $this->render('front/contact.html.twig', [
                'form' => $form->createView(),
            ]);
    }
}
