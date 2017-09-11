<?php // no direct acces
defined('_JEXEC') or die('Restricted access');
$app = JFactory::getApplication();
?>
<div class="database-list<?php echo $this->params->get('pageclass_sfx')?>">
    <?php if ($this->params->get('show_page_heading') || $this->params->get( 'show_description')) : ?>
        <div class="page-header">
            <?php if ($this->params->get('show_page_heading')) : ?>
                <h1>
                    <?php echo $this->escape($this->params->get('page_heading')); ?>
                </h1>
            <?php endif; ?>
            <?php if ( $this->params->get( 'show_description') && $this->pagination->limitstart==0 ) : ?>
                <div class="introtext">
                    <?php if ($this->params->get('image')!="-1") : ?>
                        <img src="<?php echo $this->baseurl."/".$this->params->get('image');?>" style="float: <?php echo $this->params->get('image_align');?>; margin: 0 15px;" alt="*" />
                    <?php endif; ?>
                    <?php echo nl2br($this->params->get('description')); ?>
                </div>
                <div style="clear: both"></div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <form name="searchForm" id="searchForm"  method="get" class="form-inline" action="<?php echo JURI::current(); ?>"  >
        <input type="hidden" name="option" value="com_joodb"/>
        <input type="hidden" name="view" value="catalog"/>
        <input type="hidden" name="format" value="html"/>
        <input type="hidden" name="layout" value="list"/>
        <input type="hidden" name="reset" value="false"/>
        <input type="hidden" name="ordering" value="<?php echo $app->input->get("ordering"); ?>"/>
        <input type="hidden" name="orderby" value="<?php echo $app->input->getString("orderby"); ?>"/>
        <input type="hidden" name="Itemid" value="<?php echo $app->input->getInt("Itemid"); ?>"/>
        <input type="hidden" name="task" value=""/>
        <?php

        $pageparts = preg_split("!{joodb loop}!", $this->joobase->tpl_list);
        if (count($pageparts)<3) {
            JError::raiseError(500, "Error in catalog template. Remember 2 loop declarations must be found inside catalog template!");
        }


        // get header text
        $parts = JoodbHelper::splitTemplate($pageparts[0]);

        $valid_functions = array("groupselect","searchbox","searchbutton","searchfield","resetbutton","translate","limitbox","groupselect");
        foreach ($parts AS $n => $part) {
            if (!empty($part->function) && in_array($part->function,$valid_functions)) {
                $parts[$n]->function = null;
            }
        }

        $page = new JObject();
        $page->text = $this->_parseTemplate($parts);

        // do the loop
        if ($this->items) {
            // get the parts
            $parts = JoodbHelper::splitTemplate($pageparts[1]);
            foreach ( $this->items as $n=>$item ) {
                $item->loopclass = ($n%2==0) ? "odd" : "even";
                $page->text .= JoodbHelper::parseTemplate($this->joobase,$parts,$item,$this->params);
            }
        }

        // get footer text
        $parts = JoodbHelper::splitTemplate($pageparts[2]);
        $page->text .= $this->_parseTemplate($parts);
        // render output text
        JoodbHelper::printOutput($page,$this->params,"catalog");


        $params	= JComponentHelper::getParams('com_joodb');
        if ($params->get('show_icons','1')==1) {
            $text = JHtml::image('components/com_joodb/assets/images/back.png', JText::_('BACK') );
        } else {
            $text = JText::_('BACK');
        }
        echo "<p>".JHtml::link('javascript:submitSearch(\'search\');', $text,array('title'=>  JText::_('BACK'),'class'=>'backbutton'))."</p>";
        ?>

    </form>
    <div style="clear: both;"></div>
</div>
<script type="text/javascript" >


    // Submit search form
    function submitSearch(task) {
        var form = document.searchForm;
        form.format.value="html";
        if (task=="xportxls") {
            form.format.value="xls";
        }

        if (task=="search") {
            form.layout.value="search";
        }

        form.submit();
    }

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

        $(window).load(function () {
            if (isTouchDevice()) return false;
            if ($('#limit')) {
                $('#limit').change(function(){ submitSearch('setlimit'); });
            }
        });

    })(jQuery);

</script>
