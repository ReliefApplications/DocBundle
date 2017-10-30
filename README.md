[![Latest Stable Version](https://poser.pugx.org/relief_applications/doc-manager-bundle/v/stable)](https://packagist.org/packages/relief_applications/doc-manager-bundle)
[![Total Downloads](https://poser.pugx.org/relief_applications/doc-manager-bundle/downloads)](https://packagist.org/packages/relief_applications/doc-manager-bundle)
[![Latest Unstable Version](https://poser.pugx.org/relief_applications/doc-manager-bundle/v/unstable)](https://packagist.org/packages/relief_applications/doc-manager-bundle)
[![License](https://poser.pugx.org/relief_applications/doc-manager-bundle/license)](https://packagist.org/packages/relief_applications/doc-manager-bundle)

DocBundle
==============

Step 1: Download the Bundle
---------------------------


Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require relief_applications/doc-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

For more informations about the bundle and its dependencies, please visit our packagist page : [https://packagist.org/packages/relief_applications/doc-bundle](https://packagist.org/packages/relief_applications/doc-bundle) .

Step 2: Enable the Bundle
-------------------------

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new RA\DocBundle\RADocBundle(),
        );

        // ...
    }

    // ...
}
```

Step 3: Usage
-------------------------

1. Create a class which extends Ra\DocBundle\Model\BaseDocument

    ```php
    <?php
    /**
    * @ORM\Table(name="document")
    * @ORM\Entity(repositoryClass="...")
    **/
    class Document extends BaseDocument {
        //...
        public function __construct()
        {
            parent::__construct();
            // your work
        }
        //...
    }

    ```

2. Uploading and creating a document

    ```php
    <?php

    public function createAction(Request $request){
        //build Document
        $document   = new Document();
        $file       = $request->files->get('file');

        try {
            $this->get('ra_doc.transfert')->createDocument($document, $file);
        } catch (DocumentException $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

    ```

3. Uploading and updating a document

    ```php
    <?php

    public function updateAction(Request $request, Document $document) {
        $name       = $request->request->get('name');
        $file       = $request->files->get('file'); //doesn't care if the file is empty or not
        //if the file is empty, there won't be any upload

        //changing metaname
        $document->getDocumentMeta()->setName($name);

        try {
            $this->get('ra_doc.transfert')->updateDocument($document, $file);
        } catch (DocumentException $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    ```

4. Downloading a document
    ```php
    <?php
    public function downloadAction(Request $request, Document $document)
    {
        try {
            return $this->get('ra_doc.transfert')->download($document);
        } catch (DocumentException $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
    ```

5. Removing a document
    ```php
    <?php
    public function deleteAction(Request $request, Document $document)
    {
        try{
            $this->get('ra_doc.transfert')->deleteDocument($document);
        } catch (DocumentException $e) {
            return new Response($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return new Response();
    }

    ```

6. Get document's path in the filesystem
    ```php
    <?php
    try{
        $path = $this->get('ra_doc.transfert')->getFilePath($document);
    } catch (DocumentException $e) {
        //...
    }

    ```

Step 4: Configuration
-------------------------

The configuration of Vich, Gaufrette and Liip is the responsability of the developer.

Step * : Customization
-------------------------
1.
You can change the default file's field by adding a custom field and declaring this field :

```php
<?php

//...
use RA\DocBundle\Model\BaseDocument;
use RA\DocBundle\Model\Limits;
use Symfony\Component\HttpFoundation\File\File;
//...

class Document extends BaseDocument {
    //...
    /**
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
     * @var File $customFile
     */
    protected $customFile;

    //...

    //overrides inherited function
    public function getFileField()
    {
        return 'customFile';
    }

    //overrides inherited function
    public function getFile()
    {
        return $this->getCustomFile();
    }

    //overrides inherited function
    public function setFile(\Symfony\Component\HttpFoundation\File\UploadedFile $file)
    {
        parent::setFile($file);
        $this->setCustomFile($file);
    }

}
```


You can also define your custom requirements in the Assert\File annotation.
