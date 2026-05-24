<?php
/**
 * @package Slide Out Box Module
 * @version 1.2
 * @license GNU General Public License version 2
 */

\defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;

//Get the Web Asset Manager
$document = $app->getDocument();
/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $document->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('mod_slideoutbox');

// Load module script and style as per joomla.asset.json
$wa->useScript('mod_slideoutbox.slideoutbox');
$wa->useStyle('mod_slideoutbox.slideoutbox');

// Prepare the options array
$scroll_depth = $params->get('scroll_depth', 50);
$cookie_expire = $params->get('cookie_expire', 7);
$options = [
    'scrollDepth' => (int)$scroll_depth,
    'cookieExpire' => (int)$cookie_expire,
    'moduleId' => $module->id
];

// Pass options to JavaScript 
$document->addScriptOptions('mod_slideoutbox', $options);

// Get remaining variable values from the parameters
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
    <div id="sbox-<?php echo $module->id; ?>">
        <a class="close"></a>
        <div class="sbox-content">
        <?php echo $heading_html; ?>
        <?php echo $main_text_html; ?>
        <?php echo $button_html; ?>
        </div>
    </div>
</div>