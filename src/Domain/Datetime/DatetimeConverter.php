<?php

namespace Exadsrcise\Domain\Datetime;

use DateTime;
use DateTimeZone;
use Exadsrcise\Domain\Datetime\Exceptions\InvalidWeekDay;
use function strtotime;

class DatetimeConverter
{
    /**
     * @var DateTimeZone $timeZone Current timezone instance
     */
    private DateTimeZone $timeZone;

    /**
     * @var DateTime $date Date to convert
     */
    private DateTime $date;

    /**
     * DatetimeConverter constructor.
     *
     * @param string        $timeZone
     * @param DateTime|null $now
     */
    public function __construct(string $timeZone = 'UTC', DateTime $now = null)
    {
        $this->date = $now ?? new DateTime();

        $this->date->setTimezone(
            $this->timeZone = new DateTimeZone($timeZone)
        );
    }

    /**
     * Calculate next date of given week day, based on given at time.
     * If at time is not given, current date will be used.
     *
     * @param string        $weekDay
     * @param DateTime|null $at
     *
     * @return DateTime
     * @throws InvalidWeekDay
     */
    public function getNextDateOfWeekDay(string $weekDay, DateTime $at = null) : DateTime
    {
        // Apply now as date
        if( $at === null )
            $at = new DateTime();

        // Sanitize given week day and create future date based on it
        if( ! $nextDate = $this->getStrtotime("next {$weekDay} {$at->format('H:i:s')}", $at) )
            throw new InvalidWeekDay("The given '{$weekDay}' is an invalid week day");

        // Change time of next date
        return $at->setTimestamp($nextDate);
    }

    /**
     * Get strtotime using given datetime instance
     *
     * @param string        $time time string
     * @param DateTime|null $now now time
     *
     * @return false|int
     */
    private function getStrtotime(string $time, DateTime $now = null)
    {
        return strtotime($time, ($now ?? $this->date)->getTimestamp());
    }


}
