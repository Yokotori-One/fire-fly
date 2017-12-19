<?php
/**
 * Navigation.php
 * Copyright (c) 2017 thegrumpydictator@gmail.com
 *
 * This file is part of Firefly III.
 *
 * Firefly III is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Firefly III is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Firefly III. If not, see <http://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace FireflyIII\Support;

use Carbon\Carbon;
use FireflyIII\Exceptions\FireflyException;

/**
 * Class Navigation.
 */
class Navigation
{
    /**
     * @param \Carbon\Carbon $theDate
     * @param                $repeatFreq
     * @param                $skip
     *
     * @return \Carbon\Carbon
     *
     * @throws FireflyException
     */
    public function addPeriod(Carbon $theDate, string $repeatFreq, int $skip): Carbon
    {
        $date = clone $theDate;
        $add  = ($skip + 1);

        $functionMap = [
            '1D'      => 'addDays', 'daily' => 'addDays',
            '1W'      => 'addWeeks', 'weekly' => 'addWeeks', 'week' => 'addWeeks',
            '1M'      => 'addMonths', 'month' => 'addMonths', 'monthly' => 'addMonths', '3M' => 'addMonths',
            'quarter' => 'addMonths', 'quarterly' => 'addMonths', '6M' => 'addMonths', 'half-year' => 'addMonths',
            'year'    => 'addYears', 'yearly' => 'addYears', '1Y' => 'addYears',
            'custom'  => 'addMonths', // custom? just add one month.
        ];
        $modifierMap = [
            'quarter'   => 3,
            '3M'        => 3,
            'quarterly' => 3,
            '6M'        => 6,
            'half-year' => 6,
        ];

        if (!isset($functionMap[$repeatFreq])) {
            throw new FireflyException(sprintf('Cannot do addPeriod for $repeat_freq "%s"', $repeatFreq));
        }
        if (isset($modifierMap[$repeatFreq])) {
            $add = $add * $modifierMap[$repeatFreq];
        }
        $function = $functionMap[$repeatFreq];
        $date->$function($add);

        // if period is 1M and diff in month is 2 and new DOM = 1, sub a day:
        $months     = ['1M', 'month', 'monthly'];
        $difference = $date->month - $theDate->month;
        if (in_array($repeatFreq, $months) && 2 === $difference && 1 === $date->day) {
            $date->subDay();
        }

        return $date;
    }

    /**
     * @param \Carbon\Carbon $end
     * @param                $repeatFreq
     *
     * @return \Carbon\Carbon
     *
     * @throws FireflyException
     */
    public function endOfPeriod(\Carbon\Carbon $end, string $repeatFreq): Carbon
    {
        $currentEnd = clone $end;

        $functionMap = [
            '1D'   => 'endOfDay', 'daily' => 'endOfDay',
            '1W'   => 'addWeek', 'week' => 'addWeek', 'weekly' => 'addWeek',
            '1M'   => 'addMonth', 'month' => 'addMonth', 'monthly' => 'addMonth',
            '3M'   => 'addMonths', 'quarter' => 'addMonths', 'quarterly' => 'addMonths', '6M' => 'addMonths', 'half-year' => 'addMonths',
            'year' => 'addYear', 'yearly' => 'addYear', '1Y' => 'addYear',
        ];
        $modifierMap = [
            'quarter'   => 3,
            '3M'        => 3,
            'quarterly' => 3,
            'half-year' => 6,
            '6M'        => 6,
        ];

        $subDay = ['week', 'weekly', '1W', 'month', 'monthly', '1M', '3M', 'quarter', 'quarterly', '6M', 'half-year', 'year', 'yearly'];

        // if the range is custom, the end of the period
        // is another X days (x is the difference between start)
        // and end added to $theCurrentEnd
        if ('custom' === $repeatFreq) {
            /** @var Carbon $tStart */
            $tStart = session('start', Carbon::now()->startOfMonth());
            /** @var Carbon $tEnd */
            $tEnd       = session('end', Carbon::now()->endOfMonth());
            $diffInDays = $tStart->diffInDays($tEnd);
            $currentEnd->addDays($diffInDays);

            return $currentEnd;
        }

        if (!isset($functionMap[$repeatFreq])) {
            throw new FireflyException(sprintf('Cannot do endOfPeriod for $repeat_freq "%s"', $repeatFreq));
        }
        $function = $functionMap[$repeatFreq];
        if (isset($modifierMap[$repeatFreq])) {
            $currentEnd->$function($modifierMap[$repeatFreq]);

            if (in_array($repeatFreq, $subDay)) {
                $currentEnd->subDay();
            }

            return $currentEnd;
        }
        $currentEnd->$function();
        if (in_array($repeatFreq, $subDay)) {
            $currentEnd->subDay();
        }

        return $currentEnd;
    }

    /**
     * @param \Carbon\Carbon      $theCurrentEnd
     * @param string              $repeatFreq
     * @param \Carbon\Carbon|null $maxDate
     *
     * @return Carbon
     */
    public function endOfX(Carbon $theCurrentEnd, string $repeatFreq, ?Carbon $maxDate): Carbon
    {
        $functionMap = [
            '1D'        => 'endOfDay',
            'daily'     => 'endOfDay',
            '1W'        => 'endOfWeek',
            'week'      => 'endOfWeek',
            'weekly'    => 'endOfWeek',
            'month'     => 'endOfMonth',
            '1M'        => 'endOfMonth',
            'monthly'   => 'endOfMonth',
            '3M'        => 'lastOfQuarter',
            'quarter'   => 'lastOfQuarter',
            'quarterly' => 'lastOfQuarter',
            '1Y'        => 'endOfYear',
            'year'      => 'endOfYear',
            'yearly'    => 'endOfYear',
        ];

        $currentEnd = clone $theCurrentEnd;

        if (isset($functionMap[$repeatFreq])) {
            $function = $functionMap[$repeatFreq];
            $currentEnd->$function();
        }

        if (null !== $maxDate && $currentEnd > $maxDate) {
            return clone $maxDate;
        }

        return $currentEnd;
    }

    /**
     * @param \Carbon\Carbon $start
     * @param \Carbon\Carbon $end
     *
     * @return array
     */
    public function listOfPeriods(Carbon $start, Carbon $end): array
    {
        // define period to increment
        $increment     = 'addDay';
        $format        = self::preferredCarbonFormat($start, $end);
        $displayFormat = strval(trans('config.month_and_day'));
        // increment by month (for year)
        if ($start->diffInMonths($end) > 1) {
            $increment     = 'addMonth';
            $displayFormat = strval(trans('config.month'));
        }

        // increment by year (for multi year)
        if ($start->diffInMonths($end) > 12) {
            $increment     = 'addYear';
            $displayFormat = strval(trans('config.year'));
        }

        $begin   = clone $start;
        $entries = [];
        while ($begin < $end) {
            $formatted           = $begin->format($format);
            $displayed           = $begin->formatLocalized($displayFormat);
            $entries[$formatted] = $displayed;

            $begin->$increment();
        }

        return $entries;
    }

    /**
     * @param \Carbon\Carbon $theDate
     * @param                $repeatFrequency
     *
     * @return string
     *
     * @throws FireflyException
     */
    public function periodShow(Carbon $theDate, string $repeatFrequency): string
    {
        $date      = clone $theDate;
        $formatMap = [
            '1D'      => trans('config.specific_day'),
            'daily'   => trans('config.specific_day'),
            'custom'  => trans('config.specific_day'),
            '1W'      => trans('config.week_in_year'),
            'week'    => trans('config.week_in_year'),
            'weekly'  => trans('config.week_in_year'),
            '1M'      => trans('config.month'),
            'month'   => trans('config.month'),
            'monthly' => trans('config.month'),
            '1Y'      => trans('config.year'),
            'year'    => trans('config.year'),
            'yearly'  => trans('config.year'),
            '6M'      => trans('config.half_year'),
        ];

        if (isset($formatMap[$repeatFrequency])) {
            return $date->formatLocalized(strval($formatMap[$repeatFrequency]));
        }
        if ('3M' === $repeatFrequency || 'quarter' === $repeatFrequency) {
            $quarter = ceil($theDate->month / 3);

            return sprintf('Q%d %d', $quarter, $theDate->year);
        }

        // special formatter for quarter of year
        throw new FireflyException(sprintf('No date formats for frequency "%s"!', $repeatFrequency));
    }

    /**
     * If the date difference between start and end is less than a month, method returns "Y-m-d". If the difference is less than a year,
     * method returns "Y-m". If the date difference is larger, method returns "Y".
     *
     * @param \Carbon\Carbon $start
     * @param \Carbon\Carbon $end
     *
     * @return string
     */
    public function preferredCarbonFormat(Carbon $start, Carbon $end): string
    {
        $format = 'Y-m-d';
        if ($start->diffInMonths($end) > 1) {
            $format = 'Y-m';
        }

        if ($start->diffInMonths($end) > 12) {
            $format = 'Y';
        }

        return $format;
    }

    /**
     * If the date difference between start and end is less than a month, method returns trans(config.month_and_day). If the difference is less than a year,
     * method returns "config.month". If the date difference is larger, method returns "config.year".
     *
     * @param \Carbon\Carbon $start
     * @param \Carbon\Carbon $end
     *
     * @return string
     */
    public function preferredCarbonLocalizedFormat(Carbon $start, Carbon $end): string
    {
        $format = strval(trans('config.month_and_day'));
        if ($start->diffInMonths($end) > 1) {
            $format = strval(trans('config.month'));
        }

        if ($start->diffInMonths($end) > 12) {
            $format = strval(trans('config.year'));
        }

        return $format;
    }

    /**
     * If the date difference between start and end is less than a month, method returns "endOfDay". If the difference is less than a year,
     * method returns "endOfMonth". If the date difference is larger, method returns "endOfYear".
     *
     * @param \Carbon\Carbon $start
     * @param \Carbon\Carbon $end
     *
     * @return string
     */
    public function preferredEndOfPeriod(Carbon $start, Carbon $end): string
    {
        $format = 'endOfDay';
        if ($start->diffInMonths($end) > 1) {
            $format = 'endOfMonth';
        }

        if ($start->diffInMonths($end) > 12) {
            $format = 'endOfYear';
        }

        return $format;
    }

    /**
     * If the date difference between start and end is less than a month, method returns "1D". If the difference is less than a year,
     * method returns "1M". If the date difference is larger, method returns "1Y".
     *
     * @param \Carbon\Carbon $start
     * @param \Carbon\Carbon $end
     *
     * @return string
     */
    public function preferredRangeFormat(Carbon $start, Carbon $end): string
    {
        $format = '1D';
        if ($start->diffInMonths($end) > 1) {
            $format = '1M';
        }

        if ($start->diffInMonths($end) > 12) {
            $format = '1Y';
        }

        return $format;
    }

    /**
     * If the date difference between start and end is less than a month, method returns "%Y-%m-%d". If the difference is less than a year,
     * method returns "%Y-%m". If the date difference is larger, method returns "%Y".
     *
     * @param \Carbon\Carbon $start
     * @param \Carbon\Carbon $end
     *
     * @return string
     */
    public function preferredSqlFormat(Carbon $start, Carbon $end): string
    {
        $format = '%Y-%m-%d';
        if ($start->diffInMonths($end) > 1) {
            $format = '%Y-%m';
        }

        if ($start->diffInMonths($end) > 12) {
            $format = '%Y';
        }

        return $format;
    }

    /**
     * @param \Carbon\Carbon $theDate
     * @param                $repeatFreq
     *
     * @return \Carbon\Carbon
     *
     * @throws FireflyException
     */
    public function startOfPeriod(Carbon $theDate, string $repeatFreq): Carbon
    {
        $date = clone $theDate;

        $functionMap = [
            '1D'        => 'startOfDay',
            'daily'     => 'startOfDay',
            '1W'        => 'startOfWeek',
            'week'      => 'startOfWeek',
            'weekly'    => 'startOfWeek',
            'month'     => 'startOfMonth',
            '1M'        => 'startOfMonth',
            'monthly'   => 'startOfMonth',
            '3M'        => 'firstOfQuarter',
            'quarter'   => 'firstOfQuarter',
            'quarterly' => 'firstOfQuarter',
            'year'      => 'startOfYear',
            'yearly'    => 'startOfYear',
            '1Y'        => 'startOfYear',
        ];
        if (isset($functionMap[$repeatFreq])) {
            $function = $functionMap[$repeatFreq];
            $date->$function();

            return $date;
        }
        if ('half-year' === $repeatFreq || '6M' === $repeatFreq) {
            $month = $date->month;
            $date->startOfYear();
            if ($month >= 7) {
                $date->addMonths(6);
            }

            return $date;
        }
        if ('custom' === $repeatFreq) {
            return $date; // the date is already at the start.
        }

        throw new FireflyException(sprintf('Cannot do startOfPeriod for $repeat_freq "%s"', $repeatFreq));
    }

    /**
     * @param \Carbon\Carbon $theDate
     * @param                $repeatFreq
     * @param int            $subtract
     *
     * @return \Carbon\Carbon
     *
     * @throws FireflyException
     */
    public function subtractPeriod(Carbon $theDate, string $repeatFreq, int $subtract = 1): Carbon
    {
        $date = clone $theDate;
        // 1D 1W 1M 3M 6M 1Y
        $functionMap = [
            '1D'      => 'subDays',
            'daily'   => 'subDays',
            'week'    => 'subWeeks',
            '1W'      => 'subWeeks',
            'weekly'  => 'subWeeks',
            'month'   => 'subMonths',
            '1M'      => 'subMonths',
            'monthly' => 'subMonths',
            'year'    => 'subYears',
            '1Y'      => 'subYears',
            'yearly'  => 'subYears',
        ];
        $modifierMap = [
            'quarter'   => 3,
            '3M'        => 3,
            'quarterly' => 3,
            'half-year' => 6,
            '6M'        => 6,
        ];
        if (isset($functionMap[$repeatFreq])) {
            $function = $functionMap[$repeatFreq];
            $date->$function($subtract);

            return $date;
        }
        if (isset($modifierMap[$repeatFreq])) {
            $subtract = $subtract * $modifierMap[$repeatFreq];
            $date->subMonths($subtract);

            return $date;
        }
        // a custom range requires the session start
        // and session end to calculate the difference in days.
        // this is then subtracted from $theDate (* $subtract).
        if ('custom' === $repeatFreq) {
            /** @var Carbon $tStart */
            $tStart = session('start', Carbon::now()->startOfMonth());
            /** @var Carbon $tEnd */
            $tEnd       = session('end', Carbon::now()->endOfMonth());
            $diffInDays = $tStart->diffInDays($tEnd);
            $date->subDays($diffInDays * $subtract);

            return $date;
        }

        throw new FireflyException(sprintf('Cannot do subtractPeriod for $repeat_freq "%s"', $repeatFreq));
    }

    /**
     * @param                $range
     * @param \Carbon\Carbon $start
     *
     * @return \Carbon\Carbon
     *
     * @throws FireflyException
     */
    public function updateEndDate(string $range, Carbon $start): Carbon
    {
        $functionMap = [
            '1D'     => 'endOfDay',
            '1W'     => 'endOfWeek',
            '1M'     => 'endOfMonth',
            '3M'     => 'lastOfQuarter',
            '1Y'     => 'endOfYear',
            'custom' => 'startOfMonth', // this only happens in test situations.
        ];
        $end         = clone $start;

        if (isset($functionMap[$range])) {
            $function = $functionMap[$range];
            $end->$function();

            return $end;
        }
        if ('6M' === $range) {
            if ($start->month >= 7) {
                $end->endOfYear();

                return $end;
            }
            $end->startOfYear()->addMonths(6);

            return $end;
        }
        throw new FireflyException(sprintf('updateEndDate cannot handle range "%s"', $range));
    }

    /**
     * @param                $range
     * @param \Carbon\Carbon $start
     *
     * @return \Carbon\Carbon
     *
     * @throws FireflyException
     */
    public function updateStartDate(string $range, Carbon $start): Carbon
    {
        $functionMap = [
            '1D'     => 'startOfDay',
            '1W'     => 'startOfWeek',
            '1M'     => 'startOfMonth',
            '3M'     => 'firstOfQuarter',
            '1Y'     => 'startOfYear',
            'custom' => 'startOfMonth', // this only happens in test situations.
        ];
        if (isset($functionMap[$range])) {
            $function = $functionMap[$range];
            $start->$function();

            return $start;
        }
        if ('6M' === $range) {
            if ($start->month >= 7) {
                $start->startOfYear()->addMonths(6);

                return $start;
            }
            $start->startOfYear();

            return $start;
        }
        throw new FireflyException(sprintf('updateStartDate cannot handle range "%s"', $range));
    }
}
