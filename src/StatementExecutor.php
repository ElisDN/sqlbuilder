<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder;

/**
 * Interface for classes intended to execute arbitrary SQL statements.
 */
interface StatementExecutor
{
    /**
     * Executes the SQL statement.
     *
     * @param string $sql The SQL statement.
     * @param array $params The parameters to be bound to the SQL statement.
     * @return int The number of rows affected by the command execution.
     */
    public function execute(string $sql, array $params): int;

    /**
     * Executes an insert statement.
     *
     * @return mixed Returns the ID of the last inserted row or sequence value.
     */
    public function insert(string $sql, array $params, string $sequence = null);

    /**
     * Executes the SQL statement and returns all rows.
     *
     * @param string $sql The SQL statement.
     * @param array $params The parameters to be bound to the SQL statement.
     * @return list<array<string,mixed>>
     */
    public function rows(string $sql, array $params): array;

    /**
     * Executes the SQL statement and returns the first row of the result.
     *
     * @param string $sql The SQL statement.
     * @param array $params The parameters to be bound to the SQL statement.
     * @return array<string,mixed>
     */
    public function row(string $sql, array $params): array;

    /**
     * Executes the SQL statement and returns the given column of the result.
     *
     * @param string $sql The SQL statement.
     * @param array $params The parameters to be bound to the SQL statement.
     * @return list<mixed>
     */
    public function column(string $sql, array $params): array;

    /**
     * Executes the SQL statement and returns the first column's value of the first row.
     *
     * @param string $sql The SQL statement.
     * @param array $params The parameters to be bound to the SQL statement.
     * @return mixed
     */
    public function scalar(string $sql, array $params);
}
