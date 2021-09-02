<?php

declare(strict_types=1);

namespace OchoPhpUtils;

class DatetimeLibs
{
    /**
     * Converts the seconds to JIRA Time format. For example:
     * Seconds: 3480 -> "58m"
     * Seconds: 13560 -> "3h 46m"
     * @param int $seconds The time in seconds
     * @return string Return the string in JIRA Time format
     */
    public function secondsToJiraTime(int $seconds): string
    {
        if ($seconds <= 0) {
            return '';
        }
        $return      = '';
        $seconds_new = 0;
        $minutes     = 0;
        $hours       = 0;

        if ($seconds > 60) {
            $minutes     = (int)floor($seconds / 60);
            $seconds_new = $seconds % 60;

            if ($minutes > 60) {
                $hours   = (int)floor($minutes / 60);
                $minutes = $minutes % 60;
            }
        }

        if ($hours > 0) {
            $return .= $hours . 'h';
        }
        if ($minutes > 0) {
            $return .= ' ' . $minutes . 'm';
        }
        if ($seconds_new > 0) {
            $return .= ' ' . $seconds_new . 's';
        }

        return trim($return);
    }
}
