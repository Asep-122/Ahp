<?php  
error_reporting(E_ALL);
ini_set('display_errors', '1');
	if($_POST['method']== 'optitem'){
		$conn = mysqli_connect("127.0.0.1", "root", "", "ahp");	
		if($_POST['jnsitem'] != 'All'){
			$filter = "where jenis_item = '".$_POST['jnsitem']."'";
		}else{
			$filter = "";
		}
		$query = "SELECT *FROM tbl_masteritem $filter";
		$exec = mysqli_query($conn,$query);
		$data = "<option value='All'>All</option>";
		while ($fetch = mysqli_fetch_array($exec,MYSQLI_ASSOC)) {
			$data .="<option value='".$fetch['kode_item']."'>".$fetch['nama_item']."</option>";
		}
		echo $data;die();

	}elseif($_POST['method']=='ProcessData'){
		$conn = mysqli_connect("127.0.0.1", "root", "", "ahp");	



		// echo mysqli_num_rows($exec);
		// $data = array();
		//  while ($fetch = mysqli_fetch_array($exec,MYSQLI_ASSOC)) {
		//  	$data[] = $fetch;
		//  }
		// for ($i=1; $i <= mysqli_num_rows($exec) ; $i++) { 
		//  	$fetch = mysqli_fetch_array($exec,MYSQLI_ASSOC);
		//  	// echo '<pre>'.print_r($data,true).'</pre>';
		//  	$data[$i][] = $fetch['kode_supplier'];
		//  	// $data[$i][] = $fetch['harga'];
		//  	// echo $i;
		// }
		// echo '<pre>'.print_r($data,true).'</pre>';	


		$List = array();

		if($_POST['jnsitem'] == 'All'){
			$filterjnsitem = "where jenisitem <> '' and ";
		}else{
			$filterjnsitem = "where jenisitem = '".$_POST['jnsitem']."' and ";
		}
		if($_POST['item'] == 'All'){
			$filteritem = "kode_item <> ''";
		}else{
			$filteritem = "kode_item = '".$_POST['item']."'";
		}
		$filter = $filterjnsitem.$filteritem;

		$supplier = "'".implode($_POST['supplier'],"','")."'";
		$query = "
		SELECT *FROM(
		SELECT kode_supplier,(select nama_supplier from tbl_mastersupplier b where a.kode_supplier = b.kode_supplier)namasupplier,
		kode_item,(select nama_item from tbl_masteritem b where a.kode_item = b.kode_item)namaitem,
		(select jenis_item from tbl_masteritem b where a.kode_item = b.kode_item)jenisitem,harga,qty,Harga*Qty as total
		 FROM tbl_transaksi a WHERE kode_supplier IN (".$supplier.")
		 )B	".$filter."";

		$exec = mysqli_query($conn,$query);
		

		$data = "
			<table id='example1' class='table table-bordered table-striped' >
				<tr>
			";

		while ($fetch1 = mysqli_fetch_array($exec,MYSQLI_ASSOC)) {
				$List[] = $fetch1;
		}	

		foreach ($List[0] as $key => $value) {
				$data .= "<th>".$key."</th>";

		}
		$data .="</tr>";		
		// while ($fetch = mysqli_fetch_array($exec,MYSQLI_ASSOC)) {
			foreach ($List as $fetch) {
							# code...
				$data .= "
					<tr>
						<td>".$fetch['kode_supplier']."</td> 
						<td>".$fetch['namasupplier']."</td> 
						<td>".$fetch['kode_item']."</td> 
						<td>".$fetch['namaitem']."</td> 
						<td>".$fetch['jenisitem']."</td> 
						<td>".number_format($fetch['harga'])."</td> 
						<td>".$fetch['qty']."</td> 
						<td>".number_format($fetch['total'])."</td> 
					</tr>
				";
			}	
			$data .= '<table>';
			echo $data;
			// echo '<pre>'.print_r($List,true).'</pre>';
		die();
	}
?>
<form>
<h3 class="m-0">Predikat</h3>
<hr>
	<table>
	<tr>
		<td style="width: 30%;">Pilih Kategori</td>
		<td>:</td>
		<td>
			<select name="jnsitem" id="jnsitem" data-dropdown-css-class="select2-danger" class="form-control select2 select2-danger">
				<option value="All">All</option>
				<?php  	
				$conn = mysqli_connect("127.0.0.1", "root", "", "ahp");	
				$query = "SELECT distinct jenis_item FROM tbl_masteritem";
				$exec = mysqli_query($conn,$query);
				while ($fetch = mysqli_fetch_array($exec,MYSQLI_ASSOC)) {
					echo "<option value='".$fetch['jenis_item']."'>".$fetch['jenis_item']."</option>";
				}
			?>	
			</select>
		</td>
	</tr>
	<tr>
		<td style="width: 30%;">Pilih Item</td>
		<td>:</td>
		<td colspan="5">
			<select name="item" id="item" data-dropdown-css-class="select2-danger" class="form-control select2 select2-danger">
				<option value="All">All</option>
				<?php  	
					$query = "SELECT distinct kode_item,nama_item FROM tbl_masteritem";
					$exec = mysqli_query($conn,$query);
					while ($fetch = mysqli_fetch_array($exec,MYSQLI_ASSOC)) {
						echo "<option value='".$fetch['kode_item']."'>".$fetch['nama_item']."</option>";
					}
				?>
			</select>
		</td>
	</tr>
	</table><br>
        <div class="select2-purple" >
	        <select id="supplier" class="select2" multiple="multiple" data-placeholder="Select a State" data-dropdown-css-class="select2-purple" style="width: 20%;">
				<?php  	
					$query = "SELECT *FROM tbl_mastersupplier";
					$exec = mysqli_query($conn,$query);
					while ($fetch = mysqli_fetch_array($exec,MYSQLI_ASSOC)) {
						echo "<option value='".$fetch['kode_supplier']."'>".$fetch['nama_supplier']."</option>";
					}
				?>
	        </select>
          </div><br>
       <div class="form-group">
       	<input type="button" name="Process" id="Process" class="btn btn-block btn-outline-success" value="Process" style="width: 20%;">
       </div>   
       <hr>
</form>
<div id="space"></div>

<script type="text/javascript">
	$('.select2').select2();


	$(document).ready(function (argument) {

		$("#jnsitem").change(function (argument) {
			argument.preventDefault();
			var jnsitem = $(this).val();
				$.ajax({
					url:'page/Predikat.php',
					type:'POST',
					data:{'method':'optitem',jnsitem:jnsitem},
					success:function (argument) {
						$('#item').html(argument);
					}
				})
		})

		$('#Process').click(function (argument) {
			var jnsitem = $('#jnsitem').val();	
			var item = $('#item').val();	
			var supplier = $('#supplier').val();	

				$.ajax({
				url:'page/Predikat.php',
				type:'POST',
				data:{'method':'ProcessData',jnsitem:jnsitem,item:item,supplier:supplier},
				success:function (argument) {
					$('#space').html(argument);
				}
			})

		})
	})
</script>



















