<?php

declare(strict_types=1);

namespace App\lib;

use App\Entity\Application;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileHelper
{

    public static function saveFileOfApplication(
        Application $application,
        ApplicationFile $applicationFileType,
        mixed $fileName,
        SluggerInterface $slugger,
        string $applicationFilesDirectory
    ): bool
    {
        $originalFilename = pathinfo($fileName->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$fileName->guessExtension();

        try {
            // Move the file to the directory where brochures are stored
            $fileName->move($applicationFilesDirectory, $newFilename);
        } catch (FileException $e) {
            return false;
        }

        // Update application entity
        if ($applicationFileType === ApplicationFile::Motivation) {
            $application->setLetterOfMotivation($newFilename);
        } else if ($applicationFileType === ApplicationFile::CV) {
            $application->setCurriculumVitae($newFilename);
        } else {
            return false;
        }

        return true;
    }

}
