<?php
/**
 * ZFDebug Zend Additions
 *
 * @category   ZFDebug
 * @package    ZFDebug_Controller
 * @subpackage Plugins
 * @author Daniel Remeseiro
 */

/**
 * @category   ZFDebug
 * @package    ZFDebug_Controller
 * @subpackage Plugins
 * @copyright  Copyright (c) 2008-2009 ZF Debug Bar Team (http://code.google.com/p/zfdebug)
 * @license    http://code.google.com/p/zfdebug/wiki/License     New BSD License
 */
class ZFDebug_Controller_Plugin_Debug_Plugin_Xhprof extends Zend_Controller_Plugin_Abstract implements ZFDebug_Controller_Plugin_Debug_Plugin_Interface
{
    /**
     * Contains plugin identifier name
     *
     * @var string
     */
    protected $_identifier = 'xhprof';

    
    /**
     * Creating xhprof plugin
     * @return void
     */
    public function __construct()
    {
        Zend_Controller_Front::getInstance()->registerPlugin($this);
    }

    /**
     * Gets identifier for this plugin
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->_identifier;
    }

    /**
     * Gets menu tab for the Debugbar
     *
     * @return string
     */
    public function getTab()
    {
        if (function_exists('xhprof_enable')) {
            return 'XHprof enabled';
        }
        return 'No XHprof detected';
    }

    /**
     * Gets content panel for the Debugbar
     *
     * @return string
     */
    public function getPanel()
    {
        $panel = '<h4>XHprof</h4>';
         if (function_exists('xhprof_enable')) {
        $panel .= $this->link_profiler;
         }
        return $panel;
    }
    
   
    
    
    /**
     * Defined by Zend_Controller_Plugin_Abstract
     *
     * @param Zend_Controller_Request_Abstract
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        if (function_exists('xhprof_enable')) {

            //this paths maybe differs on your system, check the paths properly
            // if you get this error , the path is not the right
            // Fatal error: Class 'XHProfRuns_Default' not found
            include_once '/usr/local/lib/xhprof_lib/utils/xhprof_lib.php';
            include_once '/usr/local/lib/xhprof_lib/utils/xhprof_runs.php';

            // do not profile builtin functions
            xhprof_enable(XHPROF_FLAGS_NO_BUILTINS + XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY );

            //profile with builtin functions
            //xhprof_enable( XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY );
        }
    }

    /**
     * Defined by Zend_Controller_Plugin_Abstract
     *
     * @param Zend_Controller_Request_Abstract
     * @return void
     */
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        if (function_exists('xhprof_enable')) {
           
            $profiler_namespace = 'nolotiro';  // namespace for your application
            $xhprof_data = xhprof_disable();
            $xhprof_runs = new XHProfRuns_Default();
            $run_id = $xhprof_runs->save_run($xhprof_data, $profiler_namespace);

            // url to the XHProf UI libraries (change the host name and path)
            $profiler_url = sprintf('/xhprof/xhprof_html/index.php?run=%s&source=%s', $run_id, $profiler_namespace);
            $this->link_profiler = '<a href="'. $profiler_url .'" target="_blank">Profiler output</a>';


        }
    }
    
}