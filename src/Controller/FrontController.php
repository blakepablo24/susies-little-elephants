<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;
use App\Form\FrontContactFormType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

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

            $email = (new TemplatedEmail())
            ->from($formData['email'])
            ->to('susie@61susielittleelephants61.djbagsofun.co.uk')
            ->subject($formData['subject'])
            ->htmlTemplate('emails/contact_form.html.twig')
            ->context([
                'senders_email' => $formData['email'],
                'subject' => $formData['subject'],
                'name' => $formData['name'],
                'number' => $formData['number'],
                'content' => $formData['content']
            ]);

            /** @var Symfony\Component\Mailer\SentMessage $sentEmail */
            $sentEmail = $mailer->send($email);
            
            $this->addFlash('success', 'Message Successfully Sent, Susie will get back to you as soon as possible');

            return $this->redirectToRoute('contact');
        }
            return $this->render('front/contact.html.twig', [
                'form' => $form->createView(),
            ]);
    }
}
