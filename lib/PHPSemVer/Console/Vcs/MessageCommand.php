<?php
/**
 * Contains command.
 *
 * LICENSE: This source file is subject to the MIT license
 * that is available through the world-wide-web at the following URI:
 * https://opensource.org/licenses/MIT. If you did not receive a copy
 * of the PHP License and are unable to obtain it through the web, please send
 * a note to pretzlaw@gmail.com so we can mail you a copy immediately.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015 Mike Pretzlaw
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.1.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */


namespace PHPSemVer\Console\Vcs;


use PHPSemVer\Config\RuleSet;
use PHPSemVer\Console\AbstractCommand;
use PHPSemVer\Constraints\FailedConstraint;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Generate commit message about the changes.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015 Mike Pretzlaw
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.1.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */
class MessageCommand extends AbstractCommand {
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->compareTrees();

        $out = [];

        foreach ($this->getEnvironment()->getConfig()->ruleSet() as $ruleSet) {
            /* @var RuleSet $ruleSet */
            foreach ($ruleSet->getErrorMessages() as $message) {
                /* @var FailedConstraint $message */
                $out[] = '- ' . $message->getMessage();
            }
        }

        natsort($out);

        $output->writeln($out);
    }

    protected function fetchResult()
    {
        $this->getConfig();
    }

}