<?php
/**
 * A view helper that prints out the department tree. This is done from a 
 * view helper because it needs to be called recursively. 
 * 
 * @version $Id: PrintDepartmentTree.php,v 1.3 2007-12-04 16:54:49 seva Exp $
 */

require_once 'Zend/Controller/Front.php';

class Zend_View_Helper_PrintDepartmentTree
{

    /**
     * Iterate and build the HTML for the department tree
     *
     * @param  array $tree
     * @return string
     */
    public function printDepartmentTree($tree)
    {
        if (!is_array($tree) || empty($tree))
            return '';
        
        $html = '<ul>';
        $baseurl = Zend_Controller_Front::getInstance()->getBaseUrl() . '/department/show/name/';
        foreach ($tree as $dep) {
            $html .= '<li><a href="' . $baseurl . urlencode($dep->getName()) . '">' . htmlspecialchars($dep->getName()) . '</a>';
            
            $children = $dep->getChildDepts();
            if (!empty($children))
                $html .= $this->printDepartmentTree($children);
            
            $html .= "</li>\n";
        }
        
        $html .= '</ul>';
        
        return $html;
    }
}