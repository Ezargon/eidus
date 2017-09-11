<?php // no direct acces
defined('_JEXEC') or die('Restricted access');

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
                <p>
                    <?php if ($this->params->get('image')!="-1") : ?>
                        <img src="<?php echo $this->baseurl . '/images/'. $this->params->get('image');?>" align="<?php echo $this->params->get('image_align');?>" hspace="6" alt="<?php echo $this->params->get('image');?>" />
                    <?php endif; ?>
                    <?php echo nl2br($this->params->get('description')); ?>
                </p>
                <div style="clear: both"></div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <h2><?php echo JText::_('Your selected items'); ?></h2>
    <?php


    // replace nodata wildcard if data is empty
    if (empty($this->items) && $this->state->get('show_data')!=false) {
        $this->joobase->tpl_list = str_replace("{joodb nodata}" , '<div class="error nodata">'.JText::_('No data found')."</div>" ,$this->joobase->tpl_list);
    }

    $pageparts = preg_split("!{joodb loop}!", $this->joobase->tpl_list);
    if (count($pageparts)<3)
        JError::raiseError(500, "Error in catalog template. Remember 2 loop declarations must be found inside catalog template!");

    $page = new JObject();
    // get header text - remove joodb commands
    $page->text = preg_replace('/\{joodb (.*?)\}/','', $pageparts[0]);

    // do the loop
    if ($this->items) {
        // get the parts
        $parts = JoodbHelper::splitTemplate($pageparts[1]);
        $n=0;
        foreach ( $this->items as $item ) {
            $item->loopclass = ($n%2) ? "odd" : "even";
            $page->text .= JoodbHelper::parseTemplate($this->joobase,$parts,$item,$this->params);
            $n++;
        }
    }

    // get footer text  - remove joodb commands
    $page->text .= preg_replace('/\{joodb (.*?)\}/','', $pageparts[2]);

    // print out the whole text
    JoodbHelper::printOutput($page,$this->params);
    // add pagination
    ?>
    <div class="pagination" style="clear: both; margin: 10px 0; padding: 5px 0; border-bottom: 1px solid #ccc;">
        <?php echo  $this->pagination->getPagesLinks(); ?>
        <?php echo  $this->pagination->getResultsCounter(); ?>
    </div>
    <?php

    ?>
</div>