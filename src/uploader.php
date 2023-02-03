<?php
	
	namespace PhpFileUploader;
	
	/**
	 * PHP Uploader
	 *
	 * @author (ahmed hasssan)
	 */
	class Uploader
	{

		/**	
		 * Holds data from the super global variable $_FILES. 
		 */
		private $file;
		private $name;
		private $size;
		private $type;
		private $tmp;
		private $error;
		private $extension;

		/**	
		 * Holds uploading errors.
		 */
		private $uploadErrors = array();

		/**
		 * Random file name.
		 */
		private $randomFileName = false;

		/**
		 * New file name.
		 */
		private $newFileName = null;

		/** 
		 *	The destination path.
		 */
		private $path;

		private $filesErrorMessages = [
	        0 => "There is no error, the file uploaded with success", 
	        1 => "The uploaded file exceeds the upload_max_filesize directive in php.ini", 
	        2 => "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form",
	        3 => "The uploaded file was only partially uploaded",
	        4 => "No file was uploaded",
	        6 => "Missing a temporary folder" 
		];

		/**
		 *	Class construct: Set the data of uploaded files.
		 */
		public function __construct (string $file)
		{
			if (isset($_FILES[$file])) 
			{
				// Get data from $_FILES variable.
				$this->file  = $_FILES[$file];
				$this->name  = $this->file['name'];
				$this->size  = $this->file['size'];
				$this->type  = $this->file['type'];
				$this->tmp   = $this->file['tmp_name'];
				$this->error = $this->file['error'];
				$this->extension = $this->parseExtension();
			}
			else
			{
				$this->error('Undefined input file name');
			}
		}

		/**
		 *	Get the file extension.
		 *	@return string
		 */
		private function parseExtension ()
		{
			$ext = null;

			if (is_array($this->name)) {
				foreach ($this->name as $value) {
					$ext_values = explode('.', $value);
					$ext_values = end($ext_values);
					$ext[] = '.'.$ext_values;
				}
			} else {
				$ext = explode('.', $this->name);
				$ext = '.'.end($ext);
			}

			return $ext;
		}

		/**
		 *	Check if the file input empty ro not.
		 *	@return boolean
		 */
		private function fileExists ()
		{
			$file = null;
			$name = $this->name;

			if (is_array($name)){
				$name = implode('', $name);
			}

			if (!empty($name)) {
				$file = $name;
			}

			return isset($this->file) && !empty($file) && !is_null($file);
		}

		/**
		 *	Create random name for the new file.
		 *	@return void
		 */
		public function createRandomName ()
		{
			$this->randomFileName = true;
		}

		/**
		 *	Create new file name.
		 *	@return void
		 */
		public function createFileName (string $filename)
		{
			$this->newFileName = $filename;
		}

		/**
		 *	Set the destination path.
		 *	@param string, $path
		 */
		public function path (string $path)
		{
			$path = trim($path, '/');
			$path = trim($path, '\\');
			$path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
			$path = $path.''.DIRECTORY_SEPARATOR;

			$this->path = $path;
		}

		/**
		 *	Upload the files to the specified destination.
		 */
		public function upload ()
		{
			if (!$this->fileExists()) {
				$this->error('Choose files to upload.');
			}

			if (empty($this->path) || $this->path == null){
				$this->error('You have to use path method to specify the destination.');
			}

			$this->move();
		}

		/**
		 *	Generate random string.
		 *
		 *	@param integer $length
		 *	@return string
		 */
		public function randomString (int $length = 10)
		{
			$char = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$char = str_shuffle($char);
			$charLength = strlen($char);
			$random = null;

			for ($i = 0; $i < $length; $i++) {
				$random .= $char[rand(0, $charLength - 1)];
			}

			return $random;
		}

		private function checkFilesErrors ($fileError)
		{
			if ($fileError > 0) {
				$this->uploadErrors[] = $this->filesErrorMessages[$fileError];
			}
		}

		private function error ($errorMessage)
		{
			$this->uploadErrors[] = $errorMessage;
		}

		/**
		 *	Move uploaded files.
		 */
		public function move ()
		{
			// Create file directory if not exists
			if (!is_dir($this->path)) {
				mkdir($this->path);
			}

			if (is_array($this->name)) 
			{
				for ($file = 0; $file < count($this->name); $file++)
				{
					// Get the temp file path
					$tmp = $this->tmp[$file];
					$name = $this->name[$file];

					// Get new file name
					if ($this->randomFileName == true) 
					{
						$this->newFileName = date('Ymd').'-'.time().'-'.$this->randomString();
						$name = $this->newFileName.$this->extension[$file];
					}
					elseif (!is_null($this->newFileName)) 
					{
						$name = $this->newFileName.$this->extension[$file];
					}

					// Check errors
					$this->checkFilesErrors($this->error[$file]);

					// Upload the file
					if ( move_uploaded_file($tmp, $this->path.$name) ) {
						// uploaded
					} else {
						$this->error("Something went wrong. this file '{$this->name[$file]}' has not been uploaded");
					}
				}
			}
			else
			{
				$name = $this->name;

				if ($this->randomFileName == true) 
				{
					$this->newFileName = date('Ymd').'-'.time().'-'.$this->randomString();
					$name = $this->newFileName.$this->extension;
				} 
				elseif (!is_null($this->newFileName)) 
				{
					$name = $this->newFileName.$this->extension;
				}

				$this->checkFilesErrors($this->error);

				if (move_uploaded_file($this->tmp, $this->path.$name) ) {
					// uploaded
				} else {
					$this->error("Something went wrong. this file '{$name}' has not been uploaded");
				}
			}
		}

		/**
		 *	Get the file name.
		 */
		public function getName ()
		{
			return $this->name;
		}

		/**
		 *	Get the file size.
		 */
		public function getSize ()
		{
			return $this->size;
		}

		/**
		 *	Get the file type.
		 */
		public function getType ()
		{
			return $this->type;
		}

		/**
		 *	Get the tmp name.
		 */
		public function getTmp ()
		{
			return $this->tmp;
		}

		/**
		 *	Get the file error.
		 */
		public function getError ()
		{
			return $this->error;
		}

		/**
		 *	Get the file extension.
		 */
		public function getExtension ()
		{
			return $this->extension;
		}

		/**
		 *	Check if there is no errors.
		 */
		public function success ()
		{
			if (empty($this->uploadErrors)) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 *	Display the upload errors.
		 */
		public function displayUploadErrors ()
		{
			return $this->uploadErrors;
		}
	}
?>