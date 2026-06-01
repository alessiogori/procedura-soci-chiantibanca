CREATE VIEW view_fasce_senzarichieste AS 

SELECT
    'Fascia 1 (1-33 azioni)' AS Fascia,
    t.cag as Cag, CONCAT(t.int1Socio,' ',t.int2Socio) as Nominativo, t.codFil as Filiale, t.int1Filiale as NomeFiliale,
    t.nAzTot as AzTotali
FROM tab_soci_as37 as t
WHERE t.cag not in (select v.cag from view_richiesteincorso as v)
AND t.nAzTot BETWEEN 1 AND 33 
AND t.StatoVAL not in ('E','S','N')
GROUP BY t.cag, t.nAzTot

UNION

SELECT
    'Fascia 2 (34-40 azioni)' AS Fascia,
    t.cag as Cag, CONCAT(t.int1Socio,' ',t.int2Socio) as Nominativo, t.codFil as Filiale, t.int1Filiale as NomeFiliale,
    t.nAzTot as AzTotali
FROM tab_soci_as37 as t
WHERE t.cag not in (select v.cag from view_richiesteincorso as v)
AND t.nAzTot BETWEEN 34 AND 40 
AND t.StatoVAL not in ('E','S','N')
GROUP BY t.cag, t.nAzTot

UNION

SELECT
    'Fascia 3 (41-60 azioni)' AS Fascia,
    t.cag as Cag, CONCAT(t.int1Socio,' ',t.int2Socio) as Nominativo, t.codFil as Filiale, t.int1Filiale as NomeFiliale,
    t.nAzTot as AzTotali
FROM tab_soci_as37 as t
WHERE t.cag not in (select v.cag from view_richiesteincorso as v)
AND t.nAzTot BETWEEN 41 AND 60 
AND t.StatoVAL not in ('E','S','N')
GROUP BY t.cag, t.nAzTot

UNION

SELECT
    'Fascia 4 (61-100 azioni)' AS Fascia,
    t.cag as Cag, CONCAT(t.int1Socio,' ',t.int2Socio) as Nominativo, t.codFil as Filiale, t.int1Filiale as NomeFiliale,
    t.nAzTot as AzTotali
FROM tab_soci_as37 as t
WHERE t.cag not in (select v.cag from view_richiesteincorso as v)
AND t.nAzTot BETWEEN 61 AND 100 
AND t.StatoVAL not in ('E','S','N')
GROUP BY t.cag, t.nAzTot

UNION

SELECT
    'Fascia 5 (101-300 azioni)' AS Fascia,
    t.cag as Cag, CONCAT(t.int1Socio,' ',t.int2Socio) as Nominativo, t.codFil as Filiale, t.int1Filiale as NomeFiliale,
    t.nAzTot as AzTotali
FROM tab_soci_as37 as t
WHERE t.cag not in (select v.cag from view_richiesteincorso as v)
AND t.nAzTot BETWEEN 101 AND 300 
AND t.StatoVAL not in ('E','S','N')
GROUP BY t.cag, t.nAzTot

UNION

SELECT
    'Fascia 6 (301-500 azioni)' AS Fascia,
    t.cag as Cag, CONCAT(t.int1Socio,' ',t.int2Socio) as Nominativo, t.codFil as Filiale, t.int1Filiale as NomeFiliale,
    t.nAzTot as AzTotali
FROM tab_soci_as37 as t
WHERE t.cag not in (select v.cag from view_richiesteincorso as v)
AND t.nAzTot BETWEEN 301 AND 500 
AND t.StatoVAL not in ('E','S','N')
GROUP BY t.cag, t.nAzTot

UNION

SELECT
    'Fascia 7 (501-1000 azioni)' AS Fascia,
    t.cag as Cag, CONCAT(t.int1Socio,' ',t.int2Socio) as Nominativo, t.codFil as Filiale, t.int1Filiale as NomeFiliale,
    t.nAzTot as AzTotali
FROM tab_soci_as37 as t
WHERE t.cag not in (select v.cag from view_richiesteincorso as v)
AND t.nAzTot BETWEEN 501 AND 1000 
AND t.StatoVAL not in ('E','S','N')
GROUP BY t.cag, t.nAzTot

UNION

SELECT
    'Fascia 8 (oltre 1001 azioni)' AS Fascia,
    t.cag as Cag, CONCAT(t.int1Socio,' ',t.int2Socio) as Nominativo, t.codFil as Filiale, t.int1Filiale as NomeFiliale,
    t.nAzTot as AzTotali
FROM tab_soci_as37 as t
WHERE t.cag not in (select v.cag from view_richiesteincorso as v)
AND t.nAzTot > 1001 
AND t.StatoVAL not in ('E','S','N')
GROUP BY t.cag, t.nAzTot


ORDER BY  1, 2