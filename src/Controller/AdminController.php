<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\User;
use App\Entity\PlanStatus;

class AdminController extends AbstractController
{
    /**
    * @Route("/admin", name="app_admin")
    */
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPERADMIN');

        $curruser = $this->getUser();
        $appname = $this->getParameter('appname');

        $repository = $this->getDoctrine()->getRepository(User::class);
        $users = $repository->findAll();

        $repository = $this->getDoctrine()->getRepository(PlanStatus::class);
        $statuses = $repository->findAll();

        return $this->render('admin/index.html.twig', [
          'curruser' => $curruser,
          'appname'=> $appname,
          'users'=>$users,
          'statuses'=>$statuses
        ]);
    }

    /**
    * @Route("/admin/getUserInfo/{id}", name="app_adminuserinfo")
    */
    public function getUserInfo(int $id): Response
    {
      $this->denyAccessUnlessGranted('ROLE_SUPERADMIN');
      $entityManager = $this->getDoctrine()->getManager();
      $user = $entityManager->getRepository(User::class)->find($id);

      $response = new Response();

      if(!$user){
        $response->setStatusCode(400);
      }
      else {
        $userInfo["id"] = $user->getId();
        $userInfo["username"] = $user->getUsername();
        $userInfo["firstname"] = $user->getFirstname();
        $userInfo["lastname"] = $user->getLastname();
        $userInfo["phone"] = $user->getPhone();
        $userInfo["roles"] = $user->getRoles();

        $response->setContent(json_encode($userInfo));
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/json');
      }
      return $response;
    }

    /**
    * @Route("/admin/deleteUser/{id}", name="app_admindeluser")
    */
    public function deleteUser(int $id): Response
    {
      $this->denyAccessUnlessGranted('ROLE_SUPERADMIN');
      $entityManager = $this->getDoctrine()->getManager();
      $user = $entityManager->getRepository(User::class)->find($id);

      $response = new Response();

      if(!$user){
        $response->setStatusCode(400);
      }
      else {
        $entityManager->remove($user);
        $entityManager->flush();

        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/json');
      }
      return $response;
    }

    /**
    * @Route("/admin/updateUser/{id}", name="app_adminsavuser")
    */
    public function updateUser(int $id): Response
    {
      $this->denyAccessUnlessGranted('ROLE_SUPERADMIN');
      $request = Request::createFromGlobals();
      $data = json_decode($request->getContent(), true);

      $entityManager = $this->getDoctrine()->getManager();

      $repository = $this->getDoctrine()->getRepository(User::class);
      $selUser = $repository->findOneById($id);

      if(!$selUser){
        throw $this->createNotFoundException(
           'No user found for id '.$updStby
        );
        $response = new Response();
        $response->setStatusCode(404);
      }
      else {
        $selUser->setEmail($data["username"]);
        $selUser->setPhone($data["phone"]);
        $selUser->setFirstname($data["firstname"]);
        $selUser->setLastname($data["lastname"]);
        $selUser->setRoles($data["roles"]);

        $entityManager->flush();

        $response = new Response();
        $response->setStatusCode(200);
      }
      return $response;
    }

    /**
    * @Route("/admin/createUser", name="app_admincrtuser")
    */
    public function createUser(): Response
    {
      $this->denyAccessUnlessGranted('ROLE_SUPERADMIN');
      $request = Request::createFromGlobals();
      $data = json_decode($request->getContent(), true);

      $entityManager = $this->getDoctrine()->getManager();
      $newUser = new User();

      $newUser->setEmail($data["username"]);
      $newUser->setPhone($data["phone"]);
      $newUser->setFirstname($data["firstname"]);
      $newUser->setLastname($data["lastname"]);
      $newUser->setRoles($data["roles"]);
      $newUser->setPassword(substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(30/strlen($x)) )),1,30));

      $entityManager->persist($newUser);
      $entityManager->flush();

      $response = new Response();
      $response->setStatusCode(200);
      return $response;
    }

    /**
    * @Route("/admin/addStatus", name="app_admincreatestatus")
    */
    public function addStatus(): Response
    {
      $this->denyAccessUnlessGranted('ROLE_SUPERADMIN');

      $request = Request::createFromGlobals();
      $submittedToken = $request->request->get('token');
      if ($this->isCsrfTokenValid('create-item', $submittedToken)) {
        $newStatusName = $request->request->get('statusName');

        $entityManager = $this->getDoctrine()->getManager();
        $newPlanStatus = new PlanStatus();

        $newPlanStatus->setStatusName($newStatusName);

        $entityManager->persist($newPlanStatus);
        $entityManager->flush();

        $response = new Response();
        $response->setStatusCode(302);
        $response->headers->set('Location', '/admin');
        return $response;
      }
      else {
        $response = new Response();
        $response->setStatusCode(403);
        return $response;
      }
    }

    /**
    * @Route("/admin/deleteStatus", name="app_admindelstatus")
    */
    public function deleteStatus(): Response
    {
      $this->denyAccessUnlessGranted('ROLE_SUPERADMIN');
      $request = Request::createFromGlobals();
      $response = new Response();
      $submittedToken = $request->request->get('token');
      if ($this->isCsrfTokenValid('delete-item', $submittedToken)) {
        $statusID = $request->request->get('statusid');
        $entityManager = $this->getDoctrine()->getManager();
        $status = $entityManager->getRepository(PlanStatus::class)->find($statusID);

        if(!$status){
          $response->setStatusCode(400);
        }
        else {
          $entityManager->remove($status);
          $entityManager->flush();

          $response->setStatusCode(302);
          $response->headers->set('Location', '/admin');
        }
      }
      else {
        $response->setStatusCode(403);
      }
      return $response;
    }
}
