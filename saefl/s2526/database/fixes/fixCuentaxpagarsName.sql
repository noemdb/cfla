SELECT id, name FROM cuentaxpagars
WHERE name REGEXP '[ÁÉÍÓÚÑáéíóúñ]';

-- ------------------

UPDATE cuentaxpagars
SET name = 
    REPLACE(
    REPLACE(
    REPLACE(
    REPLACE(
    REPLACE(
    REPLACE(
    REPLACE(
    REPLACE(
    REPLACE(
    REPLACE(
    REPLACE(
    REPLACE(name,
        'á','a'),
        'é','e'),
        'í','i'),
        'ó','o'),
        'ú','u'),
        'ñ','n'),
        'Á','A'),
        'É','E'),
        'Í','I'),
        'Ó','O'),
        'Ú','U'),
        'Ñ','N');

-- ------------------

UPDATE cuentaxpagars
SET name = UPPER(name);
