<?php

class ImageController
{
  /**
   * 1-upload image
   * 2- check for mime types for different browsers
   * 3- check the file size - Note : not added yet
   * 4- store errors
   * 5- upload / move the file if no error
   * 6- resize image for different usage
   * 7- display errors
   *
   * contructor takes 2 params array of file uplaod and file size
   * file size is optional param
   */

   private $file_tmp;
   private $file_properties;
   private $file_name;
   private $folder_path;
   private $ext;
   private $file_type;
   private $file_width;
   private $file_height;
   private $need_dim =[];
   private $msgs = [];
   private $root;

  public function __construct( $files_arr, $dim_arr = null) {
    if(is_array($files_arr)) {
      $this->root =  '/var/www/basicAdmin/';
      $this->file_tmp = $files_arr['tmp_name'];
      $this->file_properties = getimagesize($this->file_tmp);
      $this->ext = pathinfo($files_arr['name'], PATHINFO_EXTENSION);

        if(NUll != $dim_arr){
          $this->need_dim= $dim_arr;
        }
          $this->file_width= $this->file_properties[0];
          $this->file_height= $this->file_properties[1];


      $this->file_type = $this->file_properties[2];
      $this->file_name = time();
      $this->folder_path= $this->root.'images/upload/';

    }else{
      $this->msgs['general_error'] = "there is no file to upload";
    }
  }



  public function checkImageType(){
    if(in_array($this->file_type,[IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_JPEG])) {
      $this->msgs['type'] = "valid file type ".$this->file_type;
      return true;
    }else{
      $this->msgs['type_error'] = "invalid file type" ;
      return false;
    }
  }



  public function processImage () {
    switch ($this->file_type) {
      case IMAGETYPE_PNG:
          $fileSourseId = imagecreatefrompng($this->file_tmp);
          $target_layer = $this->imageResize($fileSourseId, $this->file_width, $this->file_height);
           imagepng($target_layer , $this->folder_path. $this->file_name. "_thump.".$this->ext);
          break;

      case IMAGETYPE_GIF:
          $fileSourseId = imagecreatefromgif($this->file_tmp);
          $target_layer = $this->imageResize($fileSourseId, $this->file_width, $this->file_height);
           imagegif($target_layer , $this->folder_path. $this->file_name. "_thump.".$this->ext);
          break;

      case IMAGETYPE_JPEG:
          $fileSourseId = imagecreatefromjpeg($this->file_tmp);
          $target_layer = $this->imageResize($fileSourseId, $this->file_width, $this->file_height);
           imagejpeg($target_layer , $this->folder_path. $this->file_name. "_thump.".$this->ext);
          break;

      default:
          $this->msgs['type_error'] = "invalid file type" ;
          return false;
          break;
    }

    if($this->moveImage()){
      return true;
    }
  }

  public function moveImage () {
    if(move_uploaded_file($this->file_tmp, $this->folder_path. $this->file_name. ".".$this->ext)){
        chmod($this->folder_path. $this->file_name. ".".$this->ext, '775');
      $this->msgs['upload'] = "image resized and uploaded successfully" ;
      return true;
    }else{
      $this->msgs['upload'] = "can not move the file image" ;
      return true;
    }
  }


  public function imageResize ($sourceId, $width, $height) {
    if(!empty($this->need_dim)){
      $x_size = $this->need_dim[0];
      $y_size = $this->need_dim[1];

    }
    $x_size = 200;
    $y_size = 200;

    $targetLayer = imagecreatetruecolor($x_size, $y_size);
    imagecopyresampled($targetLayer, $sourceId, 0, 0, 0, 0,$x_size, $y_size, $width, $height );
    $this->msgs['resize'] = "image resized successfully";
    return $targetLayer;
  }

//msgId = [general_error, type, type_error, upload, resize]
  public function getMessage($msgId=null){
    if(null != $msgId){
      echo $msgId. ' : '.$this->msgs[$msgId];
    }else{
      foreach($this->msgs as $id => $msg){
        echo $id .' : '.$msg.'<br />';
      }
    }
  }


  public function getImage(){
    return  $this->file_name. "_thump.".$this->ext;
  }
}
