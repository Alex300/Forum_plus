<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.topics.tags
Tags=forums.topics.tpl: {FORUMS_TOPICS_MYPOSTS}
[END_COT_EXT]
==================== */
/**
 * Forums plus plugin for Cotonti Siena CMF
 *
 * @package Forums
 * @author Alex
 * @copyright (c) 2013 Alex http://portal30.ru
 */
defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('forums_plus', 'plug');
require_once cot_incfile('forums_plus', 'plug', 'mf');
// Lang file
require_once(cot_langfile('forums_plus'));

if ($usr['id']>0){

    $mf = new MfController();

    $t-> assign(array(
        "FORUMS_TOPICS_MYPOSTS" => $mf->myPostsAction()
    ));

}
else
{
    $myforums_myposts = "";
}



