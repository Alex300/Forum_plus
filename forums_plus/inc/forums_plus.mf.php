<?php
defined('COT_CODE') or die('Wrong URL');

/**
 * Forums plus plugin for Cotonti Siena CMF
 *
 * My Forums Controller
 *
 * @package Forums
 *
 * @author  Kalnov Alexey    <kalnovalexey@yandex.ru>
 * @copyright Portal30 Studio http://portal30.ru
 */
class MfController{

    /**
     * Main (index) Action.
     */
    public function indexAction(){
        cot_die_message(404, TRUE);
        return "qwerty";

    }

    /**
     * My posts
     */
    public function myPostsAction()
    {
        if (cot::$usr['id'] <= 0){
            return false;
        }

        if(COT_AJAX) {
            $po = 'more';

        } else {
            $po = cot_import('po', 'G', 'ALP');
        }

        $s = cot_import('s','G','TXT'); // Section CODE
        if(!$s) $s = cot_import('s','P','TXT');

        $where = '';
        if ($s && !empty(cot::$structure['forums'][$s])){
            $sections = cot_structure_children('forums', $s);
            $where = " AND t.ft_cat IN ('".implode("', '", $sections)."')";
        }

        $po_max = ($po=='more' && cot::$cfg['plugin']['forums_plus']['mf_maxlines'] >
            cot::$cfg['plugin']['forums_plus']['mf_deflines']) ?
            cot::$cfg['plugin']['forums_plus']['mf_maxlines'] : cot::$cfg['plugin']['forums_plus']['mf_deflines'];


        $sql = "SELECT t.ft_id, p.fp_id, t.ft_cat, t.ft_title, t.ft_desc, t.ft_updated, t.ft_lastposterid,
              t.ft_lastpostername, t.ft_movedto, t.ft_preview
              
            FROM ".cot::$db->forum_topics." as t
            JOIN ".cot::$db->forum_posts." AS p ON t.ft_id=p.fp_topicid
            JOIN (
                SELECT fp_topicid, max(fp_creation) as max_created
                FROM ".cot::$db->forum_posts."
                WHERE fp_posterid=".cot::$usr['id']."
                GROUP BY fp_topicid
                )fp ON p.fp_creation = fp.max_created
            
            WHERE p.fp_posterid=".cot::$usr['id']." $where
            ORDER BY ft_updated DESC
            LIMIT $po_max";

        $sqltmp = cot::$db->query($sql);

        $tpl = new XTemplate(cot_tplfile('forums_plus.myposts', 'plug'));

        $myPosts = $sqltmp->fetchAll();

        if(empty($myPosts)) return false;


        foreach($myPosts as $mf_row){
            if ($mf_row['ft_movedto'] > 0){
                $mf_row['ft_url'] = cot_url('forums', "m=posts&q=".$mf_row['ft_movedto']);
                $mf_row['lastposter'] = cot::$R['forums_code_post_empty'];
                $mf_row['ft_lastposturl'] = cot_url('forums', "m=posts&q=".$mf_row['ft_movedto']."&n=last", "#bottom");
                $mf_row['ft_lastpostlink'] = cot_rc_link($mf_row['ft_lastposturl'], cot::$R['icon_follow'], 'rel="nofollow"')
                    .cot::$L['Moved'];
            } else {
                $mf_row['ft_url'] = cot_url('forums', "m=posts&q=".$mf_row['ft_id']);
                $mf_row['lastposter'] = cot_build_user($mf_row['ft_lastposterid'], htmlspecialchars($mf_row['ft_lastpostername']));
                $mf_row['ft_lastposturl']  = cot_url('forums', "m=posts&q=".$mf_row['ft_id']."&n=last", "#bottom");
                $mf_row['ft_lastpostlink'] = cot_rc_link($mf_row['ft_lastposturl'], cot::$R['icon_unread'], 'rel="nofollow"').cot_date('datetime_short', $mf_row['ft_updated']);
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

        if(COT_AJAX) {
            echo $tpl->text();
            exit();
        }

        return $tpl->text();
    }
}