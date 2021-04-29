<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\RescueOP;

class HomepageController extends AbstractController
{
  /**
    * @Route("/", name="app_home")
    */
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $curruser = $this->getUser();
        $standbies = $curruser->getStandbies();
        $appname = $this->getParameter('appname');

        return $this->render('app/home.html.twig', [
          'curruser' => $curruser,
          'appname'=> $appname,
          'standbies'=>$standbies
        ]);
    }
}
