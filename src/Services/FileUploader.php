<?php

namespace App\Services;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private SluggerInterface $slugger;
    private string $uploadsPath;
    private Filesystem $filesystem;

    public function __construct(SluggerInterface $slugger, string $uploadsPath, Filesystem $filesystem)
    {
        $this->slugger = $slugger;
        $this->uploadsPath = $uploadsPath;
        $this->filesystem = $filesystem;
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

    public function uploadFileUrl($fileUrl): string
    {
        $file = ''; // Тут должна быть загрузка файла
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