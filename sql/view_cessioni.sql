CREATE VIEW view_cessioni AS
SELECT
	count(0) AS `QtaCessioni`,
	sum(`c`.`Valore_Nominale`) AS `Valore_Nominale_Cessioni`,
	'' AS `QtaSoci`,
	'' AS `Valore_Nominale`,
	`c`.`Filiale` AS `Filiale`,
	`p`.`area` AS `Area`
FROM
	(
		`tab_xls_cessioni` `c`
		JOIN `tab_psw` `p` ON (
			(
				`c`.`Filiale` = cast(`p`.`filiale` AS UNSIGNED)
			)
		)
	)
WHERE
	(
		(`c`.`Cessione_a_Banca` = 'S')
		AND (
			`c`.`Note_AO08` NOT IN (
				'S5',
				'S4',
				'SA',
				'SB',
				'SC',
				'SM',
				'VB'
			)
		)
	)
GROUP BY
	`c`.`Filiale`
UNION
	SELECT
		'' AS `QtaCessioni`,
		'' AS `Valore_Nominale_Cessioni`,
		count(0) AS `QtaSoci`,
		sum(
			`view_volumi_as37`.`nominaleAzTot`
		) AS `Valore_Nominale`,
		`view_volumi_as37`.`codFil` AS `Filiale`,
		`view_volumi_as37`.`area` AS `Area`
	FROM
		`view_volumi_as37`
	GROUP BY
		`view_volumi_as37`.`codFil`
	ORDER BY
		`Area`