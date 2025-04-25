<?php

class  Media {

  public $imageInfo;
  public $fileName;
  public $fileType;
  public $fileTempPath;
  //Set destination for upload
  public $userPath = SITE_ROOT.DS.'..'.DS.'uploads/users';
  public $productPath = SITE_ROOT.DS.'..'.DS.'uploads/products';


  public $errors = array();
  public $upload_errors = array(
    0 => 'There is no error, the file uploaded with success',
    1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
    2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
    3 => 'The uploaded file was only partially uploaded',
    4 => 'No file was uploaded',
    6 => 'Missing a temporary folder',
    7 => 'Failed to write file to disk.',
    8 => 'A PHP extension stopped the file upload.'
  );
  public$upload_extensions = array(
   'gif',
   'jpg',
   'jpeg',
   'png',
  );
  public function file_ext($filename){
     $ext = strtolower(substr( $filename, strrpos( $filename, '.' ) + 1 ) );
     if(in_array($ext, $this->upload_extensions)){
       return true;
     }
   }

  public function upload($file)
  {
    if(!$file || empty($file) || !is_array($file)):
      $this->errors[] = "No file was uploaded.";
      return false;
    elseif($file['error'] != 0):
      $this->errors[] = $this->upload_errors[$file['error']];
      return false;
    elseif(!$this->file_ext($file['name'])):
      $this->errors[] = 'File not right format ';
      return false;
    else:
      $this->imageInfo = getimagesize($file['tmp_name']);
      $this->fileName  = basename($file['name']);
      $this->fileType  = $this->imageInfo['mime'];
      $this->fileTempPath = $file['tmp_name'];
     return true;
    endif;

  }

 public function process(){

    if(!empty($this->errors)):
      return false;
    elseif(empty($this->fileName) || empty($this->fileTempPath)):
      $this->errors[] = "The file location was not available.";
      return false;
    elseif(!is_writable($this->productPath)):
      $this->errors[] = $this->productPath." Must be writable!!!.";
      return false;
    elseif(file_exists($this->productPath."/".$this->fileName)):
      $this->errors[] = "The file {$this->fileName} already exists.";
      return false;
    else:
     return true;
    endif;
 }
 /*--------------------------------------------------------------*/
 /* Function for Process media file
 /*--------------------------------------------------------------*/
  public function process_media($id = null){
    if(!empty($this->errors)){
        return false;
    }
    if(empty($this->fileName) || empty($this->fileTempPath)){
        $this->errors[] = "The file location was not available.";
        return false;
    }
    if(!is_writable($this->productPath)){
        $this->errors[] = $this->productPath." Must be writable!!!.";
        return false;
    }

    // Generate unique filename for product images
    if($id){
        $ext = explode(".", $this->fileName);
        $new_name = 'product_'.randString(8).$id.'.'.end($ext);
        $this->fileName = $new_name;
    }

    // Check file size (max 5MB)
    if($_FILES['product-photo']['size'] > 5000000) {
        $this->errors[] = "File size must be less than 5MB";
        return false;
    }

    // Create resized version if image
    // if(strpos($this->fileType, 'image') !== false) {
    //     $this->resize_image($this->fileTempPath, 800, 600);
    // }

    // Read file content for blob storage
    $file_data = file_get_contents($this->fileTempPath);

    // Insert media with blob data
    if($this->insert_media($file_data)){
        unset($this->fileTempPath);
        return true;
    } else {
        $this->errors[] = "Failed to insert media blob data.";
        return false;
    }
  }

  private function resize_image($file, $width, $height) {
      $info = getimagesize($file);
      $mime = $info['mime'];

      switch($mime) {
          case 'image/jpeg':
              $image = imagecreatefromjpeg($file);
              break;
          case 'image/png':
              $image = imagecreatefrompng($file);
              break;
          case 'image/gif':
              $image = imagecreatefromgif($file);
              break;
          default:
              return false;
      }

      $src_w = imagesx($image);
      $src_h = imagesy($image);

      // Calculate aspect ratio
      $src_ratio = $src_w / $src_h;
      $dst_ratio = $width / $height;

      if($src_ratio > $dst_ratio) {
          // Source is wider
          $tmp_h = $height;
          $tmp_w = $height * $src_ratio;
      } else {
          // Source is taller
          $tmp_w = $width;
          $tmp_h = $width / $src_ratio;
      }

      $tmp = imagecreatetruecolor($tmp_w, $tmp_h);
      imagecopyresampled($tmp, $image, 0, 0, 0, 0, $tmp_w, $tmp_h, $src_w, $src_h);
      imagedestroy($image);

      $image = imagecreatetruecolor($width, $height);
      imagecopyresampled($image, $tmp, 0, 0, 0, 0, $width, $height, $tmp_w, $tmp_h);
      imagedestroy($tmp);

      switch($mime) {
          case 'image/jpeg':
              imagejpeg($image, $file, 90);
              break;
          case 'image/png':
              imagepng($image, $file, 9);
              break;
          case 'image/gif':
              imagegif($image, $file);
              break;
      }

      imagedestroy($image);
      return true;
  }
  /*--------------------------------------------------------------*/
  /* Function for Process user image
  /*--------------------------------------------------------------*/
 public function process_user($id){

    if(!empty($this->errors)){
        return false;
      }
    if(empty($this->fileName) || empty($this->fileTempPath)){
        $this->errors[] = "The file location was not available.";
        return false;
      }
    if(!is_writable($this->userPath)){
        $this->errors[] = $this->userPath." Must be writable!!!.";
        return false;
      }
    if(!$id){
      $this->errors[] = " Missing user id.";
      return false;
    }
    $ext = explode(".",$this->fileName);
    $new_name = randString(8).$id.'.' . end($ext);
    $this->fileName = $new_name;
    if($this->user_image_destroy($id))
    {
    if(move_uploaded_file($this->fileTempPath,$this->userPath.'/'.$this->fileName))
       {

         if($this->update_userImg($id)){
           unset($this->fileTempPath);
           return true;
         }

       } else {
         $this->errors[] = "The file upload failed, possibly due to incorrect permissions on the upload folder.";
         return false;
       }
    }
 }
 /*--------------------------------------------------------------*/
 /* Function for Update user image
 /*--------------------------------------------------------------*/
  private function update_userImg($id){
     global $db;
      $sql = "UPDATE users SET";
      $sql .=" image='{$db->escape($this->fileName)}'";
      $sql .=" WHERE id='{$db->escape($id)}'";
      $result = $db->query($sql);
      return ($result && $db->affected_rows() === 1 ? true : false);

   }
 /*--------------------------------------------------------------*/
 /* Function for Delete old image
 /*--------------------------------------------------------------*/
  public function user_image_destroy($id){
     $image = find_by_id('users',$id);
     if($image['image'] === 'no_image.png')
     {
       return true;
     } else {
       unlink($this->userPath.'/'.$image['image']);
       return true;
     }

   }
/*--------------------------------------------------------------*/
/* Function for insert media image
/*--------------------------------------------------------------*/
  private function insert_media($file_data = null){

         global $db;
         if ($file_data === null) {
             $sql  = "INSERT INTO media ( file_name,file_type )";
             $sql .=" VALUES ";
             $sql .="(
                      '{$db->escape($this->fileName)}',
                      '{$db->escape($this->fileType)}'
                      )";
             return ($db->query($sql) ? true : false);
         } else {
             $file_data_escaped = $db->escape($file_data);
             $sql  = "INSERT INTO media ( file_name,file_type,file_data )";
             $sql .=" VALUES ";
             $sql .="(
                      '{$db->escape($this->fileName)}',
                      '{$db->escape($this->fileType)}',
                      '{$file_data_escaped}'
                      )";
             return ($db->query($sql) ? true : false);
         }

  }
/*--------------------------------------------------------------*/
/* Function for Delete media by id
/*--------------------------------------------------------------*/
   public function media_destroy($id,$file_name){
     $this->fileName = $file_name;
     if(empty($this->fileName)){
         $this->errors[] = "The Photo file Name missing.";
         return false;
       }
     if(!$id){
       $this->errors[] = "Missing Photo id.";
       return false;
     }
     if(delete_by_id('media',$id)){
         unlink($this->productPath.'/'.$this->fileName);
         return true;
     } else {
       $this->error[] = "Photo deletion failed Or Missing Prm.";
       return false;
     }

   }



}


?>
