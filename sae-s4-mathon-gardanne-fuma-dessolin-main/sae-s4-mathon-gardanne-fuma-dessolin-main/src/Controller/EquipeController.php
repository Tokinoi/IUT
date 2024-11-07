<?php

namespace App\Controller;

use App\Entity\Equipe;
use App\Form\EquipeType;
use App\Repository\EquipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/equipe')]
class EquipeController extends AbstractController
{
    #[Route('/', name: 'app_equipe_index', methods: ['GET'])]
    public function index(EquipeRepository $equipeRepository): Response
    {
        return $this->render('equipe/index.html.twig', [
            'equipes' => $equipeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_equipe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EquipeRepository $equipeRepository): Response
    {
        $equipe = new Equipe();
        $form = $this->createForm(EquipeType::class, $equipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            //Get the image 
            $url_photo = $form->get('url_photo')->getData();
            $url_result = $form->get('url_result_calendrier')->getData();
            //Copy the image
            $path= $this->getParameter('kernel.project_dir');
            $photo_relatif = "/public/image/".explode("/",$url_photo)[2]; 
            $result_relatif = "/public/image/".explode("/",$url_result)[2];
            $url_photo_new_place = $path.$photo_relatif; 
            $url_result_new_place = $path.$result_relatif; 
            copy($url_photo,$url_photo_new_place);
            copy($url_result,$url_result_new_place);
            //Set the image in $equipe 
            $data->setUrlPhoto($photo_relatif);
            $data->setUrlResultCalendrier($result_relatif);

            $equipeRepository->save($equipe, true);

            return $this->redirectToRoute('app_equipe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('equipe/new.html.twig', [
            'equipe' => $equipe,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_equipe_show', methods: ['GET'])]
    public function show(Equipe $equipe): Response
    {
        $photo= $equipe->getUrlPhoto();
        $resultat = $equipe->getUrlResultCalendrier();
        return $this->render('equipe/show.html.twig', [
            'resultat' => $resultat,
            'photo' => $photo,
            'equipe' => $equipe,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_equipe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Equipe $equipe, EquipeRepository $equipeRepository): Response
    {
        $form = $this->createForm(EquipeType::class, $equipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            //Get the image 
            $url_photo = $form->get('url_photo')->getData();
            $url_result = $form->get('url_result_calendrier')->getData();
            //Copy the image
            $path= $this->getParameter('kernel.project_dir');
            if($url_photo){

            $tpm= explode("/",$url_photo); 
            $photo_relatif = "/public/image/".end($tpm); 
            $url_photo_new_place = $path.$photo_relatif;           
            copy($url_photo,$url_photo_new_place);
            $data->setUrlPhoto($photo_relatif);
            }

            if($url_result){
            $tpm= explode("/",$url_result); 
            $result_relatif = "/public/image/".end($tpm);
            $url_result_new_place = $path.$result_relatif;
            copy($url_result,$url_result_new_place);
            $data->setUrlResultCalendrier($result_relatif);
            }

            $equipeRepository->save($equipe, true);

            return $this->redirectToRoute('app_equipe_index', [], Response::HTTP_SEE_OTHER);
        }
        $path= $this->getParameter('kernel.project_dir');
        $url_photo = $equipe->getUrlPhoto();
        $photo = new File($path.$url_photo);
        $url_result = $equipe->getUrlResultCalendrier();
        $resultat = new File($path.$url_result);
        return $this->renderForm('equipe/edit.html.twig', [
            'resultat' => $resultat,
            'photo' => $photo,
            'equipe' => $equipe,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_equipe_delete', methods: ['POST'])]
    public function delete(Request $request, Equipe $equipe, EquipeRepository $equipeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$equipe->getId(), $request->request->get('_token'))) {
            $equipeRepository->remove($equipe, true);
        }

        return $this->redirectToRoute('app_equipe_index', [], Response::HTTP_SEE_OTHER);
    }
}
