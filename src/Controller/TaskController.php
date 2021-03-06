<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/tasks')]
class TaskController extends AbstractController
{
    #[Route('/all', name: 'task_all', methods: ['GET'])]
    public function index(TaskRepository $taskRepository): Response
    {

        return $this->render('task/index.html.twig', [
            'tasks' => $taskRepository->findAll(),
        ]);
    }
    #[Route('/{user}', name: 'task_index', methods: ['GET'])]
    public function all(TaskRepository $taskRepository): Response
    {
        $user = $this->getUser();
        return $this->render('task/index.html.twig', [
            'tasks' => $taskRepository->findByUser($user),
        ]);
    }


    #[Route('/{user}/new', name: 'task_new', methods: ['GET', 'POST'])]

    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $date = new \DateTime();
            $stringDate = $date->format('YmdHis');
            $task->setUser($this->getUser());
            $task->setTaskId("task-".$stringDate);
            $path = $this->getUser();
            $entityManager->persist($task);
            $entityManager->flush();

            return $this->redirect('/tasks/'.$path , Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('task/new.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'task_show', methods: ['GET'])]
    public function show(Task $task): Response
    {
        return $this->render('task/show.html.twig', [
            'task' => $task,
        ]);
    }

    #[Route('/{id}/edit', name: 'task_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
        $path = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirect('/tasks/'.$path , Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('task/edit.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'task_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->request->get('_token'))) {
            $entityManager->remove($task);
            $entityManager->flush();
        }

        return $this->redirectToRoute('task_all', [], Response::HTTP_SEE_OTHER);
    }
}
