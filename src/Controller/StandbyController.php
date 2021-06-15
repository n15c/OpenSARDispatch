<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Entity\Standby;
use App\Entity\User;
use App\Entity\PlanStatus;

function sendPikettNotif($fromdate, $todate, $number, $appName, $clientID)
{
  $message = "Dir wurde Pikett zugeteilt:\r\n ";
  $message .= $fromdate->format('d.m.Y') . " - " . $todate->format('d.m.Y');
  $url = 'https://api.swisscom.com/messaging/sms';
  $data = array(
    "from" => $appName,
    "to" => $number,
    "text" => $message,
    "callbackUrl" => "http://swisscom.com/callbackNotification"
  );
$data_str = json_encode($data);
$options = array(
  'http' => array(
    'header'  => "client_id: " . $clientID . "\r\nSCS-Version: 2\r\nContent-type: application/json\r\n",
    'method'  => 'POST',
    'content' => $data_str
    )
  );
  $context  = stream_context_create($options);
  $result = file_get_contents($url, false, $context);
  if ($result === FALSE) { /* Handle error */ }
}

class StandbyController extends AbstractController
{
  /**
    * @Route("/app/standby", name="app_standby")
    */
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();
        $appname = $this->getParameter('appname');

        $repository = $this->getDoctrine()->getRepository(User::class);
        $users = $repository->findAll();

        $repository = $this->getDoctrine()->getRepository(PlanStatus::class);
        $status = $repository->findAll();

        return $this->render('standby/standbyManagement.html.twig', [
          'curruser' => $user,
          'appname'=> $appname,
          'users'=> $users,
          'status'=>$status
        ]);
    }

    /**
    * @Route("/app/standbies/getStandbies", name="app_standbyjson")
    */
    public function getStandbies(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $standbyobj = [];
        $repository = $this->getDoctrine()->getRepository(Standby::class);
        $standbies = $repository->findAll();
        foreach ($standbies as $standby) {
          $curruser = $standby->getUser();
          $username = $curruser->getEmail();
          $todate = $standby->getDateTo();
          $fromdate = $standby->getDateFrom();
          $todate->modify('+1 day');
          // $stby["title"] = $username;
          $stby["title"] = $curruser->getFirstname() . " " . $curruser->getLastname();
          $stby["title"] .= " - " . $standby->getStatus()->getStatusName();
          $stby["id"] = $standby->getId();
          $stby["end"] = $todate->format($todate::ATOM);
          $stby["start"] = $fromdate->format($fromdate::ATOM);
          $stby["allDay"] = "true";
          $standbyobj[] = $stby;
        }
        $json = json_encode($standbyobj);
        $response = new Response();
        $response->setContent($json);
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/json');
        return $response;
    }

    /**
    * @Route("/app/standbies/createStandby", name="app_createstandby")
    */
    public function createStandby(): Response
    {
      $this->denyAccessUnlessGranted('ROLE_ADMIN');
      $user = $this->getUser();
      $request = Request::createFromGlobals();
      if ($request->getMethod() == "POST") {
        $data = json_decode($request->getContent(), true);
        $entityManager = $this->getDoctrine()->getManager();

        $repository = $this->getDoctrine()->getRepository(User::class);
        $selUser = $repository->findOneById($data["user"]);

        $repository = $this->getDoctrine()->getRepository(PlanStatus::class);
        $selStatus = $repository->findOneById($data["status"]);

        if ($this->getUser() != $selUser) {
          $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }

        $standby = new Standby();
        $standby->setDateFrom(new \DateTime($data["fromDate"]));
        $standby->setDateTo(new \DateTime($data["toDate"]));
        $standby->setStatus($selStatus);
        $standby->setUser($selUser);

        sendPikettNotif($standby->getDateFrom(),$standby->getDateTo(), $selUser->getPhone(), $this->getParameter('appname'), $this->getParameter('sms-apicid'));

        $entityManager->persist($standby);
        $entityManager->flush();

        $response = new Response();
        $response->setStatusCode(200);
        return $response;
      }
      else {
        $response = new Response();
        $response->setStatusCode(404);
        return $response;
      }
    }

    /**
     * @Route("/app/standbies/delete/{delStby}", name="app_delstby")
     */
    public function delete(int $delStby): Response{
      $this->denyAccessUnlessGranted('ROLE_ADMIN');

      $repository = $this->getDoctrine()->getRepository(Standby::class);
      $standby = $repository->find($delStby);

      $entityManager = $this->getDoctrine()->getManager();
      $entityManager->remove($standby);
      $entityManager->flush();

      $response = new Response();
      $response->setStatusCode(200);
      return $response;
    }

    /**
     * @Route("/app/standbies/update/{updStby}", name="app_updstby")
     */
    public function update(int $updStby): Response{
      $this->denyAccessUnlessGranted('ROLE_ADMIN');



      $request = Request::createFromGlobals();
      $data = json_decode($request->getContent(), true);

      $entityManager = $this->getDoctrine()->getManager();

      $repository = $this->getDoctrine()->getRepository(User::class);
      $selUser = $repository->findOneById($data["user"]);

      $repository = $this->getDoctrine()->getRepository(PlanStatus::class);
      $selStatus = $repository->findOneById($data["status"]);

      $repository = $this->getDoctrine()->getRepository(Standby::class);
      $standby = $repository->find($updStby);
      if(!$standby){
        throw $this->createNotFoundException(
           'No standby found for id '.$updStby
        );

        $response = new Response();
        $response->setStatusCode(404);
      }
      else {
        $standby->setDateFrom(new \DateTime($data["fromDate"]));
        $standby->setDateTo(new \DateTime($data["toDate"]));
        $standby->setStatus($selStatus);
        $standby->setUser($selUser);
        $entityManager->flush();

        $response = new Response();
        $response->setStatusCode(200);
      }

      return $response;
    }

    /**
     * @Route("/app/standby/getStandby/{stbyID}", name="app_getsinglestby")
     */
    public function getStbyById(int $stbyID): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $repository = $this->getDoctrine()->getRepository(Standby::class);
        $standby = $repository->find($stbyID);
        $curruser = $standby->getUser();
        $todate = $standby->getDateTo();
        $fromdate = $standby->getDateFrom();
        $todate->modify('+1 day');
        $stby["user"] = $curruser->getId();
        $stby["status"] = $standby->getStatus()->getId();
        $stby["id"] = $standby->getId();
        $stby["end"] = $todate->format($todate::ATOM);
        $stby["start"] = $fromdate->format($fromdate::ATOM);

        $json = json_encode($stby);
        $response = new Response();
        $response->setContent($json);
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/json');
        return $response;
    }
}
