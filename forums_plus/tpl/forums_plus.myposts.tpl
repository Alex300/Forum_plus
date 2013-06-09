<!-- BEGIN: MAIN -->
<div id="mf_container">
    <table class="pull-right small marginbottom10 lhn">
        <tr>
            <td colspan="3" class="text-right">
                {PHP.L.fp_you_posted}:
                <!-- IF {MF_MODE} != 'more' -->
                    (<em><a href="{MF_MODE_URL}" class="mf_more">{PHP.L.fp_show_me_more}</em>...)</a>
                    {PHP|cot_xp()} {MF_MODE_SECTION}
                <!-- ENDIF -->
            </td>
        </tr>
        <!-- BEGIN: ROW -->
        <tr>
            <td class="text-right">
                <a href="{MF_ROW_CAT_URL}" title="{MF_ROW_CAT_DESC}">{MF_ROW_CAT_TITLE}</a>
                {PHP.cfg.separator}
                <a href="{MF_ROW_TOPIC_LATSPOST_URL}">{MF_ROW_TOPIC_TITLE|cot_string_truncate('$this', 48, true, false, '...')}</a>
            </td>
            <td class="paddingleft10 text-right">
                {PHP.L.fp_last_post_by}:
                <a href="{MF_ROW_TOPIC_LASTPOSTER_URL}">{MF_ROW_TOPIC_LASTPOSTER_NAME|cot_cutstring('$this', 12)}</a>
            </td>
            <td class="paddingleft10 text-right desc">
                {MF_ROW_TOPIC_UPDATED}
            </td>
        </tr>
        <!-- END: ROW -->
    </table>
    <div class="clearfix"></div>

    <!-- IF {MF_MODE} == 'default' -->
    <script>
        $('.mf_more').click(function(){
            var regParent = $('div#mf_container');
            var x = $('input[name=x]').val();
            var s = $('#mf_section').val();
            var lLeft = Math.floor(regParent.width() / 2 - 110);
            var lTop = Math.floor(regParent.height() / 2 - 9);
            if (lTop > Math.floor(regParent.height())) lTop = 2;

            var bgspan = $('<span>', {
                id: "loading",
                class: "loading"
            })  .css('position', 'absolute')
                    .css('left',lLeft + 'px')
                    .css('top', lTop  + 'px')
                    .css('line-height', 'normal');
            bgspan.html('<img src="./images/spinner.gif" alt="loading"/>');
            regParent.append(bgspan).css('position', 'relative').css('opacity', 0.4);

            $.post('index.php?e=forums_plus&m=mf&a=myPosts', { s: s, x: x }, function(data) {
                var opts = '';
                if(data!=''){
                    $('div#mf_container').replaceWith(data);
                }
                bgspan.remove();
                regParent.css('opacity', 1);
            }, "html");

            return false;
        });
    </script>
    <!-- ENDIF -->
</div>
<!-- END: MAIN -->
