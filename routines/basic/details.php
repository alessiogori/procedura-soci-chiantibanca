<!DOCTYPE html>
<html>
<head>
<title>Basic MySQLi Commands</title>
</head>
<body>
	<div>
		<h3>Elenco iscritti all'Evento</h3>
		<table border="1" cellpadding="2" cellspacing="0">
			<thead>
						<tr>
				<?php
					include('conn.php');
					$querytitoli=mysqli_query($conn,"SHOW COLUMNS from tab_eventi_iscrizioni");
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
					$query=mysqli_query($conn,"select * from tab_eventi_iscrizioni where idevento=".$_GET['id']);
					while($row=mysqli_fetch_array($query)){
						?>
						<tr>
							<td><?php echo $row['idregistrazione']; ?></td>
							<td><?php echo $row['idevento']; ?></td>
							<td><?php echo $row['data_richiesta']; ?></td>
							<td><?php echo $row['utente_inserimento']; ?></td>
							<td><?php echo $row['nag']; ?></td>
							<td><?php echo $row['nominativo']; ?></td>
							<td><?php echo $row['data_nascita']; ?></td>
							<td><?php echo $row['luogo_nascita']; ?></td>
							<td><?php echo $row['email']; ?></td>
							<td><?php echo $row['cellulare']; ?></td>
							<td><?php echo $row['note']; ?></td>
							<td>
								<a href="edit.php?idreg=<?php echo $row['idregistrazione'];?>">Edit</a>
								&nbsp;&nbsp;&nbsp;
								<a style="color:red;" href="delete.php?idreg=<?php echo $row['idregistrazione'];?>" title="ATTENZIONE: OPERAZIONE IRREVERSIBILE">Delete</a>
							</td>
						</tr>
						<?php
					}
				?>
			</tbody>
		</table>
	</div>
	<br><br>

</body>
</html>