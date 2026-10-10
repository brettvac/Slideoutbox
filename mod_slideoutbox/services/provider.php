<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_slideoutbox
 * @version     1.6
 * @license     GNU General Public License version 2
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Extension\Service\Provider\Module as ModuleServiceProvider;
use Joomla\CMS\Extension\Service\Provider\ModuleDispatcherFactory as ModuleDispatcherFactoryServiceProvider;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

return new class () implements ServiceProviderInterface {
    public function register(Container $container): void
    {
        $container->registerServiceProvider(new ModuleDispatcherFactoryServiceProvider('\\Naftee\\Module\\Slideoutbox'));
        $container->registerServiceProvider(new ModuleServiceProvider());
    }
};