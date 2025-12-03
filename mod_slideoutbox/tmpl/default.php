<?php
/**
 * @package Slide Out Box Module
 * @version 1.1
 * @license GPLv2
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

//Get the Web Asset Manager
$document = Factory::getApplication()->getDocument();
$wa = $document->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('mod_slideoutbox');

// Get variable values from the parameters
$scroll_depth = $params->get('scroll_depth', 50);
$cookie_expire = $params->get('cookie_expire', 7);
$show_heading = $params->get('show_heading', 0);
$heading_tag = $params->get('heading_tag', 'h2');
$heading_class = $params->get('heading_class', 'display-4');
$heading = $params->get('heading', '');
$main_text = $params->get('main_text', '');
$show_button = $params->get('show_button', 0);
$button_text = $params->get('button_text', '');
$button_url = $params->get('button_url', '');
$button_class = $params->get('button_class', 'btn');
$button_target = $params->get('button_target', 0);
$prepare_content = $params->get('prepare_content', 0);

// Load module assets
$wa->useScript('mod_slideoutbox.slideoutbox');
$wa->useStyle('mod_slideoutbox.slideoutbox');

// Pass parameters to JavaScript
$options = [
    'scrollDepth' => (int)$scroll_depth,
    'cookieExpire' => (int)$cookie_expire,
    'moduleId' => $this->module->id
];
$document->addScriptOptions('mod_slideoutbox', $options);

//Prepare the Slideoutbox output
$heading_html = '';
$main_text_html = '';
$button_html = '';

if ($show_heading && $heading) {
    $heading_html = '<' . htmlspecialchars($heading_tag, ENT_QUOTES, 'UTF-8') . ' class="' . htmlspecialchars($heading_class, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($heading, ENT_QUOTES, 'UTF-8') . '</' . htmlspecialchars($heading_tag, ENT_QUOTES, 'UTF-8') . '>';
} 

if ($main_text) {
    $main_text_html = $prepare_content ? HTMLHelper::_('content.prepare', $main_text) : $main_text;
}

if ($show_button && $button_text && $button_url) {
    $target_attr = $button_target ? ' target="_blank" rel="noopener"' : '';
    $button_html = '<a class="' . htmlspecialchars($button_class, ENT_QUOTES, 'UTF-8') . '" href="' . htmlspecialchars($button_url, ENT_QUOTES, 'UTF-8') . '"' . $target_attr . '>' . htmlspecialchars($button_text, ENT_QUOTES, 'UTF-8') . '</a>';
}
?>

<div class="sbox">
    <div id="sbox-<?php echo $this->module->id; ?>">
        <a class="close"></a>
        <div class="sbox-content">
        <?php echo $heading_html; ?>
        <?php echo $main_text_html; ?>
        <?php echo $button_html; ?>
        </div>
    </div>
</div>