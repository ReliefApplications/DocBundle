<?php

namespace RA\DocBundle\Model;

//Dependencies
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Entity\File as VichFile;

use RA\DocBundle\Model\BaseDocument as Document;
use RA\DocBundle\Model\DocumentException;
use RA\DocBundle\Model\DocumentInterface;


class DocumentService
{
    private $container;
    private $em;

    public function __construct(Container $container, EntityManager $em){
        $this->container = $container;
        $this->sr = $container->get('serializer');
        $this->securityContext = $container->get('security.token_storage');
        $this->em = $em;
    }

    /**
     * Save the document to the database and trigger the upload
     * warning : the persist action trigger the upload.
     * @param  Document     $document
     * @param  UploadedFile $file
     * @return Document                 The document saved with non null id.
     * @throws DocumentException  If the validation fails
     */
    public function createDocument(Document $document, UploadedFile $file = null)
    {
        if( ! is_null($file) ){
            $document->setFile($file);
            $this->setMetadata($document, $file);
            $this->validate($document);
        }

        $this->em->persist($document);
        $this->em->flush();

        return $document;
    }

    /**
     * Update the document
     * @param  Document     $document [description]
     * @param  UploadedFile $file     [description]
     * @return Document                 The document saved with non null id.
     * @throws DocumentException  If the validation fails
     */
    public function updateDocument(Document $document, UploadedFile $file = null)
    {
        if( ! is_null($file) ){
            $document->setFile($file);
            $this->setMetadata($document, $file);
            $this->validate($document);
        }

        $this->em->persist($document);
        $this->em->flush();

        return $document;
    }

    /**
     * Deletes a document
     * @param  Document $document
     * @throws If the given document's class doesn't implement DocumentInterface
     */
    public function deleteDocument(Document $document)
    {
        if( ! ($document instanceOf DocumentInterface) ){
            throw new DocumentException([], "Document doesn't implements DocumentInterface", 1);
        }

        $this->em->remove($document);
        $this->em->flush();
    }

    /**
     * Upload document
     * @param  Document $document [description]
     * @throws If the given document's class doesn't implement DocumentInterface
     */
    public function upload(Document $document)
    {
        if( ! ($document instanceOf DocumentInterface) ){
            throw new DocumentException([], "Document doesn't implements DocumentInterface", 1);
        }

        $this->get('vich_uploader.upload_handler')->upload($document, $document->getFileField());
    }

    /**
     * Download document
     * @param  Document $document
     * @return bytes
     * @throws If the given document's class doesn't implement DocumentInterface
     */
    public function download(Document $document)
    {
        if( ! ($document instanceOf DocumentInterface) ){
            throw new DocumentException([], "Document doesn't implements DocumentInterface", 1);
        }

        $handler = $this->get('vich_uploader.download_handler');

        if( ! is_null($document->getDocumentMeta()))
            $file = $handler->downloadObject($document, $document->getFileField(), null, $document->getDocumentMeta()->getOriginalName());
        else
            $file = $handler->downloadObject($document, $document->getFileField());

        return $file;
    }

    /**
     * Set metadata of the given document using uploadFiled informations
     * @param Document     $document
     * @param UploadedFile $file
     */
    public function setMetadata(Document &$document, UploadedFile $file)
    {
        $meta = $document->getDocumentMeta() ?: new VichFile();

        $meta->setOriginalName($file->getClientOriginalName());
        $meta->setSize($file->getSize());
        $meta->setMimeType($file->getMimeType());
        $meta->setName($file->getFilename());
        $document->setDocumentMeta($meta);
    }

    /**
     * Validate a document using symfony validator
     * @param  Document $document The file to validate
     * @return boolean            True if the validation didn't raise any exception
     * @throws DocumentException  If the validation fails
     */
    public function validate(Document $document)
    {
        $violations = $this->container->get('validator')->validate($document);
        if(count($violations) > 0) {
            $errors = [];
            foreach ($violations as $key => $violation) {
                $errors[] = $violation->getMessage();
            }
            throw new DocumentException($errors, "Error Validating Document : ".implode(',', $errors), 400);
        }
        return true;
    }



}
