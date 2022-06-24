<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
session_start();
include('..\\config.php');
include("..\\class\\db.class.php");
$db = new DBClass(DB_MODERN_HOST2_NEW,DB_MODERN_NAME_NEW,DB_USER_NAME_NEW,DB_USER_PASS2_NEW);
function fixDate($dateStr) {
	if(substr($dateStr, 0, 4) == '1970' || substr($dateStr, 0, 4) == '1900') {
		return '';
	} else {
		return $dateStr;
	}
}

for ($i=17; $i <= date('y') ; $i++) {
	if ($i == 17) {
	 	$k .= "'".$i."2'";
	 	$g .= "'".$i."6'";
	} else{
		$k .= ",'".$i."2'";
		$g .= ",'".$i."6'";
	}
}

// echo $k;


if(isset($_POST['task'])) {
	switch($_POST['task']) {
		case 'getsl' :
			$tc = $_POST['tc'];
				if ($tc == "Sales Group B") {
					$queryj = "SELECT DISTINCT NamaSales fROM(
							select distinct SL01035,
							(select ST01002 from ST010100 where ST01001 = SL01035)NamaSales,
							(select GL03003 from GL0301".substr(date('Y'),2,2)." where GL03001 = 'B' and substring(SL01017,7,4) + '0' = GL03002)TradeChannel,
							(SELECT top 1 kodesaleswilayah FROM temp_penjualan_login c where c.kodesales = a.SL01035 and kodesaleswilayah is not null)KodeWilayah
							,(SELECT top 1 'ada' FROM  temp_penjualan_login c where c.kodesales = a.SL01035) cek from SL010100 a
							where (select GL03003 from GL030121 where GL03001 = 'B' and substring(SL01017,7,4) + '0' = GL03002) = '".$tc."'
							AND (select ST01002 from ST010100 where ST01001 = SL01035) IS NOT NULL
							)A where SL01035 = kodewilayah or kodewilayah is null";
				}else{
					$queryj = "SELECT DISTINCT NamaSales fROM(
								select distinct SL01035,
								(select ST01002 from ST010100 where ST01001 = SL01035)NamaSales,
							(select GL03003 from GL0301".substr(date('Y'),2,2)." where GL03001 = 'B' and substring(SL01017,7,4) + '0' = GL03002)TradeChannel
								from SL010100
								where (select GL03003 from GL0301".date('y')." where GL03001 = 'B' and substring(SL01017,7,4) + '0' = GL03002) = '".$tc."'
								AND (select ST01002 from ST010100 where ST01001 = SL01035) IS NOT NULL
								)A";
				}
				$runj = mssql_query($queryj);
				$ll =  "
				<p>Sales &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: 
					<select id='sales' name='sales'><option value='all'>-- All --</option>";
				while ($p = mssql_fetch_array($runj)) {
					$ll .= "<option value='".$p['NamaSales']."'>".$p['NamaSales']."</option>";
				}
				$ll .= "</select></p>";
				echo $ll;

		break;
		case 'getData' :
			$df = $_POST['df'];
			$dt = $_POST['dt'];
			$tc = $_POST['tc'];
			$sl = $_POST['sl'];
			$tipe = $_POST['tipe'];
			mssql_query("DELETE FROM temp_receive") or die(mssql_get_last_message());

			//dulu @flag = 'jth'
			if ($tipe == "belum") {
				$p = "@flag = 'blm'";
				$t = "WHERE [OverFromTop]  <= 0 ";
			}elseif($tipe == "sudah"){
				$p = "@flag = 'jth'";
				$t = "WHERE [OverFromTop]  > 0 ";
			}
			$query = "SET ANSI_NULLS ON; SET  ANSI_WARNINGS ON; 
			EXEC SP_Receive_NEW @tglawal = '".$df."', @tglakhir = '".$dt."',".$p.", @tempo = '".date("Y-m-d")."', @sessionID = 'nancytjan';";
			// echo "<pre>".$query."</pre>";
			mssql_query($query) or die(mssql_get_last_message());
			if ($sl == 'all') {
				$x = "";
				$y = "";
				$z = "";
			}else{
				$x = "DECLARE @Sales varchar(Max) = '".$sl."'";
				$y = "WHERE Salesman= @Sales";
				$z = "and Salesman = '".$sl."'";
			}
			if($tc == 'Sales Group A') {
				// $query = "
				// DECLARE @From VARCHAR(10) = '".$df."'
				// DECLARE @To VARCHAR(10) = '".$dt."'
				// DECLARE @TradeChannel VARCHAR(MAX) = '".$tc."'
				// ".$x."
				// if OBJECT_ID('tempdb.dbo.#temp_tanda_terima') IS NOT NULL BEGIN DROP TABLE #temp_tanda_terima END;
				// SELECT * INTO #temp_tanda_terima FROM tbl_tanda_terima WHERE Tanggal BETWEEN DATEADD(MONTH, -3, @From) AND DATEADD(MONTH, 3, @To);
				// SELECT *FROM(
				// SELECT KodeCustomer,NamaCustomer,TradeChannel,NoInv,NilaiInv,Payment,[TOP],TglInv,TglTandaTerima,Salesman,NoLTP,TglJatuhTempo, ISNULL(DATEDIFF(DAY, NULLIF(TglJatuhTempo, '1900-01-01'), GETDATE()), 0) AS [Over],
				// (select top 1 OR01072 from(select *from OR010100 where OR01001 = Noso union select *from OR200100 where OR20001 = Noso)a)Nopo, Noso
				//  FROM (
				// 	SELECT *, /*ISNULL(DATEADD(DAY, CAST([TOP] AS INT), NULLIF(TglTandaTerima, '1900-01-01')), '1900-01-01') AS TglJatuhTempo*/
				// 	case when YEAR(tgltandaterima) = '1900' then Dateadd(day,cast([top] as int),tglinv)
				// 	else Dateadd(day,cast([top] as int),tgltandaterima)
				// 	end TglJatuhTempo 
				// 	FROM (
				// 		SELECT 
				// 			Order_Number Noso,KodeCustomer, NamaCustomer, (SELECT (SELECT GL03003 FROM GL0301".substr(date("Y"), 2, 2)." WHERE GL03002 = SUBSTRING(SL01017, 7 ,5)) FROM SL010100 WHERE SL01001 = KodeCustomer) AS TradeChannel, 
				// 			Invoice AS NoInv, NilaiInvoiceRp AS NilaiInv, PaymentRp AS Payment,
				// 			(SELECT TOP 1 SL22005 FROM SL220100 WHERE SUBSTRING(SL22002,2,1) = TermOfPayment) AS [TOP],
				// 			NoSJmurni, Tgl_Invoice AS TglInv,
				// 			TglSJmurni, Tanggal AS TglSJmurniBalik,
				// 			CAST((Tanggal - TglSJmurni) AS int) AS OverSJ, 
				// 			ISNULL((SELECT TOP 1 Tanggal FROM #temp_tanda_terima WHERE SL03002 = view_ar_detail.Invoice ORDER BY Tanggal DESC), '1900-01-01') AS TglTandaTerima,
				// 			ISNULL((SELECT ST01002 FROM ST010100 WHERE ST01001 = KodeSales),'') AS Salesman, NoLTP,
				// 			ISNULL((SELECT jlhinv FROM view_jlh_ltp WHERE noInvoice = SUBSTRING(Invoice ,5,6) AND nc = Namacustomer),'0') AS JlhLTP,
				// 			view_ar_detail.Tglhari AS TglPrint, view_ar_detail.Keterangan AS [KetLTP],
				// 			ISNULL((SELECT TOP 1 tbl_updatear.Keterangan FROM tbl_updatear WHERE tbl_updatear.noInvoice = Invoice), '') AS KetInvoice
				// 		FROM dbo.view_ar_detail LEFT JOIN tbl_updatear ON noInvoice = Invoice 
				// 		WHERE sessionID='nancytjan' AND NamaCustomer <> 'Wasted Factory' AND NamaCustomer <> 'Wasted Office' 
				// 		AND Tgl_Invoice > '01-01-2007' AND LEFT(Invoice, 3) IN ('172', '162', '182','192')
				// 		AND SUBSTRING(Order_Number,3,1) <> '9' AND KodeSales <> '052' AND KodeSales <> '030' AND KodeSales <> '031' 
				// 		AND NoSJmurni NOT IN (
				// 			SELECT DNCustom FROM UDDBDN0109 
				// 			WHERE OR19012 IN (
				// 				SELECT OR20023 FROM OR200100 WHERE OR20021 IN (SELECT SL03003 FROM SL030100 WHERE SL03053=SL03013 AND SL03036 = view_ar_detail.Order_Number)
				// 			) 
				// 			AND OR19001 IN (
				// 				SELECT OR20001 FROM OR200100 WHERE OR20021 IN (SELECT SL03003 FROM SL030100 WHERE SL03053=SL03013 AND SL03036 = view_ar_detail.Order_Number)
				// 			) 
				// 		)
				// 		GROUP BY NamaCustomer,Invoice,NilaiInvoiceRp, PaymentRp,TermOfPayment,Tgl_Invoice,Tanggal,tbl_updatear.noInvoice,view_ar_detail.Order_Number,
				// 		Tgl_Jatuh_Tempo,Limit1,KodeSales,noltp,view_ar_detail.Tglhari,view_ar_detail.Keterangan,tbl_updatear.Keterangan,KodeCustomer,TglSJmurni,NoSJmurni
				// 	) AR_DETAIL
				// ) AR_FINAL
				// )AR_FINAL1
				// WHERE TradeChannel = @TradeChannel AND TglInv between @From AND @To
				// ".$y."	
				// ORDER BY [Over] DESC, NamaCustomer ASC";
				$query ="DECLARE @From VARCHAR(10) = '".$df."'
				DECLARE @To VARCHAR(10) = '".$dt."'
				DECLARE @TradeChannel VARCHAR(MAX) = '".$tc."'
				".$x."
				IF OBJECT_ID('tempdb.dbo.#temp_tanda_terima') IS NOT NULL BEGIN DROP TABLE #temp_tanda_terima END;
				IF OBJECT_ID('tempdb.dbo.#or01') IS NOT NULL BEGIN DROP TABLE #or01 END;
				IF OBJECT_ID('tempdb.dbo.#or19') IS NOT NULL BEGIN DROP TABLE #or19 END;
				IF OBJECT_ID('tempdb.dbo.#VIEW_AR_DETAIL') IS NOT NULL BEGIN DROP TABLE #VIEW_AR_DETAIL END;
				IF OBJECT_ID('tempdb.dbo.#V_CUSTOMERGROUPLIST') IS NOT NULL BEGIN DROP TABLE #V_CUSTOMERGROUPLIST END;
			
			
				select * into #or01 From (
				select or01001,or01072 From OR010100
				union
				select or20001,or20072 From or200100
				)a
				
				select * into #or19 From (
				select OR19001,OR19011 From OR190100
				union
				select OR23001,OR23011 From OR230100
				)a
				
				SELECT * INTO #VIEW_AR_DETAIL FROM VIEW_AR_DETAIL WHERE (LEFT(Invoice, 3) IN (".$k.") OR LEFT(Invoice, 3) IN (".$g."))
				SELECT * INTO #V_CUSTOMERGROUPLIST FROM V_CUSTOMERGROUPLIST 
				SELECT * INTO #temp_tanda_terima FROM tbl_tanda_terima WHERE Tanggal BETWEEN DATEADD(MONTH, -3, @From) AND DATEADD(MONTH, 3, @To);
				SELECT * FROM (
				SELECT NamaCustomerGroup,KodeCustomers KodeCustomer,NamaCustomer,TradeChannel,(select SL01037 from SL010100 where SL01001 = KodeCustomers)CreditLimit,
				NoInv,NilaiInv,Payment,[TOP],MasaTenggat,TglKirim,TglSJmurniBalik as TglSjBalik,TglPrintInvoice,TglInv,ISNULL(TglTandaTerima,'1900-01-01')TglTandaTerima,Salesman,ISNULL(NoLTP,'-')NoLTP,[Tgl Print Ltp],DATEADD(DAY,[TOP],ISNULL(TglTandaTerima,TGLINV))TglJatuhTempo,
				ISNULL(DATEDIFF(DAY, DATEADD(DAY,[TOP],ISNULL(TglTandaTerima,TglInv)), GETDATE()), 0) AS [OverFromTop],
				Nopo, Noso,Alasan,
				CASE WHEN Alasan LIKE '%Block%' OR Alasan LIKE '%UNBLOCK%' THEN '-' 
				ELSE ISNULL(DATEDIFF(DAY,SisaHariBlock,GETDATE()),'0') END SisaHariUnBlock,
				(SELECT SL01075 FROM SL010100 WHERE SL01001 = KodeCustomers) Status
				FROM (
					SELECT 
					(SELECT TOP 1 cast(NAMACUSTOMERGROUP as varchar(max)) FROM #V_CUSTOMERGROUPLIST A WHERE A.KODECUSTOMER = KODECUSTOMERS)NAMACUSTOMERGROUP,
					*, 
				--	ISNULL(DATEADD(DAY, [TOP], NULLIF(TglTandaTerima, '1900-01-01')), DATEADD(DAY, MasaTenggatTop,TGLINV)) AS TglJatuhTempo
					MasaTenggat+[Top] as MasaTenggatTop
					,(select top 1 OR01072 from #or01 where or01001 = noso)Nopo,
					ISNULL((SELECT TOP 1 alasan FROM Tbl_LogUnblockCustomer A WHERE A.KodeCustomer = AR_DETAIL.KodeCustomers ORDER BY tanggal DESC),'-')Alasan,
					CAST((SELECT TOP 1 tanggal+7 FROM Tbl_LogUnblockCustomer A WHERE A.KodeCustomer = AR_DETAIL.KodeCustomers ORDER BY tanggal DESC) AS VARCHAR(MAX))[SisaHariBlock]
					FROM (
						SELECT 
							Order_Number Noso,KodeCustomer KodeCustomers, NamaCustomer, (SELECT GL03003 FROM GL0301".substr(date("Y"), 2, 2)." WHERE GL03002 = (SELECT SUBSTRING(SL01017, 7 ,4) + '0' FROM SL010100 WHERE SL01001 = KodeCustomer)) AS TradeChannel, 
							Invoice AS NoInv, NilaiInvoiceRp AS NilaiInv, PaymentRp AS Payment,
							RealTerm AS [TOP],(select top 1 OR19011 From #or19 where OR19001 = Order_Number order by OR19011 desc)TglKirim,
							ISNULL((Select top 1 Tempo From Tbl_TempoCL c where c.KodeCustomer = v.KodeCustomer),'0')MasaTenggat,
							(SELECT TOP 1 CAST(CAST(TglPrint AS DATE) AS DATETIME) FROM tbl_log_printinv C WHERE C.NOINV = RIGHT(v.Invoice,6) ORDER BY TglPrint DESC)TglPrintInvoice,
							NoSJmurni, Tgl_Invoice AS TglInv,
							TglSJmurni, ISNULL(Tanggal,'1900-01-01') AS TglSJmurniBalik,
							CAST((Tanggal - TglSJmurni) AS int) AS OverSJ, 
							(SELECT TOP 1 Tanggal FROM #temp_tanda_terima WHERE SL03002 = v.Invoice ORDER BY Tanggal DESC) AS TglTandaTerima,
							ISNULL((SELECT ST01002 FROM ST010100 WHERE ST01001 = KodeSales),'') AS Salesman, NoLTP,TglHari [Tgl Print Ltp],
							ISNULL((SELECT jlhinv FROM view_jlh_ltp WHERE noInvoice = SUBSTRING(Invoice ,5,6) AND nc = Namacustomer),'0') AS JlhLTP,
							v.Tglhari AS TglPrint, v.Keterangan AS [KetLTP],
							ISNULL((SELECT TOP 1 tbl_updatear.Keterangan FROM tbl_updatear WHERE tbl_updatear.noInvoice = Invoice), '') AS KetInvoice
						FROM #view_ar_detail V--LEFT JOIN tbl_updatear ON noInvoice = Invoice 
						WHERE sessionID='nancytjan' AND NamaCustomer <> 'Wasted Factory' AND NamaCustomer <> 'Wasted Office' 
						AND Tgl_Invoice > '01-01-2007' 
						AND SUBSTRING(Order_Number,3,1) <> '9' AND KodeSales <> '052' AND KodeSales <> '030' AND KodeSales <> '031' 
						AND Tgl_Invoice between @From AND @To
						AND (SELECT GL03003 FROM GL0301".substr(date("Y"), 2, 2)." WHERE GL03002 = (SELECT SUBSTRING(SL01017, 7 ,5) FROM SL010100 WHERE SL01001 = KodeCustomer)) = 'Sales Group A'
						AND NoSJmurni NOT IN (
							SELECT DNCustom FROM UDDBDN01".substr(date("Y"), 2, 2)." 
							WHERE OR19012 IN (
								SELECT OR20023 FROM OR200100 WHERE OR20021 IN (SELECT SL03003 FROM SL030100 WHERE SL03053=SL03013 AND SL03036 = v.Order_Number)
							) 
							AND OR19001 IN (
								SELECT OR20001 FROM OR200100 WHERE OR20021 IN (SELECT SL03003 FROM SL030100 WHERE SL03053=SL03013 AND SL03036 = v.Order_Number)
							) 
						)
						GROUP BY NamaCustomer,Invoice,NilaiInvoiceRp, PaymentRp,realterm,Tgl_Invoice,Tanggal,v.Order_Number,
						Tgl_Jatuh_Tempo,Limit1,KodeSales,noltp,TglHari,v.Tglhari,v.Keterangan,KodeCustomer,TglSJmurni,NoSJmurni
					) AR_DETAIL
				) AR_FINAL ".$y."
			)kaka ".$t."
				ORDER BY NamaCustomerGroup ASC, [TOP] DESC";
				 
				$result = mssql_query($query) or die(mssql_get_last_message());
				if(mssql_num_rows($result) == 0) { die("<i>No Data Available!</i>"); }
				$invList = array();
				while($res = mssql_fetch_array($result, MSSQL_ASSOC)) {
					$invList[] = $res;
				}
				
				$html = "<table id='tblResult' class='kpl-table'><thead><tr><th>No.</th>";
				$html .= '<br><input class="button" type="submit" name="Export" value="Export to Excel"><br><br>';
				foreach($invList[0] as $name => $val) {

					if ($name == 'NamaCustomerGroup') {
						$html .= "<th style='display:none;'>".$name."</th>";	
					}else{
						$html .= "<th>".$name."</th>";
					}
				}
				$html .= "</tr></thead><tbody>";
				for($i=0;$i<count($invList);$i++) {
					$cond = '';
					// echo $invList[$i]['Over'];
					if ($invList[$i]['OverFromTop'] <= 0) {
						$invList[$i]['OverFromTop'] = 0;
					}elseif($invList[$i]['OverFromTop'] > 0){
						$invList[$i]['OverFromTop'] = $invList[$i]['OverFromTop'];
					}
					// if($invList[$i]['Over'] > 0) { $cond = 'style="background-color:#ffe6e6 !important;"'; }
					// if($invList[$i]['Status'] > 0) { $cond = 'style="background-color:#ff335c !important;"'; }else{$cond = 'style="background-color:#3fc078 !important;"';}
					if($invList[$i]['Status'] > 0) { $cond2 = 'style="background-color:#ff335c !important;"'; }else{$cond2 = 'style="background-color:#3fc078 !important;"';}

					$html .= "
					<tr>
						<td>".($i+1)."</td>
						<td style='display:none;'><input type='text' name='namacustomergroup[]' value='".$invList[$i]['NamaCustomerGroup']."'>".$invList[$i]['NamaCustomerGroup']."</td>
						<td><input type='hidden' name='kodecustomer[]' value='".$invList[$i]['KodeCustomer']."'>".$invList[$i]['KodeCustomer']."</td>
						<td style='min-width:180px;'><input type='hidden' name='namacustomer[]' value='".$invList[$i]['NamaCustomer']."'>".$invList[$i]['NamaCustomer']."</td>
						<td><input type='hidden' name='tradechannel[]' value='".$invList[$i]['TradeChannel']."'>".$invList[$i]['TradeChannel']."</td>
						<td  style='text-align:right;'><input type='hidden' name='creditlimit[]' value='".$invList[$i]['CreditLimit']."'>".number_format($invList[$i]['CreditLimit'], 0)."</td>
						<td><input type='hidden' name='noinv[]' value='".$invList[$i]['NoInv']."'>".$invList[$i]['NoInv']."</td>
						<td style='text-align:right;'><input type='hidden' name='nilaiinv[]' value='".$invList[$i]['NilaiInv']."'>".number_format($invList[$i]['NilaiInv'], 0)."</td>
						<td style='text-align:right;'><input type='hidden' name='payment[]' value='".$invList[$i]['Payment']."'>".number_format($invList[$i]['Payment'], 0)."</td>
						<td><input type='hidden' name='top[]' value='".$invList[$i]['TOP']."'>".$invList[$i]['TOP']."</td>
						<td><input type='hidden' name='masatenggat[]' value='".$invList[$i]['MasaTenggat']."'>".$invList[$i]['MasaTenggat']."</td>
						<td style='min-width:80px;'><input type='hidden' name='tglkirim[]' value='".fixDate(date("Y-m-d", strtotime($invList[$i]['TglKirim'])))."'>".fixDate(date("Y-m-d", strtotime($invList[$i]['TglKirim'])))."</td>
						<td style='min-width:80px;'><input type='hidden' name='tglsjbalik[]' value='".fixDate(date("Y-m-d", strtotime($invList[$i]['TglSjBalik'])))."'>".fixDate(date("Y-m-d", strtotime($invList[$i]['TglSjBalik'])))."</td>
						<td style='min-width:80px;'><input type='hidden' name='TglPrintInvoice[]' value='".fixDate(date("Y-m-d", strtotime($invList[$i]['TglPrintInvoice'])))."'>".fixDate(date("Y-m-d", strtotime($invList[$i]['TglPrintInvoice'])))."</td>
						<td style='min-width:80px;'><input type='hidden' name='tglinv[]' value='".fixDate(date("Y-m-d", strtotime($invList[$i]['TglInv'])))."'>".fixDate(date("Y-m-d", strtotime($invList[$i]['TglInv'])))."</td>
						<td><input type='hidden' name='tgltandaterima[]' value='".fixDate(date("Y-m-d", strtotime($invList[$i]['TglTandaTerima'])))."'>".fixDate(date("Y-m-d", strtotime($invList[$i]['TglTandaTerima'])))."</td>
						<td style='min-width:150px;'><input type='hidden' name='salesman[]' value='".$invList[$i]['Salesman']."'>".$invList[$i]['Salesman']."</td>
						<td><input type='hidden' name='noltp[]' value='".$invList[$i]['NoLTP']."'>".$invList[$i]['NoLTP']."</td>
						<td nowrap style='min-width:180px;'><input type='hidden' name='TglLtp[]' value='".fixDate(date("Y-m-d", strtotime($invList[$i]['Tgl Print Ltp'])))."'>".fixDate(date("Y-m-d", strtotime($invList[$i]['Tgl Print Ltp'])))."</td>
						<td><input type='hidden' name='tgljatuhtempo[]' value='".fixDate(date("Y-m-d", strtotime($invList[$i]['TglJatuhTempo'])))."'>".fixDate(date("Y-m-d", strtotime($invList[$i]['TglJatuhTempo'])))."</td>
						<td><input type='hidden' name='over[]' value='".$invList[$i]['OverFromTop']."'>".$invList[$i]['OverFromTop']."</td>
						<td><input type='hidden' name='nopo[]' value='".$invList[$i]['Nopo']."'>".$invList[$i]['Nopo']."</td>
						<td><input type='hidden' name='noso[]' value='".$invList[$i]['Noso']."'>".$invList[$i]['Noso']."</td>
						<td nowrap><input type='hidden' name='alasan[]' value='".$invList[$i]['Alasan']."'>".$invList[$i]['Alasan']."</td>
						<td><input type='hidden' name='sisahari[]' value='".$invList[$i]['SisaHariUnBlock']."'>".$invList[$i]['SisaHariUnBlock']."</td>
						<td ".$cond2."><input type='hidden' name='status[]' value='".$invList[$i]['Status']."'>".($invList[$i]['Status'] == 0 ? 'UnBlock' : ($invList[$i]['Status'] == 1 ? 'Block' : ''))."</td>
					</tr>";
				}
				$html .= "</tbody></table><table id='excelTbl' style='display:none;'></table>";
			} else {
				$nowku = date('y');
				$nowku2 = $nowku - 2;

				for ($i = $nowku2; $i <= $nowku ; $i++) {
					if ($i == $nowku2) {
					 	$inv .= "'".$i."6','".$i."2'";
					 }else{
					 	$inv .= ",'".$i."6','".$i."2'";
					 } 
				}
				// echo $inv;
				$query = "
				DECLARE @From VARCHAR(10) = '".$df."'
				DECLARE @To VARCHAR(10) = '".$dt."'
				DECLARE @TradeChannel VARCHAR(MAX) = '".$tc."'
	
				
				IF OBJECT_ID('tempdb.dbo.#temp_tanda_terima') IS NOT NULL BEGIN DROP TABLE #temp_tanda_terima END;
				IF OBJECT_ID('tempdb.dbo.#or01') IS NOT NULL BEGIN DROP TABLE #or01 END;
				IF OBJECT_ID('tempdb.dbo.#or19') IS NOT NULL BEGIN DROP TABLE #or19 END;
				IF OBJECT_ID('tempdb.dbo.#VIEW_AR_DETAIL') IS NOT NULL BEGIN DROP TABLE #VIEW_AR_DETAIL END;
				IF OBJECT_ID('tempdb.dbo.#v_customergrouplist') IS NOT NULL BEGIN DROP TABLE #v_customergrouplist END;


				SELECT * INTO #VIEW_AR_DETAIL FROM VIEW_AR_DETAIL 	
				SELECT * INTO #temp_tanda_terima FROM tbl_tanda_terima WHERE Tanggal BETWEEN DATEADD(MONTH, -3, @From) AND DATEADD(MONTH, 3, @To);
				select *into #v_customergrouplist From v_customergrouplist
			
				select * into #or01 From (
				select or01001,or01072 From OR010100
				union
				select or20001,or20072 From or200100
				)a
				
				select * into #or19 From (
				select OR19001,OR19011 From OR190100
				union
				select OR23001,OR23011 From OR230100
				)a
				
				SELECT * FROM (
				SELECT 
				(select top 1 cast(NAMACUSTOMERGROUP as varchar(max)) from #v_customergrouplist a where a.kodecustomer = kodecustomers)NamaCustomerGroup,KodeCustomers KodeCustomer,NamaCustomer,TradeChannel,
				(select SL01037 from SL010100 where SL01001 = KodeCustomers)CreditLimit,NoInv,NilaiInv,Payment,[TOP],MasaTenggat,TglKirim,isnull(TglSJmurniblk,'1900-01-01')TglSjBalik,TglPrintInvoice,TglInv,
				TglTandaTerima,Salesman,NoLTP,[Tgl Print Ltp],TglJatuhTempoScala TglJatuhTempo,
				/*DATEADD(DAY, [TOP],TglInv) as TglJatuhTempo,*/
				ISNULL(DATEDIFF(DAY,DATEADD(DAY, [TOP], CASE WHEN YEAR(TglTandaTerima) <> '1900' THEN TglTandaTerima Else Tglinv End), GETDATE()), 0) AS [OverFromTop],
				(select top 1 OR01072 from #or01 where or01001 = noso)Nopo, Noso,
				Alasan,CASE WHEN Alasan LIKE '%Block%' OR Alasan LIKE '%UNBLOCK%' THEN '-' ELSE ISNULL(DATEDIFF(DAY,SisaHariBlock,GETDATE()),0) END SisaHariUnBlock
				,(SELECT SL01075 FROM SL010100 WHERE SL01001 = KodeCustomers) Status
				FROM (
					SELECT 
						Order_Number Noso,KodeCustomer KodeCustomers,(select ST01002 from ST010100 where ST01001 = KodeSales)Salesman,NamaCustomer, (SELECT GL03003 FROM GL0301".substr(date("Y"), 2, 2)." WHERE GL03002 = (SELECT SUBSTRING(SL01017, 7, 4) + '0' FROM SL010100 WHERE SL01001 = KodeCustomer)) AS TradeChannel, Invoice AS NoInv, NilaiInvoiceRp AS NilaiInv, PaymentRp AS Payment,
						(SELECT TOP 1 CAST(SL22005 AS INT) FROM SL220100 WHERE SL22002 = (select SL01024 from SL010100 where SL01001 = KodeCustomer)) AS [TOP],
						(select top 1 OR19011 From #or19 where OR19001 = Order_Number order by OR19011 desc)TglKirim,
						ISNULL((Select top 1 Tempo From Tbl_TempoCL c where c.KodeCustomer = v.KodeCustomer),'0')MasaTenggat,
						(SELECT TOP 1 CAST(CAST(TglPrint AS DATE) AS DATETIME) FROM tbl_log_printinv C WHERE C.NOINV = RIGHT(v.Invoice,6) ORDER BY TglPrint DESC)TglPrintInvoice,
						NoSJmurni, CONVERT(VARCHAR(15), Tgl_Invoice, 107) AS TglInv,
						CONVERT(varchar(15),TglSJmurni,107) AS TglSJmurni, CONVERT(varchar(15),Tanggal,107) AS TglSJmurniblk,
						ISNULL((SELECT TOP 1 Tanggal FROM #temp_tanda_terima WHERE SL03002 = V.Invoice ORDER BY Tanggal DESC), '1900-01-01') AS TglTandaTerima,
						CAST((Tanggal - TglSJmurni) AS integer) AS [OverSJ], 
						CONVERT(varchar(15),Tgl_jth_tt,107) AS TglJatuhTempoScala, 
						CAST(Limit1 AS int) AS [Over*],
						ISNULL((SELECT ST01002 FROM ST010100 WHERE ST01001 = KodeSales),'') AS NamaSales, NoLTP,TglHari [Tgl Print Ltp]
,
						ISNULL((SELECT jlhinv FROM view_jlh_ltp WHERE noInvoice = substring(Invoice ,5,6) AND nc = Namacustomer),'0') JlhInv,
						CONVERT(varchar(15),v.Tglhari,107) AS TglPrint, v.Keterangan AS KetLTP,
						(SELECT top 1  tbl_updatear.Keterangan FROM tbl_updatear WHERE tbl_updatear.noInvoice = Invoice) KetInvoice,
						ISNULL((SELECT TOP 1 alasan FROM Tbl_LogUnblockCustomer A WHERE A.KodeCustomer = V.KodeCustomer ORDER BY tanggal DESC),'-')Alasan,
					CAST((SELECT TOP 1 tanggal+7 FROM Tbl_LogUnblockCustomer A WHERE A.KodeCustomer = V.KodeCustomer ORDER BY tanggal DESC) AS VARCHAR(MAX))[SisaHariBlock]
						--, KodeCustomer, CAST((GETDATE() - view_ar_detail.Tglhari) AS int) ARTglHari
					FROM #view_ar_detail v --left join tbl_updatear ON noInvoice = Invoice 
					WHERE sessionID='nancytjan' AND NamaCustomer <> 'Wasted Factory' AND NamaCustomer <> 'Wasted Office' AND Tgl_Invoice > '01-01-2007' 
					AND substring(Order_Number,3,1)<>'9' AND KodeSales <> '052' AND KodeSales <> '030' AND KodeSales<>'031' 
					AND NoSJmurni not in (
						SELECT DNCustom FROM UDDBDN01".substr(date("Y"), 2, 2)."
						WHERE OR19012 in (
							SELECT OR20023 FROM OR200100 WHERE OR20021 in (SELECT SL03003 FROM SL030100 WHERE SL03053=SL03013 AND SL03036 = v.Order_Number)
						) 
						AND OR19001 in (
							SELECT OR20001 FROM OR200100 WHERE OR20021 in (SELECT SL03003 FROM SL030100 WHERE SL03053=SL03013 AND SL03036 = v.Order_Number)
						) 
					) AND Tgl_Invoice between @From AND @To
					GROUP BY NamaCustomer,Invoice,NilaiInvoiceRp, PaymentRp,Tgl_Invoice,Tanggal,v.Order_Number,
					Tgl_jth_tt,Limit1,KodeSales,noltp,TglHari,v.Tglhari,v.Keterangan,KodeCustomer,TglSJmurni,NoSJmurni
				) AJT
				WHERE  Tradechannel = @Tradechannel AND LEFT(NoInv, 3) IN (".$inv.") ".$z."
		)kaka  ".$t."
				ORDER BY NamaCustomerGroup asc, [Top] desc
";
				// echo "<pre>".$query."</pre>";die();
				$result = mssql_query($query) or die(mssql_get_last_message());
				$invList = array();
				if(mssql_num_rows($result) == 0) { die("<i>No Data Available!</i>"); }
				while($res = mssql_fetch_array($result, MSSQL_ASSOC)) {
					$invList[] = $res;
				}
				$html = "<table id='tblResult' class='kpl-table'><thead><tr><th>No.</th>";
				$html .= '<input class="button" type="submit" name="export" value="Export to Excel"><br><br>';
				foreach($invList[0] as $name => $val) {
					// echo '<pre>'.print_r($name,true).'</pre>';
					if ($name == 'NamaCustomerGroup') {
						$html .= "<th style='display:none;'>".$name."</th>";	
					}else{
						$html .= "<th>".$name."</th>";
					}
				}
				$html .= "</tr></thead><tbody>";
				for($i=0;$i<count($invList);$i++) {
					$cond = '';
					if ($invList[$i]['OverFromTop'] <= 0) {
						$invList[$i]['OverFromTop'] = 0;
					}elseif($invList[$i]['OverFromTop'] > 0){
						$invList[$i]['OverFromTop'] = $invList[$i]['OverFromTop'];
					}
					// if($invList[$i]['Over'] > 0) { $cond = 'style="background-color:#ffe6e6 !important;"'; }
					// if($invList[$i]['Status'] > 0) { $cond = 'style="background-color:#ff335c !important;"'; }else{$cond = 'style="background-color:#3fc078 !important;"';}
					if($invList[$i]['Status'] > 0) { $cond2 = 'style="background-color:#ff335c !important;"'; }else{$cond2 = 'style="background-color:#3fc078 !important;"';}
					$html .= "
					<tr>
						<td>".($i+1)."</td>
						<td style='display:none;'>><input type='text' name='namacustomergroup[]' value='".$invList[$i]['NamaCustomerGroup']."'>".$invList[$i]['NamaCustomerGroup']."</td>
						<td><input type='hidden' name='kodecustomer[]' value='".$invList[$i]['KodeCustomer']."'>".$invList[$i]['KodeCustomer']."</td>
						<td style='min-width:180px;'><input type='hidden' name='namacustomer[]' value='".$invList[$i]['NamaCustomer']."'>".$invList[$i]['NamaCustomer']."</td>
						<td><input type='hidden' name='tradechannel[]' value='".$invList[$i]['TradeChannel']."'>".$invList[$i]['TradeChannel']."</td>
						<td  style='text-align:right;'><input type='hidden' name='creditlimit[]' value='".$invList[$i]['CreditLimit']."'>".number_format($invList[$i]['CreditLimit'], 0)."</td>
						<td><input type='hidden' name='noinv[]' value='".$invList[$i]['NoInv']."'>".$invList[$i]['NoInv']."</td>
						<td style='text-align:right;'><input type='hidden' name='nilaiinv[]' value='".$invList[$i]['NilaiInv']."'>".number_format($invList[$i]['NilaiInv'], 0)."</td>
						<td style='text-align:right;'><input type='hidden' name='payment[]' value='".$invList[$i]['Payment']."'>".number_format($invList[$i]['Payment'], 0)."</td>
						<td><input type='hidden' name='top[]' value='".$invList[$i]['TOP']."'>".$invList[$i]['TOP']."</td>
						<td><input type='hidden' name='masatenggat[]' value='".$invList[$i]['MasaTenggat']."'>".$invList[$i]['MasaTenggat']."</td>
						<td style='min-width:80px;'><input type='hidden' name='tglkirim[]' value='".fixDate(date("Y-m-d", strtotime($invList[$i]['TglKirim'])))."'>".fixDate(date("Y-m-d", strtotime($invList[$i]['TglKirim'])))."</td>
						<td style='min-width:80px;'><input type='hidden' name='tglsjbalik[]' value='".fixDate(date("Y-m-d", strtotime($invList[$i]['TglSjBalik'])))."'>".fixDate(date("Y-m-d", strtotime($invList[$i]['TglSjBalik'])))."</td>
						<td style='min-width:80px;'><input type='hidden' name='TglPrintInvoice[]' value='".fixDate(date("Y-m-d", strtotime($invList[$i]['TglPrintInvoice'])))."'>".fixDate(date("Y-m-d", strtotime($invList[$i]['TglPrintInvoice'])))."</td>
						<td style='min-width:80px;'><input type='hidden' name='tglinv[]' value='".fixDate(date("Y-m-d", strtotime($invList[$i]['TglInv'])))."'>".fixDate(date("Y-m-d", strtotime($invList[$i]['TglInv'])))."</td>
						<td><input type='hidden' name='tgltandaterima[]' value='".fixDate(date("Y-m-d", strtotime($invList[$i]['TglTandaTerima'])))."'>".fixDate(date("Y-m-d", strtotime($invList[$i]['TglTandaTerima'])))."</td>
						<td style='min-width:150px;'><input type='hidden' name='salesman[]' value='".$invList[$i]['Salesman']."'>".$invList[$i]['Salesman']."</td>
						<td><input type='hidden' name='noltp[]' value='".$invList[$i]['NoLTP']."'>".$invList[$i]['NoLTP']."</td>
						<td nowrap><input type='hidden' name='TglLtp[]' value='".fixDate(date("Y-m-d", strtotime($invList[$i]['Tgl Print Ltp'])))."'>".fixDate(date("Y-m-d", strtotime($invList[$i]['Tgl Print Ltp'])))."</td>
						<td><input type='hidden' name='tgljatuhtempo[]' value='".fixDate(date("Y-m-d", strtotime($invList[$i]['TglJatuhTempo'])))."'>".fixDate(date("Y-m-d", strtotime($invList[$i]['TglJatuhTempo'])))."</td>
						<td><input type='hidden' name='over[]' value='".$invList[$i]['OverFromTop']."'>".$invList[$i]['OverFromTop']."</td>
						<td><input type='hidden' name='nopo[]' value='".$invList[$i]['Nopo']."'>".$invList[$i]['Nopo']."</td>
						<td><input type='hidden' name='noso[]' value='".$invList[$i]['Noso']."'>".$invList[$i]['Noso']."</td>
						<td nowrap><input type='hidden' name='alasan[]' value='".$invList[$i]['Alasan']."'>".$invList[$i]['Alasan']."</td>
						<td><input type='hidden' name='sisahari[]' value='".$invList[$i]['SisaHariUnBlock']."'>".$invList[$i]['SisaHariUnBlock']."</td>
						<td ".$cond2."><input type='hidden' name='status[]' value='".$invList[$i]['Status']."'>".($invList[$i]['Status'] == 0 ? 'UnBlock' : ($invList[$i]['Status'] == 1 ? 'Block' : ''))."</td>
					</tr>";
				}
				$html .= "</tbody></table><table id='excelTbl' style='display:none;'></table>";
			}
			echo $html;
		break;
	}
	exit();
}
// $tcOption = array(
// 	'Sales Group A', 'Sales Group B' ,'CV.Customer' , 'Distributor/Agen', 'B2B','Delta','E-Commerce','Export', 'Sales Group Baby','Surabaya'
// );

$tcString = mssql_query("
	SELECT *FROM GL0301".substr(date('Y'),2,2)." WHERE GL03001 = 'B' AND LEFT(GL03002,2) = '32' AND RIGHT(GL03002,1) = '0' AND GL03002 NOT IN('32000','32250')
");

while ($tc = mssql_fetch_array($tcString)) {
	$tcOption.= '<option value= "'.$tc["GL03003"].'">'.$tc[GL03003].'</option>';
}

$queryh = "SELECT DISTINCT NamaSales fROM(
			select distinct SL01035,
			(select ST01002 from ST010100 where ST01001 = SL01035)NamaSales,
			(select GL03003 from GL0301".date('y')." where GL03001 = 'B' and substring(SL01017,7,4) + '0' = GL03002)TradeChannel from SL010100
			where (select GL03003 from GL0301".date('y')." where GL03001 = 'B' and substring(SL01017,7,4) + '0' = GL03002) IN ('Sales Group A')
			AND (select ST01002 from ST010100 where ST01001 = SL01035) IS NOT NULL
			)A";
$runh = mssql_query($queryh) or die(mssql_get_last_message());
?>
<script src="js/ui.datepicker.js"></script>
<link rel="stylesheet" href="css/ui.datepicker.css" />
<style>
	h3 { margin: 5px; }
	#tblResult td {
		border: solid #ccc thin;
		padding: 2px;
	}	
	#tblResult th {
		border: solid #ccc thin;
		padding: 5px;
		background-color: #FF0;
	}	
</style>

<script type="text/javascript" src="js/tablesorter.js"></script>
<script type="text/javascript" src="js/jquery.metadata.js"></script>
<script type="text/javascript" src="js/excellentexport.min.js"></script>
<b>AR Sales</b>
<hr>
<i style="color: red;"><b>** Menampilkan Invoice Belum dan Sudah Jatuh Tempo</b></i>
<br>
<i style="color: red;"><b>** Over 0 = Belum Jatuh Tempo</b></i>
<br>
<i style="color: red;"><b>** Over > 0 = Sudah Jatuh Tempo</b></i>
<br>
<i style="color: #3fc078;"><b>Hijau Untuk UnBlock</b></i>
<br>
<i style="color: #c43b40;"><b>Merah Untuk Block</b></i>
<br>
<br>
<hr>
<form class="kpl-form" method="POST" action='AR/ArSalesExcel.php' id="formku">
	<p><input type="radio" name="tipe" value="sudah" checked="">&nbsp;Sudah Jatuh Tempo&nbsp;<input type="radio" name="tipe" value="belum">&nbsp;Belum Jatuh Tempo</p>
	<p>Tanggal Invoice &nbsp;: <input type='text' class='dtp' name="from" id='from' autocomplete="off" /> - <input type='text' autocomplete="off" class='dtp' name='to' id='to' /></p>
	<p>TradeChannel&nbsp;&nbsp;&nbsp;&nbsp;: 
		<select id='tradeChannel' name="tc">
<!-- 			<?php foreach($tcOption as $tc) : ?>
			<option value='<?php echo $tc; ?>'><?php echo $tc; ?></option>
			<?php endforeach; ?> -->
			<?php echo $tcOption ?>
		</select>
	</p>
	<div id="tampil">
	<p>Sales &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: 
		<select id='sales' name="sales">
			<option value='all'>-- All --</option>
			<?php while($p = mssql_fetch_array($runh)) { ?>
				<option value="<?php echo $p['NamaSales']; ?>"><?php echo $p['NamaSales']; ?></option>
			<?php } ?>
		</select>
	</p>
	</div>
	<button class='button' type='button' id='go'>GO</button>
	<br>
	<br>

<br />
<div id='result'></div>
</form>
<script type="text/javascript" src="js/excellentexport.min.js"></script>
<script>
	$('.dtp').datepicker({'dateFormat' : 'yy-mm-dd'});
	$('#tradeChannel').change(function(){
		var tc = $(this).val();
		$.ajax({
			url: 'AR/ArSales.php',
			type: 'POST',
			data: {'task':'getsl','tc':tc},
			success: function(h) {
				$("#tampil").html(h);
			}
		})
	});
	$("#go").click(function() { 
		var df = $("#from").val();
		var dt = $("#to").val();
		var tc = $("#tradeChannel").val();
		var sl = $("#sales").val();
		var tipe = $("input[name='tipe']:checked"). val()
		if (df == '' || dt == '') {
			alert('Tanggal Tidak Boleh Kosong');
		}else{
			$.ajax({
				url: 'AR/ArSales.php',
				type: 'POST',
				data: {'task':'getData', 'df':df, 'dt':dt, 'tc':tc,'sl':sl,'tipe':tipe},
				success: function(r) {
					$("#result").html(r);
					$.tablesorter.addParser({
						id: 'fancyNumber',
						is:function(s){return false;},
						format: function(s) {return s.replace(/[\,\.]/g,'');},
						type: 'numeric'
					});

					$('#tblResult').tablesorter({
						sortMultiSortKey: 'altKey',
						textExtraction: function(node, table, cellIndex){
							var val1 = $(node).text();
							var val2 = val1.replace(/[\,\.]/g,'');
							
							if(isNaN(val2)) return val1;
							return val2;
						}
					});

					if (tipe == 'sudah') {
						$('#formku').attr('action','AR/ArSalesExcel.php');
					}else if (tipe == 'belum'){
						$('#formku').attr('action','AR/ArSalesExcel2.php');
					}
					// $("#xcelExport").click(function() { 
					// 	$("#excelTbl").html($("#tblResult").html());
					// 	return ExcellentExport.excel(this, 'excelTbl');
					// });
				}
			});	
		}
	});
</script>