<?php 
error_reporting(E_ALL);
ini_set('display_errors', '0');
$conn = mysqli_connect("127.0.0.1", "root", "", "ahp");


$query = mysqli_query($conn, "SELECT max(kode_item) as kodeTerbesar FROM tbl_masteritem");
$data = mysqli_fetch_array($query);
$kodeBarang = $data['kodeTerbesar'];
 
// mengambil angka dari kode barang terbesar, menggunakan fungsi substr
// dan diubah ke integer dengan (int)
$urutan = (int) substr($kodeBarang, 3, 3);
 
// bilangan yang diambil ini ditambah 1 untuk menentukan nomor urut berikutnya
$urutan++;
 
// membentuk kode barang baru
// perintah sprintf("%03s", $urutan); berguna untuk membuat string menjadi 3 karakter
// misalnya perintah sprintf("%03s", 15); maka akan menghasilkan '015'
// angka yang diambil tadi digabungkan dengan kode huruf yang kita inginkan, misalnya BRG 
$huruf = "BRG";
$kodeBarang = $huruf . sprintf("%03s", $urutan);




	if($_POST['method']=='insertItem'){	

		$kodeitem = $_POST['kodeitem'];
		$jnsitem = $_POST['jnsitem'];
		$namaitem = $_POST['namaitem'];
		
		
		$ins = "insert into tbl_masteritem values ('".$kodeitem."','".$jnsitem."','".$namaitem."')";

		$exec = mysqli_query($conn,$ins);
		if($exec){
			echo 1;
		}else{
			echo 0;
		}
		die();

	}
	
 ?>

<form>
<h3 class="m-0">Master Item</h3>
<hr>	
<table>
	<tr>
		<td>Kode Item</td>
		<td>:</td>
		<td><input type="text" class="form-control" name="kodeitem" value="<?php echo $kodeBarang; ?>" readonly="readonly"></td>
	</tr>
	<tr>
		<td>Jenis Item</td>
		<td>:</td>
		<td><select name="jnsitem">
			<option>--Pilih--</option>
			<option value="Furniture">Furniture</option>
			<option value="Food">Food</option>
		</select></td>
	</tr>
	<tr>
		<td>Nama Item</td>
		<td>:</td>
		<td><input type="text" name="namaitem" placeholder="Nama Item"></td>
	</tr>
	<tr>
		<td></td>
		<td></td>
		<td><input type="button" name="" value="Simpan" id="simpanItem" class="btn btn-block btn-outline-success"></td>
	</tr>
</table>
</form>


<script type="text/javascript">
	$(document).ready(function (argument) {

		$('#simpanItem').click(function (argument) {
			var kodeitem = $("input[name='kodeitem']").val();
			var jnsitem = $("select[name='jnsitem']").val();
			var namaitem = $("input[name='namaitem']").val();
			// console.log(jnsitem);
			argument.preventDefault();
				var x = confirm("Apakah kamu yakin ingin menyimpan data item");
				if (x) {
					$.ajax({
						url:'page/MasterItem.php',
						type:'POST',
						data:{'method':'insertItem',kodeitem:kodeitem,jnsitem:jnsitem,namaitem:namaitem},
						success: function(e){
							console.log(e);
							if (e == 1) {
								alert('Data Berhasil Di Simpan');
								//alert(e);
							}else{
								alert('Data gagal di simpan');
							}
						}
					});
				}
		})
	})
</script>