CREATE VIEW view_fasce_peranno AS
SELECT
    'Fascia 1 (18-30 anni)' AS Fascia,
    COUNT(DISTINCT Cag) AS qta,
    SUBSTRING(dataAmmiss, 7, 4) as AnnoAmmissione
FROM tab_soci_as37
WHERE SUBSTRING(dataNasc, 7, 4) BETWEEN (substr(curdate(),1,4) - 30) AND (substr(curdate(),1,4) ) 
AND StatoVAL not in ('E','S','N')  
AND tipoContropVAL = 11000
GROUP BY SUBSTRING(dataAmmiss, 7, 4)

UNION

SELECT
    'Fascia 2 (31-50 anni)' AS Fascia,
    COUNT(DISTINCT Cag) AS qta,
    SUBSTRING(dataAmmiss, 7, 4) as AnnoAmmissione
FROM  tab_soci_as37
WHERE  SUBSTRING(dataNasc, 7, 4) BETWEEN (substr(curdate(),1,4) - 50) AND (substr(curdate(),1,4) - 31 ) 
AND StatoVAL not in ('E','S','N') 
AND tipoContropVAL = 11000
GROUP BY SUBSTRING(dataAmmiss, 7, 4)

UNION

SELECT
    'Fascia 3 (51-60 anni)' AS Fascia,
    COUNT(DISTINCT Cag) AS qta,
    SUBSTRING(dataAmmiss, 7, 4) as AnnoAmmissione
FROM  tab_soci_as37
WHERE  SUBSTRING(dataNasc, 7, 4) BETWEEN (substr(curdate(),1,4) - 60) AND (substr(curdate(),1,4) - 51 )
AND StatoVAL not in ('E','S','N')   
AND tipoContropVAL = 11000
GROUP BY SUBSTRING(dataAmmiss, 7, 4)

UNION

SELECT
    'Fascia 4 (61-70 anni)' AS Fascia,
    COUNT(DISTINCT Cag) AS qta,
    SUBSTRING(dataAmmiss, 7, 4) as AnnoAmmissione
FROM  tab_soci_as37
WHERE  SUBSTRING(dataNasc, 7, 4) BETWEEN (substr(curdate(),1,4) - 70) AND (substr(curdate(),1,4) - 61 ) 
AND StatoVAL not in ('E','S','N') 
AND tipoContropVAL = 11000
GROUP BY SUBSTRING(dataAmmiss, 7, 4)

UNION
SELECT
    'Fascia 5 (oltre 70 anni)' AS Fascia,
    COUNT(DISTINCT Cag) AS qta,
    SUBSTRING(dataAmmiss, 7, 4) as AnnoAmmissione
FROM  tab_soci_as37
WHERE SUBSTRING(dataNasc, 7, 4) <= (substr(curdate(),1,4) - 71)
AND StatoVAL not in ('E','S','N') 
AND tipoContropVAL = 11000
GROUP BY SUBSTRING(dataAmmiss, 7, 4)

ORDER BY  1,3