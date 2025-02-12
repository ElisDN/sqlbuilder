<?php

declare(strict_types=1);

namespace AlephTools\SqlBuilder\MySql\Clause;

use AlephTools\SqlBuilder\Sql\Clause\InsertClause as BaseInsertClause;

trait InsertClause
{
    use BaseInsertClause;

    protected string $modifiers = '';

    /**
     * @return static
     */
    public function lowPriority()
    {
        return $this->modifier('LOW_PRIORITY');
    }

    /**
     * @return static
     */
    public function highPriority()
    {
        return $this->modifier('HIGH_PRIORITY');
    }

    /**
     * @return static
     */
    public function delayed()
    {
        return $this->modifier('DELAYED');
    }

    /**
     * @return static
     */
    public function ignore()
    {
        return $this->modifier('IGNORE');
    }

    /**
     * @return static
     */
    public function lowPriorityIgnore()
    {
        return $this->modifier('LOW_PRIORITY IGNORE');
    }

    /**
     * @return static
     */
    public function highPriorityIgnore()
    {
        return $this->modifier('HIGH_PRIORITY IGNORE');
    }

    /**
     * @return static
     */
    public function delayedIgnore()
    {
        return $this->modifier('DELAYED IGNORE');
    }

    /**
     * @return static
     */
    public function modifier(string $modifier)
    {
        $this->modifiers .= " $modifier";
        $this->built = true;
        return $this;
    }

    protected function buildInsert(): void
    {
        $this->sql .= 'INSERT';
        if ($this->modifiers !== '') {
            $this->sql .= " $this->modifiers";
        }
        if ($this->table) {
            $this->sql .= " INTO $this->table";
            $this->addParams($this->table->getParams());
        }
    }
}
