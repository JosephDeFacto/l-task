<?php

namespace App\Services\Uploader;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @method getParameter(string $string)
 */
class FileUploader
{
    private string $targetDirectory;

    private ContainerInterface $container;

    private string $filename;

    private array $data = [];

    private array $attributes = ['fullName', 'distance', 'time'];

    public function __construct($targetDirectory, ContainerInterface $container)
    {
        $this->targetDirectory = $targetDirectory;
        $this->container = $container;
    }

    /**
     * @param $file
     * @return string
     */
    public function upload($file): string
    {

        $this->filename = md5(uniqid()) . '.' . $file->guessClientExtension();

        $file->move(
            $this->container->getParameter('uploads_directory'),
            $this->filename
        );

        return $this->filename;
    }

    /**
     * @return false|string
     */
    public function read()
    {
        return file_get_contents($this->container->getParameter('uploads_directory') . '/' . $this->filename);
    }

    /**
     * @return array
     */
    public function parse(): array
    {
        $read = $this->read();

        $lines = explode("\n", $read);

        $data = [];
        foreach ($lines as $line) {
            $data[] = str_getcsv($line);
        }

        foreach ($data as $row) {

            if (count($row) != 3) {
                continue;
            }

            foreach ($this->attributes as $attribute) {
                if (in_array($attribute, $row)) {
                    continue 2;
                }
            }

            $this->data[] = $row;

        }

        return $this->data;

    }

   /**
    * @param UploadedFile $file
    * @return bool
    */
    public function delete(): bool
    {

        return unlink($this->container->getParameter('uploads_directory') . '/' . $this->filename);
    }

    /**
     * @return mixed
     */
    private function getTargetDirectory()
    {
        return $this->targetDirectory;
    }

    /**
     * @return string
     */
    private function getFilename()
    {
        return $this->filename;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return $this->data;
    }

    public function getData()
    {
        return $this->data;
    }
}