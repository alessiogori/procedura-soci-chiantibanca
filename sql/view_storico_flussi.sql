/*
causaleUscitaVAL
B = Cessione a Banca --> considera dataEstinzione
5 = Fusione 		 --> considera dataEstinzione
4 = Cessione a Socio --> considera dataEstinzione
3 = Esclusione		 --> considera dataUscita
2 = Decessi		 	 --> considera dataUscita
1 = Recesso		 	 --> considera dataUscita
*/



select 
`tab_psw`.`area` AS `area`,
`tab_psw`.`filiale` AS `filiale`,
`tab_psw`.`desc_filiale` AS `desc_filiale`,
concat(substr(`tab_soci_as37`.`dataEntrata`,7,4),substr(`tab_soci_as37`.`dataEntrata`,4,2)) AS `Anno`,
'IN' as Tipo,
count(concat(substr(`tab_soci_as37`.`dataEntrata`,7,4),substr(`tab_soci_as37`.`dataEntrata`,4,2))) AS `qtaEntrata`,
'' AS `qtaUscita` ,
sum(nominaleAzTot) as Nominale_IN,
'' as Nominale_OUT
from (`tab_soci_as37` join `tab_psw` 
	on((`tab_soci_as37`.`codFil` = cast(`tab_psw`.`filiale` as unsigned)))) 
group by `tab_psw`.`area`,`tab_psw`.`filiale`,`tab_psw`.`desc_filiale`,concat(substr(`tab_soci_as37`.`dataEntrata`,7,4),substr(`tab_soci_as37`.`dataEntrata`,4,2)) 

union 

select `tab_psw`.`area` AS `area`,
`tab_psw`.`filiale` AS `filiale`,
`tab_psw`.`desc_filiale` AS `desc_filiale`,
concat(substr(`tab_soci_as37`.`dataEstinzione`,7,4),substr(`tab_soci_as37`.`dataEstinzione`,4,2)) AS `Anno`,
CONCAT('OUT_',causaleUscitaVAL) as Tipo,
'' AS `qtaEntrata`,
count(concat(substr(`tab_soci_as37`.`dataEstinzione`,7,4),substr(`tab_soci_as37`.`dataEstinzione`,4,2))) AS `qtaUscita` ,
'' as Nominale_IN,
sum(nominaleAzTot) as Nominale_OUT
from (`tab_soci_as37` join `tab_psw` 
	on((`tab_soci_as37`.`codFil` = cast(`tab_psw`.`filiale` as unsigned)))) 
where causaleUscitaVAL in ('B','5','4')
group by `tab_psw`.`area`,`tab_psw`.`filiale`,`tab_psw`.`desc_filiale`,concat(substr(`tab_soci_as37`.`dataEstinzione`,7,4),substr(`tab_soci_as37`.`dataEstinzione`,4,2)) 

union 

select `tab_psw`.`area` AS `area`,
`tab_psw`.`filiale` AS `filiale`,
`tab_psw`.`desc_filiale` AS `desc_filiale`,
concat(substr(`tab_soci_as37`.`dataUscita`,7,4),substr(`tab_soci_as37`.`dataUscita`,4,2)) AS `Anno`,
CONCAT('OUT_',causaleUscitaVAL) as Tipo,
'' AS `qtaEntrata`,
count(concat(substr(`tab_soci_as37`.`dataUscita`,7,4),substr(`tab_soci_as37`.`dataUscita`,4,2))) AS `qtaUscita` ,
'' as Nominale_IN,
sum(nominaleAzTot) as Nominale_OUT
from (`tab_soci_as37` join `tab_psw` 
	on((`tab_soci_as37`.`codFil` = cast(`tab_psw`.`filiale` as unsigned)))) 
where causaleUscitaVAL in ('3','2','1')
group by `tab_psw`.`area`,`tab_psw`.`filiale`,`tab_psw`.`desc_filiale`,concat(substr(`tab_soci_as37`.`dataUscita`,7,4),substr(`tab_soci_as37`.`dataUscita`,4,2)) 

order by `area`,`filiale`,`Anno`