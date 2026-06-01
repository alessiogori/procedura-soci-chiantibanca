CREATE VIEW view_richiesteincorso AS


SELECT cag, count(*) as qta, 'Cessione a Banca' as TipoRichiesta
FROM tab_xls_cessioni
WHERE Cessione_a_banca = 'S'
AND Note_AO08 not in ('S5','S4','SA','SB','SC','SM','VB','VC')
GROUP BY cag

UNION

SELECT cag, count(*) as qta, 'Cessione a Socio' as TipoRichiesta
FROM tab_xls_cessioni
WHERE Cessione_a_socio = 'S'
AND Note_AO08 not in ('S5','S4','SA','SB','SC','SM','VB','VC')
GROUP BY cag

UNION

SELECT cag, count(*) as qta, 'Richiesta Liquidazione a Eredi' as TipoRichiesta
FROM tab_xls_decessi_eredi
WHERE Liquidazione_a_eredi = 'S'
and Note_AO08 not in ('S2','SM')
GROUP BY cag

UNION

SELECT cag, count(*) as qta, 'Richiesta Intestazione a Eredi' as TipoRichiesta
FROM tab_xls_decessi_eredi
WHERE Intestazione_a_eredi = 'S'
and Note_AO08 not in ('S2','SM')
GROUP BY cag

UNION

SELECT cag, count(*) as qta, 'In attesa decisione Eredi' as TipoRichiesta
FROM tab_xls_decessi_eredi
WHERE Intestazione_a_eredi <> 'S' 
AND Liquidazione_a_eredi <> 'S'
and Note_AO08 not in ('S2','SM')
GROUP BY cag

UNION

SELECT cag, count(*) as qta, 'Escluso Art.6' as TipoRichiesta
FROM tab_xls_recessi_esclusioni_sofferenze
WHERE Escluso_art_6 = 'S'
and Note_AO08 not in ('S4','SB','SC')
GROUP BY cag

UNION

SELECT cag, count(*) as qta, 'Escluso Art.14' as TipoRichiesta
FROM tab_xls_recessi_esclusioni_sofferenze
WHERE Escluso_art_14 = 'S'
and Note_AO08 not in ('S4','SB','SC')
GROUP BY cag

UNION

SELECT cag, count(*) as qta, 'Sofferenze' as TipoRichiesta
FROM tab_xls_recessi_esclusioni_sofferenze
WHERE Escluso_x_Passaggio_a_Sofferenze = 'S' 
and Note_AO08 not in ('S4','SB','SC')   
GROUP BY cag

ORDER BY cag