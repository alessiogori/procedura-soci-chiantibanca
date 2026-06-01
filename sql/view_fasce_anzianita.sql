// ATTENZIONE: ogni anno cambiare la condizione BETWEEN per aggiornare le date

// VERSIONE MIGLIORE

CREATE VIEW view_fasce_anzianita AS 
SELECT
    'Fascia 1 (1-3 anni)' AS Fascia,
    a.codFil,
    int1Filiale,
    COUNT(DISTINCT a.cag) AS qta
FROM tab_soci_as37 as a, tab_volumi as b
WHERE substring(dataPrimoRapporto,-4) BETWEEN (substr(curdate(),1,4) - 3) AND (substr(curdate(),1,4) ) 
AND StatoVAL not in ('E','S','N')    
AND a.Cag = b.cag
GROUP BY a.codFil

UNION

SELECT
    'Fascia 2 (4-6 anni)' AS Fascia,
    a.codFil,
    int1Filiale,
    COUNT(DISTINCT a.cag) AS qta
FROM tab_soci_as37 as a, tab_volumi as b
WHERE substring(dataPrimoRapporto,-4) BETWEEN (substr(curdate(),1,4) - 6) AND (substr(curdate(),1,4) - 4) 
AND StatoVAL not in ('E','S','N')    
AND a.Cag = b.cag
GROUP BY a.codFil

UNION

SELECT
    'Fascia 3 (7-10 anni)' AS Fascia,
    a.codFil,
    int1Filiale,
    COUNT(DISTINCT a.cag) AS qta
FROM tab_soci_as37 as a, tab_volumi as b
WHERE substring(dataPrimoRapporto,-4) BETWEEN (substr(curdate(),1,4) - 10) AND (substr(curdate(),1,4) - 7 )
AND StatoVAL not in ('E','S','N')    
AND a.Cag = b.cag
GROUP BY a.codFil

UNION

SELECT
    'Fascia 4 (11-20 anni)' AS Fascia,
    a.codFil,
    int1Filiale,
    COUNT(DISTINCT a.cag) AS qta
FROM tab_soci_as37 as a, tab_volumi as b
WHERE substring(dataPrimoRapporto,-4) BETWEEN (substr(curdate(),1,4) - 20) AND (substr(curdate(),1,4) - 11)
AND StatoVAL not in ('E','S','N')    
AND a.Cag = b.cag
GROUP BY a.codFil

UNION
SELECT
    'Fascia 5 (oltre 20 anni)' AS Fascia,
    a.codFil,
    int1Filiale,
    COUNT(DISTINCT a.cag) AS qta
FROM tab_soci_as37 as a, tab_volumi as b
WHERE substring(dataPrimoRapporto,-4) <= (substr(curdate(),1,4) - 21)  
AND StatoVAL not in ('E','S','N')    
AND a.Cag = b.cag
GROUP BY a.codFil

ORDER BY  1, 2, 3


// VERSIONE RIDOTTA
/////////////////////////////////////////////

CREATE VIEW view_fasce_anzianita AS 
SELECT
    'Fascia 1 (1-3 anni)' AS Fascia,
    codFil,
    COUNT(DISTINCT cag) AS qta
FROM tab_volumi 
WHERE substring(dataPrimoRapporto,-4) BETWEEN (substr(curdate(),1,4) - 3) AND (substr(curdate(),1,4) ) 
GROUP BY codFil

UNION

SELECT
    'Fascia 2 (4-6 anni)' AS Fascia,
    codFil,
    COUNT(DISTINCT cag) AS qta
FROM tab_volumi 
WHERE substring(dataPrimoRapporto,-4) BETWEEN (substr(curdate(),1,4) - 6) AND (substr(curdate(),1,4) - 4) 
GROUP BY codFil

UNION

SELECT
    'Fascia 3 (7-10 anni)' AS Fascia,
    codFil,
    COUNT(DISTINCT cag) AS qta
FROM tab_volumi 
WHERE substring(dataPrimoRapporto,-4) BETWEEN (substr(curdate(),1,4) - 10) AND (substr(curdate(),1,4) - 7)
GROUP BY codFil

UNION

SELECT
    'Fascia 4 (11-20 anni)' AS Fascia,
    codFil,
    COUNT(DISTINCT cag) AS qta
FROM tab_volumi 
WHERE substring(dataPrimoRapporto,-4) BETWEEN (substr(curdate(),1,4) - 20) AND (substr(curdate(),1,4) - 11)
GROUP BY codFil

UNION
SELECT
    'Fascia 5 (oltre 20 anni)' AS Fascia,
    codFil,
    COUNT(DISTINCT cag) AS qta
FROM tab_volumi 
WHERE substring(dataPrimoRapporto,-4) <= (substr(curdate(),1,4) - 21) 
GROUP BY codFil

ORDER BY  1, 2, 3

