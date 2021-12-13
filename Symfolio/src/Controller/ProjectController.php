<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ProjectController extends AbstractController
{
    /**
     * @Route("/project", name="project")
     */
    public function index(ProjectRepository $repo): Response
    {
        $repo = $this->getDoctrine()->getRepository(Project::class);
        $allProjects = $repo->findAll();
        return $this->render('project/index.html.twig', [
            'projets'         => $allProjects
        ]);
    }
    /**
     * @Route("/", name="home")
     */
    public function home(){
        return $this->render('project/home.html.twig');
    }

    /**
     * @Route("/project/admin", name="project_admin")
     */
    public function adminPanel(ProjectRepository $repo): Response
    {
        $repo = $this->getDoctrine()->getRepository(Project::class);
        $allProjects = $repo->findAll();
        return $this->render('project/admin.html.twig', [
            'projets' => $allProjects
        ]);
    }

    /** 
    *@Route("/project/new", name="project_create")
    *@Route("/project/edit/{id}", name="project_edit")
    */
    public function form(Project $project = null, Request $request, ManagerRegistry $doctrine){
        $manager = $doctrine->getManager();
        if(!$project){
            $project = new Project();
        }
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $images =$form->get('images')->getData();
            foreach ($images as $image){
                $fichier = md5(uniqid()).'.'.$image-> guessExtension();
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );
               $img = new Images(); 
               $img->setName($fichier);
               $project->addImage($img);
            }
            $manager->persist($project);
            $manager->flush();
            return $this->redirectToRoute('project_show', ['id' => $project->getId()]);
        }
        return $this->render('project/create.html.twig',[
            'formProject' => $form->createView(),
            'editMode'    => $project->getId() !== null
        ]);
    }

    /**
     * @Route("/project/{id}", name="project_show")
     */
    public function show(Project $projet){
        return $this->render("project/show.html.twig",[
            'projet'   => $projet, 
        ]);
    }
    
    /**
     * @Route("/project/delete/{id}", name="project_delete")
     */
    public function delete(Project $project, ManagerRegistry $doctrine)
    {
        $manager = $doctrine->getManager();
        $manager->remove($project);
        $manager->flush();
        return $this->redirectToRoute('project_admin');
    }


    /**
     *@Route("/project/edit/{id}", name="project_edit")
     */
    public function edit(Request $request, Project $project): Response 
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);
        if( $form->isSubmitted() && $form->isValid() ){
            $images =$form->get('images')->getData();
            foreach ($images as $image){
                $fichier = md5(uniqid()).'.'.$image-> guessExtension();
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );
               $img = new Images(); 
               $img->setName($fichier);
               $project->addImage($img);
            }
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('project');
        }
        return $this->render('project/create.html.twig',[
            'formProject' => $form->createView(),
            'projetImgEdit' => $project->getImages(),
            'editMode'    => $project->getId() !== null,
            'projects' =>$project
        ]);
    }

    /**
     *@Route("/supprime/image/{id}", name="annonce_delete_image", methods="DELETE")
     */
    public function deleteImage(Images $image, Request $request){
        $data = json_decode($request->getContent(), true);
        if($this->isCsrfTokenValid('delete'.$image->getId(), $data['_token'])){
            $nom = $image->getName();
            unlink($this->getParameter('images_directory').'/'.$nom);
            $em = $this->getDoctrine()->getManager();
            $em->remove($image);
            $em->flush();
            return new JsonResponse(['success'=> 1]);
        }else{
            return new JsonResponse(['error'=> 'Token Invalide', 400]);
        }
    }
}