## PHPFileUploader
A simple php library for uploading files.

#### Features
* Upload single or multiple file.
* Generate random name for the files.
* Create a custom name for the files.
* Files verification.

#### Installation
via composer
``` bash

```

#### Simple Example
``` php
require ('vendor/autoload.php');

$file = new \PhpFileUploader\Uploader('inputfilename'); // Specify the input file name.
$file->path('/files/'); // Specify the files destination path.
$file->upload(); // move uploaded files (You should call this method at the end).
```

#### Generate random name
You can use this method ```createRandomName()``` to generate a random name for the files.
If you don't call this method the files will be uploaded with their original name.

**Ex:**
``` php
$file = new \PhpFileUploader\Uploader('inputfilename');
$file->path('/files/');
$file->createRandomName(); // Generates random name.
$file->upload();
```

#### Create custom name
You can use this method ```createFileName()``` to create a custom name for the file.

**Ex:**
``` php
$file = new \PhpFileUploader\Uploader('inputfilename');
$file->path('/files/');
$file->createFileName('myCustomName'); // Create custom name.
$file->upload();
```

#### Check errors
This method ```displayUploadErrors()``` will return an array with error messages.
The library will verify the files to check whether the file exists, selected or has been uploaded successfully or not.

#### Upload multiple file
* Add this attribute ```multiple="multiple"``` to the **HTML** input file to allow you select multiple file.
* Make the input name as array ```name="files[]"```.
* The library will process all the files and upload it. :)

#### Full Example with HTML form
``` php
require ('vendor/autoload.php');

if (isset($_POST['upload'])) 
{
    $file = new \PhpFileUploader\Uploader('myFile'); // Specify the input file name.
    $file->path('/files/'); // Specify the files destination path.
    $file->createRandomName(); // Generate random name.
    $file->upload(); // move uploaded files (You should call this method at the end).

    // Display errors as array
    $file->displayUploadErrors()

    // Check if the files uploaded or not
    if ($file->success()) {
	    // Success
		echo 'Files have been uploaded';
	} else {
		// Failed
	}
}
```
``` html
<form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST" enctype="multipart/form-data">
	<input type="file" name="myFile[]" multiple="multiple">
	<button type="submit" name="upload">upload</button>
</form>
```