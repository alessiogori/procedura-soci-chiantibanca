CREATE VIEW view_volumi AS 
SELECT 
cag, area, v.codFil, desc_filiale,
sum(CC_num) as NumCC, sum(carte) as NumCarte, sum(TIT_polizze) as NumTitPol, sum(HB_num) as NumHB, sum(RACDIR_pers) as Raccolta, sum(IMPIEGHI_sal_pers) as Impieghi
FROM tab_volumi as v LEFT JOIN tab_psw as p 
ON v.codFil = CAST(p.filiale AS UNSIGNED)
GROUP BY cag
ORDER BY 2

CREATE VIEW view_volumi_as37 AS 
SELECT 
prot, cag, int1Socio, int2Socio, area, a.codFil, desc_filiale, nAzTot, nominaleAzTot
FROM tab_soci_as37 as a LEFT JOIN tab_psw as p 
ON a.codFil = CAST(p.filiale AS UNSIGNED)
WHERE statoVAL not in ('E','S','N')
ORDER BY 2

CREATE VIEW view_volumi_aree AS 
SELECT 
count(*) as qta, area, sum(nAzTot) as NumAzTot, sum(nominaleAzTot) as ValNomTot, 
'' as NumCC, '' as NumCarte, '' as NumTitPol, '' as NumHB, '' as Raccolta, '' as Impieghi
FROM view_volumi_as37
GROUP by area 
UNION
SELECT
'' as qta, area, '' as NumAzTot, '' as ValNomTot,
sum(NumCC), sum(NumCarte), sum(NumTitPol), sum(NumHB), sum(Raccolta), sum(Impieghi)
FROM view_volumi
GROUP by area 
ORDER BY 2

CREATE VIEW view_volumi_filiali AS 
SELECT 
count(*) as qta, codFil, sum(nAzTot) as NumAzTot, sum(nominaleAzTot) as ValNomTot, 
'' as NumCC, '' as NumCarte, '' as NumTitPol, '' as NumHB, '' as Raccolta, '' as Impieghi
FROM view_volumi_as37
GROUP by codFil 
UNION
SELECT
'' as qta, codFil, '' as NumAzTot, '' as ValNomTot,
sum(NumCC), sum(NumCarte), sum(NumTitPol), sum(NumHB), sum(Raccolta), sum(Impieghi)
FROM view_volumi
GROUP by codFil 
ORDER BY 2
