<?php
/**
 * @package    Slide Out Box Module
 * @version    1.4
 * @license    GNU General Public License version 2
 */

namespace Naftee\Module\Slideoutbox\Site\Dispatcher;

\defined('_JEXEC') or die; 
//No direct access

use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

class Dispatcher extends AbstractModuleDispatcher
    {
    /**
     * Returns the layout data.
     *
     * @return  array|false
     */
    protected function getLayoutData(): array|false
        {  
        // Get base data (module, app, input, params and template)
        $data = parent::getLayoutData();

        // The parent getLayoutData() puts the module's Registry object into $data['params']
        $params = $data['params'];
        
        // Get the module parameters from manifest file
        $exclude_queries = $params->get('exclude_queries', '');
        
        // Check query exclusion
        if ($exclude_queries)
        {
            /** @var Joomla\CMS\Uri\Uri $uri */
            $uri = Uri::getInstance();
            $query = $uri->getQuery(true); // Return the query as a key => value pair array

            $exclude = array_map('trim', explode(',', $exclude_queries));

            // flatten query into tokens (keys + values)
            $tokens = [];

            foreach ($query as $key => $value)
            {
                $tokens[] = (string) $key;
                $tokens[] = (string) $value;
            }

            foreach ($exclude as $string)
            {
                if ($string === '')
                {
                    continue;
                }

                if (in_array($string, $tokens, true))
                {
                    return false;  // Don't show the Slideoutbox if query string contains excluded term
                }
            }
        }
       
       // Check session variable if set to show only once per session
       $show_once_session = $params->get('show_once_session', 0);
       
       if ($show_once_session) {
          $session = Factory::getSession();
          $sessionKey = 'mod_slideoutbox_seen_' . $this->module->id;
     
         if ($session->get($sessionKey)) {
              return false; // Skip rendering if already seen this session
          }
    
          $session->set($sessionKey, 'seen'); // Mark as seen
        }

        return $data;
        }
    }