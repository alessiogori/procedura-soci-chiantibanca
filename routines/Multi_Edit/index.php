<?php 
include('header.php');
?>
<body>
<div class="container">
<br>
<br>
<form action="edit.php" method="post">
	<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="example">
		<div class="alert alert-success">
			<h2 style="text-align:center; font-family:cursive;">Aggiornamento Protocollo alla ricezione dei documenti</h2>
		</div>
		<thead>
			<tr>
				<th style="text-align:center; font-family:cursive; font-size:12px; color:blue;">Protocollo</th>
				<th style="text-align:center; font-family:cursive; font-size:12px; color:blue;">Qta</th>
				<th style="text-align:center; font-family:cursive; font-size:12px; color:blue;">Modulo</th>
				<th style="text-align:center; font-family:cursive; font-size:12px; color:blue;">Tessera</th>
				<th style="text-align:center; font-family:cursive; font-size:12px; color:blue;">Socio</th>
				<th style="text-align:center; font-family:cursive; font-size:12px; color:blue;">Filiale</th>
				<th style="text-align:center; font-family:cursive; font-size:12px; color:blue;">Operatore</th>
				<th style="text-align:center; font-family:cursive; font-size:12px; color:blue;">Data Compilaz</th>
				<th style="text-align:center; font-family:cursive; font-size:12px; color:blue;">Action</th>
			</tr>
		</thead>
		<tbody>
		<?php 
		$query=mysql_query("select p.Id, p.Protocollo, p.Qta, p.Tessera, concat(c.Cognome,' ',c.Nome) as Socio, 
							p.Modello, p.DataCompilazione, p.Filiale, p.OperatoreBanca, p.IpAddress, p.DataRicezioneMutua, p.OperatoreMutua, p.Note
							from tab_protocollo as p left join tab_comipa as c
							ON p.Tessera = c.Tessera
							ORDER BY p.Protocollo desc, concat(c.Cognome,' ',c.Nome) 
							")or die(mysql_error());
		while($row=mysql_fetch_array($query)){
		$id=$row['Id'];
		?>
			<tr>
				<td style="text-align:center; font-family:cursive; font-size:10px;"><?php echo $row['Protocollo'] ?></td>
				<td style="text-align:center; font-family:cursive; font-size:10px;"><?php echo $row['Qta'] ?></td>
				<td style="text-align:center; font-family:cursive; font-size:10px;"><?php echo $row['Modello'] ?></td>
				<td style="text-align:center; font-family:cursive; font-size:10px;"><?php echo $row['Tessera'] ?></td>
				<td style="text-align:center; font-family:cursive; font-size:10px;"><?php echo $row['Socio'] ?></td>
				<td style="text-align:center; font-family:cursive; font-size:10px;"><?php echo $row['Filiale'] ?></td>
				<td style="text-align:center; font-family:cursive; font-size:10px;"><?php echo $row['OperatoreBanca'] ?></td>
				<td style="text-align:center; font-family:cursive; font-size:10px;"><?php echo $row['DataCompilazione'] ?></td>
				<td style="text-align:center; font-family:cursive; font-size:10px;">
					<input name="selector[]" type="checkbox" value="<?php echo $id; ?>">
				</td>
			</tr>
		<?php  } ?>						 
		</tbody>
	</table>
	<br />				
	<button class="btn btn-success pull-right" style="font-family:cursive;" name="submit_mult" type="submit">
		Aggiorna i dati
	</button>
</form>



</div>
</body>
</html>