<?php

namespace RA\DocBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\MappedSuperclass;

use RA\DocBundle\Model\Limits;
use RA\DocBundle\Model\DocumentInterface;

use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\VirtualProperty;

/**
 * BaseDocument
 *
 * @MappedSuperclass
 * @Vich\Uploadable
 */
class BaseDocument implements DocumentInterface
{

    /**
     * @ORM\Column(type="string", length=255, name="name")
     *
     * @var string $name
     * @expose
     * @Groups({"FullDocument"})
     */
    protected $name;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Assert\NotNull(
     *     message = "The file is required"
     * )
     * @Assert\File(
     *     maxSize= Limits::max_document_size,
     *     mimeTypes={"application/pdf", "application/x-pdf",
     *         "application/vnd.ms-excel", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
     *         "image/jpeg", "image/png",
     *         "application/vnd.ms-powerpoint", "application/vnd.openxmlformats-officedocument.presentationml.presentation",
     *         "application/msword", "application/vnd.openxmlformats-officedocument.wordprocessingml.document","application/vnd.ms-powerpoint.addin.macroEnabled.12",
     *         "application/vnd.openxmlformats-officedocument.presentationml.template", "application/vnd.openxmlformats-officedocument.presentationml.slideshow",
     *         "text/plain", "image/pjpeg","application/vnd.oasis.opendocument.spreadsheet"
     *     },
     *     mimeTypesMessage="ra.document.type-of-file-is-invalid"
     * )
     * @Vich\UploadableField(mapping="upload_file", fileNameProperty="name")
     *
     * @var File $file
     */
    protected $file;

    /**
     * @ORM\Embedded(class="Vich\UploaderBundle\Entity\File")
     *
     * @var EmbeddedFile
     */
    protected $documentMeta;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    protected $updatedAt;

    public function __construct()
    {
        $this->name = "";
        $this->documentMeta = null;
        $this->updatedAt = new \DateTime('now');
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set Name
     * @param string $name
     */
    public function setName(string $name = null)
    {
        if( ! is_null($name))
            $this->name = $name;

        return $this;
    }

    /**
     * Return the extension of the file
     *
     */
    public function getExtension()
    {
        return strtolower($this->file->getExtension());
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $file
     */
    public function setFile(File $file)
    {
        $this->file = $file;
        $this->size = $this->file->getSize();

        if ($file) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }

    /**
     * UV4d for the fixtures
     *
     *  @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param EmbeddedFile $documentMeta
     */
    public function setDocumentMeta(EmbeddedFile $documentMeta)
    {
        $this->documentMeta = $documentMeta;

        return $this;
    }

    /**
     * @return EmbeddedFile
     */
    public function getDocumentMeta()
    {
        return $this->documentMeta;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return BaseDocument
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function getFileField()
    {
        return 'file';
    }
}
