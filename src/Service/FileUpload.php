<?php

namespace App\Service;


use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUpload
{
    private string $destinationPath;
    private SluggerInterface $slugger;
    private LoggerInterface $logger;
    private string $fileName;

    public function getFileName() : string
    {
        return $this->fileName;
    }

    public function setFileName($fileName): void
    {
        $this->fileName = $fileName;
    }
    public function __construct(SluggerInterface $slugger,LoggerInterface $logger)
    {
        $this->slugger = $slugger;
        $this->logger = $logger;
    }

    public function upload(UploadedFile $file): bool
    {
        $originalFileName =  pathinfo($file->getClientOriginalName(),PATHINFO_FILENAME);
        $safeFileName = $this->slugger->slug($originalFileName);
        $fileName = substr(uniqid().$safeFileName,0,190).".".$file->guessExtension();

        try {
            $file->move($this->destinationPath,$fileName);
            $this->setFileName($fileName);
        }catch (\Exception $e)
        {
            $this->logger->error($e->getMessage());
            return false;
        }
        return  true;
    }

    public function validate(UploadedFile $file,$allowedExtentions = [],$allowedSizeInMB = 20): bool
    {
        if(!$this->isFileExtensionAllowed($file->guessClientExtension(),$allowedExtentions))
        {
            return false;
        }

        if(!$this->isValidFileSize($file->getSize(),$allowedSizeInMB))
        {
            return  false;
        }

        return true;
    }

    public function setDestinationPath($path): void
    {
        $this->destinationPath = $path;
    }

    public function isFileExtensionAllowed($extension,$allowedExtensions): bool
    {
        return in_array($extension,$allowedExtensions);
    }

    public function isValidFileSize($uploadedFileSize,$allowedSizeInMB): bool
    {
        $uploadedFileSize = $uploadedFileSize / (102 * 1024); //Convert size from Bytes to MB
        return $uploadedFileSize <= $allowedSizeInMB;
    }

}