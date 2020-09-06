<?php

namespace Exadsrcise\Application\Commands;

use Exadsrcise\Application\Contracts\CommandInterface;
use Exadsrcise\Domain\Promotion\PromotionSelector;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Promotion extends Command implements CommandInterface
{
    const DESCRIPTION = 'A/B test a number of promotional designs to see which provides the best conversion rate.';
    const HELP = 'With given promotion model, we will pick one promotion based on its percentage.';

    /**
     * @var string $defaultName Name of command
     */
    protected static $defaultName = 'run:promotion';

    /**
     * Configure command
     */
    protected function configure()
    {
        $this
            // the short description shown while running command
            ->setDescription(self::DESCRIPTION)

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp(self::HELP);
    }

    /**
     * Command logic execution
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $promotionPicker = new PromotionSelector();

        $promotionPicker->pushPromotion(new \Exadsrcise\Model\Promotion("Design no. 1", 50));
        $promotionPicker->pushPromotion(new \Exadsrcise\Model\Promotion("Design no. 2", 25));
        $promotionPicker->pushPromotion(new \Exadsrcise\Model\Promotion("Design no. 3", 25));

        $selected = $promotionPicker->pickOne();

        $output->writeln(
            "The selected item was {$selected->getName()} with percent of {$selected->weight()}%"
        );

        return Command::SUCCESS;
    }
}
