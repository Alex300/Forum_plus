<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=standalone
[END_COT_EXT]
==================== */
/**
 * Forums plus plugin for Cotonti Siena CMF
 *
 * @package Forums
 * @author Alex <natty-photo@yandex.ru>
 * @copyright (c) 2013 Alex http://portal30.ru
 */
defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('forums', 'module');
require_once cot_incfile('forums_plus', 'plug');
require_once cot_langfile('forums_plus');

// Роутер
// Only if the file exists...
if (!$m) $m = 'main';

if (file_exists(cot_incfile('forums_plus', 'plug', $m))) {
    require_once cot_incfile('forums_plus', 'plug', $m);
    /* Create the controller */
    $_class = ucfirst($m).'Controller';
    $controller = new $_class();

    /* Perform the Request task */
    $shop_action = $a.'Action';
    if (!$a && method_exists($controller, 'indexAction')){
        $content = $controller->indexAction();
    }elseif (method_exists($controller, $shop_action)){
        $content = $controller->$shop_action();
    }else{
        // Error page
        cot_die_message(404);
        exit;
    }

}else{
    // Error page
    cot_die_message(404);
    exit;
}
