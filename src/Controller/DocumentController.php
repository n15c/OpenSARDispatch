<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\Filesystem\Filesystem;

use App\Entity\User;
use App\Entity\Document;

class DocumentController extends AbstractController
{
    /**
    * @Route("/app/documents", name="app_documents")
    */
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $curruser = $this->getUser();
        $appname = $this->getParameter('appname');

        $repository = $this->getDoctrine()->getRepository(Document::class);
        $documents = $repository->findAll();

        return $this->render('documents/docmgmt.html.twig', [
          'curruser' => $curruser,
          'appname'=> $appname,
          'documents'=>$documents
        ]);
    }

    /**
    * @Route("/app/documents/upload", name="app_upload_doc")
    */
    public function uploadDoc(SluggerInterface $slugger): Response
    {
      $this->denyAccessUnlessGranted('ROLE_ADMIN');
      $response = new Response();
      $response->setStatusCode(403);
      $request = Request::createFromGlobals();
      $submittedToken = $request->request->get('token');
      if ($this->isCsrfTokenValid('upload-doc', $submittedToken)) {
        $doctitle = $request->request->get('docTitle');
        $docdesc = $request->request->get('docDesc');
        $docfile = $request->files->get('docFile');
        if ($doctitle && $docdesc && $docfile) {
          $originalFilename = pathinfo($docfile->getClientOriginalName(), PATHINFO_FILENAME);
          $safeFilename = $slugger->slug($originalFilename);
          $newFilename = $safeFilename.'-'.uniqid().'.'.$docfile->guessExtension();
          try {
            $docfile->move(
                $this->getParameter('upload_dir'),
                $newFilename
            );
            $newDoc = new Document();
            $newDoc->setFilename($newFilename);
            $newDoc->setDescription($docdesc);
            $newDoc->setTitle($doctitle);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($newDoc);
            $entityManager->flush();
            $response = new Response();
            $response->setStatusCode(302);
            $response->headers->set('Location', '/app/documents');
            return $response;
          } catch (FileException $e) {
            // ... handle exception if something happens during file upload
          }
        }
      }
      return $response;
    }

    /**
    * @Route("/app/documents/get/{id}", name="app_documentget")
    */
    public function getDoc(int $id): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $repository = $this->getDoctrine()->getRepository(Document::class);
        $document = $repository->find($id);

        if ($document) {
          $publicResourcesFolderPath = $this->getParameter('upload_dir');
        $filename = $document->getFilename();

        // This should return the file to the browser as response
        $response = new BinaryFileResponse($publicResourcesFolderPath.$filename);

        // To generate a file download, you need the mimetype of the file
        $mimeTypes = new MimeTypes();
        $mimeType = $mimeTypes->guessMimeType($publicResourcesFolderPath.$filename);
        $response->headers->set('Content-Type', $mimeType);

        // Set content disposition inline of the file
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );
        return $response;
        }
    }

    /**
    * @Route("/app/documents/delete/{id}", name="app_documentdel")
    */
    public function delDoc(int $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $repository = $this->getDoctrine()->getRepository(Document::class);
        $document = $repository->find($id);
        $response = new Response();
        $request = Request::createFromGlobals();

        $submittedToken = $request->query->get('csrf');
        if ($this->isCsrfTokenValid('delete-doc', $submittedToken)) {
          if ($document) {
            $publicResourcesFolderPath = $this->getParameter('upload_dir');
            $filename = $document->getFilename();
            try {
              $filesystem = new Filesystem();
              $filesystem->remove([$publicResourcesFolderPath.$filename]);
              $entityManager = $this->getDoctrine()->getManager();
              $entityManager->remove($document);
              $entityManager->flush();
              $response->setStatusCode(303);
              $response->headers->set('Location','/app/documents');
            } catch (\Exception $e) {
              $response->setStatusCode(500);
              echo $e;
            }
          }
          else {
            $response->setStatusCode(404);
          }
        }
        else {
          $response->setStatusCode(401);
        }
        return $response;
    }
}
