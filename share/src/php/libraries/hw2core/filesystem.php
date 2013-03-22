<?php namespace Hw2;
S_Core::checkAccess();

class S_FileSys extends S_Object {
    public static function replaceInFile($file,$data) {
        $c = file_get_contents($file);
        if($c) {
            $orig = fileperms($file);
            chmod($file, octdec('0644'));
            $c = str_replace(array_keys($data), array_values($data), $c);
            file_put_contents($file,$c);
            chmod($file, $orig);
        }
    }
    
    public static function pathCombine($path, $fileName)
    {
            return rtrim($path, "/\\") . '/' . $fileName;
    }
    
    public static function normalizePath($path) {
        // fix / - // - \ - \\ with correct directory separator
        $path=str_replace(array('/', '\\','//','\\\\'), DIRECTORY_SEPARATOR, $path);
        return $path;
    }
    
    public static function isAbsolute($path) {
        return $path[0]==DS;
    }
    
    public static function getFileExt($src) {
        return strtolower(pathinfo($src, PATHINFO_EXTENSION));
    }
    
    public static function copyDir($source, $destination, Array $ignoreList = null, $avoidLinks = false) {
        if ($source == $destination || in_array($source, $ignoreList) || ($avoidLinks && self::is_link($source)))
            return;

        if (is_dir($source)) {
            if (@mkdir($destination)) {
                $directory = dir($source);
                while (FALSE !== ( $readdirectory = $directory->read() )) {
                    $PathDir = $source . DS . $readdirectory;

                    if ($readdirectory == '.' || $readdirectory == '..' || ($avoidLinks && self::is_link($PathDir)))
                        continue;

                    if (is_dir($PathDir))
                        self::copyDir($PathDir, $destination . DS . $readdirectory, $ignoreList, $avoidLinks);
                    else
                        copy($PathDir, $destination . DS . $readdirectory);
                }

                $directory->close();
            }
        }else {
            copy($source, $destination);
        }
    }
    
    
    public static function recursiveRemoveDirectory($directory) {
        $directory = \Hwj\JPath::clean($directory);

        if (!\Hwj\JFolder::exists($directory))
            return false;

        $files = \Hwj\JFolder::files($directory, '.', false, true);

        if (count($files)) {
            \Hwj\JFile::delete($files);
        }

        $folders = \Hwj\JFolder::folders($directory, '.', false, true);

        if (count($folders)) {
            foreach ($folders as $folder)
            {
                self::recursiveRemoveDirectory($folder);
            }
        }

        \Hwj\JFolder::delete($directory);
        return true;
    }
    
    /**
     * 
     * @param type $dir
     * @param array $ignoreList
     * @param type $linkMethod 0: skip links, 1: remove only link, 2: remove link and it's content ( DANGEROUS )
     * @return type
     */
    public static function rrmDir($dir,Array $ignoreList = null,$linkMethod=0) {
        if (in_array($dir,$ignoreList) || ($linkMethod==0 && self::is_link($PathDir)))
                return;
        
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                $path=$dir . DS . $object;
                if ($object != "." && $object != ".." && !in_array($path,$ignoreList)) {
                    if (is_dir($path) && (!self::is_link($path) || $linkMethod==2)) {
                        self::rrmDir($path,$ignoreList);
                    } else {
                        if (!self::is_link($path) || $linkMethod==1)
                            unlink($path);
                    }
                }
            }
            reset($objects);
            rmdir($dir); // finally delete empty folder
        }
    }
    
    public static function find($dir,Array $ignoreList = null,$avoidLinks=false) {
        if (in_array($dir,$ignoreList))
                return;
        
        if (is_dir($dir)) {
            $objects = scandir($dir);
            $fileList=Array();
            foreach ($objects as $object) {
                $path=$dir . DS . $object;
                if ($object != "." && $object != ".." && !in_array($path,$ignoreList)
                        && ( !self::is_link($path) || !$avoidLinks )) {
                    if (is_dir($path)) {
                        $fileList=array_merge($fileList,self::find($path, $ignoreList));
                    } else {
                        $fileList[]=$path;
                    }
                }
            }
            reset($objects);
        }
        
        return $fileList;
    }
    
    /**
     * 
     * @param type $dir
     * @param type $dest path+filename without extension
     * @param type $remove_path
     * @param array $ignoreList
     * @param type $direct
     * @return type
     */
    public static function pclZipDir($dir, $dest, $remove_path, Array $ignoreList = null,$direct=false,$avoidLinks=true) {
        if (in_array($dir,$ignoreList))
                return;
        
        $archive = new \PclZip($dest . '.zip');
        
        if ($direct) {
            $files=self::find($dir,$ignoreList,$avoidLinks);
            
            $v_list=$archive->add($files, PCLZIP_OPT_REMOVE_PATH, $remove_path);
        } else {
            self::copyDir($dir, $dest, $ignoreList,$avoidLinks);
            
            $v_list=$archive->create($dest, PCLZIP_OPT_REMOVE_PATH, $remove_path);
            
            self::rrmDir($dest);
        }
        if ($v_list == 0) {
            die("Error : " . $archive->errorInfo(true));
        }
    }
    
    public static function is_link($filename,$check_hard_link=true) {
        if (is_link($filename))
            return true;
        // if not symlink, check if it's hardlink
        if (!is_dir($filename) && $check_hard_link) {
            $stat=stat($filename);
            return $stat['nlink']>1;
        }
        
        return false;
    }
}

?>
