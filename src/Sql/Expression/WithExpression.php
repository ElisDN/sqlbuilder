<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Expression;

use AlephTools\SqlBuilder\Query;

class WithExpression extends AbstractExpression
{
    /**
     * @param mixed $query
     * @param mixed $alias
     */
    public function __construct($query = null, $alias = null, bool $recursive = false)
    {
        if ($query !== null) {
            $this->append($query, $alias, $recursive);
        }
    }

    /**
     * @param mixed $query
     * @param mixed $alias
     * @return static
     */
    public function append($query, $alias = null, bool $recursive = false)
    {
        if ($this->sql !== '') {
            $this->sql .= ', ';
        }
        if ($alias === null) {
            $expression = $query;
        } elseif (is_scalar($alias)) {
            $expression = [(string)$alias => $query];
        } else {
            $expression = [[$alias, $query]];
        }
        $this->sql .= ($recursive ? 'RECURSIVE ' : '') . $this->convertNameToString($expression);
        return $this;
    }

    /**
     * @param mixed $expression
     */
    protected function convertNameToString($expression): string
    {
        if ($expression === null) {
            return $this->nullToString();
        }
        if ($expression instanceof RawExpression) {
            return $this->rawExpressionToString($expression);
        }
        if ($expression instanceof Query) {
            return $this->queryToString($expression);
        }
        if (is_array($expression)) {
            return $this->arrayToString($expression);
        }
        return (string)$expression;
    }

    protected function arrayToString(array $expression): string
    {
        $list = [];
        foreach ($expression as $alias => $query) {
            if (is_numeric($alias)) {
                if (is_array($query) && \count($query) === 2) {
                    [$alias, $query] = $query;
                } else {
                    $alias = null;
                }
            }
            $alias = $alias === null ? '' : $this->convertNameToString($alias);
            $list[] = ($alias === '' ? '' : $alias . ' AS ') . $this->convertNameToString($query);
        }
        return implode(', ', $list);
    }
}
