<?php

declare(strict_types = 1);

namespace Statistics\Calculator;

use SocialPost\Dto\SocialPostTo;
use Statistics\Dto\StatisticsTo;

class NoopCalculator extends AbstractCalculator
{
    protected const UNITS = 'posts';
    
    private $totals = [];
    
    /**
     * @inheritDoc
     */
    protected function doAccumulate(SocialPostTo $postTo): void
    {
        // Noops
        $key = $postTo->getAuthorName();
        $this->totals[$key] = ($this->totals[$key] ?? 0) + 1;
    }

    /**
     * @inheritDoc
     */
    protected function doCalculate(): StatisticsTo
    {
        $stats = new StatisticsTo();
        
        foreach ($this->totals as $splitUser => $total) {
            $child = (new StatisticsTo())
                ->setName($this->parameters->getStatName())
                ->setSplitPeriod($splitUser)
                ->setValue($total)
                ->setUnits(self::UNITS);
            $stats->addChild($child);
        }
        return $stats;
    }
}