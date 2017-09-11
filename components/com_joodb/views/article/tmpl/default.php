<div class="joodb database-article item-page<?php echo $this->params->get('pageclass_sfx')?>">
    <?php // no direct access
    defined('_JEXEC') or die('Restricted access');

    // get the parts
    $parts = JoodbHelper::splitTemplate($this->joobase->tpl_single);

    // parse the template
    $page = new JObject();
    $page->text = JoodbHelper::parseTemplate($this->joobase, $parts, $this->item);


    // render output text
    JoodbHelper::printOutput($page,$this->params);
    ?>
</div>
<script type="text/javascript" >

    // Check if touch device
    function isTouchDevice() {
        try {
            document.createEvent("TouchEvent");
            return true;
        } catch (e) {
            return false;
        }
    }

    // Jquery encapsulation
    (function ($) {

        $(document).ready(function () {

            if (isTouchDevice()) return false;
            if ($('#limit')) {
                $('#limit').change(function(){ submitSearch('setlimit'); });
            }
        });

        $(window).load(function () {
        });

    })(jQuery);

</script>
