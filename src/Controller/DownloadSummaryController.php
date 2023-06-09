<?php

namespace App\Controller;

use App\Entity\Rooms;
use App\Helper\JitsiAdminController;
use App\Service\Summary\CreateSummaryService;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class DownloadSummaryController extends JitsiAdminController
{
    #[Route('room/download/sumary', name: 'app_download_sumary')]
    public function index(Request $request, CreateSummaryService $createSummaryService)
    {
        $room = $this->doctrine->getRepository(Rooms::class)->find($request->get('room'));

        if (!$room || $room->getModerator() !== $this->getUser()){
            throw new NotFoundHttpException('Room not found');
        }

        $createSummaryService->createSummaryPdf($room)->stream($room->getName().".pdf", [
            "Attachment" => true
        ]);
    }
}
