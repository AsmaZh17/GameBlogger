<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Form\BlogType;
use App\Repository\BlogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Repository\CategorieRepository;
use App\Repository\CommentaireRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\SecurityBundle\Security;

class BlogController extends AbstractController
{
    #[Route('/blog', name: 'app_blog')]
    public function blog(BlogRepository $blogRepository, 
        PaginatorInterface $paginator , 
        Request $request ,
        CategorieRepository $categorieRepository
        ): Response
    {
        $data = $blogRepository->findAll();

        $blogs = $paginator->paginate(
            $data,
            $request->query->getInt('page' , 1),
            2
        );
        return $this->render('blog/blog.html.twig', [
            'blogs' => $blogs ,
            'categories' => $categorieRepository->findAll(), 
            'popularBlogs' => $blogRepository->lastTree()   
        ]);
    }

    #[Route('/blog/{categorieId}', name: 'app_blog_categorie')]
    public function blogByCategorie($categorieId , BlogRepository $blogRepository, 
        PaginatorInterface $paginator , 
        Request $request ,
        CategorieRepository $categorieRepository
        ): Response
    {
        $data = $blogRepository->findByCategorieId($categorieId);

        $blogs = $paginator->paginate(
            $data,
            $request->query->getInt('page' , 1),
            2
        );
        return $this->render('blog/blog.html.twig', [
            'blogs' => $blogs ,
            'categories' => $categorieRepository->findAll(),
            'popularBlogs' => $blogRepository->lastTree()   
        ]);
    }
    #[Route('/single_blog/{id}', name: 'app_single_blog')]
    public function single_blog($id , BlogRepository $blogRepository ,
        CommentaireRepository $commentaireRepository ,
        Request $request, 
        EntityManagerInterface $entityManager,
        Security $security
    ): Response
    { 
        $blog = $blogRepository->find($id);
        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);
        $blog->setViews($blog->getViews() + 1);
        $entityManager->flush();

        $commentId = $request->query->get('comment_id');
        $editcommentId = $request->query->get('edit_comment_id');
        $commentaireReply = null;
        if ($commentId) {
            $commentaireReply = $commentaireRepository->find($commentId);
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $security->getUser();
            if ($user) {
                $commentaire->setAuthor($user);
                $commentaire->setBlog($blog);
                $commentaire->setCreateAt(new \DateTime());
                $commentaire->setParent($commentaireReply);
                $entityManager->persist($commentaire);
                $entityManager->flush();
                $commentaireReply = null;

                return $this->redirectToRoute('app_single_blog', ['id' => $id], Response::HTTP_SEE_OTHER);
            }else{
                return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
            }
        }
        
        return $this->render('blog/single_blog.html.twig' , [
            'blog' => $blog,
            'popularBlogs' => $blogRepository->lastTreeDiffId($id),
            'count' => $commentaireRepository->countCommentsByBlogId($blog->getId()),
            'commentaire' => $commentaire,
            'form' => $form,
            'favorite' => $blogRepository->countFavoritesForBlogIdOne($id),
            'user' => $security->getUser(),
            'commentaireReply' => $commentaireReply
        ]);
    }
    #[Route('/single_blog/{id}/edit', name: 'app_single_blog_edit')]
    public function single_blog_edit($id , BlogRepository $blogRepository ,
        CommentaireRepository $commentaireRepository ,
        Request $request, 
        EntityManagerInterface $entityManager,
        Security $security,
        Commentaire $commentaire
    ): Response
    { 
        $blog = $blogRepository->find($id);
        $editcommentId = $request->query->get('edit_comment_id');
        $commentaire = $commentaireRepository->find($editcommentId);
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $security->getUser();
            if ($user) {
                $entityManager->flush();

                return $this->redirectToRoute('app_single_blog', ['id' => $id], Response::HTTP_SEE_OTHER);
            }else{
                return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
            }
        }
        
        return $this->render('blog/single_blog.html.twig' , [
            'blog' => $blog,
            'popularBlogs' => $blogRepository->lastTreeDiffId($id),
            'count' => $commentaireRepository->countCommentsByBlogId($blog->getId()),
            'commentaire' => $commentaire,
            'form' => $form,
            'favorite' => $blogRepository->countFavoritesForBlogIdOne($id),
            'user' => $security->getUser(),
        ]);
    }
    #[Route('/single_blog/{blogid}/{id}', name: 'app_single_blog_delete', methods: ['POST'])]
    public function deleteComment($blogid, Request $request, Commentaire $commentaire, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commentaire->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($commentaire);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_single_game', ['id' => $blogid], Response::HTTP_SEE_OTHER);
    }
    #[Route('/single_blog/{id}/like', name: 'app_single_blog_like')]
    public function like($id, EntityManagerInterface $entityManager , BlogRepository $blogRepository , Security $security): Response
    {
        $blog = $blogRepository->find($id);
        $user = $security->getUser();
        if ($user) {
            $blog->addUsersFavorite($user);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_single_blog', ['id' => $id]);
        }else{
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }
    }
    #[Route('/single_blog/{id}/unlike', name: 'app_single_blog_unlike')]
    public function unlike($id, EntityManagerInterface $entityManager, BlogRepository $blogRepository, Security $security): Response
    {
        $blog = $blogRepository->find($id);
        $user = $security->getUser();
        if ($user) {
            $blog->removeUsersFavorite($user);
            $entityManager->flush();
            
            return $this->redirectToRoute('app_single_blog', ['id' => $id]);
        }else{
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }
    }
    #[Route('/admin/blogs/', name: 'app_blogs_index', methods: ['GET'])]
    public function index(BlogRepository $blogRepository): Response
    {
        return $this->render('admin/blog/index.html.twig', [
            'blogs' => $blogRepository->findAll(),
        ]);
    }

    #[Route('/admin/blogs/new', name: 'app_blogs_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $blog = new Blog();
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $blog->setCreateAt(new \DateTime());
            $entityManager->persist($blog);
            $entityManager->flush();

            return $this->redirectToRoute('app_blogs_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/blog/new.html.twig', [
            'blog' => $blog,
            'form' => $form,
        ]);
    }

    #[Route('/admin/blogs/{id}', name: 'app_blogs_show', methods: ['GET'])]
    public function show(Blog $blog): Response
    {
        return $this->render('admin/blog/show.html.twig', [
            'blog' => $blog,
        ]);
    }

    #[Route('/admin/blogs/{id}/edit', name: 'app_blogs_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Blog $blog, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_blogs_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/blog/edit.html.twig', [
            'blog' => $blog,
            'form' => $form,
        ]);
    }

    #[Route('/admin/blogs/{id}', name: 'app_blogs_delete', methods: ['POST'])]
    public function delete(Request $request, Blog $blog, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$blog->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($blog);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_blogs_index', [], Response::HTTP_SEE_OTHER);
    }
}
