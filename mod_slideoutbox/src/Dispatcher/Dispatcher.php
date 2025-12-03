<?php
/**
 * @package Slide Out Box Module
 * @version 1.1
 * @license GPLv2
 */

namespace Naftee\Module\Slideoutbox\Site\Dispatcher;

\defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\Registry\Registry;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

class Dispatcher extends AbstractModuleDispatcher
{
    protected function getLayoutData(): array
    {
        return parent::getLayoutData();
    }
    
    public function dispatch()
    {
        
        // Get the module parameters from manifest file
        $params = new Registry($this->module->params);
        $exclude_queries = $params->get('exclude_queries', '');
        $show_once_session = $params->get('show_once_session', 0);
        
        // Check query exclusion
        if ($exclude_queries)
          {  
          $query = Uri::getInstance()->getQuery();
          $exclude = array_map('trim', explode(',', $exclude_queries));
    
          foreach ($exclude as $string) {
             if ($string && strpos($query, $string) !== false) {
               return; // Don't show the Slideoutbox if query string contains excluded term
             }
          }
        }

       // Check session variable if set to show only once per session
       if ($show_once_session) {
          $session = Factory::getSession();
          $sessionKey = 'mod_slideoutbox_seen_' . $this->module->id;
     
         if ($session->get($sessionKey)) {
              return; // Skip rendering if already seen this session
          }
    
          $session->set($sessionKey, 'seen'); // Mark as seen
        }

        // Load the layout
        require ModuleHelper::getLayoutPath('mod_slideoutbox', $params->get('layout', 'default'));
    }
}