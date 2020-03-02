<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\NewFamilyType;
use App\Form\NewUserType;
use App\Form\EditFamilyType;
use App\Form\AddChildType;
use App\Form\AddPostType;
use App\Form\EditPostType;
use App\Form\ResetPasswordType;
use App\Form\NewPolicyType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Entity\Family;
use App\Entity\Child;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\Policy;
use Symfony\Component\Filesystem\Filesystem;
use Intervention\Image\ImageManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


/**
 * @Route("/admin")
*/

class AdminController extends AbstractController
{

    public function __construct(UserPasswordEncoderInterface $password_encoder)
    {
        $this->password_encoder = $password_encoder;
    }

    /**
     * @Route("/", name="admin_main_page")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig');
    }

    /**
     * @Route("/su/new-user", name="new_user", methods={"GET","POST"})
     */
    public function newUser(Request $request)
    {

        $user = new User();
        $form = $this->createForm(NewUserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $password = $request->request->get('new_user')['password'];
            $user->setPassword($this->password_encoder->encodePassword($user, $password));
            $user->setName($request->request->get('new_user')['name']);
            $user->setLastName($request->request->get('new_user')['Last_Name']);
            $user->setEmail($request->request->get('new_user')['email']);
            $user->setRoles(['ROLE_USER']);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $lastQuestion = $entityManager->getRepository(User::class)->findOneBy([], ['id' => 'desc']);
            $id = $lastQuestion->getId();

            return $this->redirectToRoute('new_family', ['id' => $id]);
        }

        return $this->render('admin/new-user.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/su/new-family/{id}", name="new_family", methods={"GET","POST"})
     */
    public function newFamily(User $user, Request $request)
    {

        $family = new Family();
        $family->setUser($user);
        $family->setName($user->getLastName());

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($family);
        $entityManager->flush();
        
        return $this->redirectToRoute('admin_main_page');
    }

    /**
     * @Route("/family/{familyname}, {id}", name="admin_family", methods={"GET","POST"})
     */
    public function family($id, Family $specific_family, Request $request)
    {

        $specific_family = $this->getDoctrine()->getRepository(Family::class)->find($id);
        $specific_child = $this->getDoctrine()->getRepository(Child::class)->findChildByFamilyId($id);
        
        return $this->render('admin/family.html.twig', [
        'specific_family'=>$specific_family,
        'specific_child'=>$specific_child
        ]);
    }

    /**
     * @Route("/time-line/{childname}, {id}", name="admin_time_line", methods={"GET","POST"})
     */
    public function timeLine($id, Child $child, Request $request)
    {   

        $childs_posts = $this->getDoctrine()->getRepository(Post::class)->findPostsByChildId($id);

        $newPost = new Post();
        $form = $this->createForm(AddPostType::class, $newPost);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            
            $imageFile = $form->get('image')->getData();
            

            if($imageFile)
            {

                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('post_image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
    
                $newPost->setImageFilename($newFilename);
            }

            $newPost->setChild($child);
            $newPost->setSubject($request->request->get('add_post')['subject']);
            $newPost->setContent($request->request->get('add_post')['content']);
            $newPost->setDate(new \DateTime());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($newPost);
            $entityManager->flush();

            return $this->redirectToRoute('admin_main_page');
        }

        return $this->render('admin/time-line.html.twig', [
        'child' => $child,
        'childs_posts' => $childs_posts,
        'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/su/delete-post/{id}", name="delete_post")
     */
    public function deletePost(Post $post)
    {
        $image = $post->getImageFileName();
        $path=$this->getParameter('post_image_directory').'/'.$image;

        $fs = new FileSystem();
        $fs->remove(array($path));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($post);
        $entityManager->flush();

        return $this->redirectToRoute('admin_main_page');
    }

    /**
     * @Route("/su/edit-post/{id}", name="edit_post", methods={"GET","POST"})
     */
    public function editPost(Post $editPost, Request $request)
    {

        $form = $this->createForm(EditPostType::class, $editPost);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            $editPost->setSubject($request->request->get('edit_post')['subject']);
            $editPost->setContent($request->request->get('edit_post')['content']);

            $imageFile = $form->get('image')->getData();

            if($imageFile)
            {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('post_image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
    
                $editPost->setImageFilename($newFilename);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($editPost);
            $entityManager->flush();

            return $this->redirectToRoute('admin_main_page');
        }

        return $this->render('admin/edit-post.html.twig', [
            'form' => $form->createView(),
            'editPost' => $editPost
        ]);
    }

    /**
     * @Route("/su/delete-family/{id}", name="delete_family")
     */
    public function deleteFamily(Family $family)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($family);
        $entityManager->flush();

        return $this->redirectToRoute('admin_main_page');
    }

    /**
     * @Route("/su/edit-family/{id}", name="edit_family", methods={"GET","POST"})
     */
    public function editFamily(Family $editFamily, Request $request)
    {

        $form = $this->createForm(EditFamilyType::class, $editFamily);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $editFamily->setName($request->request->get('edit_family')['name']);
            $editFamily->setMum($request->request->get('edit_family')['mum']);
            $editFamily->setDad($request->request->get('edit_family')['dad']);
            $editFamily->setGuardian($request->request->get('edit_family')['guardian']);
            $editFamily->getChildren();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($editFamily);
            $entityManager->flush();

            return $this->redirectToRoute('admin_main_page');
        }

        return $this->render('admin/edit-family.html.twig', [
            'form' => $form->createView(),
            'editFamily' => $editFamily
        ]);
    }

    /**
     * @Route("/su/reset-password/{id}", name="reset_password", methods={"GET","POST"})
     */
    public function resetPassword(User $editUser, Request $request)
    {

        $form = $this->createForm(ResetPasswordType::class, $editUser);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            
            $password = $request->request->get('reset_password')['password'];
            $editUser->setPassword($this->password_encoder->encodePassword($editUser, $password));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($editUser);
            $entityManager->flush();

            return $this->redirectToRoute('admin_main_page');
        }

        return $this->render('admin/reset-password.html.twig', [
            'form' => $form->createView(),
            'editUser' => $editUser
        ]);
    }
    
    /**
     * @Route("/su/add-child/{id}", name="add_child", methods={"GET","POST"})
     */
    public function addChild(Family $family, Request $request)
    {

        $child = new Child();
        $form = $this->createForm(AddChildType::class, $child);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $child->setFamily($family);
            $child->setStartDate('n/a');
            $child->setName($request->request->get('add_child')['name']);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($child);
            $entityManager->flush();

            return $this->redirectToRoute('admin_main_page');
        }


        return $this->render('admin/add-child.html.twig',[
            'form' => $form->createView(),
            'family' => $family
        ]);
    }

    /**
     * @Route("/policies", name="policies")
     */
    public function policiesForFamily()
    {
        return $this->render('admin/policies.html.twig');
    }

    /**
     * @Route("/su/new-policy", name="new_policy", methods={"GET","POST"})
     */
    public function newPolicy(Request $request)
    {

        $policy = new Policy();
        $form = $this->createForm(NewPolicyType::class, $policy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $brochureFile */
            $policyFile = $form->get('policy')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($policyFile) {
                $originalFilename = pathinfo($policyFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$policyFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $policyFile->move(
                        $this->getParameter('policy_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $policy->setPolicyFilename($newFilename);
            }

            $policy->setPolicyName($request->request->get('new_policy')['policy_name']);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($policy);
            $entityManager->flush();


            return $this->redirectToRoute('admin_main_page');
        }

        return $this->render('admin/new-policy.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/su/delete-policy/{id}", name="delete_policy")
     */
    public function deletePolicy(Policy $policy)
    {
        $policyFile = $policy->getPolicyFileName();
        $path=$this->getParameter('policy_directory').'/'.$policyFile;

        $fs = new FileSystem();
        $fs->remove(array($path));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($policy);
        $entityManager->flush();

        return $this->redirectToRoute('admin_main_page');
    }

    // Functions

    public function families()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $families = $this->getDoctrine()->getRepository(Family::class)->findAll();
        return $this->render('admin/includes/_families.html.twig', [
            'families'=>$families
            ]);

    }

    public function policies()
    {

        $policies = $this->getDoctrine()->getRepository(Policy::class)->findAll();
        return $this->render('admin/includes/_policies.html.twig', [
            'policies'=>$policies
            ]);

    }

    public function specificChild()
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user_id = $this->getUser()->getId();

        $specific_family_id = $this->getDoctrine()->getRepository(Family::class)->findFamilyIdByUserId($user_id);
        $specific_family_child = $this->getDoctrine()->getRepository(Child::class)->findChildByFamilyId($specific_family_id);

        return $this->render('admin/includes/_specific_child.html.twig', [
            'specific_family_child'=>$specific_family_child
            ]);
    }
}