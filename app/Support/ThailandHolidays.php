<?php

namespace App\Support;

/**
 * Lookup of Thai public holidays on which no worship event may be scheduled.
 *
 * Used by StoreEventRequest::after(). Keys are 'Y-m-d' strings so a lookup
 * is a simple array_key_exists().
 *
 * TODO: The list ends in December 2027. Move holidays into a database table
 *       with an admin screen so staff can maintain them (bonus task).
 */
class ThailandHolidays
{
    // Weekday holidays and observances only; weekends and seasons are excluded.
    // Sources: https://www.timeanddate.com/holidays/thailand/2026
    //          https://www.timeanddate.com/holidays/thailand/2027
    // Checked 2026-10-22. Update this dated list when new dates are published.
    public const DATES = [
        '2026-01-01' => 'New Year',
        '2026-01-02' => 'New Year special holiday',
        '2026-01-16' => "Teachers' Day",
        '2026-02-17' => 'Chinese New Year (day 1)',
        '2026-02-18' => 'Chinese New Year (day 2)',
        '2026-02-19' => 'Chinese New Year (day 3)',
        '2026-03-03' => 'Makha Bucha',
        '2026-04-06' => 'Chakri Day',
        '2026-04-13' => 'Songkran',
        '2026-04-14' => 'Songkran',
        '2026-04-15' => 'Songkran',
        '2026-05-01' => 'Labor Day',
        '2026-05-04' => 'Coronation Day',
        '2026-05-13' => 'Royal Ploughing Ceremony',
        '2026-06-01' => 'Visakha Bucha substitute day',
        '2026-06-03' => "Queen Suthida's birthday",
        '2026-07-28' => "King Vajiralongkorn's birthday",
        '2026-07-29' => 'Asalha Bucha',
        '2026-08-12' => "Queen Mother's birthday",
        '2026-09-24' => 'Mahidol Day',
        '2026-10-13' => 'King Bhumibol memorial',
        '2026-10-23' => 'Chulalongkorn Day',
        '2026-12-07' => "King Bhumibol's birthday substitute day",
        '2026-12-10' => 'Constitution Day',
        '2026-12-24' => 'Christmas Eve',
        '2026-12-25' => 'Christmas',
        '2026-12-31' => "New Year's Eve",
        '2027-01-01' => 'New Year',
        '2027-02-08' => 'Chinese New Year (day 3)',
        '2027-02-22' => 'Makha Bucha substitute day',
        '2027-04-06' => 'Chakri Day',
        '2027-04-13' => 'Songkran',
        '2027-04-14' => 'Songkran',
        '2027-04-15' => 'Songkran',
        '2027-05-03' => 'Labor Day substitute day',
        '2027-05-04' => 'Coronation Day',
        '2027-05-20' => 'Visakha Bucha',
        '2027-06-03' => "Queen Suthida's birthday",
        '2027-07-19' => 'Asalha Bucha substitute day',
        '2027-07-28' => "King Vajiralongkorn's birthday",
        '2027-08-12' => "Queen Mother's birthday",
        '2027-09-24' => 'Mahidol Day',
        '2027-10-13' => 'King Bhumibol memorial',
        '2027-10-25' => 'Chulalongkorn Day substitute day',
        '2027-12-06' => "King Bhumibol's birthday substitute day",
        '2027-12-10' => 'Constitution Day',
        '2027-12-24' => 'Christmas Eve',
        '2027-12-31' => "New Year's Eve",
    ];

    /**
     * Whether the given 'Y-m-d' date is a listed holiday.
     */
    public static function isHoliday(string $date): bool
    {
        return array_key_exists($date, self::DATES);
    }
}
