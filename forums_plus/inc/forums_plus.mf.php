<?php
defined('COT_CODE') or die('Wrong URL');
/**
 * Forums plus plugin for Cotonti Siena CMF
 *      My Forums Controller
 * @package Forums
 * @author Alex <natty-photo@yandex.ru>
 * @copyright (c) 2013 Alex http://portal30.ru
 */
class MfController{

    /**
     * Main (index) Action.
     * Объявления пользователя
     */
    public function indexAction(){
        global $t, $L, $cfg, $usr, $sys, $out, $db_users, $db;
        cot_die_message(404, TRUE);
        return "qwerty";

    }

    /**
     * Последние сообщения пользователя
     */
    public function myPostsAction(){
        global $L, $usr, $cfg, $db, $db_forum_posts, $db_forum_topics, $R, $structure;

        if ($usr['id'] <= 0){
            return false;
        }

        if(COT_AJAX){
            $po = 'more';
        }else{
            $po = cot_import('po', 'G', 'ALP');
        }
        $s = cot_import('s','G','TXT'); // Section CODE
        if(!$s) $s = cot_import('s','P','TXT');

        $where = '';
        if ($s && !empty($structure['forums'][$s])){
            $sections = cot_structure_children('forums', $s);
            $where = " AND t.ft_cat IN ('".implode("', '", $sections)."')";
        }

        $po_max = ($po=='more' && $cfg['plugin']['forums_plus']['mf_maxlines'] >
                                                                    $cfg['plugin']['forums_plus']['mf_deflines']) ?
                        $cfg['plugin']['forums_plus']['mf_maxlines'] : $cfg['plugin']['forums_plus']['mf_deflines'];

        $sqltmp = $db->query("SELECT p.fp_id, t.ft_id, t.ft_cat, t.ft_title, t.ft_desc, t.ft_updated, t.ft_lastposterid,
              t.ft_lastpostername, t.ft_movedto, t.ft_preview
            FROM $db_forum_posts AS p
            LEFT JOIN $db_forum_topics AS t ON t.ft_id=p.fp_topicid
            WHERE fp_posterid=? $where
            GROUP BY t.ft_id
            ORDER BY ft_updated DESC
            LIMIT $po_max", array($usr['id']));

        $tpl = new XTemplate(cot_tplfile('forums_plus.myposts', 'plug'));

        $myPosts = $sqltmp->fetchAll();

        if(empty($myPosts)) return false;


        foreach($myPosts as $mf_row){
            if ($mf_row['ft_movedto'] > 0){
                $mf_row['ft_url'] = cot_url('forums', "m=posts&q=".$mf_row['ft_movedto']);
                $mf_row['lastposter'] = $R['forums_code_post_empty'];
                $mf_row['ft_lastposturl'] = cot_url('forums', "m=posts&q=".$mf_row['ft_movedto']."&n=last", "#bottom");
                $mf_row['ft_lastpostlink'] = cot_rc_link($mf_row['ft_lastposturl'], $R['icon_follow'], 'rel="nofollow"')
                    .$L['Moved'];
            }else{
                $mf_row['ft_url'] = cot_url('forums', "m=posts&q=".$mf_row['ft_id']);
                $mf_row['lastposter'] = cot_build_user($mf_row['ft_lastposterid'], htmlspecialchars($mf_row['ft_lastpostername']));
                $mf_row['ft_lastposturl']  = cot_url('forums', "m=posts&q=".$mf_row['ft_id']."&n=last", "#bottom");
                $mf_row['ft_lastpostlink'] = cot_rc_link($mf_row['ft_lastposturl'], $R['icon_unread'], 'rel="nofollow"').cot_date('datetime_short', $mf_row['ft_updated']);
            }

            $tpl->assign(array(
                'MF_ROW_POST_ID' => $mf_row['fp_id'],
                'MF_ROW_TOPIC_ID' => $mf_row['ft_id'],
                'MF_ROW_TOPIC_TITLE' => htmlspecialchars($mf_row['ft_title']),
                'MF_ROW_TOPIC_DESC' => htmlspecialchars($mf_row['ft_desc']),
                'MF_ROW_TOPIC_UPDATED_STAMP' => $mf_row['ft_updated'],
                'MF_ROW_TOPIC_LASTPOSTER_ID' => $mf_row['ft_lastposterid'],
                'MF_ROW_TOPIC_LASTPOSTER' => $mf_row['lastposter'],
                'MF_ROW_TOPIC_LASTPOSTER_URL' => cot_url('users', 'm=details&id='.$mf_row['ft_lastposterid'].'&u='.
                    $mf_row['ft_lastpostername']),
                'MF_ROW_TOPIC_LASTPOSTER_NAME' => htmlspecialchars($mf_row['ft_lastpostername']),
                'MF_ROW_TOPIC_URL' => $mf_row['ft_url'],
                'MF_ROW_TOPIC_LATSPOST_URL' => $mf_row['ft_lastposturl'],
                'MF_ROW_TOPIC_PREVIEW' => $mf_row['ft_preview'].'...',
                'MF_ROW_TOPIC_UPDATED' => cot_date('datetime_medium', $mf_row['ft_updated']),
                'MF_ROW_TOPIC_UPDATED_STAMP' => $mf_row['ft_updated'],
                'MF_ROW_CAT' => $mf_row['ft_cat'],
                'MF_MODE' => ($po == 'more') ? 'more' : 'default',
            ));
            $tpl->assign(cot_generate_sectiontags($mf_row['ft_cat'], 'MF_ROW_CAT_'));
            $tpl->parse('MAIN.ROW');
        }

        if (empty($s)) $s = '';
        $tpl->assign(array(
            'MF_ROWCOUNT' => $sqltmp->rowCount(),
            'MF_MODE' => ($po == 'more') ? 'more' : 'default',
            'MF_MODE_URL' => ($po != 'more') ? cot_url('forums', 'po=more') : "",
            'MF_SECTION' => $s,
            'MF_MODE_SECTION' => cot_inputbox('hidden', 's', $s, array('id' => 'mf_section')),
        ));

        $tpl->parse('MAIN');

        if(COT_AJAX){
            echo $tpl->text();
            exit();
        }

        return $tpl->text();
    }
}