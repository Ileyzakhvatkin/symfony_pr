<?php

namespace App\Services;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private SluggerInterface $slugger;
    private string $uploadsPath;

    public function __construct(SluggerInterface $slugger, string $uploadsPath)
    {
        $this->slugger = $slugger;
        $this->uploadsPath = $uploadsPath;
    }

    public function uploadFile(UploadedFile $file): string
    {
        $fileName = $this->slugger
            ->slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
            ->append('-' . uniqid())
            ->append('.' . $file->guessExtension())
            ->toString()
        ;

        $file->move($this->uploadsPath, $fileName);

        return $fileName;
    }
}