<?php include(TPL_PATH . '/inc_header.php'); ?>

<style>
<!--
body {font:14px arial; }
table {margin-top:10px; border-top:2px threedface solid; border-left:2px threedface solid;}
table td, table th {color:gray; height:30px; border-right:2px threedface solid; border-bottom:2px threedface solid; text-align:center;}
.tr_green td {color:green;}
.tr_blue td {color:blue;}
-->
</style>

<h1><?php p($strTitle) ?></h1>

<?php
$arrClassName = array(
    'g' => 'tr_green',
	'p' => 'tr_blue',
    'a' => 'tr_gray'
);
?>

<form action="<?php p(HTTP_URL); ?>/?c=acl" method="post" onsubmit="return false;">
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<th>类型</th>
		<th>ID</th>
		<th>名称</th>
		<th>英文名</th>
		<th>说明</th>
		<th>创建时间</th>
		<th colspan="2">操作</th>
	</tr>
	<?php $blnFirst = true; foreach ($arrListG as $arrTemp) { ?>
	<tr class="<?php p($arrClassName[$arrTemp['type']]) ?>">
		<?php if ($blnFirst) { ?>
		<td rowspan="<?php p(count($arrListG)) ?>"><b><?php p($arrType[$arrTemp['type']]); ?></b></td>
		<?php } ?>
		<td><?php p($arrTemp['id']); ?></td>
		<td><?php p($arrTemp['name']); ?></td>
		<td><?php p($arrTemp['name_en']); ?></td>
		<td><?php p($arrTemp['title']); ?></td>
		<td><small><?php p($arrTemp['created_at']); ?></small></td>
		<td><button class="edit_btn" id="edit_<?php p($arrTemp['id']); ?>">编辑</button></td>
		<td><button class="del_btn" id="del_<?php p($arrTemp['id']); ?>">删除</button></td>
	</tr>
	<?php $blnFirst = false; } ?>
	<?php $blnFirst = true; foreach ($arrListP as $arrTemp) { ?>
	<tr class="<?php p($arrClassName[$arrTemp['type']]) ?>">
		<?php if ($blnFirst) { ?>
		<td rowspan="<?php p(count($arrListP)) ?>"><b><?php p($arrType[$arrTemp['type']]); ?></b></td>
		<?php } ?>
		<td><?php p($arrTemp['id']); ?></td>
		<td><?php p($arrTemp['name']); ?></td>
		<td><?php p($arrTemp['name_en']); ?></td>
		<td><?php p($arrTemp['title']); ?></td>
		<td><small><?php p($arrTemp['created_at']); ?></small></td>
		<td><button class="edit_btn" id="edit_<?php p($arrTemp['id']); ?>">编辑</button></td>
		<td><button class="del_btn" id="del_<?php p($arrTemp['id']); ?>">删除</button></td>
	</tr>
	<?php $blnFirst = false; } ?>
	<?php $blnFirst = true; foreach ($arrListA as $arrTemp) { ?>
	<tr class="<?php p($arrClassName[$arrTemp['type']]) ?>">
		<?php if ($blnFirst) { ?>
		<td rowspan="<?php p(count($arrListA)) ?>"><b><?php p($arrType[$arrTemp['type']]); ?></b></td>
		<?php } ?>
		<td><?php p($arrTemp['id']); ?></td>
		<td><?php p($arrTemp['name']); ?></td>
		<td><?php p($arrTemp['name_en']); ?></td>
		<td><?php p($arrTemp['title']); ?></td>
		<td><small><?php p($arrTemp['created_at']); ?></small></td>
		<td><button class="edit_btn" id="edit_<?php p($arrTemp['id']); ?>">编辑</button></td>
		<td><button class="del_btn" id="del_<?php p($arrTemp['id']); ?>">删除</button></td>
	</tr>
	<?php $blnFirst = false; } ?>
	<tr>
		<td>
			<select name="type">
    			<?php foreach ($arrType as $k => $v) { ?>
    			<option value="<?php p($k); ?>"><?php p($v); ?></option>
    			<?php } ?>
			</select>
		</td>
		<td>New</td>
		<td><input type="text" name="name" size="15" /></td>
		<td><input type="text" name="name_en" size="15" /></td>
		<td><input type="text" name="title" size="40" /></td>
		<td><small><?php p(date('Y-m-d H:i:s')) ?></small></td>
		<td colspan="2">
			<input type="hidden" name="sub_btn" value="y">
			<input type="button" onclick="this.form.submit();" value="添加" />
		</td>
	</tr>
	<?php foreach ($arrList_ as $arrTemp) { ?>
	<tr>
		<td><?php p($arrType[$arrTemp['type']]); ?></td>
		<td><?php p($arrTemp['id']); ?></td>
		<td><?php p($arrTemp['name']); ?></td>
		<td><?php p($arrTemp['name_en']); ?></td>
		<td><?php p($arrTemp['title']); ?></td>
		<td><small><?php p($arrTemp['created_at']); ?></small></td>
		<td><button class="back_btn" id="back_<?php p($arrTemp['id']); ?>">还原</button></td>
		<td><button class="clean_btn" id="clean_<?php p($arrTemp['id']); ?>">清除</button></td>
	</tr>
	<?php } ?>
</table>
</form>

<script type="text/javascript">
$('.del_btn').each(function() {
	$(this).click(function() {
		var _this = $(this);
		var id = _this.attr('id').replace('del_', '');
		if (confirm('确定删除吗?')) {
			location.href = '<?php p(HTTP_URL) ?>/?c=acl&a=del&id=' + id;
		}
	});
});

$('.back_btn').each(function() {
	$(this).click(function() {
		var _this = $(this);
		var id = _this.attr('id').replace('back_', '');
		if (confirm('确定还原吗?')) {
			location.href = '<?php p(HTTP_URL) ?>/?c=acl&a=back&id=' + id;
		}
	});
});

$('.clean_btn').each(function() {
	$(this).click(function() {
		var _this = $(this);
		var id = _this.attr('id').replace('clean_', '');
		if (confirm('清除后将不可还原,确定清除吗?')) {
			location.href = '<?php p(HTTP_URL) ?>/?c=acl&a=clean&id=' + id;
		}
	});
});

$('.edit_btn').each(function() {
	$(this).click(function() {
		var _this = $(this);
		var id = _this.attr('id').replace('edit_', '');
		location.href = '<?php p(HTTP_URL) ?>/?c=acl&a=edit&id=' + id;
	});
});
</script>

<?php include(TPL_PATH . '/inc_footer.php'); ?>