
CREATE VIEW view_fasce_anzianitasocio AS 

SELECT
    'Socio 10' AS Fascia,
    cag,
    concat(int1Socio, ' ', int2Socio) as Nominativo,
CASE
    WHEN dataEntrata = '31/12/1994' THEN dataEntrata_origine 
    ELSE dataEntrata 
END as dataEntrata,
CASE
    WHEN dataEntrata = '31/12/1994' THEN substr(curdate(),1,4) - substr((dataEntrata_origine),7,4) 
    ELSE substr(curdate(),1,4) - substr((dataEntrata),7,4)
END as AnzSocio,
    nAzTot,
    round(nAzTot*30.09) as Importo,
    dataNasc,
    substr(curdate(),1,4) - substr((dataNasc),7,4) as Eta,
    telefono,
    indirizzoEMail,
    codFil as Filiale,
    int1Filiale as NomeFiliale
FROM tab_soci_as37 as s LEFT JOIN tab_storico_pistoia as p
        ON s.prot = p.prot
WHERE (substring(dataEntrata,-4) BETWEEN ((substr(curdate(),1,4) - 10) - 10) AND (substr(curdate(),1,4) - 10)
                OR substring(dataEntrata_origine,-4) BETWEEN ((substr(curdate(),1,4) - 10) - 10) AND (substr(curdate(),1,4) - 10))
AND StatoVAL not in ('E','S','N') 	
AND codFil <> 90

UNION

SELECT
    'Socio 20' AS Fascia,
    cag,
    concat(int1Socio, ' ', int2Socio) as Nominativo,
CASE
    WHEN dataEntrata = '31/12/1994' THEN dataEntrata_origine 
    ELSE dataEntrata 
END as dataEntrata,
CASE
    WHEN dataEntrata = '31/12/1994' THEN substr(curdate(),1,4) - substr((dataEntrata_origine),7,4) 
    ELSE substr(curdate(),1,4) - substr((dataEntrata),7,4)
END as AnzSocio,
    nAzTot,
    round(nAzTot*30.09) as Importo,
    dataNasc,
    substr(curdate(),1,4) - substr((dataNasc),7,4) as Eta,
    telefono,
    indirizzoEMail,
    codFil as Filiale,
    int1Filiale as NomeFiliale
FROM tab_soci_as37 as s LEFT JOIN tab_storico_pistoia as p
        ON s.prot = p.prot
WHERE (substring(dataEntrata,-4) BETWEEN ((substr(curdate(),1,4) - 20) - 10) AND (substr(curdate(),1,4) - 20)
                OR substring(dataEntrata_origine,-4) BETWEEN ((substr(curdate(),1,4) - 20) - 10) AND (substr(curdate(),1,4) - 20))
AND StatoVAL not in ('E','S','N') 	
AND codFil <> 90

UNION

SELECT
    'Socio 30' AS Fascia,
    cag,
    concat(int1Socio, ' ', int2Socio) as Nominativo,
CASE
    WHEN dataEntrata = '31/12/1994' THEN dataEntrata_origine 
    ELSE dataEntrata 
END as dataEntrata,
CASE
    WHEN dataEntrata = '31/12/1994' THEN substr(curdate(),1,4) - substr((dataEntrata_origine),7,4) 
    ELSE substr(curdate(),1,4) - substr((dataEntrata),7,4)
END as AnzSocio,
    nAzTot,
    round(nAzTot*30.09) as Importo,
    dataNasc,
    substr(curdate(),1,4) - substr((dataNasc),7,4) as Eta,
    telefono,
    indirizzoEMail,
    codFil as Filiale,
    int1Filiale as NomeFiliale
FROM tab_soci_as37 as s LEFT JOIN tab_storico_pistoia as p
        ON s.prot = p.prot
WHERE (substring(dataEntrata,-4) BETWEEN ((substr(curdate(),1,4) - 30) - 10) AND (substr(curdate(),1,4) - 30)
                OR substring(dataEntrata_origine,-4) BETWEEN ((substr(curdate(),1,4) - 30) - 10) AND (substr(curdate(),1,4) - 30))
AND StatoVAL not in ('E','S','N') 	
AND codFil <> 90

UNION

SELECT
    'Socio 40' AS Fascia,
    cag,
    concat(int1Socio, ' ', int2Socio) as Nominativo,
CASE
    WHEN dataEntrata = '31/12/1994' THEN dataEntrata_origine 
    ELSE dataEntrata 
END as dataEntrata,
CASE
    WHEN dataEntrata = '31/12/1994' THEN substr(curdate(),1,4) - substr((dataEntrata_origine),7,4) 
    ELSE substr(curdate(),1,4) - substr((dataEntrata),7,4)
END as AnzSocio,
    nAzTot,
    round(nAzTot*30.09) as Importo,
    dataNasc,
    substr(curdate(),1,4) - substr((dataNasc),7,4) as Eta,
    telefono,
    indirizzoEMail,
    codFil as Filiale,
    int1Filiale as NomeFiliale
FROM tab_soci_as37 as s LEFT JOIN tab_storico_pistoia as p
        ON s.prot = p.prot
WHERE (substring(dataEntrata,-4) BETWEEN ((substr(curdate(),1,4) - 40) - 10) AND (substr(curdate(),1,4) - 40)
                OR substring(dataEntrata_origine,-4) BETWEEN ((substr(curdate(),1,4) - 40) - 10) AND (substr(curdate(),1,4) - 40))
AND StatoVAL not in ('E','S','N')   
AND codFil <> 90

UNION

SELECT
    'Socio 50' AS Fascia,
    cag,
    concat(int1Socio, ' ', int2Socio) as Nominativo,
CASE
    WHEN dataEntrata = '31/12/1994' THEN dataEntrata_origine 
    ELSE dataEntrata 
END as dataEntrata,
CASE
    WHEN dataEntrata = '31/12/1994' THEN substr(curdate(),1,4) - substr((dataEntrata_origine),7,4) 
    ELSE substr(curdate(),1,4) - substr((dataEntrata),7,4)
END as AnzSocio,
    nAzTot,
    round(nAzTot*30.09) as Importo,
    dataNasc,
    substr(curdate(),1,4) - substr((dataNasc),7,4) as Eta,
    telefono,
    indirizzoEMail,
    codFil as Filiale,
    int1Filiale as NomeFiliale
FROM tab_soci_as37 as s LEFT JOIN tab_storico_pistoia as p
        ON s.prot = p.prot
WHERE (substring(dataEntrata,-4) < (substr(curdate(),1,4) - 50)
                OR substring(dataEntrata_origine,-4) < (substr(curdate(),1,4) - 50) )
AND StatoVAL not in ('E','S','N') 
AND codFil <> 90

ORDER BY  1, 2, 3