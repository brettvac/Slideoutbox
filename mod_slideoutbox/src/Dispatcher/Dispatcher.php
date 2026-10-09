<?php
/**
 * @package    Slide Out Box Module
 * @version    1.5
 * @license    GNU General Public License version 2
 */

namespace Naftee\Module\Slideoutbox\Site\Dispatcher;

\defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

class Dispatcher extends AbstractModuleDispatcher
{
    /**
     * Returns the layout data.
     *
     * @return array|false
     */
    protected function getLayoutData(): array|false
    {
        // Get base data (module, app, input, params and template)
        $data = parent::getLayoutData();

        // The parent getLayoutData() puts the module's Registry object into $data['params']
        $params = $data['params'];

        // Check URL filter
        if (!$this->passesUrlFilter($params))
        {
            return false;
        }

        // Check query exclusion
        if (!$this->passesQueryFilter($params))
        {
            return false;
        }

        // Check session restriction
        if (!$this->passesSessionFilter($params))
        {
            return false;
        }

        return $data;
    }

    /**
     * Check whether the current URL passes the "URL contains" filter.
     *
     * If no URL filters are configured, the filter passes. Multiple filter strings are supported, one per line. The current URL only needs to contain one of the strings.
     *
     * @param   object  $params  Module parameters.
     *
     * @return  bool
     */
    private function passesUrlFilter($params): bool
    {
        $filterUrlContains = trim($params->get('filter_url_contains', ''));

        // No filter configured
        if ($filterUrlContains === '')
        {
            return true;
        }

        $uri = Uri::getInstance();

        // Only check the URL path, not the query string
        $currentPath = $uri->getPath();

        // One filter string per line
        $filters = preg_split(
            '/\R+/',
            $filterUrlContains,
            -1,
            PREG_SPLIT_NO_EMPTY
        );

        foreach ($filters as $filter)
        {
            $filter = trim($filter);

            if ($filter !== '' && strpos($currentPath, $filter) !== false)
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Check whether the current query string passes the exclusion filter.
     *
     * @param   object  $params  Module parameters.
     *
     * @return  bool
     */
    private function passesQueryFilter($params): bool
    {
        $excludeQueries = $params->get('exclude_queries', '');

        // No exclusions configured
        if (!$excludeQueries)
        {
            return true;
        }

        $uri = Uri::getInstance();
        $query = $uri->getQuery(true);

        $exclude = array_map('trim', explode(',', $excludeQueries));

        // Flatten query into tokens (keys + values)
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
                return false;
            }
        }

        return true;
    }

    /**
     * Check whether the module passes the "show once per session" restriction.
     *
     * @param   object  $params  Module parameters.
     *
     * @return  bool
     */
    private function passesSessionFilter($params): bool
    {
        $showOnceSession = $params->get('show_once_session', 0);

        // Session restriction disabled
        if (!$showOnceSession)
        {
            return true;
        }

        $session = Factory::getSession();
        $sessionKey = 'mod_slideoutbox_seen_' . $this->module->id;

        if ($session->get($sessionKey))
        {
            return false;
        }

        $session->set($sessionKey, 'seen');

        return true;
    }
}