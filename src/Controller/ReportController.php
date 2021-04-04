<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\OPReport;
use App\Entity\RescueOP;
use App\Entity\User;

class ReportController extends AbstractController
{
  /**
  * @Route("/app/reports/{opid}", name="app_editviewopreport", methods={"GET","HEAD"}))
  */
  public function editviewopreport(int $opid): Response
  {
      $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

      $repository = $this->getDoctrine()->getRepository(RescueOP::class);
      $rescueop = $repository->find($opid);

      if ($rescueop == NULL ) {
        $response = new Response();
        $response->setStatusCode(404);
        return $response;
      }
      else {
        $report = $rescueop->getOPReport();
        if ($report == NULL) {
          $report = new OPReport();
          $report->setOperation($rescueop);
          $entityManager = $this->getDoctrine()->getManager();
          $entityManager->persist($report);
          $entityManager->flush();
        }
        $appname = $this->getParameter('appname');
        $repository = $this->getDoctrine()->getRepository(User::class);
        $users = $repository->findAll();

        return $this->render('report/show_report.html.twig', [
          'curruser' => $this->getUser(),
          'appname'=> $appname,
          'report'=>$report,
          'users'=>$users
        ]);
        return $html;
      }
  }

  /**
  * @Route("/app/reports/{opid}", name="app_saveopreport", methods={"POST"}))
  */
  public function saveopreport(int $opid): Response
  {
      $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

      $repository = $this->getDoctrine()->getRepository(RescueOP::class);
      $rescueop = $repository->find($opid);

      if ($rescueop == NULL ) {
        $response = new Response();
        $response->setStatusCode(404);
        return $response;
      }
      else {
        $report = $rescueop->getOPReport();
        if ($report == NULL || $report->getReportClosed()) {
          $response = new Response();
          $response->setStatusCode(404);
          return $response;
        }
        else {
          $appname = $this->getParameter('appname');
          $request = Request::createFromGlobals();
          $submittedToken = $request->request->get('token');
          if ($this->isCsrfTokenValid('save-report', $submittedToken)) {
            $report->setReportText($request->request->get('ReportText'));
            $report->setRating($request->request->get('operationRating'));
            if ($request->request->get('reportSigned') !== NULL) {
              $report->setReportClosed(1);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $response = new Response();
            $response->headers->set('Location', '/app/reports/' . $rescueop->getId());
            $response->setStatusCode(302);
            return $response;
          }
          else {
            $response = new Response();
            $response->setStatusCode(401);
            return $response;
          }
        }
      }
  }
}
