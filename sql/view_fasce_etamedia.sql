CREATE VIEW `view_fasce_etamedia` AS 
SELECT
	'Fascia 1 (18-30 anni)' AS `Fascia`,
	avg(
		(
			(substr(curdate(),1,4) ) - substr(
				`tab_soci_as37`.`dataNasc`,
				7,
				4
			)
		)
	) AS `eta`
FROM
	`tab_soci_as37`
WHERE
	(
		(
			substr(
				`tab_soci_as37`.`dataNasc`,
				7,
				4
			) BETWEEN (substr(curdate(),1,4) - 30) AND (substr(curdate(),1,4) ) 
		)
		AND (
			`tab_soci_as37`.`statoVAL` NOT IN ('E', 'S', 'N')
		)
		AND (
			`tab_soci_as37`.`tipoContropVAL` = 11000
		)
	)
UNION
	SELECT
		'Fascia 2 (31-50 anni)' AS `Fascia`,
		avg(
			(
				(substr(curdate(),1,4) ) - substr(
					`tab_soci_as37`.`dataNasc`,
					7,
					4
				)
			)
		) AS `eta`
	FROM
		`tab_soci_as37`
	WHERE
		(
			(
				substr(
					`tab_soci_as37`.`dataNasc`,
					7,
					4
				) BETWEEN (substr(curdate(),1,4) - 50) AND (substr(curdate(),1,4) - 31) 
			)
			AND (
				`tab_soci_as37`.`statoVAL` NOT IN ('E', 'S', 'N')
			)
			AND (
				`tab_soci_as37`.`tipoContropVAL` = 11000
			)
		)
	UNION
		SELECT
			'Fascia 3 (51-60 anni)' AS `Fascia`,
			avg(
				(
					(substr(curdate(),1,4) ) - substr(
						`tab_soci_as37`.`dataNasc`,
						7,
						4
					)
				)
			) AS `eta`
		FROM
			`tab_soci_as37`
		WHERE
			(
				(
					substr(
						`tab_soci_as37`.`dataNasc`,
						7,
						4
					) BETWEEN (substr(curdate(),1,4) - 60) AND (substr(curdate(),1,4) - 51) 
				)
				AND (
					`tab_soci_as37`.`statoVAL` NOT IN ('E', 'S', 'N')
				)
				AND (
					`tab_soci_as37`.`tipoContropVAL` = 11000
				)
			)
		UNION
			SELECT
				'Fascia 4 (61-70 anni)' AS `Fascia`,
				avg(
					(
						(substr(curdate(),1,4) ) - substr(
							`tab_soci_as37`.`dataNasc`,
							7,
							4
						)
					)
				) AS `eta`
			FROM
				`tab_soci_as37`
			WHERE
				(
					(
						substr(
							`tab_soci_as37`.`dataNasc`,
							7,
							4
						) BETWEEN (substr(curdate(),1,4) - 70) AND (substr(curdate(),1,4) - 61) 
					)
					AND (
						`tab_soci_as37`.`statoVAL` NOT IN ('E', 'S', 'N')
					)
					AND (
						`tab_soci_as37`.`tipoContropVAL` = 11000
					)
				)
			UNION
				SELECT
					'Fascia 5 (oltre 70 anni)' AS `Fascia`,
					avg(
						(
							(substr(curdate(),1,4) ) - substr(
								`tab_soci_as37`.`dataNasc`,
								7,
								4
							)
						)
					) AS `eta`
				FROM
					`tab_soci_as37`
				WHERE
					(
						(
							substr(
								`tab_soci_as37`.`dataNasc`,
								7,
								4
							) <= (substr(curdate(),1,4) - 71) 
						)
						AND (
							`tab_soci_as37`.`statoVAL` NOT IN ('E', 'S', 'N')
						)
						AND (
							`tab_soci_as37`.`tipoContropVAL` = 11000
						)
					)
				ORDER BY
					`Fascia`