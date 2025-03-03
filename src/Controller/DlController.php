<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DlController extends AbstractController
{
    /**
     * Gère le téléchargement de fichiers.
     *
     * Si le fichier est autorisé, il est envoyé pour téléchargement. Sinon, un fichier par défaut est envoyé.
     *
     * Les paramètres sont (fileName: le nom du fichier à télécharger).
     *
     * @param string $fileName : Le nom du fichier demandé par l'utilisateur pour le téléchargement.
     * @return Response La réponse contenant le fichier à télécharger ou un fichier par défaut.
     */
    #[Route('/dl/{fileName}', name: 'app_dl')]
    public function index(string $fileName): Response
    {
        $authorizedFiles=['CGV','MentionsLegalesCocoque','PolitiqueConfidentialiteCocoque'];
        if(in_array($fileName,$authorizedFiles)){
            return $this->file("/home/debian/project-sae/public/files/$fileName.pdf");
        }else{
            return $this->file("/home/debian/project-sae/public/files/datas.webp");
        }
    }

}
