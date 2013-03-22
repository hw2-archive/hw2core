<?php namespace Hw2;

S_Core::checkAccess(true);


class S_jTools {

    static public function isHomePage() {
        $app = JFactory::getApplication();
        $menu = $app->getMenu();
        return $menu->getActive() == $menu->getDefault();
    }

    public static function filterCategoriesList(&$options, $extension, $oldcat = null, $oldparent = null) {
        // Get the current user object.
        $user = JFactory::getUser();

        // For new items we want a list of categories you are allowed to create in.
        if ($oldCat == 0) {
            foreach ($options as $i => $option) {
                // To take save or create in a category you need to have create rights for that category
                // unless the item is already in that category.
                // Unset the option if the user isn't authorised for it. In this field assets are always categories.
                if ($user->authorise('core.create', $extension . '.category.' . $option->value) != true) {
                    unset($options[$i]);
                }
            }
        }
        // If you have an existing category id things are more complex.
        else {
            // If you are only allowed to edit in this category but not edit.state, you should not get any
            // option to change the category parent for a category or the category for a content item,
            // but you should be able to save in that category.
            foreach ($options as $i => $option) {
                if ($user->authorise('core.edit.state', $extension . '.category.' . $oldCat) != true && !isset($oldParent)) {
                    if ($option->value != $oldCat) {
                        unset($options[$i]);
                    }
                }
                if ($user->authorise('core.edit.state', $extension . '.category.' . $oldCat) != true
                        && (isset($oldParent)) && $option->value != $oldParent) {
                    unset($options[$i]);
                }

                // However, if you can edit.state you can also move this to another category for which you have
                // create permission and you should also still be able to save in the current category.
                if (($user->authorise('core.create', $extension . '.category.' . $option->value) != true)
                        && ($option->value != $oldCat && !isset($oldParent))) { {
                        unset($options[$i]);
                    }
                }
                if (($user->authorise('core.create', $extension . '.category.' . $option->value) != true)
                        && (isset($oldParent)) && $option->value != $oldParent) { {
                        unset($options[$i]);
                    }
                }
            }
        }
    }
    
    public static function getExtensionParams($element) {
        $db = \Hwj\JFactory::getDbo();
        $query="SELECT params"
                ." FROM #__extensions"
                ." WHERE element=\"".$element."\"";
        $db->setQuery($query);
        $res=$db->loadResult();
        $params=new \Hwj\JRegistry($res);
        return $params;
    }
    
    public static function loadLangFile($extension,$language_tag="en-GB",$base_dir=JPATH_SITE,$reload=true) {
        $lang =& JFactory::getLanguage();
        if (!$language_tag)
            $language_tag = $lang->getTag();
        $lang->load($extension, $base_dir, $language_tag, $reload);
    }
    
    /**
     * unparse the Joomla SEF/non-SEF url to get the internal joomla URL also before the application routing process
     * @param string $var the var name, if not defined it will return the entire query array
     * @param mixed $default default value in case var defined and no value found in query
     * @return mixed
     */
    public static function getUriQuery($var=null,$default=null,$build=false) {
        if (!S_Core::isBackend()) {
            \JURI::current();// It's very strange, but without this line at least Joomla 3 fails to fulfill the task
            // JAdministrator::getRouter() we could use it in backend if needed
            $router =& \JSite::getRouter(); // get router 
            // cloning object instead pass the reference, in this way we won't modify original instance
            $uri=clone \JURI::getInstance(); 
            $query = $router->parse($uri); // Get the real joomla query as an array - parse current joomla link
            $result= is_null($var) ? $query : ( empty($query[$var]) ? $default : $query[$var] );
        } else {
            // in backend sef is not enabled
            $result= is_null($var) ? $_GET : S_Uri::getInput()->get($var,$default,"GET");
        }
        
        return $result;
    }
    
    /**
     * build original url query when sef 
     * @param type $filter
     * @return type
     */
    public static function buildQuery($filter=null) {
        $query=Array();
        if (is_array($filter)) {
            foreach ($filter as $value) {
                $query[$value]=self::getUriQuery($value);
            }
        } else
            $query[$value]=self::getUriQuery($value);
        
        return 'index.php?'.\JURI::getInstance()->buildQuery($query);
    }
    
    /*
     * Return the Request menu parameters as a Hw2jJRegistry() object
     */
    function getMenuParameters($itemId)
    {
            $arrQueryString	= array();
            $db 			= \Hwj\JFactory::getDbo();
            $query 			= $db->getQuery(true);
            $queryString 	= new \Hwj\JRegistry();

            $query->select('link');
            $query->from('#__menu');
            $query->where('id='.(int)$itemId);

            $db->setQuery($query->__toString());

            parse_str(parse_url($db->loadResult(), PHP_URL_QUERY), $arrQueryString);

            $queryString->loadArray($arrQueryString);

            return $queryString;
    }
}

?>
