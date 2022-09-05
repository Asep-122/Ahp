<?php  
error_reporting(E_ALL);
ini_set('display_errors', '1');
	$conn = mysqli_connect("127.0.0.1", "root", "", "ahp");	
	if($_POST['method']=='insertTransaksi'){
		$Supplierarray = [];
		for ($i=1; $i <=5; $i++) { 
			if($_POST['kodeitem'.$i]!='' ){
				$Supplierarray['Supplier'.$i] = '';

			}else{
				$Supplierarray['Supplier'.$i] = $_POST['optMaster'];
			}

			$ins = "insert into tbl_transaksi values ('".$Supplierarray['Supplier'.$i]."','".$_POST['kodeitem'.$i]."','".$_POST['harga'.$i]."','".$_POST['qty'.$i]."'); 
			delete 	from tbl_transaksi where kode_item = '';
			";

			$exec = mysqli_query($conn,$ins);
			if($exec){
				echo 1;
			}else{
				echo 0;
			}

			// echo  1;				


			echo  ($ins);				
		}
		die();
	}
?>
<form id='submit_form'>
<table>
	<tr>
		<td>Nama Supplier</td>
		<td colspan="2">
		<select name="optMaster" id="optMaster" class="form-control select2 select2-danger" data-dropdown-css-class="select2-danger" style="width: 200px;">
			<option selected="selected">--Piih--</option>	
			<?php  	
				$query = "SELECT *FROM tbl_mastersupplier";
				$exec = mysqli_query($conn,$query);
				while ($fetch = mysqli_fetch_array($exec,MYSQLI_ASSOC)) {
					echo "<option value='".$fetch['kode_supplier']."'>".$fetch['nama_supplier']."</option>";
				}


			?>
		</select>
	</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
<tr>
	<td>Item</td>
	<td>Harga</td>
	<td>Qty</td>
</tr>
<?php  
	$no = 1;			
	for ($i=0; $i <= 4; $i++) { 
?>	
	<tr>
		<td>
			<select name='kodeitem<?php echo $no;  ?>' class="form-control select2 select2-danger" data-dropdown-css-class="select2-danger" style="width: 200px;">
				<option>--Piih--</option>	
			<?php  	
				$conn = mysqli_connect("127.0.0.1", "root", "", "ahp");	
				$query = "SELECT *FROM tbl_masteritem";
				$exec = mysqli_query($conn,$query);
				while ($fetch = mysqli_fetch_array($exec,MYSQLI_ASSOC)) {
					echo "<option value='".$fetch['kode_item']."'>".$fetch['nama_item']."</option>";
				}
			?>	
			</select>
		</td>
		<td><input type="text" class="form-control" name="harga<?php echo $no;  ?>" size=10></td>
		<td><input type="text" class="form-control" name="qty<?php echo $no; ?>" size=5></td>
	</tr>
<?php 
 $no++;	}
?>
<tr>
	<td><input type="submit" class="btn btn-block bg-gradient-success btn-lg" name="simpantransaksi" id='simpantransaksi' size='40'></td>
</tr>
</table>
</form>

<script type="text/javascript">
    $('.select2').select2();
	$(document).ready(function (argument) {

		$('#simpantransaksi').click(function (argument) {
			// console.log(jnsitem);

			argument.preventDefault();
				var x = confirm("Apakah kamu yakin ingin menyimpan data Supplier");
				if (x) {

					var optMaster = $("select[name='optMaster']").val();
					var kodeitem1 = $("select[name='kodeitem1']").val();
					var kodeitem2 = $("select[name='kodeitem2']").val();
					var kodeitem3 = $("select[name='kodeitem3']").val();
					var kodeitem4 = $("select[name='kodeitem4']").val();
					var kodeitem5 = $("select[name='kodeitem5']").val();

					console.log(kodeitem1);
					var qty1 = $("input[name='qty1']").val();
					var qty2 = $("input[name='qty2']").val();
					var qty3 = $("input[name='qty3']").val();
					var qty4 = $("input[name='qty4']").val();
					var qty5 = $("input[name='qty5']").val();

					var harga1 = $("input[name='harga1']").val();
					var harga2 = $("input[name='harga2']").val();
					var harga3 = $("input[name='harga3']").val();
					var harga4 = $("input[name='harga4']").val();
					var harga5 = $("input[name='harga5']").val();

					$.ajax({
						url:'page/Transaksi.php',
						type:'POST',
						data:{'method':'insertTransaksi',
						optMaster:optMaster,

						kodeitem1:kodeitem1,
						kodeitem2:kodeitem2,
						kodeitem3:kodeitem3,
						kodeitem4:kodeitem4,
						kodeitem5:kodeitem5,
						harga1:harga1,
						harga2:harga2,
						harga3:harga3,	
						harga4:harga4,
						harga5:harga5,	
						qty1:qty1,
						qty2:qty2,
						qty3:qty3,	
						qty4:qty4,
						qty5:qty5

					},
						success: function(e){
							console.log(e);
							// if (e == 1) {
							// 	alert('Data Berhasil Di Simpan');
							// 	//alert(e);
							// }else{
							// 	alert('Data gagal di simpan');
							// }
						}
					});
				}
		})
	})
</script>
