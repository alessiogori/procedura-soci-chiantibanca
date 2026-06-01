CREATE VIEW view_tmp_cessioni as 

SELECT round(sum(azioni_sottoscritte * 30.09)) as importo, concat(substring(CDA,7,4), substring(CDA,4,2)) as AnnoMese
FROM tab_xls_ammissioni 
WHERE Flag_da_SUCC_CESS not in ('S','C','D')
AND Pac <> 'S'
GROUP BY concat(substring(CDA,7,4), substring(CDA,4,2))

UNION

SELECT round(sum(Qta_Ulteriori_azioni_da_acquistare * 30.09)) as importo, concat(substring(CDA,7,4), substring(CDA,4,2)) as AnnoMese
FROM tab_xls_ammissioni 
WHERE Flag_da_SUCC_CESS = 'S'
AND Qta_Ulteriori_azioni_da_acquistare > 0
GROUP BY concat(substring(CDA,7,4), substring(CDA,4,2))

UNION

SELECT round(sum(Qta_Ulteriori_azioni_da_acquistare * 30.09)) as importo, concat(substring(CDA,7,4), substring(CDA,4,2)) as AnnoMese
FROM tab_xls_ammissioni 
WHERE Flag_da_SUCC_CESS = 'C'
AND Qta_Ulteriori_azioni_da_acquistare > 0
GROUP BY concat(substring(CDA,7,4), substring(CDA,4,2))

UNION

SELECT round(sum(Rata01+Rata02+Rata03+Rata04+Rata05+Rata06+Rata07+Rata08+Rata09+Rata10+Rata11+Rata12) * 30.09) as importo, concat(substring(CDA,7,4), substring(CDA,4,2)) as AnnoMese
FROM tab_xls_ammissioni 
WHERE Flag_da_SUCC_CESS not in ('S','C','D')
AND Pac = 'S'
GROUP BY concat(substring(CDA,7,4), substring(CDA,4,2))

ORDER BY 2