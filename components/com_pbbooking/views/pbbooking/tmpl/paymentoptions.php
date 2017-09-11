<?php
/**
 * @license	    GNU General Public License version 2 or later; see  LICENSE.txt
 * @author      Eric Fernance
 *
 * @copyright   Eric Fernance
 */

defined("_JEXEC") or die("Invalid Direct Access");

JHtml::_("jquery.framework");
JHtml::_("stylesheet","com_pbbooking/pbbooking-site.css",array(),true);
JHtml::_("script",'com_pbbooking/app/com_pbbooking.checkout.js',true,true);
JText::script("COM_PBBOOKING_PAYMENT_ERROR_NO_PROVIDER_CHOSEN");

$doc = JFactory::getDocument();

?>

<h1><?php echo $doc->title;?></h1>
<div class="pbbooking checkout">
	
	<form action="#" method="POST" id="payment">
		<input type="hidden" name="option" value="com_pbbooking"/>
		<input type="hidden" name="task" value="pbbooking.doPluginPayment"/>
		<input type="hidden" name="event_id" value="<?php echo $this->event->id;?>"/>
		<input type="hidden" name="service_id" value="<?php echo $this->service->id;?>"/>
		<div class="box booking-details">
			<div class="box-heading"><h3><?php echo JText::_("COM_PBBOOKING_BOOKINGDETAILS");?></h3></div>
			<div class="body">
				<table>
					<thead>
						<tr><th><h4><?php echo JText::_("COM_PBBOOKING_SUCCESS_SUB_HEADING");?></h4></th><th><h4><?php echo JText::_("COM_PBBOOKING_PAYMENT_AMOUNT_OWING");?></h4> </th></tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<?php echo htmlentities($this->event->getService()->name);?><br/>
								<?php echo JHtml::_("date", $this->event->dtstart->format(DATE_ATOM),JText::_("COM_PBBOOKING_SUCCESS_DATE_FORMAT") );?><br/>
								<?php echo JHtml::_("date", $this->event->dtstart->format(DATE_ATOM), JText::_("COM_PBBOOKING_SUCCESS_TIME_FORMAT"));?>
							</td>
							<td>
								<?php echo \Pbbooking\Pbbookinghelper::pbb_money_format($this->event->getService()->price);?>
								<?php 
									$service = $this->event->getService();
									if ($service->pointscost && $service->pointscost !== 0 && $service->pointscost !== "") {
										echo JText::_("COM_PBBOOKING_SERVICE_POINTS_OR").$service->pointscost." ".JText::_("COM_PBBOOKING_SERVICE_POINTS_SUFFIX");
									}
								?>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="box payment-details">
			<div class="box-heading"><h3><?php echo JText::_("COM_PBBOOKING_PAYMENT_PAYMENT_OPTIONS");?></h3></div>
			<div class="body">
				<?php foreach ($this->providers as $provider):?>
					<input type="radio" name="provider" value="<?php echo $provider['key'];?>">
					<label><?php echo $provider['name'];?></label>
				<?php endforeach;?>
			</div>
		</div>

		<div style="clear:both;"></div>

		<input type="submit" value="Pay Now"/>

	</form>

</div>
