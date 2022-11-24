<?php

//namespace GenerateThumbnails\Job;
//
//use Omeka\Api\Adapter\MediaAdapter;
//use Omeka\Api\Representation\MediaRepresentation;
//use Omeka\Entity\Media;
//use Omeka\File\Store\Local as LocalStore;
//use Omeka\Job\AbstractJob;

class GenerateThumbnails_Job_Regenerate extends Omeka_Job_AbstractJob
{
    /**
     * @var Omeka_Storage
     */
    protected $_storage;

    /**
     * @var Omeka_File_Derivative_Image_Creator
     */
    protected $_imageCreator;

    /**
     * @var array Valid derivative types
     */
    protected array $_validDerivativeTypes = array('fullsize', 'thumbnail', 'square_thumbnail');

    /**
     * @var array Derivative types to create and their image size constraints
     */
    protected array $_derivatives = array();

    protected const _mimeTypes = array("image/jpeg", "image/gif", "audio/mpeg", "audio/x-wav", "image/x-ms-bmp",
        "video/quicktime", "application/pdf", "audio/x-aiff", "video/mp4", "image/tiff", "text/plain",
        "audio/mp4", "image/png");

    public function __construct(array $options)
    {
        parent::__construct($options);

        // Set the storage.
        $this->_storage = Zend_Registry::get('storage');
        if (!($this->_storage->getAdapter() instanceof Omeka_Storage_Adapter_Filesystem)) {
            throw new Omeka_Storage_Exception(__('The storage adapter is not an instance of Omeka_Storage_Adapter_Filesystem.'));
        }

        // Set the image creator.
        try {
            $this->_imageCreator = Zend_Registry::get('file_derivative_creator');
        } catch (Zend_Exception $e) {
            throw new Omeka_File_Derivative_Exception(__('The ImageMagick directory path is missing.'));
        }

        // Set the configured derivative types and constraints.
        foreach ($this->_validDerivativeTypes as $type) {
            if (!is_array($this->_options['derivative_types'])
                || in_array($type, $this->_options['derivative_types'])
            ) {
                $this->_derivatives[$type] = get_option("{$type}_constraint");
            }
        }
    }

    public function getDb()
    {
        return $this->_db;
    }

    public function perform()
    {
        _log('##### Job GenerateThumbnails_Job_Regenerate started');
        $this->generateThumbnails();
        _log('##### Job GenerateThumbnails_Job_Regenerate completed');
    }

    public function getFileIds() {
        // Fetch file IDs according to the passed options.
        $db = $this->getDb();
        $select = $db->select()->from($db->File, array("id"));

        // only ones without thumbnail
        $select->where('has_derivative_image = 0');

        // only these mime_type
        $select->where('mime_type IN (?)', $this::_mimeTypes);

        return $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
    }

    public function getRecordById($modelName, $recordId) {
        return $this->getDb()->getTable(Inflector::camelize($modelName))->find($recordId);
    }

    protected function generateThumbnail(string $fileId) {
        $file = $this->getRecordById('file', $fileId);

        // Register which image derivatives to create.
        foreach ($this->_derivatives as $type => $constraint) {
            $this->_imageCreator->addDerivative($type, $constraint);
        }

        // Create derivatives.
        try {
            $imageCreated = $this->_imageCreator->create(
                FILES_DIR . '/' . $file->getStoragePath('original'),
                $file->getDerivativeFilename(),
                $file->mime_type
            );
        } catch (Exception $e) {
            _log($e);
            $imageCreated = false;
        }

        // Confirm that the file was derivable.
        if (!$imageCreated) {
            return;
        }

        // Save the file record.
        $file->has_derivative_image = 1;
        $file->save();

        // Delete existing derivative images and replace them with the
        // temporary ones made during image creation above.
        foreach ($this->_derivatives as $type => $constraint) {
            $this->_storage->delete($file->getStoragePath($type));
            $source = FILES_DIR . "/original/{$type}_" . $file->getDerivativeFilename();
            $this->_storage->store($source, $file->getStoragePath($type));
        }

        // Release the file record to prevent memory leaks.
        release_object($file);
    }

    /**
     * Perform this job.
     */
    public function generateThumbnails()
    {
        // Get all File Ids
        $fileIds = $this->getFileIds();

        // Iterate files and create derivatives.
        $fileCounter = 0;
        foreach ($fileIds as $fileId) {
            _log("file " . $fileId);
            $this->generateThumbnail($fileId);
            $fileCounter++;
        }
        _log("Processed " . $fileCounter);
    }
}