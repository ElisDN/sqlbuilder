<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Expression;

use AlephTools\SqlBuilder\Query;
use Closure;

class ConditionalExpression extends AbstractExpression
{
    /**
     * @param mixed $column
     * @param mixed $operator
     * @param mixed $value
     */
    public function __construct($column = null, $operator = null, $value = null)
    {
        if ($column !== null) {
            $this->with($column, $operator, $value);
        }
    }

    /**
     * @param mixed $column
     * @param mixed $operator
     * @param mixed $value
     * @return static
     */
    public function andWhere($column, $operator = null, $value = null)
    {
        return $this->with($column, $operator, $value);
    }

    /**
     * @param mixed $column
     * @param mixed $operator
     * @param mixed $value
     * @return static
     */
    public function orWhere($column, $operator = null, $value = null)
    {
        return $this->with($column, $operator, $value, 'OR');
    }

    /**
     * @param mixed $column
     * @param mixed $operator
     * @param mixed $value
     * @return static
     */
    public function where($column, $operator = null, $value = null, string $connector = 'AND')
    {
        return $this->with($column, $operator, $value, $connector);
    }

    /**
     * @param mixed $column
     * @param mixed $operator
     * @param mixed $value
     * @return static
     */
    public function and($column, $operator = null, $value = null)
    {
        return $this->with($column, $operator, $value, 'AND');
    }

    /**
     * @param mixed $column
     * @param mixed $operator
     * @param mixed $value
     * @return static
     */
    public function or($column, $operator = null, $value = null)
    {
        return $this->with($column, $operator, $value, 'OR');
    }

    /**
     * @param mixed $column
     * @param mixed $operator
     * @param mixed $value
     * @return static
     */
    public function with($column, $operator = null, $value = null, string $connector = 'AND')
    {
        if ($this->sql !== '') {
            $this->sql .= " $connector ";
        }
        if ($operator !== null) {
            if (is_string($operator)) {
                $this->sql .= $this->convertOperandToString($column) . " $operator " .
                    $this->convertValueToString($value, $operator);
            } else {
                $this->sql .= "$column " . $this->convertValueToString($operator, $column);
            }
        } else {
            $this->sql .= $this->convertOperandToString($column);
        }
        return $this;
    }

    /**
     * @param mixed $expression
     */
    protected function convertOperandToString($expression): string
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
        if ($expression instanceof ConditionalExpression) {
            return $this->conditionToString($expression);
        }
        if (is_array($expression)) {
            return $this->arrayToString($expression);
        }
        if ($expression instanceof Closure) {
            return $this->closureToString($expression);
        }
        return (string)$expression;
    }

    /**
     * @param mixed $expression
     */
    private function convertValueToString($expression, string $operator): string
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
            return $this->arrayValueToString($expression, $operator);
        }
        $param = self::nextParameterName();
        $this->params[$param] = $expression;
        return ":$param";
    }

    protected function conditionToString(ConditionalExpression $expression): string
    {
        $this->addParams($expression->getParams());
        return "($expression)";
    }

    protected function closureToString(Closure $expression): string
    {
        $conditions = new ConditionalExpression();
        $expression($conditions);
        return $this->convertOperandToString($conditions);
    }

    protected function arrayToString(array $expression): string
    {
        $list = [];
        foreach ($expression as $key => $value) {
            if (is_numeric($key)) {
                $list[] = $this->convertOperandToString($value);
            } else {
                $list[] = $this->convertOperandToString($key) .
                    ' = ' . $this->convertValueToString($value, '=');
            }
        }
        return implode(' AND ', $list);
    }

    protected function arrayValueToString(array $expression, string $operator): string
    {
        $isBetween = $this->isBetween($operator);
        $list = [];
        foreach ($expression as $value) {
            $list[] = $this->convertValueToString($value, $operator);
        }
        $sql = implode($isBetween ? ' AND ' : ', ', $list);
        if (!$isBetween) {
            $sql = "($sql)";
        }
        return $sql;
    }

    protected function isBetween(string $operator): bool
    {
        $op = strtolower($operator);
        return $op === 'between' || $op === 'not between';
    }
}
