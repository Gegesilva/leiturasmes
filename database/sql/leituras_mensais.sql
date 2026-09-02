-- Consulta de referência do relatório. A aplicação gera as colunas mensais
-- dinamicamente conforme o período selecionado.
SELECT TB02112_CODIGO AS contrato,
       TB01010_REFERENCIA AS modelo,
       TB02111_CODCLI AS codcli,
       TB01008_NOME AS cliente,
       TB02112_NUMSERIE AS serial,
       TB02117_TOTPB AS totpb,
       TB02117_TOTCOLOR AS totcolor,
       TB02117_TOTGF AS totgf,
       TB02117_DTCAD AS data_da_leitura_de_contadores,
       TB02117_MEDATUAL AS medatual
  FROM TB02112
  LEFT JOIN TB02111 ON TB02111_CODIGO = TB02112_CODIGO
  LEFT JOIN TB01010 ON TB01010_CODIGO = TB02112_PRODUTO
  LEFT JOIN TB02117 ON TB02117_CODIGO = TB02111_CODIGO
                   AND TB02117_NUMSERIE = TB02112_NUMSERIE
  LEFT JOIN TB01008 ON TB01008_CODIGO = TB02111_CODCLI;
