<?php 
error_reporting(E_ALL);
ini_set('display_errors', '0');

$conn = mysqli_connect("127.0.0.1", "root", "", "ahp");


$query = mysqli_query($conn, "SELECT max(kode_supplier) as kodeTerbesar FROM tbl_mastersupplier");
$data = mysqli_fetch_array($query);
$kodesupplier = $data['kodeTerbesar'];
 
// mengambil angka dari kode barang terbesar, menggunakan fungsi substr
// dan diubah ke integer dengan (int)
$urutan = (int) substr($kodesupplier, 3, 3);
 
// bilangan yang diambil ini ditambah 1 untuk menentukan nomor urut berikutnya
$urutan++;
 
// membentuk kode barang baru
// perintah sprintf("%03s", $urutan); berguna untuk membuat string menjadi 3 karakter
// misalnya perintah sprintf("%03s", 15); maka akan menghasilkan '015'
// angka yang diambil tadi digabungkan dengan kode huruf yang kita inginkan, misalnya BRG 
$huruf = "S";
$kodesupplier = $huruf . sprintf("%03s", $urutan);


	if($_POST['method']=='insertSupplier'){	

		$kodesupplier = $_POST['kodesupplier'];
		$namasupplier = $_POST['namasupplier'];

		$conn = mysqli_connect("127.0.0.1", "root", "", "ahp");
		$ins = "insert into tbl_mastersupplier values ('".$kodesupplier."','".$namasupplier."')";

		$exec = mysqli_query($conn,$ins);
		if($exec){
			echo 1;
		}else{
			echo 0;
		}
		die();

	}
	
 ?>

<h3 class="m-0">Master Supplier</h3>
<hr>
<table>
	<tr>
		<td>Kode Supplier</td>
		<td>:</td>
		<td><input type="text" class="form-control" name="kodesupplier" value="<?php echo $kodesupplier ?>" readonly="readonly"></td>
	</tr>
	<tr>
		<td>Nama Supplier</td>
		<td>:</td>
		<td><input type="text" name="namasupplier" class="form-control" placeholder="Nama Supplier"></td>
	</tr>
	<tr>
		<td></td>
		<td></td>
		<td><input type="button" name="simpansupplier" class="btn btn-block btn-outline-success" id= "simpansupplier" value="Simpan"></td>
	</tr>
</table>


<script type="text/javascript">
	$(document).ready(function (argument) {

		$('#simpansupplier').click(function (argument) {
			var kodesupplier = $("input[name='kodesupplier']").val();
			var namasupplier = $("input[name='namasupplier']").val();
			// console.log(jnsitem);
			argument.preventDefault();
				var x = confirm("Apakah kamu yakin ingin menyimpan data Supplier");
				if (x) {
					$.ajax({
						url:'page/Mastersupplier.php',
						type:'POST',
						data:{'method':'insertSupplier',kodesupplier:kodesupplier,namasupplier:namasupplier},
						success: function(e){
							console.log(e);
							if (e == 1) {
								alert('Data Berhasil Di Simpan');
								location.reload();
							}else{
								alert('Data gagal di simpan');
								location.reload();
							}
						}
					});
				}
		})
	})
</script>