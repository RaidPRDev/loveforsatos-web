<?php

class PHOTOS
{
    private $db;
    public $target_dir;
    private $upload_max_size;
    private $upload_success;
    private $upload_error;
    private $upload_logger;

    private $image_types = array(
        'gif',
        'jpeg',
        'png'
    );

	function __construct($DB_con, $upload_dir)
	{
     	$this->db = $DB_con;
        $this->target_dir = $upload_dir;        // "../images/photos/";
        $this->upload_success = false;
        $this->upload_error = '';
        $this->upload_logger = array();         // declaring note PHP 5.4+
        $this->upload_max_size = 25000000;    // 25MB ( number * 1 million )
	}

    public function trace($msg)
	{
		echo "Logger: [ " . $msg . "]";
	}

	public function convertHashToThumb($hashFile, $ext)
    {
        if (strrpos($ext, ".") === false) $ext = "." . $ext;

        $hash = substr($hashFile, 0, count($hashFile) - (strlen($ext) + 1));

        return $hash . "_thumb" . $ext;
    }

    public function convertHashToFull($hashFile, $ext)
    {
        if (strrpos($ext, ".") === false) $ext = "." . $ext;

        $hash = substr($hashFile, 0, count($hashFile) - (strlen($ext) + 1));

        return $hash . "_full" . $ext;
    }

    public function addPrefixPathToTargetDir($dir)
    {
        $target_dir = $dir;
        if (strpos($target_dir, '../') == false)
        {
            $target_dir = '../' . $target_dir;
        }

        $targetPath = realpath($target_dir) . '/';

        return $targetPath;
    }

	public function checkIfUploadFolderExists($addPrefix = true)
    {
        if ($addPrefix) $targetPath = $this->addPrefixPathToTargetDir($this->target_dir);
        else $targetPath = $this->target_dir;

        return (is_dir($targetPath) && is_writable($targetPath));
    }

    public function uploadPhotos($files, $addPrefixPath)
    {
        /*
        $upload_success – becomes "true" or "false" if upload was unsuccessful;
        $upload_error – an error message of if upload was unsuccessful;
        */

        $this->upload_success = true;
        $targetFile = '';
        $imageSize = null;
        $imageFileType = null;
        $imgWidth = null;
        $imgHeight = null;

        $fileNames = $files["files"]["name"];
        $fileTmpNames = $files["files"]["tmp_name"];
        $fileSizes = $files["files"]["size"];

        $fileNamesLen = count($fileNames);
        $fileTmpNamesLen = count($fileTmpNames);

        // saved hash file names
        $maskedFiles = array();

        // on iOS 11.4.1 - for whatever reason we get duplicate files
        // we will use this to double check that we will only upload 1 file
        $duplicates = array();

        // valid extensions
        $validExtensions = array('jpeg', 'jpg', 'png', 'gif');

        for ($x = 0; $x < $fileNamesLen; $x++) {

            if (empty($fileTmpNames[$x])) continue;

            // on iOS 11.4.1 - for whatever reason we get duplicate files
            // we will use this to double check that we will only upload 1 file
            if ($duplicates[$fileNames[$x]])
            {
                unset($fileNames[$x]);
                unset($fileTmpNames[$x]);
                unset($fileSizes[$x]);

                // skip this file
                continue;
            }

            // check if file has been already uploaded
            if(is_uploaded_file($fileTmpNames[$x]) )
            {

            }

            // validate file is an image
            $imageSize = getimagesize($fileTmpNames[$x]);

            // get image width and height
            $imgTmpData = ImageCreateFromJpeg($fileTmpNames[$x]);
            $imgWidth = ImageSX($imgTmpData);
            $imgHeight = ImageSY($imgTmpData);

            // check if image meets requirement size
            $imgTooSmall = false;
            if ($imgWidth < 720 || $imgHeight < 720) $imgTooSmall = true;

            // clear img data
            $imgTmpData = null;

            // check if image
            if ($imageSize !== false && !$imgTooSmall) {
                $this->upload_success = true;

                // set target specs
                $targetFile = $this->target_dir . basename($fileNames[$x]);
                $imageFileType = strtolower(pathinfo($targetFile,PATHINFO_EXTENSION));

                // create hash file
                $tempHashFile = MD5(uniqid(date("Y/m/d h:i:s")));
                $tempLength = strlen($tempHashFile);
                $tempHashFile = substr($tempHashFile, $tempLength / 2);
                $maskTargetFile = $tempHashFile . "." . $imageFileType;
                $maskedFiles[] = $maskTargetFile;

                // check if file already exists
                if (file_exists($targetFile)) {
                    $upload_success = false;
                    $upload_error = "Sorry, file already exists.";
                    $uploadLogger[] = $upload_error;
                }

                // check file size
                if ($fileSizes[$x] > $this->upload_max_size) {
                    $this->upload_success = false;
                    $this->upload_error = "Sorry, your file is too large.";
                    $this->upload_logger[] = $this->upload_error;
                }

                if (!in_array($imageFileType, $validExtensions))
                {

                }

                // allow certain file formats
                if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                    && $imageFileType != "gif" ) {
                    $this->upload_success = false;
                    $this->upload_error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                    $this->upload_logger[] = $this->upload_error;
                }

                // if all goes well start upload
                if ($this->upload_success)
                {
                    // "/tmp/phpSk1Qh7", "uploads/Icon-167.png"
                    if (@move_uploaded_file($fileTmpNames[$x], $addPrefixPath . $this->target_dir . $maskTargetFile)) {
                        // the file has been uploaded.
                        $successMsg = "The file ". $fileNames[$x]. " has been uploaded.";
                        $this->upload_logger[] = $successMsg;

                        // save this file to double check for possible duplicates
                        $duplicates[$fileNames[$x]] = $fileNames[$x];

                    } else {
                        $this->upload_success = false;
                        $fileError = $this->target_dir . $maskTargetFile;//"There was an error uploading your file";
                        $this->upload_error = $fileError;
                        $this->upload_logger[] = $fileError;
                    }
                }
            }
            else
            {
                // check if our image does not meet requirement
                // if so, show error
                if ($imgTooSmall)
                {
                    $this->upload_success = false;
                    $this->upload_error = "The image is too small. Please use an image with at least 720px";
                    $this->upload_logger[] = $this->upload_error;
                }
                else
                {
                    // not an image, do something...
                    $this->upload_success = false;
                    $this->upload_error = "Sorry, this does not seem to be an image.";
                    $this->upload_logger[] = $this->upload_error;
                }
            }
        }

        // return data as array
        return array(
            "success" => $this->upload_success,
            "error" => $this->upload_error,
            "uploadLogger" => $this->upload_logger,
            "imageFileType" => $imageFileType,
            "maskedFiles" => $maskedFiles,
            "fileNames" => $fileNames,
            "fileTmpNames" => $fileTmpNames,
            "fileSizes" => $fileSizes,
            "fileCount" => $fileNamesLen,
            "imageSize" => $imageSize,
            "maxSize" => $this->upload_max_size,
            "targetFile" => $targetFile,
            "imgWidth," => $imgWidth,
            "imgHeight" => $imgHeight
        );
    }

    public function uploadPhotosV2($files)
    {
        $fileNames = $files["files"]["name"];
        $fileTmpNames = $files["files"]["tmp_name"];
        $fileSizes = $files["files"]["size"];

        $fileTmpNamesLen = count($fileTmpNames);

        // valid extensions
        $valid_exts = array('jpeg', 'jpg', 'png', 'gif');
        $max_size = 80000 * 1024; // max file size in bytes

        if ( $_SERVER['REQUEST_METHOD'] === 'POST' )
        {

            for ($i = 0; $i < $fileTmpNamesLen; $i++)
            {
                $path = $this->target_dir;

                if ( is_uploaded_file($fileTmpNames[$i]) )
                {
                    $ext = strtolower(pathinfo($fileNames[$i], PATHINFO_EXTENSION));

                    if (in_array($ext, $valid_exts) AND $fileSizes[$i] < $max_size)
                    {
                        // unique file path
                        $uid = uniqid();
                        $date = date('Y-m-d-H-i-s');
                        $path = $path ."image_" .$date. '_' . $uid . "." .$ext;

                        $filename = "image_" . $date . "_" .$uid . "." . $ext;
                        // $this->createthumb($i,$filename);

                        // move uploaded file from temp to uploads directory
                        if (move_uploaded_file($fileTmpNames[$i], $path))
                        {
                            //$status = 'Image successfully uploaded!';
                            //perform sql updates here
                        }
                        else {
                            $status = 'Upload Fail: Unknown error occurred!';
                        }
                    }
                }
            }
        }
    }

    public function processImage()
    {
        // convert uploaded image into full and thumb images
    }

    public function addPhoto($itemInfo)
    {
        if (empty($itemInfo['description'])) $itemInfo['description'] = "";
        if (empty($itemInfo['full_image_url'])) $itemInfo['full_image_url'] = "";
        if (empty($itemInfo['thumb_image_url'])) $itemInfo['thumb_image_url'] = "";

        $updated = date("Y/m/d h:i:s");

        $db_statement = "INSERT INTO photos(";

        $db_statement .= "dog_id, ";
        $db_statement .= "description, ";
        $db_statement .= "full_image_url, ";
        $db_statement .= "thumb_image_url, ";
        $db_statement .= "original_image_url, ";
        $db_statement .= "updated) ";

        $db_statement .= "VALUES(";
        $db_statement .= ":dog_id, ";
        $db_statement .= ":description, ";
        $db_statement .= ":full_image_url, ";
        $db_statement .= ":thumb_image_url, ";
        $db_statement .= ":original_image_url, ";
        $db_statement .= ":updated) ";

        try
        {
            $stmt = $this->db->prepare($db_statement);

            $stmt->bindValue(':dog_id', $itemInfo['dog_id'], PDO::PARAM_INT);
            $stmt->bindValue(':description', $itemInfo['description'], PDO::PARAM_STR);
            $stmt->bindValue(':full_image_url', $itemInfo['full_image_url'], PDO::PARAM_STR);
            $stmt->bindValue(':thumb_image_url', $itemInfo['thumb_image_url'], PDO::PARAM_STR);
            $stmt->bindValue(':original_image_url', $itemInfo['original_image_url'], PDO::PARAM_STR);
            $stmt->bindValue(':updated', $updated, PDO::PARAM_STR);

            if ($stmt->execute())
            {
                $response["success"] = true;

                // save the new insert ID
                $response["lastInsertId"] = $this->db->lastInsertId();
            }
            else
            {
                $response["success"] = false;
                $response["error"] = $stmt->errorInfo();
            }

            return $response;
        }
        catch(PDOException $e)
        {
            $response["success"] = false;
            $response["error"] = $e->getMessage();
            return $response;
        }
    }

    public function getPhotosByItemID($itemID)
    {
        $db_statement = "SELECT ";
        $db_statement .= "id, ";
        $db_statement .= "dog_id, ";
        $db_statement .= "description, ";
        $db_statement .= "full_image_url, ";
        $db_statement .= "thumb_image_url, ";
        $db_statement .= "original_image_url ";
        $db_statement .= "FROM photos ";
        $db_statement .= "WHERE dog_id = :itemID";

        try
        {
            $stmt = $this->db->prepare($db_statement);

            $stmt->bindValue(':itemID', $itemID, PDO::PARAM_INT);

            if ($stmt->execute())
            {
                $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $response["success"] = true;
                $response["items"] = $photos;
            }
            else
            {
                $response["success"] = false;
                $response["error"] = $stmt->errorInfo();
            }
   }
        catch(PDOException $e)
        {
            $response["success"] = false;
            $response["error"] = $e->getMessage();
        }

        return $response;
    }

    public function getPhotoByID($photoID)
    {
        $db_statement = "SELECT ";
        $db_statement .= "id, ";
        $db_statement .= "dog_id, ";
        $db_statement .= "description, ";
        $db_statement .= "full_image_url, ";
        $db_statement .= "thumb_image_url, ";
        $db_statement .= "original_image_url ";
        $db_statement .= "FROM photos ";
        $db_statement .= "WHERE id = :photoID";

        try
        {
            $stmt = $this->db->prepare($db_statement);

            $stmt->bindValue(':photoID', $photoID, PDO::PARAM_INT);

            if ($stmt->execute())
            {
                $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($photos) > 0)
                {
                    $response["success"] = true;
                    $response["photoData"] = $photos[0];
                }
                else
                {
                    $response["success"] = false;
                    $response["error"] = "No photo data found. ID: $photoID";
                }
            }
            else
            {
                $response["success"] = false;
                $response["error"] = $stmt->errorInfo();
            }
        }
        catch(PDOException $e)
        {
            $response["success"] = false;
            $response["error"] = $e->getMessage();
        }

        return $response;
    }

    public function removePhotoImageByName($imageFilename, $debug = false, $addPrefixPath = '')
    {
        $response = array();
        $response["success"] = true;

        $targetPath = realpath($addPrefixPath . $this->target_dir) . '/';

        $targetLink = $targetPath . $imageFilename;

        if ($debug)
        {
            highlight_string( 'imageFilename: ' . $imageFilename . PHP_EOL);
            highlight_string( 'targetPath: ' . $targetPath . PHP_EOL);
            highlight_string( 'targetLink: ' . $targetLink . PHP_EOL);
        }

        if (!file_exists($targetPath)) {
            $response["success"] = false;
            $response["error"] = "Path does not exist.\n$targetPath";
        }

        if (!file_exists($targetLink)) {
            $response["success"] = false;
            $response["error"] = "File does not exist.\n$targetLink";
        }

        if (!is_writable($targetPath))
        {
            $response["success"] = false;
            $response["error"] = "Could not remove file.\nCheck file permissions.\n$targetPath";
        }

        if ($response["success"])
        {
            unlink($targetLink);
        }

        return $response;
    }

    public function removePhotoByItemID($itemID)
    {
        try
        {
            $stmt = $this->db->prepare("DELETE FROM photos   
				WHERE dog_id = :itemID");

            $stmt->bindValue(':itemID', $itemID, PDO::PARAM_INT);

            if ($stmt->execute())
            {
                // echo 'success: ' . $itemID . PHP_EOL;
                $response["success"] = true;
            }
            else
            {
                $response["success"] = false;
                $response["error"] = $stmt->errorInfo();
            }
        }
        catch(PDOException $e)
        {
            $response["success"] = false;
            $response["error"] = $e->getMessage();
        }

        return $response;
    }

    public function removePhotoByID($photoID)
    {
        try
        {
            $stmt = $this->db->prepare("DELETE FROM photos   
				WHERE id = :photoID");

            $stmt->bindValue(':photoID', $photoID, PDO::PARAM_INT);

            if ($stmt->execute())
            {
                // echo 'success: ' . $photoID . PHP_EOL;
                $response["success"] = true;
            }
            else
            {
                $response["success"] = false;
                $response["error"] = $stmt->errorInfo();
            }
        }
        catch(PDOException $e)
        {
            $response["success"] = false;
            $response["error"] = $e->getMessage();
        }

        return $response;
    }

    // takes in an array of removed photos
    // param: itemID
    // param: removedPhotoList
    public function removePhotosFromListByID($removedPhotoList, $addPrefixPath = '')
    {
        $result = true;
        $error = '';
        $response = null;
        $id = 0;

        // iterate through removed photo list and remove
        $photoItemData = null;
        $photoListLen = count($removedPhotoList);
        for ($index = 0; $index < $photoListLen; $index++)
        {
            $photoResult = $this->getPhotoByID($removedPhotoList[$index]);

            $id = $removedPhotoList[$index];

            if ($photoResult['success'])
            {
                $photoItemData = $photoResult['photoData'];

                $removeFullImage = $this->removePhotoImageByName($photoItemData['full_image_url'], false, $addPrefixPath);
                if (!$removeFullImage['success'])
                {
                    $result = false;
                    $error = $removeFullImage['error'];
                }

                $removeThumbImage = $this->removePhotoImageByName($photoItemData['thumb_image_url'], false, $addPrefixPath);
                if (!$removeThumbImage['success'])
                {
                    $result = false;
                    $error = $removeFullImage['error'];
                }

                $removeOriginalImage = $this->removePhotoImageByName($photoItemData['original_image_url'], false, $addPrefixPath);
                if (!$removeOriginalImage['success'])
                {
                    $result = false;
                    $error = $removeFullImage['error'];
                }

                $removePhotoData = $this->removePhotoByID($photoItemData['id']);
                if (!$removePhotoData['success'])
                {
                    $result = false;
                    $error = "Unable to remove photo data item.";
                }
            }
            else
            {
                $result = false;
                $error = $photoResult['error'];
            }
        }

        $response['success'] = $result;
        $response['error'] = $id . ' ' . $error;
        $response['photoItemData'] = $photoItemData;
        return $response;
    }
}
?>