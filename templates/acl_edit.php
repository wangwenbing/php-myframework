<?php include(TPL_PATH . '/inc_header.php'); ?>

<style>
<!--
body {font:14px arial; }
fieldset {border:2px threedface solid;}
table {margin-top:10px; border-top:2px threedface solid; border-left:2px threedface solid;}
table td, table th {padding:2px 5px 2px 5px; height:30px; border-right:2px threedface solid; border-bottom:2px threedface solid; text-align:left;}
table .text_center {text-align:center;}
table .text_right {text-align:right;}
-->
</style>

<h1><?php p($strTitle) ?></h1>

<p><a href="<?php p(HTTP_URL) ?>/?c=acl">«back to index ?</a></p><br />

<fieldset>
	<legend>&nbsp;<b>编辑元素</b>&nbsp;</legend>
	<form action="<?php p(HTTP_URL); ?>/?c=acl&a=edit&id=<?php p($id) ?>" method="post">
		中文名称:<input type="text" name="name" value="<?php p($arrData['name']) ?>" />&nbsp;
		英文名称:<input type="text" name="name_en" value="<?php p($arrData['name_en']) ?>" />&nbsp;
		说明文字:<input type="text" name="title" size="30" value="<?php p($arrData['title']) ?>" />&nbsp;
		<input type="submit" name="sub_btn_0" value="保存修改" />
	</form>
</fieldset>

<?php if ('g' == $arrData['type']) { ?>
<form action="<?php p(HTTP_URL); ?>/?c=acl&a=edit&id=<?php p($id) ?>" method="post">
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<th class="text_center" rowspan="2">操作对象</th>
		<th class="text_center" colspan="<?php p(count($arrAction) + 1) ?>">操作类型</th>
	</tr>
	<tr>
		<td class="text_right"><label title="所有全选"><input id="qs_all" class="qs" type="checkbox" /><i>全选</i></label></td>
		<?php foreach ($arrAction as $arrC) { ?>
		<td><label title="竖向全选"><input class="qs all_box" id="qs_s_<?php p($arrC['id']) ?>" type="checkbox" /><i>全选</i></label></td>
		<?php } ?>
	</tr>
	<?php foreach ($arrProject as $arrP) { ?>
	<tr>
		<td class="text_center" title="<?php p($arrP['title']) ?>"><?php p($arrP['name']) ?></td>
		<td class="text_right"><label title="横向全选"><input class="qs all_box" id="qs_h_<?php p($arrP['id']) ?>" type="checkbox" /><i>全选</i></label></td>
		<?php foreach ($arrAction as $arrC) { ?>
		<td><label title="<?php p($arrC['title']) ?>"><input id="<?php p($arrP['id']) ?>_<?php p($arrC['id']) ?>" name="checked_values[]" value="<?php p($arrP['id']) ?>|<?php p($arrC['id']) ?>|<?php p($arrP['name_en']) ?>|<?php p($arrC['name_en']) ?>" class="all_box h_<?php p($arrP['id']) ?> s_<?php p($arrC['id']) ?>" type="checkbox" /><?php p($arrC['name']) ?></label></td>
		<?php } ?>
	</tr>
	<?php } ?>
	<tr>
		<th class="text_center" colspan="<?php p(count($arrAction) + 2) ?>">
			<input type="submit" name="sub_btn" value="保存设置" />
		</th>
	</tr>
</table>
</form>

<script type="text/javascript">
var blnTemp;
var strCheck = '<?php p($strCheck) ?>';
$(document).ready(function() {
	if (strCheck.length > 1) {
		$(strCheck).each(function() {
			this.checked = true;
		});
	}
	set_bg();
});

function set_bg() {
	$(":checkbox[name='checked_values[]']").each(function() {
		$(this).parent().parent().css('background-color', this.checked ? 'silver' : 'white');
	});
	setTimeout(set_bg, 500);
}

$('.qs').each(function() {
	$(this).click(function() {
		var _this = $(this);
		var id = _this.attr('id');
		var d = 'all';
		if ('qs_all' == id) {
			id = 'box';
		} else if (id.indexOf('qs_s_') > -1) {
			id = id.replace('qs_s_', '');
			d = 's';
		} else if (id.indexOf('qs_h_') > -1) {
			id = id.replace('qs_h_', '');
			d = 'h';
		}
		blnTemp = this.checked;
		$('.' + d + '_' + id).each(function() {
			this.checked = blnTemp;
		});
	});
});
</script>
<?php } ?>

<?php include(TPL_PATH . '/inc_footer.php'); ?>