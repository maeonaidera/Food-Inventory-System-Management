<?php
  $page_title = 'All Media';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(2);

  // Handle delete request
  if(isset($_POST['delete_media'])) {
    $media_id = (int)$_POST['media_id'];
    $file_name = remove_junk($db->escape($_POST['file_name']));
    require_once('includes/upload.php');
    $media = new Media();
    if($media->media_destroy($media_id, $file_name)) {
      $session->msg('s', "Media deleted successfully.");
    } else {
      $session->msg('d', join(' ', $media->errors));
    }
    redirect('media.php', false);
  }

  // Handle upload request
  if(isset($_POST['upload_media'])) {
    require_once('includes/upload.php');
    $media = new Media();
    if(!empty($_FILES['media-file']['name'])) {
      if($media->upload($_FILES['media-file'])) {
        $unique_id = time() . rand(1000, 9999);
        if($media->process_media($unique_id)) {
          $session->msg('s', "Media uploaded successfully.");
        } else {
          $session->msg('d', join(' ', $media->errors));
        }
      } else {
        $session->msg('d', join(' ', $media->errors));
      }
    } else {
      $session->msg('d', "No file selected for upload.");
    }
    redirect('media.php', false);
  }

  // Fetch all media
  $all_media = find_all('media');
?>

<?php include_once('layouts/header.php'); ?>
<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div> 
</div>

<div class="row">
  <div class="col-md-12">
    <form method="post" action="media.php" enctype="multipart/form-data" class="form-inline" style="margin-bottom: 20px;">
      <div class="form-group">
        <label for="media-file">Upload Media File:</label>
        <input type="file" name="media-file" id="media-file" class="form-control" required>
      </div>
      <button type="submit" name="upload_media" class="btn btn-primary">Upload</button>
    </form>
  </div>
</div>

<div class="row">
  <?php if(empty($all_media)): ?>
    <div class="col-md-12">
      <div class="alert alert-warning text-center">No media files found.</div>
    </div>
  <?php else: ?>
    <?php foreach($all_media as $media): ?>
      <div class="col-md-3 text-center" style="margin-bottom: 20px;">
        <div class="thumbnail">
          <img src="uploads/products/<?php echo $media['file_name']; ?>" alt="<?php echo $media['file_name']; ?>" style="max-width: 100%; height: 150px; object-fit: contain;">
          <div class="caption">
            <p><?php echo $media['file_name']; ?></p>
            <form method="post" action="media.php" onsubmit="return confirm('Are you sure you want to delete this media?');">
              <input type="hidden" name="media_id" value="<?php echo (int)$media['id']; ?>">
              <input type="hidden" name="file_name" value="<?php echo $media['file_name']; ?>">
              <button type="submit" name="delete_media" class="btn btn-danger btn-xs">Delete</button>
            </form>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<?php include_once('layouts/footer.php'); ?>
