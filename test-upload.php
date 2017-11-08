<?php



include('config/config.php');
include(CLASSES.'ImageController.php');


if( $_SERVER['REQUEST_METHOD'] == 'POST'){

  $image = new ImageController($_FILES['image'],[300,300]);
//  $image->checkImageType();
  if($image->processImage()){
      $image_path = $image->getImage();
  }
//  $image->getMessage();

}

?>

<form class="" action="test-upload.php" method="post" enctype="multipart/form-data">
  <input type="file" name="image" value=""> <br>
  <input type="submit" name="submit" value="upload">
</form>

<img src="../images/upload/<?php echo (isset($image_path)? $image_path: ''); ?>">
