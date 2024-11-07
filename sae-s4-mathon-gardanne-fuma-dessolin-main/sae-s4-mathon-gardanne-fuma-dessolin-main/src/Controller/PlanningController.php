<?php

namespace App\Controller;
use App\Repository\RencontreRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlanningController extends AbstractController
{
    #[Route('/planning/{offset}', name: 'app_planning')]
    public function index(RencontreRepository $rencontreRepository ,$offset=0): Response
    {
        $today = date('d-m-y H:i');
        $dayNumber = date('w');
        $mondayOffset= 1-$dayNumber+ $offset*7;

        $monday = date('d-m-y H:i',strtotime("$today +".strval($mondayOffset) ." day"));
        $tuesday= date('d-m-y H:i',strtotime("$today +".strval($mondayOffset+1) ." day"));
        $wednesday= date('d-m-y H:i',strtotime("$today +".strval($mondayOffset+2) ." day"));
        $thursday= date('d-m-y H:i',strtotime("$today +".strval($mondayOffset+3) ." day"));
        $friday= date('d-m-y H:i',strtotime("$today +".strval($mondayOffset+4) ." day"));
        $saturday= date('d-m-y H:i',strtotime("$today +".strval($mondayOffset+5) ." day"));
        $sunday= date('d-m-y H:i',strtotime("$today +".strval($mondayOffset+6) ." day"));
        
        return $this->render('planning/index.html.twig', ["days" => [
                'monday' => $rencontreRepository->findBy(array('date_heure' => date_create_from_format('d-m-y H:i',$monday))),
                'tuesday' => $rencontreRepository->findBy(array('date_heure' => date_create_from_format('d-m-y H:i',$tuesday))),
                'wednesday' => $rencontreRepository->findBy(array('date_heure' => date_create_from_format('d-m-y H:i',$wednesday))),
                'thursday' => $rencontreRepository->findBy(array('date_heure' => date_create_from_format('d-m-y H:i',$thursday))),
                'friday' => $rencontreRepository->findBy(array('date_heure' => date_create_from_format('d-m-y H:i',$friday))),
                'saturday' => $rencontreRepository->findBy(array('date_heure' => date_create_from_format('d-m-y H:i',$saturday))),
                'sunday' => $rencontreRepository->findBy(array('date_heure' => date_create_from_format('d-m-y H:i',$sunday))),
            ]]);
}
}