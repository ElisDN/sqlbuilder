<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\Sql\Clause;

use AlephTools\SqlBuilder\Query;

trait QueryClause
{
    protected ?Query $query = null;

    /**
     * @return static
     */
    public function select(Query $query)
    {
        $this->query = $query;
        $this->built = false;
        return $this;
    }

    protected function buildQuery(): void
    {
        if ($this->query) {
            $this->sql .= " $this->query";
            $this->addParams($this->query->getParams());
        }
    }
}
