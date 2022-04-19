<?php

namespace App\Service;


use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUpload
{
    private $destinationPath;
    private $slugger;
    private $logger;
    public function __construct(SluggerInterface $slugger,LoggerInterface $logger)
    {
        $this->slugger = $slugger;
        $this->logger = $logger;
    }

    public function upload(UploadedFile $file)
    {
        $originalFileName =  pathinfo($file->getClientOriginalName(),PATHINFO_FILENAME);
        $safeFileName = $this->slugger->slug($originalFileName);
        $fileName = substr(uniqid().$safeFileName,0,190).".".$file->guessExtension();

        try {
            $file->move($this->destinationPath,$fileName);
        }catch (\Exception $e)
        {
            $this->logger->error($e->getMessage());
            return false;
        }
        return  $fileName;
    }

    public function setDestinationPath($path)
    {
        $this->destinationPath = $path;
    }

    public function isFileExtensionAllowed($extension,$allowedExtensions)
    {
        return in_array($extension,$allowedExtensions);
    }

    public function isValidFileSize($uploadedFileSize,$allowedSizeInMB = 20)
    {
        $uploadedFileSize = $uploadedFileSize / (102 * 1024); //Convert size from Bytes to MB
        return $uploadedFileSize <= $allowedSizeInMB;
    }

}