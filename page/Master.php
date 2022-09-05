
<table>
	<tr>
		<td>Pilih Master</td>
		<td>:</td>
		<td>
		<select name="optMaster" id="optMaster" class="form-control select2 select2-danger" data-dropdown-css-class="select2-danger"  style="width: 200px;">
			<option>--Piih--</option>	
			<option value="Item">Master Item</option>	
			<option value="Supplier">Master Supplier</option>	
		</select>
	</td>
	</tr>
</table>
<hr>
<div class="master">
	
</div>

<script type="text/javascript">
	    $('.select2').select2();
  $(document).ready(function() {
    $("#optMaster").change(function(argument) {
    	if($(this).val() == 'Item'){
    		$(".master").load('page/MasterItem.php');
    	}else if($(this).val() == 'Supplier'){
    		$(".master").load('page/MasterSupplier.php');    		
    	}else{
    		$(".master").text('');
    	}

    })
    // console.log('data');
  })
</script>