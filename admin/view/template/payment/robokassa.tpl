<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<button type="submit" form="form-globalpay" data-toggle="tooltip" title="<?php echo $$button_save_and_go; ?>" class="btn btn-primary" onclick="$('#robokassa_stay').attr('value', '0'); $('#form').submit();"><i class="fa fa-save"></i></button>
				<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
				<a onclick="$('#robokassa_stay').attr('value', '1'); $('#form').submit();" class="btn btn-default"><?php echo $button_save_and_stay; ?></a>
			</div>
			<h1><?php echo $heading_title; ?></h1>
			<ul class="breadcrumb">
			<?php foreach ($breadcrumbs as $breadcrumb) { ?>
				<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
			<?php } ?>
			</ul>
		</div>
	</div>

	<div class="container-fluid">
		<?php if ($error_warning) { ?>
		<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> 
			<?php foreach ($error_warning as $value) {
			echo '<p>'.$value.'</p>';
			} ?>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>
		<?php } ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
			</div>
			<div class="panel-body">


			<form action="<?php echo $action; ?>" method="post"  autocomplete="off" enctype="multipart/form-data" id="form" class="form-horizontal">
			<input type="hidden" name="robokassa_stay" id="robokassa_stay" value="0">
			<div class="alert alert-warning"><i class="fa fa-exclamation-circle"></i> <?php echo $notice; ?></div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="robokassa_status"><?php echo $entry_status; ?></label>
				<div class="col-sm-10">
					<select name="robokassa_status" id="robokassa_status" class="form-control">
						<?php if ($robokassa_status) { ?>
						<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
						<option value="0"><?php echo $text_disabled; ?></option>
						<?php } else { ?>
						<option value="1"><?php echo $text_enabled; ?></option>
						<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="robokassa_shop_login"><?php echo $entry_shop_login; ?></label>
				<div class="col-sm-10">
					<input type="text" class="form-control" id="robokassa_shop_login" name="robokassa_shop_login" value="<?php echo $robokassa_shop_login; ?>" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="robokassa_password1"><?php echo $entry_password1; ?></label>
				<div class="col-sm-10">
					<input type="password" class="form-control" id="robokassa_password1" name="robokassa_password1" /> <?php if($robokassa_password1) echo $text_saved; ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="robokassa_password2"><?php echo $entry_password1; ?></label>
				<div class="col-sm-10">
					<input type="password" class="form-control" id="robokassa_password2" name="robokassa_password2" /> <?php if($robokassa_password2) echo $text_saved; ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="robokassa_icons"><?php echo $entry_icons; ?></label>
				<div class="col-sm-10">
					<select name="robokassa_icons" id="robokassa_icons" class="form-control">
					<?php if ($robokassa_icons) { ?>
					<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
					<option value="0"><?php echo $text_disabled; ?></option>
					<?php } else { ?>
					<option value="1"><?php echo $text_enabled; ?></option>
					<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
					<?php } ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><?php echo $entry_result_url; ?></label>
				<div class="col-sm-10">
					http://<?php echo $_SERVER['HTTP_HOST']; ?>/index.php?route=payment/robokassa/result
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><?php echo $entry_result_method; ?></label>
				<div class="col-sm-10">POST</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><?php echo $entry_success_url; ?></label>
				<div class="col-sm-10">
					http://<?php echo $_SERVER['HTTP_HOST']; ?>/index.php?route=checkout/success
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><?php echo $entry_success_method; ?></label>
				<div class="col-sm-10">
					POST
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><?php echo $entry_fail_url; ?></label>
				<div class="col-sm-10">
					http://<?php echo $_SERVER['HTTP_HOST']; ?>/index.php?route=payment/robokassa/fail
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><?php echo $entry_fail_method; ?></label>
				<div class="col-sm-10">
					POST
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="robokassa_test_mode"><?php echo $entry_test_mode; ?></label>
				<div class="col-sm-10">
					<select id="robokassa_test_mode" name="robokassa_test_mode" class="form-control">
					<?php if ($robokassa_test_mode) { ?>
					<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
					<option value="0"><?php echo $text_disabled; ?></option>
					<?php } else { ?>
					<option value="1"><?php echo $text_enabled; ?></option>
					<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
					<?php } ?>
					</select>
					<div class="alert alert-danger"><?php echo $text_mode_notice; ?></div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="robokassa_confirm_status"><?php echo $entry_confirm_status; ?></label>
				<div class="col-sm-10">
					<select name="robokassa_confirm_status" id="robokassa_confirm_status" class="form-control" onchange="show_hide_block(this.value)">
					<?php if ($robokassa_confirm_status == 'after') { ?>

					<option value="before"><?php echo $entry_confirm_status_before; ?></option>
					<option value="after" selected="selected"><?php echo $entry_confirm_status_after; ?></option>

					<?php } else { ?>

					<option value="before" selected="selected"><?php echo $entry_confirm_status_before; ?></option>
					<option value="after"><?php echo $entry_confirm_status_after; ?></option>

					<?php } ?>
					</select>
					<div class="alert alert-notice"><?php echo $entry_confirm_status_notice; ?></div>
				</div>
			</div>
			<div id="dopmail">
				<div class="form-group">
					<label class="col-sm-2 control-label" for="robokassa_confirm_notify"><?php echo $entry_confirm_notify; ?></label>
					<div class="col-sm-10">
						<select name="robokassa_confirm_notify" id="robokassa_confirm_notify" class="form-control">
						<?php if ($robokassa_confirm_notify) { ?>
						<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
						<option value="0"><?php echo $text_disabled; ?></option>
						<?php } else { ?>
						<option value="1"><?php echo $text_enabled; ?></option>
						<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
						<?php } ?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label"><?php echo $entry_confirm_comment; ?></label>
					<div class="col-sm-10">
						<?php foreach ($languages as $language) { ?>
						<div class="input-group">
							<span class="input-group-addon">
								<img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>">
							</span>
							<textarea class="form-control" rows="5"
							name="robokassa_confirm_comment[<?php echo $language['code']; ?>]"
							><?php echo isset($robokassa_confirm_comment[$language['code']]) ? $robokassa_confirm_comment[$language['code']] : '';
							?></textarea>
						</div>
						<?php } ?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">
						<span data-toggle="tooltip" title="<?php echo $entry_order_comment_notice; ?>"><?php echo $entry_order_comment; ?></span>
					</label>
					<div class="col-sm-10">
						<?php foreach ($languages as $language) { ?>
						<div class="input-group">
							<span class="input-group-addon">
								<img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>">
							</span>
							<textarea rows="5" class="form-control"
							name="robokassa_order_comment[<?php echo $language['code']; ?>]"
							><?php echo isset($robokassa_order_comment[$language['code']]) ? $robokassa_order_comment[$language['code']] : '';
							?></textarea>
						</div>
						<?php } ?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="robokassa_preorder_status_id"><?php echo $entry_preorder_status; ?></label>
					<div class="col-sm-10">
						<select name="robokassa_preorder_status_id" id="robokassa_preorder_status_id" class="form-control">
							<?php foreach ($order_statuses as $order_status) { ?>
							<?php if ($order_status['order_status_id'] == $robokassa_preorder_status_id) { ?>
							<option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
							<?php } else { ?>
							<option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
							<?php } ?>
							<?php } ?>
						</select>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="robokassa_order_status_id"><?php echo $entry_order_status; ?></label>
				<div class="col-sm-10">
					<select name="robokassa_order_status_id" class="form-control" id="robokassa_order_status_id">
					<?php foreach ($order_statuses as $order_status) { ?>
					<?php if ($order_status['order_status_id'] == $robokassa_order_status_id) { ?>
					<option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
					<?php } else { ?>
					<option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
					<?php } ?>
					<?php } ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="robokassa_geo_zone_id"><?php echo $entry_geo_zone; ?></label>
				<div class="col-sm-10">
					<select name="robokassa_geo_zone_id" id="robokassa_geo_zone_id" class="form-control">
					<option value="0"><?php echo $text_all_zones; ?></option>
					<?php foreach ($geo_zones as $geo_zone) { ?>
					<?php if ($geo_zone['geo_zone_id'] == $robokassa_geo_zone_id) { ?>
					<option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
					<?php } else { ?>
					<option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
					<?php } ?>
					<?php } ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="robokassa_commission"><?php echo $entry_commission; ?></label>
				<div class="col-sm-10">
					<select name="robokassa_commission" id="robokassa_commission" class="form-control">
					<option value="j" <?php if( $robokassa_commission == 'j' ) { ?> selected <?php } ?>
					><?php echo $text_commission_j; ?></option>
					<option value="customer" <?php if( $robokassa_commission == 'customer' ) { ?> selected <?php } ?>
					><?php echo $text_commission_customer; ?></option>
					<option value="shop" <?php if( $robokassa_commission == 'shop' ) { ?> selected <?php } ?>
					><?php echo $text_commission_shop; ?></option>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="robokassa_sort_order"><?php echo $entry_sort_order; ?></label>
				<div class="col-sm-10">
					<input type="text" name="robokassa_sort_order" id="robokassa_sort_order" value="<?php echo $robokassa_sort_order; ?>" class="form-control" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="robokassa_currency"><?php echo $entry_currency; ?></label>
				<div class="col-sm-10">
					<select name="robokassa_currency" id="robokassa_currency" class="form-control">
					<?php foreach( $opencart_currencies as $currency=>$val ) { ?>
					<option value="<?php echo $currency; ?>" <?php if( ($robokassa_currency && $currency==$robokassa_currency) || (!$robokassa_currency && $currency=='RUB' ) ) { ?> selected <?php } ?> ><?php echo $currency; ?></option>
					<?php } ?>
					</select>
					<div class="alert alert-notice"><?php echo $text_currency_notice; ?></div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="robokassa_interface_language"><?php echo $entry_interface_language; ?></label>
				<div class="col-sm-10">
					<select name="robokassa_interface_language" id="robokassa_interface_language" onchange="show_hide_lang_block(this.value)" class="form-control">
					<option value="ru" 
					<?php if( $robokassa_interface_language == 'ru' ) { ?>
					selected="selected"
					<?php } ?>
					><?php echo $entry_interface_language_ru; ?></option>
					<option value="en"
					<?php if( $robokassa_interface_language == 'en' ) { ?>
					selected="selected"
					<?php } ?>><?php echo $entry_interface_language_en; ?></option>
					<option value="detect"
					<?php if( $robokassa_interface_language == 'detect' ) { ?>
					selected="selected"
					<?php } ?>><?php echo $entry_interface_language_detect; ?></option>
					</select>
				</div>
			</div>
			<div class="form-group" id="lang_block">
				<label class="col-sm-2 control-label" for="robokassa_default_language"><?php echo $entry_default_language; ?></label>
				<div class="col-sm-10">
					<select name="robokassa_default_language" id="robokassa_default_language" class="form-control">
					<option value="ru" 
					<?php if( $robokassa_default_language == 'ru' ) { ?>
					selected="selected"
					<?php } ?>
					><?php echo $entry_interface_language_ru; ?></option>
					<option value="en"
					<?php if( $robokassa_default_language == 'en' ) { ?>
					selected="selected"
					<?php } ?>><?php echo $entry_interface_language_en; ?></option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="robokassa_log"><?php echo $entry_log; ?></label>
				<div class="col-sm-10">
					<select name="robokassa_log" id="robokassa_log" class="form-control">
					<?php if ($robokassa_log) { ?>
					<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
					<option value="0"><?php echo $text_disabled; ?></option>
					<?php } else { ?>
					<option value="1"><?php echo $text_enabled; ?></option>
					<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
					<?php } ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="robokassa_"><?php echo $entry_methods; ?></label>
				<div class="col-sm-10">
					<?php if( !$robokassa_shop_login ) { ?>
						<h3><?php echo $entry_no_methods; ?></h3>           
					<?php } elseif( !$currencies ) { ?>
						<h3><?php echo $entry_no_robokass_methods; ?></h3>
					<?php } else { ?>
							<p><?php echo $text_img_notice; ?></p>
							<table class="table table-bordered">
							<thead>
								<tr>
									<th>#</th>
									<th><?php echo $methods_col1; ?></th>
									<th><?php echo $methods_col2; ?></th>
									<th><?php echo $methods_col3; ?></th>
								</tr>
							</thead>
							<tbody>
							<?php for($i=0; $i<20; $i++) { ?>
								<tr>
									<td>#<?php echo ($i+1); ?></td>
									<td>
									<?php foreach ($languages as $language) { ?>
										<input type="text" class="form-control" name="robokassa_methods[<?php echo $i; ?>][<?php echo $language['code']; ?>]"
										value="<?php 
										if( !empty($robokassa_methods[$i][$language['code']]) )
										echo $robokassa_methods[$i][$language['code']]; ?>"
										>&nbsp;<img src="view/image/flags/<?php echo $language['image']; ?>" 
										title="<?php echo $language['name']; ?>" />
										<?php } ?>
									</td>
									<td>
										<select name="robokassa_currencies[<?php echo $i; ?>]" onchange="show_img(<?php echo $i; ?>, this.value)" class="form-control">
											<option value="0"><?php echo $select_currency; ?></option>
										<?php foreach($currencies as $key=>$val) { ?>
											<option value="<?php echo $key; ?>" <?php 
											if( $robokassa_currencies[$i]==$key ) { ?>selected<?php }?>
											><?php echo $val; ?></option>
										<?php } ?>
											<option value="robokassa" <?php 
											if( $robokassa_currencies[$i]=='robokassa' ) { ?> selected <?php } ?>
											><?php echo $text_robokassa_method; ?></option>
										</select>
									</td>
									<td>
										<div id="img<?php echo $i; ?>" style="display: <?php 
											if( !empty($robokassa_currencies[$i]) ) echo 'block'; else echo 'none';?>;">
											<img src="<?php echo $robokassa_images[$i]['thumb']; ?>" id="thumb_<?php echo $i; ?>">
											<input type="hidden" name="robokassa_images[]" id="image_<?php echo $i; ?>" value="<?php echo $robokassa_images[$i]['value']; ?>">
												<br>
											<a onclick="image_upload('image_<?php echo $i; ?>', 'thumb_<?php echo $i; ?>');"><?php echo $text_browse; ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="$('#thumb_<?php echo $i; ?>').attr('src', '<?php echo $no_image; ?>'); $('#image_<?php echo $i; ?>').attr('value', '');"><?php echo $text_clear; ?></a>
										</div>
									</td>
								</tr>
							<?php } ?>
						</tbody></table>
				<?php } ?>
					</div>
				</div>
			</form>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	
	var all_images = new Array();
	var all_images2 = new Array();
	<?php foreach($all_images as $key=>$val) { ?>
	all_images["<?php echo $key; ?>"] = "<?php echo $val['value']; ?>";
	all_images2["<?php echo $key; ?>"] = "<?php echo $val['thumb']; ?>";
	<?php } ?>
	
	function show_img(ID, value)
	{
		$('#thumb_' + ID).replaceWith('<img src="' + all_images2[value] + '" alt="" id="thumb_' + ID + '" />');
		
		$('#image_' + ID).attr('value', all_images[value]);
		
		if( value!=0 )
		document.getElementById('img'+ID).style.display = 'block';
		else
		document.getElementById('img'+ID).style.display = 'none';
	}
	
	function show_hide_lang_block( value )
	{
		if( value=='detect' )
		{
			document.getElementById('lang_block').style.display = 'block';
		}
		else
		{
			document.getElementById('lang_block').style.display = 'none';
		}
	}
	
	show_hide_lang_block( document.getElementById('robokassa_interface_language').value );
	
	function show_hide_block( value )
	{
		if( value=='before' )
		{
			document.getElementById('dopmail').style.display = 'block';
		}
		else
		{
			document.getElementById('dopmail').style.display = 'none';
		}
	}

	show_hide_block( document.getElementById('robokassa_confirm_status').value );

function image_upload(field, thumb) {
	$('#dialog').remove();
	
	$('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe src="index.php?route=common/filemanager&token=<?php echo $token; ?>&field=' + encodeURIComponent(field) + '&directory=robokassa_icons" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');
	
	$('#dialog').dialog({
		title: '<?php echo $text_image_manager; ?>',
		close: function (event, ui) {
			if ($('#' + field).attr('value')) {
				$.ajax({
					url: 'index.php?route=payment/robokassa/image&token=<?php echo $token; ?>&image=' + encodeURIComponent($('#' + field).attr('value')),
					dataType: 'text',
					success: function(text) {
						$('#' + thumb).replaceWith('<img src="' + text + '" alt="" id="' + thumb + '" />');
					}
				});
			}
		},	
		bgiframe: false,
		width: 800,
		height: 400,
		resizable: false,
		modal: false
	});
};
</script> 
<?php echo $footer; ?> 