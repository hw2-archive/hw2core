<?php namespace Hw2;
S_Core::checkAccess(true);

class S_jHtml
{
        public static function renderArticleLink($articleId,$catid,$content) {
            $seflink = 'index.php?option=com_content&view=article&id='. $articleId . '&catid=' . $catid;
            return "<a href=\"".$seflink."\">".$content."</a>";
        }
        
        public static function renderCategoryLink($catid,$content) {
            $seflink = 'index.php?option=com_content&view=category&id='. $catid .'&Itemid='.S_Uri::getInput()->getInt("Itemid");
            return "<a href=\"".$seflink."\">".$content."</a>";
        }
        
        public static function renderReadMore($articleId,$catid) {
            $readMore = "Read More..";
            return self::renderArticleLink($articleId, $catid, $readMore);
        } 
}


?>
