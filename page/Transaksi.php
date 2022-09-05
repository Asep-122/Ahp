<?php  
error_reporting(E_ALL);
ini_set('display_errors', '0');
	
	if(isset($_POST['submit']))
	{ 
		$conn = mysqli_connect("127.0.0.1", "root", "", "ahp");	
		// $nodo 		= strtoupper(str_replace(' ','', $_POST['no_do']));
		// $query 		= sqlsrv_query("select * from tbl_do where no_do = 'DO".$nodo."'") or die(mssql_get_last_message());
		// if(mssql_num_rows($query)==0)
		// {
			$item 	= $_POST['kodeitem'];
			$nomor 	= 0;
			foreach($item as $key=>$value)
			{
				$nomor++;
				$query = ("insert into tbl_transaksi values('".$_POST['optMaster']."','".$_POST['kodeitem'][$key]."','".$_POST['qty'][$key]."','".$_POST['harga'][$key]."',CURDATE())");
				$data = mysqli_query($conn,$query);
				// echo $query
			}
			if($data){

			echo "<script>alert('Berhasil Input Pembelian');</script>";
			}
		else
		{
			echo "<script>alert('Gagal Input Pembelian');</script>";
		}
		echo "<script>document.location='http://localhost/Ahp/?page=Transaksi'; </script>";
	}

	if($_POST['method']== 'optitem'){
		$conn = mysqli_connect("127.0.0.1", "root", "", "ahp");	
		$query = "SELECT *FROM tbl_masteritem";
		$exec = mysqli_query($conn,$query);
		$data = "<option>--Pilih--</option>";
		while ($fetch = mysqli_fetch_array($exec,MYSQLI_ASSOC)) {
			$data .="<option value='".$fetch['kode_item']."'>".$fetch['nama_item']."</option>";
		}
		echo $data;die();

	}
?>

<form method="post">
<!-- <div> -->
<h3 class="m-0">Transaksi</h3>
<hr>
<table>
	<tr>
<td>Supplier</td> 
<td>:</td> 
<td>
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
<!-- </div> -->
<table id="myTable">
	<tr>
		<th>Kode Item</th>
		<th>Harga</th>
		<th>Qty</th>
	</tr>
</table><br>
<table>
<tr>
	<td>	
		<input type="button" name="" class="btn btn-block btn-outline-info" value="Tambah" id="Tambah">
	</td>
	<td>		
		<button type="submit" class="btn btn-block btn-outline-success" name='submit' id='submit'>Submit</button>
	</td>
</table>
</form>


              <script type="text/javascript">
              	   $('.select2').select2();
              	   $('#submit').hide();
              	   $('#myTable').hide();
              	   $(document).ready(function (argument) {
              	   	var x = 0;
              	   	$(".item").unbind("click");
						$(document).on('click','.kurang',function(){
							var temp=this.id.match(/\d+/)[0];
							$('#baris'+temp).remove();
							// if(sum(temp) == 0){
							// 	$('#myTable').hide();
							// }
						});	

              	   		$('#Tambah').on('click',function (argument) {
              	   			$('.select3').select2();
              	   			$('#submit').show();
              	   			$('#myTable').show();
              	   			x++;
              	   				$('#baris_total').remove();
								$('#baris_total_final').remove();
								$('#baris_ppn').remove();
								$('#baris_ppn_final').remove();
              	   				$('#myTable').append("<tr id='baris"+x+"'><td><select name='kodeitem[]' class='form-control item select3 select3-danger' data-dropdown-css-class='select3-danger' id='item"+x+"' required></select><input type='button' class='hitung btn btn-primary' style='display:none;' id='hitung"+x+"' value='Pilih Eancode'></td><td><input type='text' name='harga[]' value='0' id='harga"+x+"' class='form-control kanan' required></td><td><input type='text' name='qty[]' id='qty"+x+"' size=5 class='form-control angka' required></td><td><input type='text' name='subtotal[]' id='subtotal"+x+"' class='form-control angka kanan' id='test2' readonly required></td><td><button type='button' class='btn btn-danger kurang' id='kurang"+x+"'>-</button></td></tr><tr id='baris_total'><td></td><td></td><td></td><td></td><td></td><td></td></tr>");

							$.ajax({
								url:'page/Transaksi.php',
								type:'POST',
								data:{'method':'optitem'},
								success:function (argument) {
									console.log(argument);
									$('#item'+x+'').html(argument);
								}
							})
	
	              	   		$('#qty'+x+'').on('change',function (argument) {
	              	   			$('#subtotal'+x+'').val($('#qty'+x+'').val() * $('#harga'+x+'').val());
	              	   		})

              	   		})

              	   		// $('#submit').click(function (argument) {
              	   		// 	alert('data');
              	   		// })


              	   })
              </script>
