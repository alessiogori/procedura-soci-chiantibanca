<?php 
include('header.php');
?>
<body>
<br />

<div class="container">

<a href="index.php" style="margin-left: 165px; font-family:cursive;" class="btn btn-danger">Indietro</a>
<br />
<br />
<form class="form-horizontal" action="edit_save.php" method="post">    
<?php
include('dbcon.php');
$id=$_POST['selector'];
$N = count($id);
for($i=0; $i < $N; $i++)
{
$result = mysql_query("	select p.Id, p.Protocollo, p.Qta, p.Tessera, concat(c.Cognome,' ',c.Nome) as Socio, 
							p.Modello, p.DataCompilazione, p.Filiale, p.OperatoreBanca, p.IpAddress, p.DataRicezioneMutua, p.OperatoreMutua, p.Note
							from tab_protocollo as p left join tab_comipa as c
							ON p.Tessera = c.Tessera
							where Id='$id[$i]'
							ORDER BY p.Protocollo desc, concat(c.Cognome,' ',c.Nome) 
						");
while($row = mysql_fetch_array($result))
{ ?>
	<div class="thumbnail" style="margin:auto; width:600px;">
	<div style="margin-left: 70px; margin-top: 20px;">

		Protocollo:<br> <b>nr.</b><span style="font-family:cursive; font-weight:bold; font-size:18px; color:blue;"><?php echo $row['Protocollo'] ?></span> - <b>Qtà </b><?php echo $row['Qta'] ?> - <b>Modulo </b><?php echo $row['Modello'] ?>
		- <b>Tessera </b><?php echo $row['Tessera'] ?> - <b>Socio </b><?php echo $row['Socio'] ?> - <b>Data Compilazione </b><?php echo $row['DataCompilazione'] ?> - <b>Operatore Banca </b><?php echo $row['OperatoreBanca'] ?> - - <b>Filiale </b><?php echo $row['Filiale'] ?> 

		<div class="control-group">
		<label class="control-label" for="inputEmail" style="font-family:cursive; font-weight:bold; font-size:18px; color:blue;">Note</label>
		<div class="controls">
			<input name="Note[]" type="text"  />
			<input name="Id[]" type="hidden" value="<?php echo  $row['Id'] ?>" />
		</div>
		</div>
		
		<div class="control-group">
		<label class="control-label" for="inputEmail" style="font-family:cursive; font-weight:bold; font-size:18px; color:blue;">Operatore Mutua</label>
		<div class="controls">
			<select name="OperatoreMutua[]" required>
				<option value=''></option>
				<option value='Foscoli'>Foscoli Daniela</option>
				<option value='Masciovecchio'>Masciovecchio Roberta</option>
				<option value='Turi'>Turi Giampaolo</option>
				<option value='Fedi'>Fedi Alessio</option>
			</select>
		</div>
		</div>
	
	</div>
	</div>

	<br />	
<?php 
}
}
?>
<input name="" class="btn btn-success" style="margin-left: 165px; font-family:cursive;" type="submit" value="Conferma ricezione">
</form>

</div>
</body>
</html>