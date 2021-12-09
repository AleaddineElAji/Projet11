<?php

namespace App\Controller;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ProjectType;

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
}
