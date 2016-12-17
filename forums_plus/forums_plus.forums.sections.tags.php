<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.sections.tags
Tags=forums.sections.tpl: {FORUMS_SECTIONS_MYPOSTS}
[END_COT_EXT]
==================== */
/**
 * Forums plus plugin for Cotonti Siena CMF
 *
 * @package Forums
 *
 * @author  Kalnov Alexey    <kalnovalexey@yandex.ru>
 * @copyright Portal30 Studio http://portal30.ru
 */
defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('forums_plus', 'plug');
require_once cot_incfile('forums_plus', 'plug', 'mf');
// Lang file
require_once(cot_langfile('forums_plus'));

if (cot::$usr['id']>0) {

    $mf = new MfController();

    $t-> assign(array(
        "FORUMS_SECTIONS_MYPOSTS" => $mf->myPostsAction()
    ));

} else {
    $myforums_myposts = "&nbsp;";
}



