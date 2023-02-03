<?php
	require ('vendor/autoload.php');

	if (isset($_POST['upload'])) 
	{
		$file = new \PhpFileUploader\Uploader('image');
		$file->path('/files/');
		$file->createRandomName();
		$file->upload();

		var_dump($file->displayUploadErrors());

		if ($file->success()) {
			echo 'The files uploaded';
		}
	}
?>

<form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST" enctype="multipart/form-data">
	<input type="file" name="image[]" multiple="multiple">
	<button type="submit" name="upload">upload</button>
</form>