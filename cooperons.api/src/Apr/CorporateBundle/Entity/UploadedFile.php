<?php
 
namespace Apr\CorporateBundle\Entity;
 
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="uploaded_files")
 * @ORM\HasLifecycleCallbacks
 */
class UploadedFile
{
 
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
    protected $id;
 
    /**
     * @Assert\Regex(pattern="/^[\w]*$/", message = "Nom de fichier invalide")
     * @ORM\Column(type="string",length=255, nullable=true)
     */
    private $title;
 
    /**
     * @Assert\NotBlank(message = "Fichier requis")
     * @Assert\File(maxSize="2M", maxSizeMessage = "La taille du fichier doit être inférieure à 2 Mo")
     */
    private $file;

    /**
     * @ORM\Column(name="filename" , type="string", length=255)
     */
    private $filename;

    private $dirname;

    /**
     * @ORM\Column(name="date_uploaded" , type="datetime")
     */
    private $uploadedAt;

    public function __construct() {
        $this->uploadedAt = \Apr\CoreBundle\Tools\Tools::DateTime();
    }
 
    /**
     * @return integer
     */
    public function getId() {
        return $this->id;
    }
 
    /**
     * Get uploadedAt
     *
     * @return string 
     */
    public function getUploadedAt() {
        return $this->uploadedAt;
    }
 
    /**
     * Set title
     *
     * @param string $titre
     */
    public function setTitle($title) {
            $this->title = $title;
    }
 
    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle() {
        return $this->title;
    }


    public function loadFile() {
        $filename = $this->getFullFilePath();
        return (file_exists($filename))?new File($filename):null;
    }

    public function getFile() {
        return $this->file;
    }

    public function setFile($file) {
        $this->file = $file;
        if (!is_null($this->filename)) {
            // $fileName = substr($this->filename, strpos($this->filename, "/") + 1);
            if($this->filename != $this->file->getFilename()) {
                // store the old name to delete after the update
                $this->oldFilename = $this->filename;
            }
        }
    }

    public function getFilename() {
        // Avec un timestamp pour forcer le raffraichissement du cache OVH !!!
        return $this->filename.'?'.time();
    }

    public function getFullFilePath() {
        return $this->getUploadRootDir() . $this->filename;
    }
 
    protected function getUploadRootDir() {
        // the absolute directory path where uploaded documents should be saved
        return __DIR__ . '/../../../../web/Uploads/';
    }

    public function uploadFile($filename, $dirname) {
        // the file property can be empty if the field is not required
        if (null === $this->file) {
            return;
        }
        $this->title = $filename;
        $filename .= ".".$this->file->guessClientExtension();
        $this->filename = $dirname."/".$filename;
        $this->dirname = $dirname;
        $this->uploadedAt = \Apr\CoreBundle\Tools\Tools::DateTime();
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload() {
        if(is_null($this->file)) return;

        $uploadRootDir = $this->getUploadRootDir().$this->dirname."/";
        if(isset($this->oldFilename)) {
            $oldFilename = str_replace($this->dirname."/", "", $this->oldFilename);
            if(file_exists($uploadRootDir.$oldFilename)) {
                unlink($uploadRootDir.$oldFilename);
            }
            $this->oldFilename = null;
        }

        $fileName = str_replace($this->dirname."/", "", $this->filename);
        $this->file->move($uploadRootDir, $fileName);

    }

    /**
     * @ORM\PreRemove()
     */
    public function removeFile() {
        if(file_exists($this->getFullFilePath())) {
            unlink($this->getFullFilePath());
        }
    }
    
    public function hasChanged() {
        return isset($this->oldFilename) &&($this->file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile);
    }
}