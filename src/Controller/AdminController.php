<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\NewFamilyType;
use App\Form\NewUserType;
use App\Form\EditFamilyType;
use App\Form\AddChildType;
use App\Form\AddPostType;
use App\Form\AddCommentType;
use App\Form\AddGlobalPostCommentType;
use App\Form\AddGlobalPostType;
use App\Form\EditGlobalPostType;
use App\Form\EditPostType;
use App\Form\ResetPasswordType;
use App\Form\NewPolicyType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Entity\Family;
use App\Entity\Child;
use App\Entity\Post;
use App\Entity\GlobalPost;
use App\Entity\User;
use App\Entity\Policy;
use App\Entity\Comment;
use App\Entity\GlobalPostComment;
use Symfony\Component\Filesystem\Filesystem;
use Intervention\Image\ImageManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Knp\Component\Pager\PaginatorInterface;


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
            $user->setLastName($request->request->get('new_user')['last_name']);
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
     * @Route("/time-line/{id}", name="admin_time_line", methods={"GET","POST"})
     */
    public function timeLine(Child $child, PaginatorInterface $paginator, Request $request)
    {   

        $specificChildUserId = $child->getFamily()->getUser()->getId();
        $user_id = $this->getUser()->getId();

        // check if user is allowed to see childs timeline

        if ($specificChildUserId == $user_id || $this->getUser()->getRoles()[0] == "ROLE_ADMIN")
        {
            $childs_posts = $this->getDoctrine()->getRepository(Post::class)->findPostsByChildId($child->getId());

            $pagination = $paginator->paginate(
                $childs_posts, /* query NOT result */
                $request->query->getInt('page', 1), /*page number*/
                6 /*limit per page*/
            );

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
                $newPost->setTime(new \DateTime());

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($newPost);
                $entityManager->flush();

                $this->addFlash('post_added', 'Post Successfully Added');

                return $this->redirectToRoute('admin_time_line', ['id' => $child->getId()]);

            }

            return $this->render('admin/time-line.html.twig', [
            'child' => $child,
            'pagination' => $pagination,
            'form' => $form->createView()
            ]);
        }

        else
        // if user is no allowed to see the page redirect to main admin page
        {
            return $this->redirectToRoute('admin_main_page');
        }
    }

    /**
     * @Route("/timeline-post/{id}", name="timeline_post", methods={"GET","POST"})
     */
    public function timeLinePost(Post $post, Request $request)
    {
        // check if user is allowed to see childs timeline
        
        $specificChildUserId = $post->getChild()->getFamily()->getUser()->getId();
        $user_id = $this->getUser()->getId();

        if ($specificChildUserId == $user_id || $this->getUser()->getRoles()[0] == "ROLE_ADMIN")
        {
            
            $child = $post->getChild();

            $user = $this->getUser();

            $comments = $this->getDoctrine()->getRepository(Comment::class)->findCommentsByPostId($post->getId());

            $comment = new Comment;
            $form = $this->createForm(AddCommentType::class, $comment);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid())
            {

                $comment->setPost($post);
                $comment->setUser($user);
                $comment->setContent($request->request->get('add_comment')['content']);
                $comment->setDate(new \DateTime());
                $comment->setTime(new \DateTime());

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($comment);
                $entityManager->flush();
                
                $this->addFlash('comment_added', 'Comment Sucessfully added. Thank You');

                return $this->redirectToRoute('timeline_post', ['id' => $post->getId()]);

            }

            return $this->render('admin/timeline-post.html.twig', [
                'form' => $form->createView(),
                'post' => $post,
                'child' => $child,
                'comments' => $comments
            ]);
        }
        else
        // if user is no allowed to see the page redirect to main admin page
        {
            return $this->redirectToRoute('admin_main_page');
        }    

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

        return $this->redirectToRoute('admin_time_line', ['id' => $post->getChild()->getId()]);
    }

    /**
     * @Route("/su/edit-post/{id}", name="edit_post", methods={"GET","POST"})
     */
    public function editPost(Post $editPost, Request $request)
    {

        $form = $this->createForm(EditPostType::class, $editPost);
        $form->handleRequest($request);

        $child = $editPost->getChild();

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
            
            $this->addFlash('post_updated', 'Post Successfully Updated');

            return $this->render('admin/edit-post.html.twig', [
                'form' => $form->createView(),
                'editPost' => $editPost,
                'child' => $child
            ]);

        }

        return $this->render('admin/edit-post.html.twig', [
            'form' => $form->createView(),
            'editPost' => $editPost,
            'child' => $child
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

    /**
     * @Route("/su/global-post", name="global_post", methods={"GET","POST"})
     */
    public function globalPost(Request $request)
    {

        $newGlobalPost = new GlobalPost();
        $form = $this->createForm(AddGlobalPostType::class, $newGlobalPost);
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
    
                $newGlobalPost->setImageFilename($newFilename);
            }

            $newGlobalPost->setSubject($request->request->get('add_global_post')['subject']);
            $newGlobalPost->setContent($request->request->get('add_global_post')['content']);
            $newGlobalPost->setDate(new \DateTime());
            $newGlobalPost->setTime(new \DateTime());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($newGlobalPost);
            $entityManager->flush();

            $this->addFlash('post_added', 'Global Post Successfully Added');

            return $this->redirectToRoute('admin_main_page');

        }

        return $this->render('admin/global-post.html.twig', [
        'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/su/edit-global-post/{id}", name="edit_global_post", methods={"GET","POST"})
     */
    public function editGlobalPost(GlobalPost $editGlobalPost, Request $request)
    {

        $form = $this->createForm(EditGlobalPostType::class, $editGlobalPost);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            $editGlobalPost->setSubject($request->request->get('edit_post')['subject']);
            $editGlobalPost->setContent($request->request->get('edit_post')['content']);

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
    
                $editGlobalPost->setImageFilename($newFilename);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($editGlobalPost);
            $entityManager->flush();
            
            $this->addFlash('post_updated', 'Global Post Successfully Updated');

            return $this->redirectToRoute('admin-main-page');

        }

        return $this->render('admin/edit-global-post.html.twig', [
            'form' => $form->createView(),
            'editGlobalPost' => $editGlobalPost,
        ]);
    }

    /**
     * @Route("/su/delete-global-post/{id}", name="delete_global_post")
     */
    public function deleteGlobalPost(GlobalPost $global_post)
    {
        $image = $global_post->getImageFileName();
        $path=$this->getParameter('post_image_directory').'/'.$image;

        $fs = new FileSystem();
        $fs->remove(array($path));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($global_post);
        $entityManager->flush();

        return $this->redirectToRoute('admin_main_page');
    }

    /**
     * @Route("/global-single-post/{id}", name="global_single_post", methods={"GET","POST"})
     */
    public function globalSinglePost(GlobalPost $globalPost, Request $request)
    {

        $user = $this->getUser();

        $comments = $this->getDoctrine()->getRepository(GlobalPostComment::class)->findCommentsByPostId($globalPost->getId());

        $comment = new GlobalPostComment;
        $form = $this->createForm(AddGlobalPostCommentType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            $comment->setGlobalPost($globalPost);
            $comment->setUser($user);
            $comment->setContent($request->request->get('add_global_post_comment')['content']);
            $comment->setDate(new \DateTime());
            $comment->setTime(new \DateTime());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();
            
            $this->addFlash('comment_added', 'Comment Sucessfully added. Thank You');

            return $this->redirectToRoute('global_single_post', ['id' => $globalPost->getId()]);

        }

        return $this->render('admin/global-single-post.html.twig', [
            'form' => $form->createView(),
            'post' => $globalPost,
            'comments' => $comments
        ]);
    }

    /**
     * @Route("/global-multi-posts", name="global_multi_posts", methods={"GET","POST"})
     */
    public function globalMultiPosts()
    {
        return $this->render('admin/global-multi-posts.html.twig');
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

    public function globalPosts(Request $request, PaginatorInterface $paginator)
    {
        $global_posts = $this->getDoctrine()
        ->getRepository(GlobalPost::class)
        ->findAllGlobalPosts();
        
        $pagination = $paginator->paginate(
            $global_posts, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            6 /*limit per page*/
        );

        return $this->render('admin/includes/_global_posts.html.twig', [
            'pagination' => $pagination
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