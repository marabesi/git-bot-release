<?php
declare(strict_types=1);

namespace App\Domain\Gitlab\Entity;

class File
{

    private string $id;
    private string $name;
    private string $path;
    private string $content;

    public function __construct(string $id, string $name, string $path, string $content)
    {
        $this->id = $id;
        $this->name = $name;
        $this->path = $path;
        $this->content = $content;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): File
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function  urlEncodePath(): string
    {
        return str_replace('/', '|', $this->path);
    }
}