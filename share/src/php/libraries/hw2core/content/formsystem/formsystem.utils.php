<?php namespace Hw2;
S_Core::checkAccess();

jimport('joomla.html.pagination');
jimport('joomla.utilities.date');
jimport('joomla.filesystem.file');


class S_FS_File {
    const pImages="images";
    const pFiles="";
    
    public static function GetUrl(S_CT_Sec $section, $itemId, $relative = false,$folder="")
    {
        if (end($folder)!="/")
            $folder.="/";
        
        $url=$folder."s".$section->getValue()."/i".$itemId."/" ;
        if (!$relative)
            $url=S_jTools::GetClientRoot()."hw2/local/files/".$url;

        return $url;
    }
    
    public static function GetRootPath($relative = false,$folder="")
    {
            if (end($folder)!="/")
                $folder.=DS;
            
            $path=$folder;
            if(!$relative)
                    $path=JPATH_SITE.DS."hw2".DS."local".DS."files".DS.$path;	
            
            return $path;
    }
    
    public static function CreateFullFileName($uploadFilename, $itemId)
    {
            if(!$uploadFilename)
            {
                    return '';
            }	

            return $itemId . '.' . \Hwj\JFile::getExt($uploadFilename);
    }

    public static function Delete($path)
    {
            if(\Hwj\JFile::exists($path))
                    \Hwj\JFile::delete($path);
    }
    
}

class S_FS_Image
{
	function S_FS_Image()
	{
	}
	
	function GetImagesUrl(S_CT_Sec $section, $itemId, $relative = false)
	{
            return S_FS_File::GetUrl($section, $itemId,$relative,S_FS_File::pImages);
	}
	
	function GetThumbnailsUrl(S_CT_Sec $section, $itemId, $relative = false)
	{
            return S_jTools::pathCombine(S_FS_Image::GetImagesUrl($section, $itemId, $relative), 'thumbs');	
	}

	function GetImagesRootPath($relative = false)
	{
            return S_FS_File::GetRootPath($relative,S_FS_File::pImages);
	}
	
	function GetImagesPath(S_CT_Sec $section, $itemId, $relative = false)
	{
		return S_jTools::pathCombine(S_FS_Image::GetImagesRootPath($relative), 's'.$section->getValue().'/'.'i'.$itemId);
	}

	function GetThumbnailsPath(S_CT_Sec $section, $itemId, $relative = false)
	{
		return S_jTools::pathCombine(S_FS_Image::GetImagesPath($section, $itemId, $relative), 'thumbs');	
	}

	function CreateFullImageName($uploadFilename, $itemId)
	{
            return S_FS_File::CreateFullFileName($uploadFilename, $itemId);
	}
	
	function CreateThumbnailImageName($uploadFilename, $itemId)
	{
            return S_FS_File::CreateFullFileName($uploadFilename, $itemId);	
	}	

	function Delete(S_CT_Sec $section, $itemId, $filename)
	{
		// delete thumbnail
		S_FS_File::Delete(S_jTools::pathCombine($this->GetThumbnailsPath($section, $itemId), $filename));
		// delete image
		S_FS_File::Delete(S_jTools::pathCombine($this->GetImagesPath($section, $itemId), $filename));
	}

	function deleteEntityImages(S_CT_Sec $section,  Hw2CtType $type, $entityId)
	{
		$db =& \Hwj\JFactory::getDbo();
	
		switch($section)
		{
			case S_CT_Sec::jcategory():
                                S_jTools::recursiveRemoveDirectory(S_jTools::pathCombine(S_FS_Image::GetImagesRootPath(), "p$entityId"));		
				break;
				
			case S_CT_Sec::jarticle():
				$db->setQuery("SELECT frm.projectid FROM #__f2c_form frm WHERE frm.id = $entityId");
				$projectid = $db->loadResult();
				S_jTools::recursiveRemoveDirectory(S_jTools::pathCombine(S_FS_Image::GetImagesRootPath(), "p$projectid/f$entityId"));							
				break;
				
			case HW2_ENTITY_CONTENTTYPEFIELD:
				$sql =	"SELECT pfl.projectid, fct.formid, fct.content " .
						"FROM #__f2c_projectfields pfl " .
						"INNER JOIN #__f2c_fieldcontent fct ON pfl.id = fct.fieldid " .
						"WHERE pfl.fieldtypeid = 6 AND pfl.id = $entityId";
						
				$db->setQuery($sql);
				$rows = $db->loadObjectList();
				
				for ($i=0, $n=count($rows); $i < $n; $i++) 
				{
			  		$row = &$rows[$i];
					  		
			  		if($row->content)
			  		{
			  			$imageData = new \Hwj\JRegistry();
			  			$imageData->loadString($row->content);
			  		
			  			$imageFile = S_jTools::pathCombine(S_FS_Image::GetImagesPath($row->projectid, $row->formid), $imageData->get('filename'));
			  			$thumbNailFile = S_jTools::pathCombine(S_FS_Image::GetThumbnailsPath($row->projectid, $row->formid), $imageData->get('filename'));
			  		
			  			if(\Hwj\JFile::exists($imageFile)) \Hwj\JFile::delete($imageFile);
			  			if(\Hwj\JFile::exists($thumbNailFile)) \Hwj\JFile::delete($thumbNailFile);
			  		}
				}					
				break;
		}	
	}	
}


class S_FS_FileInfo
{
	var $id;
	var $fileName;
	var $fileLocation;
	var $fileSize;
	var $fileExtension;
	
	function FS_FileInfo($fileLocation, $fileName)
	{
		$this->id = $fileLocation.$fileName;
		$this->fileName = $fileName;
		$this->fileLocation = $fileLocation;
		$this->fileExtension = \Hwj\JFile::getExt($this->id);
		$this->fileSize = Hw2_FileInfo::FormatFileSize(filesize($this->id));
	}
	
	function FormatFileSize($filesize)
	{	
		if($filesize > 1024 * 1024)
		{
			$filesize = round($filesize / (1024 * 1024), 2);
			return $filesize . ' Mb';
		}
	
		if($filesize > 1024)
		{
			$filesize = round($filesize / 1024, 2);
			return $filesize . ' kb';
		}
		
		return $filesize . ' bytes';
	}
}

class S_ImageHelper
{
    function ResizeImage($srcFile, $dstFile = null, &$dstWidth, &$dstHeight, $jpegQuality = 75) 
    {
		// Initialize variables
		jimport('joomla.client.helper');
		$FTPOptions = \Hwj\JClientHelper::getCredentials('ftp');
    	
		if(!ImageHelper::isGdiLibInstalled())
		{
			\Hwj\JError::raiseError("ResizeImage(): Missing GD Libraries");
			return false;
		}
    	
    	if(!$dstFile)
    	{
			$dstFile = $srcFile;    		
    	}
    	
    	$fileExt = strtolower(\Hwj\JFile::getExt($srcFile));

		if($fileExt != 'gif' && $fileExt != 'jpg' && $fileExt != 'jpeg' && $fileExt != 'png')
		{
			\Hwj\JError::raiseError("ResizeImage(): Invalid file extension: {$fileExt}");
			return false;			
		}
		
		if($dstWidth == 0 || $dstHeight == 0) 
		{
			\Hwj\JError::raiseError("ResizeImage(): Invalid size value (w:{$dstWidth}, h:{$dstHeight})");
			return false;
		}
		
		list($srcWidth, $srcHeight, $imgType) = getimagesize($srcFile);
		
		if(!$srcWidth || !$srcHeight || !$imgType) 
		{
			\Hwj\JError::raiseError("ResizeImage(): Cannot retrieve image information; not a valid image");			

			if(!unlink($srcFile))
			{
				\Hwj\JError::raiseError("ResizeImage(): Cannot delete file");
			}
			
			return false;
		}
		
    	if($srcWidth == 0 || $srcHeight == 0) 
    	{
			\Hwj\JError::raiseError("ResizeImage(): Invalid size value for source image (original width: {$srcWidth}, original height:{$srcHeight})");
			return false;
		}
		
    	if(($srcWidth < $dstWidth) && ($srcHeight < $dstHeight))
    	{
    		// No resize necessary: fill the output parameters
    		$dstWidth = $srcWidth;
    		$dstHeight = $srcHeight;
    		
    		if($srcFile == $dstFile)
    		{
    			return true;
    		}
    		
    		return \Hwj\JFile::copy($srcFile, $dstFile);
    	}
    	
    	$srcRatio = $srcWidth / $srcHeight;

		if($dstWidth / $dstHeight > $srcRatio)
		{
		   $dstWidth = $dstHeight * $srcRatio;
		}
		else
		{
		   $dstHeight = $dstWidth / $srcRatio;
		}

		$dstHeight = (int)ceil($dstHeight);
		$dstWidth = (int)ceil($dstWidth);

		if(!($dstImage = imagecreatetruecolor($dstWidth, $dstHeight)))
		{
			return false;
		}
		    			
		switch($imgType)
		{
			case 1: // gif
				if(!($srcImage = imagecreatefromgif($srcFile))) 
				{
					\Hwj\JError::raiseError("ResizeImage(): Invalid GIF file");
					return false;
				}
				
				$colorcount = imagecolorstotal($srcImage);
				
				if($colorcount)
				{
					imagetruecolortopalette($dstImage, true, $colorcount);
				}
				
				imagepalettecopy($dstImage,$srcImage);
				$transparentcolor = imagecolortransparent($srcImage);
				
				if($transparentcolor > -1)
				{
					imagefill($dstImage,0,0,$transparentcolor);
				}
				
				imagecolortransparent($dstImage,$transparentcolor);
				imagecopyresampled($dstImage, $srcImage, 0,0,0,0, $dstWidth, $dstHeight, $srcWidth, $srcHeight); 
				
				if ($FTPOptions['enabled'] == 1)
				{
					ob_start();

					if(!(imagegif($dstImage, null)))
					{
						ob_end_clean();
						\Hwj\JError::raiseError("ResizeImage(): Could not create buffered GIF file");
						return false;
					}
					
					$imgGifData = ob_get_contents();
					ob_end_clean();
					
					if(!\Hwj\JFile::write($dstFile, $imgGifData)) 
					{
						\Hwj\JError::raiseError("ResizeImage(): Could not create GIF file (JFile write)");
						return false;
					}
				}
				else
				{
					if(!(imagegif($dstImage, $dstFile)))
					{
						\Hwj\JError::raiseError("ResizeImage(): Could not create GIF file (Direct write)");
						return false;
					}
				}
				
				break;
				
			case 2: // jpg
				if(!($srcImage = imagecreatefromjpeg($srcFile)))
				{
					\Hwj\JError::raiseError("ResizeImage(): Invalid JPG file");
					return false;
				}
			
				Imagefill($dstImage, 0, 0, imagecolorallocate($dstImage, 255, 255, 255));
				imagecopyresampled($dstImage, $srcImage, 0,0,0,0, $dstWidth, $dstHeight, $srcWidth, $srcHeight);

				if ($FTPOptions['enabled'] == 1)
				{
					ob_start();

					if(!(imagejpeg($dstImage, null, $jpegQuality)))
					{
						ob_end_clean();
						\Hwj\JError::raiseError("ResizeImage(): Could not create buffered JPG file");
						return false;
					}
					
					$imgJpgData = ob_get_contents();
					ob_end_clean();
					
					if(!\Hwj\JFile::write( $dstFile, $imgJpgData)) 
					{
						\Hwj\JError::raiseError("ResizeImage(): Could not create JPG file (JFile write)");
						return false;
					}
				}
				else
				{
					if(!(imagejpeg($dstImage, $dstFile, $jpegQuality)))
					{
						\Hwj\JError::raiseError("ResizeImage(): Could not create JPG file (Direct write)");
						return false;
					}
				}
				break;
				
			case 3: // png
				if(!($srcImage = imagecreatefrompng($srcFile)))
				{
					\Hwj\JError::raiseError("ResizeImage(): Invalid PNG file");
					return false;
				}
			
				imagealphablending($dstImage, false);
	            $colorTransparent = imagecolorallocatealpha($dstImage, 0, 0, 0, 127);
	            imagefill($dstImage, 0, 0, $colorTransparent);
	            imagesavealpha($dstImage, true);
				imagecopyresampled($dstImage, $srcImage, 0,0,0,0, $dstWidth, $dstHeight, $srcWidth, $srcHeight); 
			
				if ($FTPOptions['enabled'] == 1)
				{
					ob_start();

					if(!(imagepng($dstImage, null)))
					{
						ob_end_clean();
						\Hwj\JError::raiseError("ResizeImage(): Could not create buffered PNG file");
						return false;
					}
					
					$imgPngData = ob_get_contents();
					ob_end_clean();
					
					if(!\Hwj\JFile::write($dstFile, $imgPngData)) 
					{
						\Hwj\JError::raiseError("ResizeImage(): Could not create PNG file (JFile write)");
						return false;
					}
				}
				else
				{
					if(!(imagepng($dstImage, $dstFile)))
					{
						\Hwj\JError::raiseError("ResizeImage(): Could not create PNG file (Direct write)");
						return false;
					}
				}
												
				break;
		}

		imagedestroy($dstImage);
		imagedestroy($srcImage);
		return true;
    }
    
    function isGdiLibInstalled()
    {
    	if((!function_exists('imagecreatetruecolor')) 	|| (!function_exists('imagecreatefromgif')) 	||
		   (!function_exists('imagecopyresampled'))		|| (!function_exists('imagegif')) 				||
		   (!function_exists('imagecreatefromgif')) 	|| (!function_exists('imagecreatefromjpeg')) 	||
		   (!function_exists('imagecreatefrompng'))		|| (!function_exists('imagecolorstotal'))		||
		   (!function_exists('imagecolortransparent'))	|| (!function_exists('imagefill'))				||
		   (!function_exists('imagetruecolortopalette'))|| (!function_exists('imagepalettecopy')))
		{		
			return false;
		}
		else
		{
			return true;
		}
    }	
}

class S_FS_DateTimeHelper
{
	function ParseDate($date, $format)
	{
		$day = 0;
		$month = 0;
		$year = 0;
		$date = trim($date);

		if(stristr($date, ' ') === FALSE)
		{
			$date .= ' 00:00:00';
		}
		
		list($datePart, $timePart) = explode(' ', $date);
	
		$dateFormat = explode('-', $format); 
		$dateParts = explode('-', $datePart);
		$timeParts = explode(':', $timePart);
	
		if(count($dateParts) != 3)
		{
			return false;
		}
	
		if(count($timeParts) == 1)
		{
			$timeParts[1] = '00';
			$timeParts[2] = '00';
		}
	
		if(count($timeParts) == 2)
		{
			$timeParts[2] = '00';
		}
	
		$timeParts[0] = (int)$timeParts[0];
		$timeParts[1] = (int)$timeParts[1];
		$timeParts[2] = (int)$timeParts[2];
		
		if(!F2cDateTimeHelper::checktime($timeParts[0], $timeParts[1], $timeParts[2]))
		{
			return false;
		}
				
		for($i = 0; $i < count($dateFormat); $i++)
		{
			switch($dateFormat[$i])
			{
				case '%d':
					$day = (int)$dateParts[$i];
					break;
				case '%m':
					$month = (int)$dateParts[$i];
					break;
				case '%Y':
					$year = (int)$dateParts[$i];
					break;
			}
		}
				
		if(checkdate($month, $day, $year))
		{
			return new \Hwj\JDate($year.'-'.$month.'-'.$day. ' '.$timeParts[0].':'.$timeParts[1].':'.$timeParts[2]);
		}
		else
		{
			return false;
		}	
	}
	
	function checktime($hours, $minutes, $seconds)
	{
		if($hours < 0 || $hours > 23) return false;
		if($minutes < 0 || $minutes > 59) return false;
		if($seconds < 0 || $seconds > 59) return false;
		return true;
	}
	
	function getTranslatedDateFormat()
	{
		$dateFormat	= F2cFactory::getConfig()->get('date_format');
		$dateFormat = str_replace('%', '', $dateFormat);
		$dateFormat = str_replace('-', '_', $dateFormat);
		return \Hwj\JText::_('COM_FORM2CONTENT_DATE_FORMAT_'.strtoupper($dateFormat));
	}
}

class S_FS_ArrayHelper
{
	function getValue($array, $key, $default = '')
	{
		return array_key_exists($key, $array) ? $array[$key] : $default;  
	}
}

?>
