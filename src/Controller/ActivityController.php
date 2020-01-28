<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Activity;

class ActivityController extends AbstractController
{
    /**
     * @Route("/activity/{project_id}", name="activity")
     */
    public function index(EntitYManagerInterface $em, $project_id)
    {
        $repository = $em->getRepository(Activity::class);

        $todo_activities = $repository->findActivitiesByStatus("todo", $project_id);
        $inProgress_activities = $repository->findActivitiesByStatus("in progress", $project_id);
        $complete_activities = $repository->findActivitiesByStatus("complete", $project_id);
        
        return $this->render('activity/index.html.twig', [
            'controller_name' => 'ActivityController',
            'todo_activities' => $todo_activities,
            'inProgress_activities' => $inProgress_activities,
            'complete_activities' => $complete_activities,
            'project_id' => $project_id
        ]);
    }

    /**
     * @Route("/activity/{project_id}/new", name="new_activity", methods={"POST"})
     */
    public function newActivity(Request $request, EntityManagerInterface $em, $project_id)
    {
        $name = $request->request->get("new_activity");
        $startedAt = null;
        $completedAt = null;
        $status = "todo";

        $activity = new Activity();
        $activity->setName($name)
            ->setStartedAt($startedAt)
            ->setCompletedAt($completedAt)
            ->setStatus($status)
            ->setProjectId($project_id);

        $em->persist($activity);
        $em->flush();
        
        return $this->redirectToRoute("activity", ['project_id' => $project_id]);
    }

    /**
     * @Route("/activity/{project_id}/update", name="update_activity", methods={"POST"})
     */
    public function updateActivity(Request $request, EntityManagerInterface $em, $project_id)
    {
        $action = $request->request->get("action");
        $id = $request->request->get("activity_id");
        $repository = $em->getRepository(Activity::class); 
        $activity = $repository->find($id);
        
        switch ($action) {
            case 'Active':
                $activity->setStatus('in progress');
                $activity->setStartedAt(new \DateTime());
                break;
            case 'Complete':
                $activity->setStatus('complete');
                $activity->setCompletedAt(new \DateTime());
                break;
            case 'Back':
                if ($activity->getStatus() === "in progress") {
                    $activity->setStatus('todo');
                    $activity->setStartedAt(null);
                } else if ($activity->getStatus() === "complete") {
                    $activity->setStatus('in progress');
                    $activity->setCompletedAt(null);
                }
                break;
            default:
                break;
        }
        
        $em->persist($activity);
        $em->flush();

        return $this->redirectToRoute("activity", ['project_id' => $project_id]);
    }

    /**
     * @Route("/activity/{project_id}/update/name", name="activity_update_name", methods={"POST"})
     */
    public function updateActivityName(Request $request, EntityManagerInterface $em, $project_id)
    {
        $name = $request->request->get("name");
        $id = $request->request->get("activity_id");
        $repository = $em->getRepository(Activity::class); 
        $activity = $repository->find($id);

        $activity->setName($name);
                
        $em->persist($activity);
        $em->flush();

        return $this->redirectToRoute("activity", ['project_id' => $project_id]);
    }

    /**
     * @Route("/activity/{project_id}/delete", name="activity_delete", methods={"POST"})
     */
    public function activityProject(Request $request, EntityManagerInterface $em, $project_id)
    {
        $id = $request->request->get("activity_id");
        $repository = $em->getRepository(Activity::class); 
        $activity = $repository->find($id); 

        $em->remove($activity);
        $em->flush();

        return $this->redirectToRoute("activity", ['project_id' => $project_id]);
    }

}
