<?php

namespace Utils;

	abstract class Tools {
		
		static public function getValue($key, $defaultValue = false)
		{
			if (!isset($key) || empty($key) || !is_string($key))
				return false;
			$ret = (isset($_POST[$key]) ? $_POST[$key] : (isset($_GET[$key]) ? $_GET[$key] : $defaultValue));
	
			if (is_string($ret) === true)
				$ret = urldecode(preg_replace('/((\%5C0+)|(\%00+))/i', '', urlencode($ret)));
			return !is_string($ret)? $ret : stripslashes($ret);
		}
		
		static public function getSValue($key)
		{
			$string = self::getValue($key);
			$string = addslashes($string);
			$string = strip_tags($string);
			$string = str_replace(array("\r\n", "\r", "\n"), '<br>', $string);
			return $string;
		}

		static public function getSession($key, $defaultValue = false)
		{
			if (!isset($key) || empty($key) || !is_string($key))
				return false;
			$ret = (isset($_SESSION[$key]) ? $_SESSION[$key] : $defaultValue);
	
			if (is_string($ret) === true)
				$ret = urldecode(preg_replace('/((\%5C0+)|(\%00+))/i', '', urlencode($ret)));
			return !is_string($ret)? $ret : stripslashes($ret);
		}
	
		static public function getIsset($key)
		{
			if (!isset($key) || empty($key) || !is_string($key))
				return false;
			return isset($_POST[$key]) ? true : (isset($_GET[$key]) ? true : false);
		}

		static public function uploadFile($file, $dossier) 
		{
			$fichier = basename($file['name']);
			$taille_maxi = 50000000;
			$taille = filesize($file['tmp_name']);
			$extensions = array('.png', '.gif', '.jpg', '.jpeg');
			$extension = \strtolower(strrchr($file['name'], '.'));

			// die(var_dump($extension));
			$path = NULL;

            if(!in_array($extension, $extensions)) { //Si l'extension n'est pas dans le tableau
                $erreur = 'Seuls les fichiers de type png, gif, jpg ou jpeg sont acceptés';
            }
            if($taille>$taille_maxi) {
                $erreur = 'Le fichier est trop volumineux';
            }
            if(!isset($erreur)) { //S'il n'y a pas d'erreur, on upload
                //Formatage du nom du fichier
                $fichier = strtr($fichier,
                    'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ',
                    'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
                $fichier = preg_replace('/([^.a-z0-9]+)/i', '-', $fichier);
                move_uploaded_file($file['tmp_name'], $dossier . $fichier);
                $path = './public/img/' . $fichier;
            }
            else {
                //echo $erreur;
            }
			return $path;
		}
		
		static public function checkMail($mail) {
			return filter_var($mail, FILTER_VALIDATE_EMAIL);	
		}
	
	public static function getPOST($prefix="form_") {
		$updates = array();
		foreach($_POST as $k=>$v) {
			if(substr($k,0,strlen($prefix))==$prefix)
				$updates[str_replace($prefix,'',$k)] = $v;
		}
		return $updates;
	}
	
	public static function convertImage($src,$dst,$quality=100) {
		$img_info = getimagesize($src);
		switch ($img_info[2]) {
		  case IMAGETYPE_GIF  : $src = imagecreatefromgif($src);  break;
		  case IMAGETYPE_JPEG : $src = imagecreatefromjpeg($src); break;
		  case IMAGETYPE_PNG  : $src = imagecreatefrompng($src);  break;
		}
		imagejpeg($src, $dst, $quality);
		@unlink($src);
		
	}
}
?>
