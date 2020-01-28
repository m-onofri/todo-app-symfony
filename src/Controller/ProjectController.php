<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Project;
use App\Entity\Activity;

class ProjectController extends AbstractController
{
    /**
     * @Route("/", name="project")
     */
    public function index(EntityManagerInterface $em)
    {
        $repository = $em->getRepository(Project::class);
        $active_projects = $repository->findProjectsByStatus("active");
        $complete_projects = $repository->findProjectsByStatus("complete");
        
        return $this->render('project/index.html.twig', [
            'controller_name' => 'ProjectController',
            'active_projects' => $active_projects,
            'complete_projects' => $complete_projects
        ]);
    }

    /**
     * @Route("/projects/new", name="new_project", methods={"POST"})
     */
    public function newProject(Request $request, EntityManagerInterface $em, \Cocur\Slugify\SlugifyInterface $slugify)
    {
        $name = $request->request->get("new_project");
        $startedAt = new \DateTime();
        $completedAt = null;
        $status = "active";
        $slug = $slugify->slugify($name);

        $project->setName($name)
            ->setStartedAt($startedAt)
            ->setCompletedAt($completedAt)
            ->setStatus($status)
            ->setUserId(1)
            ->setSlug($slug);

        $em->persist($project);
        $em->flush();
        $lastId = $project->getId();
        
        return $this->redirectToRoute("project");
    }

    /**
     * @Route("/projects/update", name="project_update", methods={"POST"})
     */
    public function updateProject(Request $request, EntityManagerInterface $em)
    {
        $action = $request->request->get("action");
        $id = $request->request->get("project_id");
        $repository = $em->getRepository(Project::class); 
        $project = $repository->find($id);

        switch ($action) {
            case 'Complete':
                $project->setStatus('complete');
                $project->setCompletedAt(new \DateTime());
                break;
            case 'Active':
                $project->setStatus('active');
                $project->setCompletedAt(null);
                break;
            default:
                break;
        }

        $em->persist($project);
        $em->flush();

        return $this->redirectToRoute("project");
    }

    /**
     * @Route("/projects/delete", name="project_delete", methods={"POST"})
     */
    public function deleteProject(Request $request, EntityManagerInterface $em)
    {
        $id = $request->request->get("project_id");
        $repository = $em->getRepository(Project::class); 
        $project = $repository->find($id); 

        $em->remove($project);
        $em->flush();

        $repo = $em->getRepository(Activity::class); 
        $repo->deleteActivitiesByProjectId(intval($id)); 

        return $this->redirectToRoute("project");
    }

    /**
     * @Route("/projects/update/name", name="project_update_name", methods={"POST"})
     */
    public function updateProjectName(Request $request, EntityManagerInterface $em, \Cocur\Slugify\SlugifyInterface $slugify)
    {
        $name = $request->request->get("name");
        $slug = $slugify->slugify($name);
        $id = $request->request->get("project_id");
        $repository = $em->getRepository(Project::class); 
        $project = $repository->find($id);

        $project->setName($name);
        $project->setSlug($slug);
                
        $em->persist($project);
        $em->flush();

        return $this->redirectToRoute("project");
    }
}
