<?php

class Date extends Kohana_Date {

	private static function datetime_locale($term, $count = 0)
	{
		$term .= ($count == 1) ? '.one' : '.other';
		return str_replace(':count', $count, i18n::get($term));
	}

	/**
	 * Reports the approximate distance in time between two integers as seconds.
	 * This method is a port of DateHelper `distance_of_time_in_words()`
	 * from Ruby on Rails.
	 * Set <tt>$include_seconds</tt> to true if you want more detailed approximations
	 * when distance < 1 min, 29 secs.
	 * Distances are reported based on the following table:
	 *
	 *   0 <-> 29 secs                                                        => less than a minute
	 *   30 secs <-> 1 min, 29 secs                                           => 1 minute
	 *   1 min, 30 secs <-> 44 mins, 29 secs                                  => [2..44] minutes
	 *   44 mins, 30 secs <-> 89 mins, 29 secs                                => about 1 hour
	 *   89 mins, 30 secs <-> 23 hrs, 59 mins, 29 secs                        => about [2..24] hours
	 *   23 hrs, 59 mins, 30 secs <-> 41 hrs, 59 mins, 29 secs                => 1 day
	 *   41 hrs, 59 mins, 30 secs  <-> 29 days, 23 hrs, 59 mins, 29 secs      => [2..29] days
	 *   29 days, 23 hrs, 59 mins, 30 secs <-> 59 days, 23 hrs, 59 mins, 29 secs => about 1 month
	 *   59 days, 23 hrs, 59 mins, 30 secs <-> 1 yr minus 1 sec               => [2..12] months
	 *   1 yr <-> 1 yr, 3 months                                              => about 1 year
	 *   1 yr, 3 months <-> 1 yr, 9 months                                    => over 1 year
	 *   1 yr, 9 months <-> 2 yr minus 1 sec                                  => almost 2 years
	 *   2 yrs <-> max time or date                                           => (same rules as 1 yr)
	 *
	 * With <tt>$include_seconds</tt> = true and the difference < 1 minute 29 seconds:
	 *   0-4   secs* => less than 5 seconds
	 *   5-9   secs* => less than 10 seconds
	 *   10-19 secs* => less than 20 seconds
	 *   20-39 secs* => half a minute
	 *   40-59 secs* => less than a minute
	 *   60-89 secs* => 1 minute
	 *
	 * Examples
	 *   $from_time = time();
	 *   Date::distance_of_time_in_words($from_time, $from_time + 50 * 60); // about 1 hour
	 *   Date::distance_of_time_in_words($from_time, strtotime('+ 50 minutes')); // about 1 hour
	 *   Date::distance_of_time_in_words($from_time, $from_time + 15); // less than a minute
	 *   Date::distance_of_time_in_words($from_time, $from_time + 15, true); // less than 20 seconds
	 *   Date::distance_of_time_in_words($from_time, strtotime('+ 3 years')); // about 3 years
	 *   Date::distance_of_time_in_words($from_time, $from_time + 60 * 60 * 60); // 3 days
	 *   Date::distance_of_time_in_words($from_time, $from_time + 45, true); // less than a minute
	 *   Date::distance_of_time_in_words($from_time, $from_time - 45, true); // less than a minute
	 *   Date::distance_of_time_in_words($from_time, time() + 76); // 1 minute
	 *   Date::distance_of_time_in_words($from_time, strtotime('+ 1 year 3 days')); // about 1 year
	 *   Date::distance_of_time_in_words($from_time, strtotime('+ 3 years 6 months')); // over 3 years
	 *   Date::distance_of_time_in_words($from_time, strtotime('+ 4 years 9 days 30 minutes 5 seconds')); // about 4 years
	 *
	 *   $to_time = strtotime('+ 6 years 19 days');
	 *   Date::distance_of_time_in_words($from_time, $to_time, true); // about 6 years
	 *   Date::distance_of_time_in_words($to_time, $from_time, true); // about 6 years
	 *   Date::distance_of_time_in_words(time(), time()); // less than a minute
	 *
	 * @param   integer  "from time" timestamp
	 * @param   integer  "to time" timestamp
	 * @param   boolean  include seconds into output or not
	 * @return  string
	 */
	public static function distance_of_time_in_words($from_time, $to_time = 0, $include_seconds = false)
	{
		$distance_in_minutes = round((abs($to_time - $from_time)) / 60);
		$distance_in_seconds = round( abs($to_time - $from_time));
	
		if ($distance_in_minutes < 0)
		{
			return '';
		}

		if ($distance_in_minutes <= 1) // when (0..1)
		{
			if (! $include_seconds)
			{
				return $distance_in_minutes == 0
					? self::datetime_locale('less_than_x_minutes', $count = 1)
					: self::datetime_locale('x_minutes', $count = $distance_in_minutes);
			}

			if ($distance_in_seconds >= 0)
			{
				if ($distance_in_seconds <= 4) // when (0..4)
				{
					return self::datetime_locale('less_than_x_seconds', $count = 5);
				}
				else if ($distance_in_seconds <= 9) // when (5..9)
				{
					return self::datetime_locale('less_than_x_seconds', $count = 10);
				}
				else if ($distance_in_seconds <= 19) // when (10..19)
				{
					return self::datetime_locale('less_than_x_seconds', $count = 20);
				}
				else if ($distance_in_seconds <= 39) // when (20..39)
				{
					return self::datetime_locale('half_a_minute');
				}
				else if ($distance_in_seconds <= 59) // when (40..59)
				{
					return self::datetime_locale('less_than_x_minutes', $count = 1);
				}
				else
				{
					return self::datetime_locale('x_minutes', $count = 1);
				}
			}
		}
		else if ($distance_in_minutes <= 44) // when (2..44)
		{
			return self::datetime_locale('x_minutes', $count = $distance_in_minutes);
		}
		else if ($distance_in_minutes <= 89) // when (45..89)
		{
			return self::datetime_locale('about_x_hours', $count = 1);
		}
		else if ($distance_in_minutes <= 1439) // when (90..1439)
		{
			return self::datetime_locale('about_x_hours', $count = round($distance_in_minutes * 1.0 / 60.0));
		}
		else if ($distance_in_minutes <= 2519) // when (1440..2519)
		{
			return self::datetime_locale('x_days', $count = 1);
		}
		else if ($distance_in_minutes <= 43199) // when (2520..43199)
		{
			return self::datetime_locale('x_days', $count = round($distance_in_minutes * 1.0 / 1440.0));
		}
		else if ($distance_in_minutes <= 86399) // when (43200..86399 )
		{
			return self::datetime_locale('about_x_months', $count = 1);
		}
		else if ($distance_in_minutes <= 525599) // when (86400..525599)
		{
			return self::datetime_locale('x_months', $count = round($distance_in_minutes * 1.0 / 43200.0));
		}
		else
		{
			$fyear = (int)date('Y', $from_time);
			if ((int)date('n', $from_time) >= 3)
			{
				$fyear++;
			}
			$tyear = date('Y', $to_time);
			if ((int)date('n', $to_time) < 3)
			{
				$tyear--;
			}
			$leap_years = 0;
			if ($fyear <= $tyear)
			{
				for ($i = $fyear; $i <= $tyear; $i++)
				{
					$leap_years += (int)date('L', strtotime("$i-01-01"));
				}
			}
			$minute_offset_for_leap_year = $leap_years * 1440;
			// Discount the leap year days when calculating year distance.
			// e.g. if there are 20 leap year days between 2 dates having the same day
			// and month then the based on 365 days calculation
			// the distance in years will come out to over 80 years when in written
			// english it would read better as about 80 years.
			$minutes_with_offset = $distance_in_minutes - $minute_offset_for_leap_year;
			$remainder = ($minutes_with_offset % 525600);
			$distance_in_years = (int)($minutes_with_offset / 525600);
			if ($remainder < 131400)
			{
				return self::datetime_locale('about_x_years', $count = $distance_in_years);
			}
			else if ($remainder < 394200)
			{
				return self::datetime_locale('over_x_years', $count = $distance_in_years);
			}
			else
			{
				return self::datetime_locale('almost_x_years', $count = $distance_in_years + 1);
			}
		}
	}

	/**
	 * Returns the difference between a time and now in a "fuzzy" way.
	 * Displaying a fuzzy time instead of a date is usually faster to read
	 * and understand. Text generation algorythm is based DateHelper
	 * `distance_of_time_in_words()` from Ruby on Rails.
	 *
	 * @param   integer  "remote" timestamp
	 * @return  string
	 */
	public static function fuzzy_span($timestamp)
	{
		$now = time();
		$span = self::distance_of_time_in_words($timestamp, $now);
		if ($timestamp <= $now)
		{
			// This is in the past
			return $span . ' ago';
		}
		else
		{
			// This in the future
			return 'in ' . $span;
		}
	}
}
