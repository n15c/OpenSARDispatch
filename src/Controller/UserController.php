<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;

class UserController extends AbstractController
{
    #[Route('/app/getUsers', name: 'userList')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $curruser = $this->getUser();
        $request = Request::createFromGlobals();
        $appname = $this->getParameter('appname');

        $repository = $this->getDoctrine()->getRepository(User::class);
        $users = $repository->findBy([],['Lastname' => 'ASC']);
        return $this->render('user/getUsers.html.twig', [
          'curruser' => $curruser,
          'appname'=> $appname,
          'users' => $users
        ]);
    }

    #[Route('/app/userConfig', name: 'userconfig')]
    public function userConfig(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $curruser = $this->getUser();
        $request = Request::createFromGlobals();
        $appname = $this->getParameter('appname');

        $repository = $this->getDoctrine()->getRepository(User::class);
        $users = $repository->findAll();
        return $this->render('user/userConfig.html.twig', [
          'curruser' => $curruser,
          'appname'=> $appname
        ]);
    }

    #[Route('/app/userConfig/setConfig', name: 'setuserconfig')]
    public function setuserConfig(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $curruser = $this->getUser();
        $request = Request::createFromGlobals();
        $data = json_decode($request->getContent(), true);

        $entityManager = $this->getDoctrine()->getManager();

        $curruser->setEmail($data["mail"]);
        $curruser->setPhone($data["phone"]);
        $curruser->setFirstname($data["firstname"]);
        $curruser->setLastname($data["lastname"]);

        $entityManager->flush();

        $response = new Response();
        $response->setStatusCode(200);
        return $response;
    }
}
