-- Garante que o banco já existente possua a mesma proteção de concorrência do schema.sql.
-- Execute uma vez no banco de produção caso ele tenha sido criado antes da restrição UNIQUE.

SET @index_exists = (
    SELECT COUNT(1)
    FROM information_schema.statistics
    WHERE table_schema = DATABASE()
      AND table_name = 'agendamentos'
      AND index_name = 'uq_agendamento_barbeiro_horario'
);

SET @sql = IF(
    @index_exists = 0,
    'ALTER TABLE agendamentos ADD CONSTRAINT uq_agendamento_barbeiro_horario UNIQUE (barbeiro, data_agendamento)',
    'SELECT 1'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
