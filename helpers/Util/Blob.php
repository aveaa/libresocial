<?php
namespace Unionity\Maximizer\Util;

class Blob
{
    private $hashedName;
    
    private $filename;
    
    private $destination = NULL;
    
    private $handle;
    
    public  $uploadedName = NULL;
    
    private function computeHash($content)
    {
        return sodium_bin2hex(hash("sha512", $content, true));
    }
    
    private function verifyMIME($mime)
    {
        return (new \Finfo(FILEINFO_MIME_TYPE))->file($this->filename) === $mime;
    }
    
    function getFilename() { return $this->filename; }
    
    function getDest() { return $this->destination; }
    
    function setDest($dest) { $this->destination = $dest; }
    
    function getHandle() { return $this->handle; }
    
    function __construct($file)
    {
        if(gettype($file) === "array") {
            if($file["error"] != 0) throw new \Exception("File upload error. Is input corrupted, or a transmission error occured?");
            $this->filename     = $file["tmp_name"];
            $this->uploadedName = $file["name"];
            if(!$this->verifyMIME($file["type"])) throw new \Exception("File MIME mismatch!");;
        } else {
            $this->filename = $file;
        }
        $this->handle = fopen($this->filename, "r");
        $hash         = $this->computeHash(fread($this->handle, filesize($this->filename)));
        $this->hashedName = $hash.".".array_values(array_slice(explode(".", $this->uploadedName ?? $this->filename), -1))[0];
        fclose($this->handle);
    }
    
    function _move()
    {
        if(!is_null($this->uploadedName))
            move_uploaded_file($this->filename, $this->destination);
        else
            rename($this->filename, $this->destination);
        chmod($this->destination, 0664);
    }
    
    function mv($overrideFilename = NULL)
    {
        $path = "storage/".substr($this->hashedName, 0, 2);
        if(!is_dir($path)) mkdir($path);
        $this->destination = $path."/".($overrideFilename ?? $this->hashedName);
        
        return new BlobVerifier($this);
    }
}
