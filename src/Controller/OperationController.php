<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\RescueOP;
use App\Entity\Standby;

function sendNotification($date, $number, $mapsURL)
{
  $message = "Es wurde ein neuer Einsatz geplant. Du bist am ";
  $message .= $date->format('d.m.Y H:i:s') . " im Einsatz! " . $mapsURL;
  echo $message;
  $url = 'https://api.swisscom.com/messaging/sms';
  $data = array(
    "from" => "Test",
    "to" => $number,
    "text" => $message,
    "callbackUrl" => "http://swisscom.com/callbackNotification"
  );
$data_str = json_encode($data);
$options = array(
  'http' => array(
    'header'  => "client_id: JljGXaNspaWP28yUbuqClDAl7ByYo9Sq\r\nSCS-Version: 2\r\nContent-type: application/json\r\n",
    'method'  => 'POST',
    'content' => $data_str
    )
  );
  $context  = stream_context_create($options);
  $result = file_get_contents($url, false, $context);
  if ($result === FALSE) { /* Handle error */ }
}

class OperationController extends AbstractController
{
  /**
   * @Route("/app/createOp", name="app_createOp")
   */
  public function createOP(): Response
  {
      $this->denyAccessUnlessGranted('ROLE_FARMER');
      $curruser = $this->getUser();
      $request = Request::createFromGlobals();
      if ($request->getMethod() == "POST") {
        $entityManager = $this->getDoctrine()->getManager();
        $rescueop = new RescueOP();
        $rescueop->setPosition([$request->request->get('InputPos')]);
        $rescueop->setCreationDate(new \DateTime());
        $rescueop->setCreator($curruser);
        $rescueop->setComment($request->request->get('InputComment'));
        $plannedDatedate = new \DateTime($request->request->get('InputPlanDate'));
        $rescueop->setPlannedDate($plannedDatedate);

        $entityManager->persist($rescueop);
        $entityManager->flush();
        $plannedDatedate_tstmp = date_timestamp_get($plannedDatedate);

        //Die eingeplanten Personen informieren
        $position = json_decode($request->request->get('InputPos'));
        $mapsURL = "https://www.google.com/maps/search/?api=1&query=" . strval($position->lat) . "," . strval($position->lng);
        $standbies = $this->getDoctrine()
          ->getRepository(Standby::class)
          ->findAllPlannedStandbies($plannedDatedate);

        foreach ($standbies as $standby) {
          sendNotification($plannedDatedate, $standby->getUser()->getPhone(),$mapsURL);
        }

        $response = new Response();
        $response->headers->set('Location', '/app/createOp');
        $response->setStatusCode(303);
        return $response;
      }
      else {
        $appname = $this->getParameter('appname');

        return $this->render('operation/createOP.html.twig', [
          'curruser' => $curruser,
          'appname'=> $appname
        ]);
      }
  }

  /**
   * @Route("/app/operations", name="app_getOps")
   */
  public function getOps(): Response
  {
      $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
      $curruser = $this->getUser();
      $request = Request::createFromGlobals();
      $appname = $this->getParameter('appname');

      $repository = $this->getDoctrine()->getRepository(RescueOP::class);
      if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
          $operations = $repository->findBy([],['plannedDate'=>'DESC']);
      }
      else if ($this->get('security.authorization_checker')->isGranted('ROLE_PILOT')) {
          $operations = $repository->findBy([],['plannedDate'=>'DESC']);
      }
      else {
        $operations = $repository->findBy(
          ['creator' => $curruser],
          ['plannedDate' => 'DESC']
        );
      }
      return $this->render('operation/getOP.html.twig', [
        'curruser' => $curruser,
        'appname'=> $appname,
        'rescueops' => $operations
      ]);
  }

  /**
  * @Route("/app/operations/delete/{id}", name="app_delOps")
  */
  public function deleteOP(int $id): Response
  {
      $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

      $repository = $this->getDoctrine()->getRepository(RescueOP::class);
      $operation = $repository->find($id);

      if ($this->getUser() != $operation->getCreator()) {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
      }

      $entityManager = $this->getDoctrine()->getManager();
      $entityManager->remove($operation);
      $entityManager->flush();

      $response = new Response();
      $response->setStatusCode(302);
      $response->headers->set('Location', '/app/operations');
      return $response;
  }

  /**
  * @Route("/app/operations/{id}", name="app_intelOps")
  */
  public function OPIntel(int $id): Response
  {
      $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

      $repository = $this->getDoctrine()->getRepository(RescueOP::class);
      $operation = $repository->find($id);

      if ($this->getUser() != $operation->getCreator()) {
        $this->denyAccessUnlessGranted('ROLE_PILOT');
      }

      $appname = $this->getParameter('appname');

      $standbies = $this->getDoctrine()
        ->getRepository(Standby::class)
        ->findAllPlannedStandbies($operation->getPlannedDate());

      return $this->render('operation/opintel.html.twig', [
        'curruser' => $this->getUser(),
        'appname'=> $appname,
        'operation'=>$operation,
        'standbies'=>$standbies
      ]);
  }

  /**
  * @Route("/app/operations/pdf/{id}", name="app_oppdf")
  */
  public function getOPPDF(int $id): Response
  {
      $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

      $repository = $this->getDoctrine()->getRepository(RescueOP::class);
      $operation = $repository->find($id);

      if ($this->getUser() != $operation->getCreator()) {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
      }

      $appname = $this->getParameter('appname');

      $standbies = $this->getDoctrine()
        ->getRepository(Standby::class)
        ->findAllPlannedStandbies($operation->getPlannedDate());

      $html = $this->renderView('operation/opintel_page.html.twig', [
        'curruser' => $this->getUser(),
        'appname'=> $appname,
        'operation'=>$operation,
        'standbies'=>$standbies
      ]);
  }
}
