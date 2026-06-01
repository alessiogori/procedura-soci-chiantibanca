<!DOCTYPE html>
<html>
<head>
<title>Basic MySQLi Commands</title>
</head>
<body>
	<div>
		<h3>Elenco Eventi</h3>
		<table border="1" cellpadding="2" cellspacing="0">
			<thead>
						<tr>
				<?php
					include('conn.php');
					$querytitoli=mysqli_query($conn,"SHOW COLUMNS from tab_eventi");
					while($titoli=mysqli_fetch_array($querytitoli)){
						?>
							<td style="background-color:lightgreen;"><?php echo $titoli['Field']; ?></td>
						<?php
					}
				?>
				<tdstyle="background-color:gray;">&nbsp;</td>
						</tr>
			</thead>
			<tbody>
				<?php
					include('conn.php');
					$query=mysqli_query($conn,"select * from tab_eventi");
					while($row=mysqli_fetch_array($query)){
						?>
						<tr>
							<td><?php echo $row['idevento']; ?></td>
							<td><?php echo $row['tipo_evento']; ?></td>
							<td><?php echo $row['descrizione_evento']; ?></td>
							<td><?php echo $row['data_evento']; ?></td>
							<td><?php echo $row['ora_evento']; ?></td>
							<td><?php echo $row['luogo_evento']; ?></td>
							<td><?php echo $row['note']; ?></td>
							<td><?php echo $row['link']; ?></td>
							<td><?php echo $row['posti_disponibili']; ?></td>
							<td><?php echo $row['posti_residui']; ?></td>
							<td>
								<a href="edit.php?id=<?php echo $row['idevento']; ?>">Edit</a>
								&nbsp;&nbsp;&nbsp;
								<a href="details.php?id=<?php echo $row['idevento']; ?>">Elenco</a>
								&nbsp;&nbsp;&nbsp;
								<a style="color:red;" href="delete.php?id=<?php echo $row['idevento']; ?>" title="ATTENZIONE: OPERAZIONE IRREVERSIBILE">Delete</a>
							</td>
						</tr>
						<?php
					}
				?>
			</tbody>
		</table>
	</div>
	<br><br>

	<div>
		<form method="POST" action="add.php">
		<label>Tipo Evento:</label>
			<input type="text" name="tipo_evento">
		<br>
		<label>Descrizione Evento:</label>
			<input type="text" name="descrizione_evento">
		<br>
		<label>Data Evento:</label>
			<input type="text" name="data_evento" placeholder="gg/mm/aaaa">
		<br>
		<label>Ora Evento:</label>
			<input type="text" name="ora_evento" placeholder="hh:mm">
		<br>
		<label>Luogo Evento:</label>
			<input type="text" name="luogo_evento">
		<br>
		<label>Note:</label>
			<input type="text" name="note">
		<br>
		<label>Link:</label>
			<input type="text"name="link">
		<br>
		<label>Posti Disponibili:</label>
			<input type="text" name="posti_disponibili">
		<br>
		<label>Posti Residui:</label>
			<input type="text" name="posti_residui">
			<input type="submit" name="add" value="Carica Nuovo Evento">
		</form>
	</div>

</body>
</html>