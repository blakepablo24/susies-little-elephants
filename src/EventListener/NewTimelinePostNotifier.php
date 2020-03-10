<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use App\Entity\Post;
use App\Entity\Child;
use App\Entity\Family;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;


class NewTimelinePostNotifier
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function postPersist(Post $post, LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        // only act if a new post has been submitted
        if (!$entity instanceof Post) {
            return;
        }

        $entityManager = $args->getObjectManager();

        $child = $post->getChild();
        $family = $child->getFamily();
        $user = $family->getUser();

        if($user)
        {

            $email = (new TemplatedEmail())
            ->from('no-reply@61susielittleelephants61.djbagsofun.co.uk')
            ->to($user->getEmail())
            ->subject('New Post on '.$child->getName().'\'s Timeline')
            ->htmlTemplate('emails/new_timeline_post.html.twig')
            ->context([
                'senders_email' => $user->getEmail(),
                'mums_name' => $family->getMum(),
                'dads_name' => $family->getDad(),
                'child_name' => $child->getName()
            ]);

            // $this->mailer->send($email);

        } 

    }
}